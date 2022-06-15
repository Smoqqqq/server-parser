<?php

include("index.php");

if (isset($_GET["table"])) $table = true;
else $table = false;

$sql = "SELECT * FROM servers ORDER BY id ASC";
$query = $dbh->prepare($sql);
$query->execute();
$servers = $query->fetchAll(PDO::FETCH_OBJ);

$sql = "SELECT * FROM website ORDER BY `server` ASC";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.5/umd/popper.min.js" integrity="sha512-8cU710tp3iH9RniUh6fq5zJsGnjLzOWLWdZqBMLtqaoZUA6AWIE34lwMB3ipUNiTBP5jEZKY95SfbNnQ8cCKvA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0-beta1/js/bootstrap.min.js" integrity="sha512-Hqe3s+yLpqaBbXM6VA0cnj/T56ii5YjNrMT9v+us11Q81L0wzUG0jEMNECtugqNu2Uq5MSttCg0p4KK0kCPVaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>Tous les sites BH</title>
</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-5">
        <div class="card">
            <div class="card-header" style="display: flex; flex-direction: row; justify-content: space-between">
                <h1 class="card-title">Liste des sites de BH Internet</h1>
                <div>
                    <div id="results"></div>
                    <input type="text" id="search" placeholder="Rechercher un site">
                </div>
            </div>
            <div class="card-body">
                <div class="full-row" id="row">
                    <div class="full-row-item">
                        <?php
                        $currentServer = $sites[0]->server;
                        $server = $servers[$currentServer]->name;
                        echo "<div class='head'><b>$server</b></div>";
                        foreach ($sites as $site) { ?>
                            <a href="<?= $site->url; ?>" target="_blank" class="site"><?= $site->url; ?></a>
                        <?php
                            if ($site->server != $currentServer) {
                                $sql = "SELECT `name` FROM servers WHERE id = $site->server";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $server = $query->fetch(PDO::FETCH_OBJ);
                                echo "</div><div class='full-row-item'><div class='head'><b>$server->name</b></div>";
                                $currentServer = $site->server;
                            }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    let search = document.getElementById("search");
    let sites = document.getElementsByClassName("site");
    let row = document.getElementById("row");

    let matches = [];

    let count = 0;

    search.addEventListener("keyup", (e) => {
        if (e.key != "Enter") {
            let actives = document.getElementsByClassName("focus-active");
            for (let i = 0; i < actives.length; i++) {
                actives[i].classList.remove("focus-active");
            }
            if (search.value.length > 0) {
                count = 0;
                for (let i = 0; i < sites.length; i++) {
                    if (sites[i].innerText.includes(search.value)) {
                        let x = sites[i].getBoundingClientRect().x - sites[0].getBoundingClientRect().x;
                        let y = sites[i].getBoundingClientRect().y - sites[0].getBoundingClientRect().y;
                        row.scroll(x, y);
                        if (!matches.includes(sites[i])) matches.push(sites[i]);
                        sites[i].classList.add("focus")
                    } else {
                        sites[i].classList.remove("focus");
                        let index = matches.indexOf(sites[i]);
                        if (index != -1) matches.splice(index, 1);
                    }
                }
            } else {
                let actives = document.getElementsByClassName("focus");
                for (let i = 0; i < actives.length; i++) {
                    actives[i].classList.remove("focus");
                }
            }
        } else {
            let actives = document.getElementsByClassName("focus-active");
            for (let i = 0; i < actives.length; i++) {
                actives[i].classList.remove("focus-active");
            }
            let x = matches[count].getBoundingClientRect().x - sites[0].getBoundingClientRect().x;
            let y = matches[count].getBoundingClientRect().y + -sites[0].getBoundingClientRect().y;
            row.scroll(x, y);
            matches[count].classList.add("focus-active");
            if (count == matches.length - 1) {
                count = 0;
            } else {
                count++;
            }
        }
        document.getElementById("results").innerText = matches.length + " résultats";
    })
</script>

<style>
    .full-row {
        <?php if ($table) { ?>

            display: flex;
            flex-direction: row;

        <?php } else { ?>

            height: 75vh;

        <?php } ?>

        overflow: scroll;
    }

    .full-row-item {
        margin: 0px 5px;
        min-width: 300px;
    }

    .full-row-item:nth-child(even) {
        background: #EEE;
    }

    .head {
        background: #CCC;
        padding: 10px 5px;
    }

    .site {
        padding: 5px;
        display: block;
        border-bottom: 2px solid transparent;
    }

    .site.focus {
        border-color: rgba(255, 0, 0, 0.5);
    }

    .site.focus-active {
        background: rgba(255, 0, 0, 0.25) !important;
        border-color: rgba(255, 0, 0, 0.5);
    }
</style>

</html>