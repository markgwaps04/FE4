<?php

/**
 * @author Mark Anthony Libres
 */

include_once "lib/helper.php";
include_once "lib/globals.php";
include_once "lib/database.php";
include_once "lib/json_readable.php";

if(!$_POST)
{
    header("HTTP/1.0 404 Not Found");
    echo "Invalid Request Sent!!";
    die();
}

/**
 * return Error if the parameters is not
 * available on current request
 */

$request = ajax::post_strict([
    "id",
],false);

$database = new database();

$result = $database->select('vw_student',"*",$request);

if(!$result)
{
    echo "Not found!!";
    exit();
}

constraint::filter_keys($result,[
    "FullName",
    "Other name",
    "DOB",
    "POB"
]);

header('Content-Type: application/json');
echo _format_json(json_encode($result));
