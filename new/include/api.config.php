<?php

define("API_BASE_URL", "https://07cf-87-103-122-166.ngrok-free.app");  // BASE FIXA
define("API_KEY", "cruz");

function api_request($method, $endpoint, $data = null, $extraHeaders = []) {
    $url = API_BASE_URL . $endpoint;

    $ch = curl_init();

    $defaultHeaders = [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-API-Key: ' . API_KEY
    ];
    $allHeaders = array_merge($defaultHeaders, $extraHeaders);

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $allHeaders,
        CURLOPT_CUSTOMREQUEST => strtoupper($method)
    ]);

    if (in_array(strtoupper($method), ['POST', 'PUT']) && $data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);

    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'status' => $httpCode,
        'data' => $decoded,
        'raw' => $response
    ];
}