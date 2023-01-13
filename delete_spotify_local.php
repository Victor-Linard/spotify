<?php
    if (!isset($_POST['table']) && !isset($_POST['id']) && !isset($_POST['mode'])) {
        echo '$_POST not set';
        exit(1);
    }
    $id_column = 'id_'.substr($_POST['table'], 0, -1);
    try {
        $db = new PDO("sqlite:./ld_spotify.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $pe) {
        die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
    }

    if ($_POST['mode'] == "single") {
        $req = $db->prepare("DELETE FROM {$_POST['table']} WHERE {$id_column}=:id;");
        $req->bindParam(":id",$_POST['id']);
    }
    else {
        $req = $db->prepare("DELETE FROM {$_POST['table']};");
    }
    $req->execute();
    $req = null;
    $db = null;