<?php
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

//id, Name, Adress, School, Mobile Number

$request = ajax::post_strict([
    "name",
    "address",
    "school",
    "mobile number"
],false);

$database = new database();

$database->debug(true)->insert('tbl_exercise3',$request);

$result = $database->query($database->tempQuery)->fetchAll();

echo "Success";