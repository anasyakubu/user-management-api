<?php
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/helpers.php';

// Verify JWT
$token = getBearerToken();
if (!$token || !verifyJWT($token)) {
    sendResponse(['error' => 'Unauthorized'], 401);
}

$db = getDB();
$users = $db->selectCollection('users');

// Get single user if ID is provided
if (isset($_GET['id'])) {
    try {
        $user = $users->findOne([
            '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
        ]);
        
        if (!$user) {
            sendResponse(['error' => 'User not found'], 404);
        }
        
        sendResponse([
            'id' => (string)$user['_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'createdAt' => $user['createdAt']->toDateTime()->format('c'),
            'updatedAt' => $user['updatedAt']->toDateTime()->format('c')
        ]);
    } catch (Exception $e) {
        sendResponse(['error' => 'Invalid user ID'], 400);
    }
}

// Get all users (paginated)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$skip = ($page - 1) * $limit;

$cursor = $users->find([], [
    'skip' => $skip,
    'limit' => $limit,
    'projection' => ['password' => 0]
]);

$result = [];
foreach ($cursor as $user) {
    $result[] = [
        'id' => (string)$user['_id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'createdAt' => $user['createdAt']->toDateTime()->format('c'),
        'updatedAt' => $user['updatedAt']->toDateTime()->format('c')
    ];
}

sendResponse([
    'data' => $result,
    'page' => $page,
    'limit' => $limit,
    'total' => $users->countDocuments([])
]);
?>