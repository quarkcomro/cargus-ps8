<?php
/**
 * @author    Quark
 * @copyright 2026 Quark
 * @license   Proprietary
 * @version   1.0.0
 */

namespace Cargus\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CargusV3Client
{
    private $apiUrl;
    private $subscriptionKey;
    private $token = null;
    private $timeout = 10; // Timeout in seconds to prevent checkout blocking

    public function __construct()
    {
        $this->apiUrl = \Configuration::get('CARGUS_API_URL');
        $this->subscriptionKey = \Configuration::get('CARGUS_SUBSCRIPTION_KEY');
    }

    /**
     * Normalizes Romanian diacritics to standard Latin characters.
     * Handles both standard (comma) and legacy (cedilla) diacritics.
     *
     * @param string $string
     * @return string
     */
    public static function normalizeString($string)
    {
        if (empty($string)) {
            return $string;
        }

        $search = [
            'ă', 'Ă', 'â', 'Â', 'î', 'Î',
            'ș', 'Ș', 'ț', 'Ț', // Standard (comma)
            'ş', 'Ş', 'ţ', 'Ţ'  // Legacy (cedilla)
        ];
        $replace = [
            'a', 'A', 'a', 'A', 'i', 'I',
            's', 'S', 't', 'T',
            's', 'S', 't', 'T'
        ];

        return str_replace($search, $replace, $string);
    }

    /**
     * Authenticates the user and retrieves the Bearer token.
     * * @return string|bool Token on success, false on failure
     * @throws \Exception If authentication fails
     */
    public function login()
    {
        $username = \Configuration::get('CARGUS_USERNAME');
        $password = \Configuration::get('CARGUS_PASSWORD');

        if (!$username || !$password || !$this->subscriptionKey) {
            throw new \Exception('Missing API credentials. Please configure them in the module settings.');
        }

        $response = $this->request('LoginUser', 'POST', [
            'UserName' => $username,
            'Password' => $password
        ], false); // false = do not use bearer token for this request

        if (isset($response['error'])) {
            // Translate technical errors to human-readable format
            throw new \Exception('Authentication failed: ' . $this->translateError($response['error']));
        }

        $this->token = $response; // Cargus V3 usually returns the token as a direct string or in a specific key
        return $this->token;
    }

    /**
     * Centralized method to handle all cURL requests to Cargus API.
     *
     * @param string $endpoint The API endpoint (e.g., 'Counties', 'Localities')
     * @param string $method HTTP Method (GET, POST, PUT)
     * @param array $data Payload for POST/PUT requests
     * @param bool $useAuth Token usage flag
     * @return mixed Array or string containing the response
     */
    public function request($endpoint, $method = 'GET', $data = [], $useAuth = true)
    {
        $url = rtrim($this->apiUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'Ocp-Apim-Subscription-Key: ' . $this->subscriptionKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($useAuth) {
            if (!$this->token) {
                $this->login();
            }
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['error' => 'Connection Error: ' . $curlError];
        }

        $decodedResponse = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = isset($decodedResponse['message']) ? $decodedResponse['message'] : 'HTTP Error ' . $httpCode;
            return ['error' => $errorMessage, 'code' => $httpCode];
        }

        return $decodedResponse !== null ? $decodedResponse : $response;
    }

    /**
     * Translates raw API errors into user-friendly messages.
     *
     * @param string $rawError
     * @return string
     */
    private function translateError($rawError)
    {
        // Add specific mapping based on Cargus V3 documentation
        if (strpos($rawError, '401') !== false) {
            return 'Invalid credentials or expired subscription key.';
        }
        if (strpos($rawError, 'timeout') !== false) {
            return 'The Cargus server took too long to respond. Please try again.';
        }
        return $rawError; // Fallback to raw error if no translation matches
    }
}
