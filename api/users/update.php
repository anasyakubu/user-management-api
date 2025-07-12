<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/helpers.php';

// Verify JWT
$token = getBearerToken();
$decoded = verifyJWT($token);
if (!$decoded) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !isset($data['name'])) {
    sendResponse(['error' => 'ID and name are required'], 400);
}

// Verify the user is updating their own record
if ($data['id'] !== $decoded['sub']) {
    sendResponse(['error' => 'You can only update your own profile'], 403);
}

$db = getDB();
$users = $db->selectCollection('users');

try {
    $result = $users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($data['id'])],
        ['$set' => [
            'name' => $data['name'],
            'updatedAt' => new MongoDB\BSON\UTCDateTime()
        ]]
    );
    
    if ($result->getModifiedCount() === 0) {
        sendResponse(['error' => 'User not found or no changes made'], 404);
    }
    
    sendResponse(['success' => true]);
} catch (Exception $e) {
    sendResponse(['error' => 'Invalid user ID'], 400);
}
?>