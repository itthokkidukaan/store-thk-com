<?php
$data = file_get_contents('php://input');
file_put_contents('dom_debug.txt', $data);
echo json_encode(["status" => "ok"]);
