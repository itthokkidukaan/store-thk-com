<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once dirname(__DIR__) . '/application/config/database.php';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Find groups for user 837
$groups = $conn->query("SELECT * FROM users_groups WHERE user_id = 837");
echo "=== groups for user 837 ===\n";
while ($row = $groups->fetch_assoc()) {
    print_r($row);
}

// Find seller_data for user 837
$seller = $conn->query("SELECT * FROM seller_data WHERE user_id = 837");
echo "\n=== seller_data for user 837 ===\n";
while ($row = $seller->fetch_assoc()) {
    print_r($row);
}
$conn->close();
