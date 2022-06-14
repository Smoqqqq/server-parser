<?php

$dbh;

try {
    $dbh = new PDO('mysql:host=localhost;dbname=server_parser', "root", "");
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}