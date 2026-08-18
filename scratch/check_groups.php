<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once dirname(__DIR__) . '/application/config/database.php';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Find all distinct group_name
$res = $conn->query("SELECT DISTINCT group_name FROM geopos_permissions");
echo "=== Distinct Permission Groups ===\n";
while ($row = $res->fetch_assoc()) {
    echo $row['group_name'] . "\n";
}
$conn->close();
