<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$info = [
    'library_name' => getSetting($pdo, 'library_name', 'Perpustakaan Kalurahan Trimulyo'),
    'description' => getSetting($pdo, 'library_description', ''),
    'address' => getSetting($pdo, 'library_address', ''),
    'phone' => getSetting($pdo, 'library_phone', ''),
    'email' => getSetting($pdo, 'library_email', ''),
    'head_of_library' => getSetting($pdo, 'library_head', ''),
    'last_updated' => date('Y-m-d H:i:s')
];

echo json_encode([
    'status' => 'success',
    'data' => $info
]);
