<?php

include("index.php");

if (!isset($_GET["server"])) header("Location: servers.php");

$id = $_GET["server"];
$serverName = $_GET["name"];

$sql = "SELECT * FROM website WHERE `server` = $id";
$query = $dbh->prepare($sql);
$query->execute();
$sites = $query->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0-beta1/css/bootstrap.min.css" integrity="sha512-o/MhoRPVLExxZjCFVBsm17Pkztkzmh7Dp8k7/3JrtNCHh0AQ489kwpfA3dPSHzKDe8YCuEhxXq3Y71eb/o6amg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Sites du server <?= $serverName ?></title>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Liste des sites du server <?= $serverName ?></h1>
            </div>
            <div class="card-body">
                <a href="servers.php" class="btn btn-outline-primary">Retour</a><br><br>
                <p>Sites :</p>
                <hr>
                <?php foreach ($sites as $site) { ?>
                    <div class="py-2">
                        <?= $site->url ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>