<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once dirname(__DIR__) . '/application/config/database.php';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach ([834, 464] as $uid) {
    $r = $conn->query("SELECT permissions FROM seller_data WHERE user_id = $uid");
    $row = $r->fetch_assoc();
    $perm = json_decode($row['permissions'], true);
    $names = $perm['permission_names'] ?? [];
    echo "=== user $uid sales/invoice related perms ===\n";
    foreach ($names as $n) {
        if (strpos($n, 'sales') === 0 || strpos($n, 'invoice') !== false || strpos($n, 'order') === 0 || strpos($n, 'quote') === 0 || strpos($n, 'new-invoice') === 0 || strpos($n, 'pos-invoice') === 0 || strpos($n, 'trash-invoice') === 0) {
            echo "  $n\n";
        }
    }
    echo "\n";
}
$conn->close();
