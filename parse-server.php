<?php

include("index.php");

use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

if(!isset($_GET["server"])) header("servers.php");

$serverId = $_GET["server"];

$sql = "SELECT * FROM servers WHERE id = $serverId";
$query = $dbh->prepare($sql);
$query->bindParam(":id", $serverId, PDO::PARAM_INT);
$query->execute();

$server = $query->fetch(PDO::FETCH_OBJ);

$username = $server->user;
$password = $server->password;
$host = $server->host;
$serverId = $server->id;

$filesystem = new Filesystem(new SftpAdapter(
    new SftpConnectionProvider(
        $host, // host (required)
        $username, // username (required)
        $password, // password (optional, default: null) set to null if privateKey is used
    ),
    '/', // root path (required)
    PortableVisibilityConverter::fromArray([
        'file' => [
            'public' => 0640,
            'private' => 0604,
        ],
        'dir' => [
            'public' => 0740,
            'private' => 7604,
        ],
    ])
));

$folders = $filesystem->listContents("vhosts");

// Pour chaque dossier (site)
foreach ($folders as $folder) {
    $url = str_replace("vhosts/", "", $folder->path());

    // On vérifie qu'il n'est pas déja présent
    $sql = "SELECT * FROM website WHERE url = '$url'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $website = $query->fetch(PDO::FETCH_OBJ);

    // On l'ajoute si il ne l'est pas
    if (!$website || $website->server != $serverId) {
        $sql = "INSERT INTO `website` VALUES (NULL, '$url', $serverId)";
        $query = $dbh->prepare($sql);
        $query->execute();
    }
}

header("location: list-sites.php?server=$serverId&name=$server->name");