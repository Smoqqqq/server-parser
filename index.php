<?php

function dd($data)
{
    var_dump($data);
    die;
}

require_once("vendor/autoload.php");

include("dbh.php");
// include("parse-all-servers.php");