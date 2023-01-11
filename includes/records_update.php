<?php
    function getNbRecords() {
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT (SELECT COUNT(*) FROM tracks) + (SELECT COUNT(*) FROM artists) + (SELECT COUNT(*) FROM albums) as total_count;");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['total_count'];
    }

    function getNbUpToDateRecords() {
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT (SELECT COUNT(*) FROM tracks) + (SELECT COUNT(*) FROM artists) + (SELECT COUNT(*) FROM albums) as total_count;");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['total_count'];
    }