<?php
require_once __DIR__ . '/../../lib/helpers.php';

$token = getBearerToken();

if (!$token) {
    sendResponse(['error' => 'Authorization token missing'], 401);
}

$decoded = verifyJWT($token);

if (!$decoded) {
    sendResponse(['error' => 'Invalid or expired token'], 401);
}

sendResponse([
    'valid' => true,
    'user_id' => $decoded['sub']
]);
?>