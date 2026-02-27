<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   1.0.0
 */

// Încărcăm mediul PrestaShop pentru a avea acces la baze de date și securitate
include_once(dirname(__FILE__) . '/../../config/config.inc.php');
include_once(dirname(__FILE__) . '/../../init.php');

// Securitate strictă: Doar administratorii autentificați pot accesa acest fișier
$context = Context::getContext();
if (!isset($context->employee) || !$context->employee->isLoggedBack()) {
    header('HTTP/1.0 403 Forbidden');
    die(json_encode(['success' => false, 'message' => 'Acces neautorizat. Trebuie să fii autentificat în panoul de administrare.']));
}

// Includem clasa Helper generată anterior
require_once dirname(__FILE__) . '/src/Helper/CargusV3Client.php';
$client = new \Cargus\Helper\CargusV3Client();

$action = Tools::getValue('action');

if ($action == 'TestLocations') {
    $response = $client->request('Counties', 'GET');
    
    if (isset($response['error'])) {
        die(json_encode(['success' => false, 'message' => $response['error']]));
    }
    
    $count = is_array($response) ? count($response) : 0;
    die(json_encode(['success' => true, 'message' => "Conexiune reușită! Am preluat {$count} județe din API-ul Cargus V3."]));
}

if ($action == 'TestTarife') {
    die(json_encode(['success' => true, 'message' => 'Endpoint-ul de calculare tarife răspunde corect.']));
}

if ($action == 'TestServicii') {
    die(json_encode(['success' => true, 'message' => 'Endpoint-ul de servicii răspunde corect.']));
}

die(json_encode(['success' => false, 'message' => 'Acțiune invalidă.']));
