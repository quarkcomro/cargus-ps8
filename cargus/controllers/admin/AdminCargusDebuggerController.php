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
        $this->ajax = true;
    }

    public function displayAjaxTestLocations()
    {
        $client = new CargusV3Client();
        
        try {
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
                'message' => "Succes! Am preluat {$count} județe din Cargus V3."
            ]));
            
        } catch (Exception $e) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
        }
    }

    public function displayAjaxTestTarife()
    {
        $this->ajaxDie(json_encode([
            'success' => true,
            'message' => 'Endpoint-ul de calculare tarife răspunde corect (Mock).'
        ]));
    }

    public function displayAjaxTestServicii()
    {
        $this->ajaxDie(json_encode([
            'success' => true,
            'message' => 'Endpoint-ul de servicii răspunde corect (Mock).'
        ]));
    }
}
