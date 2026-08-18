<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once dirname(__DIR__) . '/application/config/database.php';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check geopos_user_roles for user 89
$res = $conn->query("SELECT * FROM geopos_user_roles WHERE user_id = 89");
echo "=== geopos_user_roles for 89 ===\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

// Find roles for user 89
$res2 = $conn->query("SELECT gp.name FROM geopos_user_roles gur JOIN geopos_role_permissions grp ON grp.role_id = gur.role_id JOIN geopos_permissions gp ON gp.id = grp.permission_id WHERE gur.user_id = 89");
echo "\n=== permissions for user 89 ===\n";
while ($row = $res2->fetch_assoc()) {
    echo $row['name'] . "\n";
}
$conn->close();
