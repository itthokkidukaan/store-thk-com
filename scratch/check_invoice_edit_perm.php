<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once dirname(__DIR__) . '/application/config/database.php';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== Permission names matching sales-edit / invoice-edit ===\n";
$res = $conn->query("SELECT id, name, group_name FROM geopos_permissions WHERE name LIKE 'sales-edit%' OR name LIKE 'invoice-edit%'");
$count = 0;
while ($row = $res->fetch_assoc()) {
    print_r($row);
    $count++;
}
echo "Matches: $count\n";

echo "\n=== All distinct permission name prefixes (first token) ===\n";
$res2 = $conn->query("SELECT DISTINCT name FROM geopos_permissions ORDER BY name");
while ($row = $res2->fetch_assoc()) {
    echo $row['name'] . "\n";
}

echo "\n=== seller_data rows (count) ===\n";
$res3 = $conn->query("SELECT COUNT(*) c FROM seller_data");
print_r($res3->fetch_assoc());

$conn->close();
