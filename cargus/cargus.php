<?php
/**
 * cargus.php - v6.0.9
 * Versiune stabilă: Include Tab-uri, Debugger AJAX și TOATE setările comerciale (PS 1.7).
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

// Autoloader manual pentru servicii
require_once dirname(__FILE__) . '/src/Service/Calculator/CargusPricingService.php';
require_once dirname(__FILE__) . '/src/Service/API/AccountService.php';

class Cargus extends CarrierModule
{
    // Listă completă a cheilor de configurare pentru a evita omisiunile la salvare
    private const ALL_CONFIG_KEYS = [
        'CARGUS_API_URL', 'CARGUS_API_KEY', 'CARGUS_API_USER', 'CARGUS_API_PASS',
        'CARGUS_SENDER_LOCATION_ID', 'CARGUS_PRICE_TABLE_ID', 'CARGUS_SERVICE_ID',
        'CARGUS_PAYER_TYPE', 'CARGUS_PARCEL_TYPE', 'CARGUS_COD_TYPE',
        'CARGUS_INSURANCE', 'CARGUS_OPEN_PKG', 'CARGUS_SATURDAY',
        'CARGUS_PRE10', 'CARGUS_PRE12', 'CARGUS_BASE_PRICE_STD', 
        'CARGUS_BASE_PRICE_PUDO', 'CARGUS_EXTRA_KG_PRICE', 'CARGUS_COD_FEE'
    ];

    public function __construct()
    {
        $this->name = 'cargus';
        $this->tab = 'shipping_logistics';
        $this->version = '6.0.9';
        $this->author = 'Cargus';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Cargus Courier Premium';
        $this->description = 'Modul avansat Cargus V3: Standard & Ship & Go cu Smart Pricing.';
    }

    public function hookActionAdminControllerSetMedia(): void
    {
        // Definim URL-ul pentru AJAX Debugger înainte de a încărca JS-ul
        Media::addJsDef([
            'cargus_ajax_url' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name])
        ]);
        $this->context->controller->addJS($this->_path . 'views/js/cargus_admin.js');
    }

    /**
     * Procesează apelurile AJAX de test din tab-ul Debug
     */
    public function ajaxProcessTestEndpoint(): void
    {
        $endpoint = Tools::getValue('endpoint');
        $service = new \Cargus\Service\API\AccountService();
        $data = [];

        try {
            switch ($endpoint) {
                case 'PickupLocations': $data = $service->getSenderLocations(); break;
                case 'PriceTables': $data = $service->getPriceTables(); break;
                case 'Services': $data = $service->getServices(); break;
            }
            header('Content-Type: application/json');
            die(json_encode(['success' => !empty($data), 'data' => $data]));
        } catch (Exception $e) {
            header('Content-Type: application/json');
            die(json_encode(['success' => false, 'raw' => $e->getMessage()]));
        }
    }

    public function getContent(): string
    {
        // Detectare apel AJAX (Controllerul AdminModules redirectează automat către ajaxProcess...)
        if (Tools::getValue('ajax')) {
            $this->ajaxProcessTestEndpoint();
        }

        $output = '';
        if (Tools::isSubmit('submitCargusConfig')) {
            foreach (self::ALL_CONFIG_KEYS as $key) {
                $val = Tools::getValue($key);
                // Nu suprascriem parola dacă este goală (Userul vrea să păstreze parola veche)
                if ($key === 'CARGUS_API_PASS' && empty($val)) {
                    continue;
                }
                Configuration::updateValue($key, $val);
            }
            $output .= $this->displayConfirmation('Toate setările au fost salvate cu succes.');
        }

        return $output . $this->renderConfigForm();
    }

    public function renderConfigForm(): string
    {
        $service = new \Cargus\Service\API\AccountService();
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->submit_action = 'submitCargusConfig';

        $fieldsForm = [
            'form' => [
                'legend' => ['title' => 'Cargus V3 Premium Configuration', 'icon' => 'icon-rocket'],
                'tabs' => [
                    'general' => '1. Cont & API',
                    'commercial' => '2. Preferințe & Servicii',
                    'debug' => '3. API Debugger',
                ],
                'input' => [
                    // --- TAB: GENERAL ---
                    ['type' => 'text', 'label' => 'API URL', 'name' => 'CARGUS_API_URL', 'tab' => 'general', 'required' => true],
                    ['type' => 'text', 'label' => 'Subscription Key', 'name' => 'CARGUS_API_KEY', 'tab' => 'general', 'required' => true],
                    ['type' => 'text', 'label' => 'User WebExpress', 'name' => 'CARGUS_API_USER', 'tab' => 'general', 'required' => true],
                    ['type' => 'password', 'label' => 'Parolă', 'name' => 'CARGUS_API_PASS', 'tab' => 'general'],

                    // --- TAB: COMMERCIAL ---
                    [
                        'type' => 'select', 'label' => 'Punct Ridicare Implicit', 'name' => 'CARGUS_SENDER_LOCATION_ID', 'tab' => 'commercial',
                        'options' => ['query' => $service->getSenderLocations(), 'id' => 'id', 'name' => 'name']
                    ],
                    [
                        'type' => 'select', 'label' => 'Plan Tarifar', 'name' => 'CARGUS_PRICE_TABLE_ID', 'tab' => 'commercial',
                        'options' => ['query' => $service->getPriceTables(), 'id' => 'id', 'name' => 'name']
                    ],
                    [
                        'type' => 'select', 'label' => 'Serviciu Implicit', 'name' => 'CARGUS_SERVICE_ID', 'tab' => 'commercial',
                        'options' => ['query' => $service->getServices(), 'id' => 'id', 'name' => 'name']
                    ],
                    [
                        'type' => 'select', 'label' => 'Plătitor Expediție', 'name' => 'CARGUS_PAYER_TYPE', 'tab' => 'commercial',
                        'options' => ['query' => [['id' => 'sender', 'name' => 'Expeditor'], ['id' => 'receiver', 'name' => 'Destinatar']], 'id' => 'id', 'name' => 'name']
                    ],
                    [
                        'type' => 'select', 'label' => 'Tip Ramburs', 'name' => 'CARGUS_COD_TYPE', 'tab' => 'commercial',
                        'options' => ['query' => [['id' => 'cash', 'name' => 'Numerar'], ['id' => 'collector', 'name' => 'Cont Colector']], 'id' => 'id', 'name' => 'name']
                    ],
                    [
                        'type' => 'select', 'label' => 'Tip Expediție', 'name' => 'CARGUS_PARCEL_TYPE', 'tab' => 'commercial',
                        'options' => ['query' => [['id' => '1', 'name' => 'Plic'], ['id' => '2', 'name' => 'Colet']], 'id' => 'id', 'name' => 'name']
                    ],
                    ['type' => 'switch', 'label' => 'Deschidere Colet', 'name' => 'CARGUS_OPEN_PKG', 'tab' => 'commercial', 'is_bool' => true, 'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]]],
                    ['type' => 'switch', 'label' => 'Livrare Sâmbăta', 'name' => 'CARGUS_SATURDAY', 'tab' => 'commercial', 'is_bool' => true, 'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]]],
                    ['type' => 'switch', 'label' => 'Asigurare (Valoare Declarată)', 'name' => 'CARGUS_INSURANCE', 'tab' => 'commercial', 'is_bool' => true, 'values' => [['id' => 'on', 'value' => 1], ['id' => 'off', 'value' => 0]]],
                    ['type' => 'text', 'label' => 'Preț Bazic Standard', 'name' => 'CARGUS_BASE_PRICE_STD', 'tab' => 'commercial', 'class' => 'fixed-width-sm'],
                    ['type' => 'text', 'label' => 'Preț Bazic PUDO', 'name' => 'CARGUS_BASE_PRICE_PUDO', 'tab' => 'commercial', 'class' => 'fixed-width-sm'],
                    ['type' => 'text', 'label' => 'Preț KG Extra', 'name' => 'CARGUS_EXTRA_KG_PRICE', 'tab' => 'commercial', 'class' => 'fixed-width-sm'],
                    ['type' => 'text', 'label' => 'Taxă Ramburs (COD Fee)', 'name' => 'CARGUS_COD_FEE', 'tab' => 'commercial', 'class' => 'fixed-width-sm'],

                    // --- TAB: DEBUG ---
                    [
                        'type' => 'html', 'name' => 'api_debug_ui', 'tab' => 'debug',
                        'html_content' => '
                            <div class="panel">
                                <div class="panel-heading"><i class="icon-bug"></i> API Tester Console</div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" onclick="runCargusDebug(\'PickupLocations\')">Test Locații</button>
                                    <button type="button" class="btn btn-default" onclick="runCargusDebug(\'PriceTables\')">Test Tarife</button>
                                    <button type="button" class="btn btn-default" onclick="runCargusDebug(\'Services\')">Test Servicii</button>
                                </div>
                                <pre id="cargus_debug_log" style="margin-top:20px; background:#1a1a1a; color:#2ecc71; padding:15px; border-radius:4px; font-family:monospace; min-height:200px; overflow:auto;">Sistem gata pentru testare...</pre>
                            </div>'
                    ],
                ],
                'submit' => ['title' => 'Salvează Configurarea Modulului']
            ]
        ];

        // Populăm toate valorile salvate
        foreach (self::ALL_CONFIG_KEYS as $f) {
            $helper->fields_value[$f] = Configuration::get($f);
        }
        $helper->fields_value['CARGUS_API_PASS'] = ''; // Nu afișăm parola salvată

        return $helper->generateForm([$fieldsForm]);
    }

    // Metodele de calcul costuri (legate de PricingService)
    public function getOrderShippingCost($params, $shipping_cost)
    {
        $pricing = new \Cargus\Service\Calculator\CargusPricingService();
        return $pricing->calculateShippingCost($this->context->cart, (float)$shipping_cost, (int)$this->id_carrier);
    }
    public function getOrderShippingCostExternal($params) { return $this->getOrderShippingCost($params, 0); }
}