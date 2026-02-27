<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   6.1.5
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/src/Helper/CargusV3Client.php';

class Cargus extends CarrierModule
{
    public $id_carrier;

    public function __construct()
    {
        $this->name = 'cargus';
        $this->tab = 'shipping_logistics';
        $this->version = '6.1.5';
        $this->author = 'Quark';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cargus Courier Premium');
        $this->description = $this->l('Advanced shipping integration with Cargus API V3 for PrestaShop 8.2+ and 9.0.');
        $this->ps_versions_compliancy = array('min' => '8.2.0', 'max' => '9.9.9');
    }

    public function install()
    {
        if (parent::install() &&
            $this->installDb() &&
            $this->installTab('AdminCargusDebugger', 'Cargus Debugger', -1) &&
            $this->forceInitialSettings()
        ) {
            $this->trackInstallation('install');
            return true;
        }
        return false;
    }

    public function uninstall()
    {
        $this->uninstallTab('AdminCargusDebugger');
        return parent::uninstall();
    }

    protected function installDb()
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cargus_pudo` (
                `id_pudo` INT(11) NOT NULL AUTO_INCREMENT,
                `pudo_id` VARCHAR(50) NOT NULL,
                `name` VARCHAR(255),
                `city` VARCHAR(100),
                `address` TEXT,
                `active` TINYINT(1) DEFAULT 1,
                PRIMARY KEY (`id_pudo`),
                UNIQUE KEY `pudo_id` (`pudo_id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cargus_agabaritic` (
                `id_rule` INT(11) NOT NULL AUTO_INCREMENT,
                `id_category` INT(11),
                `weight_threshold` DECIMAL(10,2),
                PRIMARY KEY (`id_rule`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;"
        ];

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) return false;
        }
        return true;
    }

    protected function forceInitialSettings()
    {
        Configuration::updateValue('CARGUS_TAX_RULE_ID', 1); 
        Configuration::updateValue('CARGUS_HEAVY_THRESHOLD', 31); 
        return true;
    }

    private function trackInstallation($action)
    {
        $url = 'https://license.quark.com.ro/track'; 
        $data = [
            'domain' => Tools::getHttpHost(),
            'module' => $this->name,
            'version' => $this->version,
            'ps_version' => _PS_VERSION_,
            'php_version' => phpversion(),
            'action' => $action
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        @curl_exec($ch);
        @curl_close($ch);
    }

    private function installTab($className, $tabName, $idParent)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        $tab->id_parent = $idParent;
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab($className)
    {
        $idTab = (int)Tab::getIdFromClassName($className);
        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }

    public function getContent()
    {
        $output = '';

        if ((int)Tab::getIdFromClassName('AdminCargusDebugger') == 0) {
            $this->installTab('AdminCargusDebugger', 'Cargus Debugger', -1);
        }

        if (Tools::isSubmit('submitCargusConfig')) {
            Configuration::updateValue('CARGUS_API_URL', rtrim(Tools::getValue('CARGUS_API_URL'), '/') . '/');
            Configuration::updateValue('CARGUS_SUBSCRIPTION_KEY', Tools::getValue('CARGUS_SUBSCRIPTION_KEY'));
            Configuration::updateValue('CARGUS_USERNAME', Tools::getValue('CARGUS_USERNAME'));
            Configuration::updateValue('CARGUS_PASSWORD', Tools::getValue('CARGUS_PASSWORD'));
            
            // Tab 2 Fields
            Configuration::updateValue('CARGUS_PICKUP_LOCATION', Tools::getValue('CARGUS_PICKUP_LOCATION'));
            Configuration::updateValue('CARGUS_PRICE_PLAN', Tools::getValue('CARGUS_PRICE_PLAN'));
            Configuration::updateValue('CARGUS_DEFAULT_SERVICE', Tools::getValue('CARGUS_DEFAULT_SERVICE'));
            Configuration::updateValue('CARGUS_PAYER', Tools::getValue('CARGUS_PAYER'));
            Configuration::updateValue('CARGUS_COD_TYPE', Tools::getValue('CARGUS_COD_TYPE'));
            Configuration::updateValue('CARGUS_SHIPMENT_TYPE', Tools::getValue('CARGUS_SHIPMENT_TYPE'));
            
            Configuration::updateValue('CARGUS_OPEN_PACKAGE', (int)Tools::getValue('CARGUS_OPEN_PACKAGE'));
            Configuration::updateValue('CARGUS_SATURDAY_DELIVERY', (int)Tools::getValue('CARGUS_SATURDAY_DELIVERY'));
            Configuration::updateValue('CARGUS_INSURANCE', (int)Tools::getValue('CARGUS_INSURANCE'));
            
            Configuration::updateValue('CARGUS_BASIC_PRICE_STD', Tools::getValue('CARGUS_BASIC_PRICE_STD'));
            Configuration::updateValue('CARGUS_BASIC_PRICE_PUDO', Tools::getValue('CARGUS_BASIC_PRICE_PUDO'));
            Configuration::updateValue('CARGUS_EXTRA_KG_PRICE', Tools::getValue('CARGUS_EXTRA_KG_PRICE'));
            Configuration::updateValue('CARGUS_COD_FEE', Tools::getValue('CARGUS_COD_FEE'));
            Configuration::updateValue('CARGUS_HEAVY_THRESHOLD', (float)Tools::getValue('CARGUS_HEAVY_THRESHOLD'));
            
            $output .= $this->displayConfirmation($this->l('Setările au fost salvate cu succes.'));
        }

        $pickupLocations = [];
        $apiError = false;
        try {
            $client = new \Cargus\Helper\CargusV3Client();
            $response = $client->request('PickupLocations', 'GET');
            
            if (isset($response['error'])) {
                $apiError = $response['error'];
            } elseif (is_array($response)) {
                $pickupLocations = $response;
            }
        } catch (Exception $e) {
            $apiError = $e->getMessage();
        }

        $ajax_link = $this->context->link->getAdminLink('AdminCargusDebugger');

        $this->context->smarty->assign([
            'cargus_api_url' => Configuration::get('CARGUS_API_URL', 'https://urgentcargus.azure-api.net/api/'),
            'cargus_subscription_key' => Configuration::get('CARGUS_SUBSCRIPTION_KEY'),
            'cargus_username' => Configuration::get('CARGUS_USERNAME'),
            'cargus_password' => Configuration::get('CARGUS_PASSWORD'),
            
            'cargus_pickup_location' => Configuration::get('CARGUS_PICKUP_LOCATION'),
            'cargus_price_plan' => Configuration::get('CARGUS_PRICE_PLAN'),
            'cargus_default_service' => Configuration::get('CARGUS_DEFAULT_SERVICE'),
            'cargus_payer' => Configuration::get('CARGUS_PAYER', 'Expeditor'),
            'cargus_cod_type' => Configuration::get('CARGUS_COD_TYPE', 'Numerar'),
            'cargus_shipment_type' => Configuration::get('CARGUS_SHIPMENT_TYPE', 'Plic'),
            'cargus_open_package' => Configuration::get('CARGUS_OPEN_PACKAGE', 0),
            'cargus_saturday_delivery' => Configuration::get('CARGUS_SATURDAY_DELIVERY', 0),
            'cargus_insurance' => Configuration::get('CARGUS_INSURANCE', 0),
            
            'cargus_basic_price_std' => Configuration::get('CARGUS_BASIC_PRICE_STD'),
            'cargus_basic_price_pudo' => Configuration::get('CARGUS_BASIC_PRICE_PUDO'),
            'cargus_extra_kg_price' => Configuration::get('CARGUS_EXTRA_KG_PRICE', 0),
            'cargus_cod_fee' => Configuration::get('CARGUS_COD_FEE'),
            'cargus_heavy_threshold' => Configuration::get('CARGUS_HEAVY_THRESHOLD', 31),
            
            'pickup_locations' => $pickupLocations,
            'api_error' => $apiError,
            'cargus_ajax_link' => $ajax_link
        ]);

        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        return $shipping_cost;
    }

    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params, 0);
    }
}
