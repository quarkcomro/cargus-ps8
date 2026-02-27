<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'cargus/src/Helper/CargusV3Client.php';

use Cargus\Helper\CargusV3Client;

class AdminCargusDebuggerController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        // This controller only handles AJAX requests
        $this->ajax = true;
    }

    /**
     * AJAX action triggered by the "Test Locații" button.
     * Tests connectivity by fetching a small dataset (e.g., Counties).
     */
    public function displayAjaxTestLocations()
    {
        $client = new CargusV3Client();
        
        try {
            // Example test: Fetching counties to verify API connection
            $response = $client->request('Counties', 'GET');
            
            if (isset($response['error'])) {
                $this->ajaxDie(json_encode([
                    'success' => false,
                    'message' => $response['error']
                ]));
            }

            $count = is_array($response) ? count($response) : 0;
            
            $this->ajaxDie(json_encode([
                'success' => true,
                'message' => "Success! Retrieved {$count} counties from Cargus V3."
            ]));
            
        } catch (Exception $e) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }

    /**
     * AJAX action for "Test Tarife"
     */
    public function displayAjaxTestTarife()
    {
        // Implementation for testing pricing calculation can be added here
        $this->ajaxDie(json_encode([
            'success' => true,
            'message' => 'Pricing endpoint reachable (Mock success).'
        ]));
    }

    /**
     * AJAX action for "Test Servicii"
     */
    public function displayAjaxTestServicii()
    {
        // Implementation for testing services available on the account
        $this->ajaxDie(json_encode([
            'success' => true,
            'message' => 'Services endpoint reachable (Mock success).'
        ]));
    }
}
