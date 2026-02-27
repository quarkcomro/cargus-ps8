<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   1.0.1
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
        $this->ajax = true;
    }

    /**
     * În PrestaShop 8, cererile cu action=TestLocations caută automat
     * o metodă numită ajaxProcessTestLocations()
     */
    public function ajaxProcessTestLocations()
    {
        $client = new CargusV3Client();
        
        try {
            // Facem ping pe Counties doar pentru verificarea conexiunii globale
            $response = $client->request('Counties', 'GET');
            
            if (isset($response['error'])) {
                die(json_encode([
                    'success' => false,
                    'message' => $response['error']
                ]));
            }

            $count = is_array($response) ? count($response) : 0;
            
            die(json_encode([
                'success' => true,
                'message' => "Succes! Conexiune validă. Am preluat {$count} județe din Cargus V3."
            ]));
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }

    public function ajaxProcessTestTarife()
    {
        die(json_encode([
            'success' => true,
            'message' => 'Endpoint-ul de calculare tarife răspunde corect (Mock).'
        ]));
    }

    public function ajaxProcessTestServicii()
    {
        die(json_encode([
            'success' => true,
            'message' => 'Endpoint-ul de servicii răspunde corect (Mock).'
        ]));
    }
}
