<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['email']) || !isset($data['password'])) {
    sendResponse(['error' => 'Email and password are required'], 400);
}

$db = getDB();
$users = $db->selectCollection('users');

$user = $users->findOne(['email' => $data['email']]);

if (!$user || !password_verify($data['password'], $user['password'])) {
    sendResponse(['error' => 'Invalid credentials'], 401);
}

$token = generateJWT((string)$user['_id']);

sendResponse([
    'token' => $token,
    'user' => [
        'id' => (string)$user['_id'],
        'email' => $user['email'],
        'name' => $user['name']
    ]
]);
?>