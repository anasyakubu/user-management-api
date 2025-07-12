<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/helpers.php';

// Verify JWT
$token = getBearerToken();
$decoded = verifyJWT($token);
if (!$decoded) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id'])) {
    sendResponse(['error' => 'ID is required'], 400);
}

// Verify the user is deleting their own record
if ($data['id'] !== $decoded['sub']) {
    sendResponse(['error' => 'You can only delete your own account'], 403);
}

$db = getDB();
$users = $db->selectCollection('users');

try {
    $result = $users->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($data['id'])
    ]);
    
    if ($result->getDeletedCount() === 0) {
        sendResponse(['error' => 'User not found'], 404);
    }
    
    sendResponse(['success' => true]);
} catch (Exception $e) {
    sendResponse(['error' => 'Invalid user ID'], 400);
}
?>