<?php
header('Content-Type: application/json');
echo json_encode([
    'message' => 'User Management API',
    'status' => 'running',
    'version' => '1.0'
]);
?>