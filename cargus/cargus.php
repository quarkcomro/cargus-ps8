<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   6.1.1
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Cargus extends CarrierModule
{
    public $id_carrier;
    private $_html = '';
    private $_postErrors = array();

    public function __construct()
    {
        $this->name = 'cargus';
        $this->tab = 'shipping_logistics';
        $this->version = '6.1.1';
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
            $this->registerHook('actionAdminControllerSetMedia') &&
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

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cargus_awb` (
                `id_cargus_awb` INT(11) NOT NULL AUTO_INCREMENT,
                `id_order` INT(11) NOT NULL,
                `awb_number` VARCHAR(50),
                `cost` DECIMAL(10,2),
                `date_add` DATETIME,
                PRIMARY KEY (`id_cargus_awb`)
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
        // Funcția de monitorizare (va fi activată ulterior)
        return true;
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

    public function hookActionAdminControllerSetMedia($params)
    {
        if (Tools::getValue('configure') == $this->name) {
            $ajax_link = $this->context->link->getAdminLink('AdminCargusDebugger');
            Media::addJsDef([
                'cargus_debugger_ajax_url' => $ajax_link,
            ]);
            $this->context->controller->addJS($this->_path . 'views/js/api_debugger.js');
        }
    }

    public function getContent()
    {
        // Până implementăm noul controller Symfony, afișăm interfața standard din fișierul inițial
        return "Modul instalat. Te rugăm să folosești interfața standard pentru configurare.";
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
