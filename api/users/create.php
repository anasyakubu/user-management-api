<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
    sendResponse(['error' => 'Name, email and password are required'], 400);
}

$db = getDB();
$users = $db->selectCollection('users');

// Check if user already exists
if ($users->findOne(['email' => $data['email']])) {
    sendResponse(['error' => 'User already exists'], 400);
}

$result = $users->insertOne([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
    'createdAt' => new MongoDB\BSON\UTCDateTime(),
    'updatedAt' => new MongoDB\BSON\UTCDateTime()
]);

sendResponse([
    'id' => (string)$result->getInsertedId(),
    'name' => $data['name'],
    'email' => $data['email']
], 201);
?>