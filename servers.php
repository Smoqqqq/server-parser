<?php

include("index.php");

$sql = "SELECT * FROM servers";
$query = $dbh->prepare($sql);
$query->execute();
$servers = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0-beta1/css/bootstrap.min.css" integrity="sha512-o/MhoRPVLExxZjCFVBsm17Pkztkzmh7Dp8k7/3JrtNCHh0AQ489kwpfA3dPSHzKDe8YCuEhxXq3Y71eb/o6amg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Serveurs</title>
</head>

<body>
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Liste des serveurs</h1>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">Nom</div>
                    <div class="col-md-2">Hôte</div>
                    <div class="col-md-3">Utilisateur</div>
                    <div class="col-md-3">Mot de passe</div>
                    <div class="col-md-2">Actions</div>
                </div><hr>
                <?php foreach ($servers as $server) { ?>
                    <div class="row py-2">
                        <div class="col-md-2"><?= $server->name; ?></div>
                        <div class="col-md-2"><?= $server->host; ?></div>
                        <div class="col-md-3"><?= $server->user; ?></div>
                        <div class="col-md-3"><?= htmlentities($server->password); ?></div>
                        <div class="col-md-2">
                            <a href="list-sites.php?server=<?= $server->id; ?>&name=<?= $server->name; ?>" class="btn btn-outline-primary">Sites</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>