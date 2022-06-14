<?php

use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

$sql = "SELECT * FROM servers";
$query = $dbh->prepare($sql);
$query->bindParam(":id", $serverId, PDO::PARAM_INT);
$query->execute();

$res = $query->fetchAll(PDO::FETCH_OBJ);

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

    foreach ($folders as $folder) {
        $url = str_replace("vhosts/", "", $folder->path());

        $sql = "SELECT * FROM website WHERE url = '$url'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $website = $query->fetch(PDO::FETCH_OBJ);

        if (!$website || $website->server != $serverId) {
            $sql = "INSERT INTO `website` VALUES (NULL, '$url', $serverId)";
            $query = $dbh->prepare($sql);
            $query->execute();
            var_dump($serverId);
        }
    }
}

// ftp://espautomobiles2:AT%3Fpxy%2AKNHl1@ftp.publicationvo.com/datas/mu91m9.xml

function dd($data)
{
    var_dump($data);
    die;
}
