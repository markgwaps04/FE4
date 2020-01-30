<?php

include_once "lib/helper.php";
include_once "lib/globals.php";
include_once "lib/database.php";
include_once "lib/json_readable.php";

header('Content-Type: application/json');

$database = new database();

$query = $database->debug(true)->select('vw_exercise3');

$result = $database->query($database->tempQuery)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
