<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type");
header("Access-Control-Allow-Methods", "POST, GET, PUT, DELETE, OPTIONS");
echo json_encode($_POST);
