<?php

include("index.php");

use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

// On récupère la dernière mise à jour de la base

$sql = "SELECT * FROM updates ORDER BY id DESC LIMIT 1";
$query = $dbh->prepare($sql);
$query->execute();
$lastUpdate = $query->fetch(PDO::FETCH_OBJ);
    
$update = strtotime($lastUpdate->date);
$now = strtotime(date("Y-m-d"));

$update = (abs($update - $now)/(60*60*24) > 7) ? true : false;

// Si la dernière MAJ est plus ancienne que 1 semaine
if ($update) {
    $sql = "SELECT * FROM servers";
    $query = $dbh->prepare($sql);
    $query->bindParam(":id", $serverId, PDO::PARAM_INT);
    $query->execute();

    $res = $query->fetchAll(PDO::FETCH_OBJ);

    // Pour chaque serveur
    foreach ($res as $server) {
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
    }

    $sql = "INSERT INTO updates VALUES (NULL, NOW())";
    $query = $dbh->prepare($sql);
    $query->execute();
} else {
    $message = base64_encode("Les servers on déja été actualisés récemmentw");
    header("location: servers.php?message=$message");
}