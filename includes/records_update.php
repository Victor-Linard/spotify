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

        $req = $db->prepare("SELECT (SELECT COUNT(*) FROM tracks WHERE ABS(julianday('now') - julianday(search_date)) < 30 ) + (SELECT COUNT(*) FROM artists WHERE ABS(julianday('now') - julianday(search_date)) < 30 ) + (SELECT COUNT(*) FROM albums WHERE ABS(julianday('now') - julianday(search_date)) < 30 ) as total_count;");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['total_count'];
    }

    function getNbOutdatesRecords() {
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT (SELECT COUNT(*) FROM tracks WHERE ABS(julianday('now') - julianday(search_date)) >= 30 ) + (SELECT COUNT(*) FROM artists WHERE ABS(julianday('now') - julianday(search_date)) >= 30 ) + (SELECT COUNT(*) FROM albums WHERE ABS(julianday('now') - julianday(search_date)) >= 30 ) as total_count;");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['total_count'];
    }

    function updateRecords() {
        date_default_timezone_set('Europe/Paris');
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT * FROM tracks WHERE ABS(julianday('now') - julianday(search_date)) >= 30");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req = null;

        foreach ($data as $track) {
            $url = "https://api.spotify.com/v1/tracks/".$track["id_track"];
            $json = runCurl($url);
            var_dump($json);
            $req = $db->prepare("UPDATE tracks SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, id_artist=?, uri=?, preview=?, image=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['id']);
            $req->bindParam(3, $json['uri']);
            $req->bindParam(4, $json['preview_url']);
            $req->bindParam(5, $json['images'][0]);
            $req->execute();
        }

        $req = $db->prepare("SELECT * FROM artists WHERE ABS(julianday('now') - julianday(search_date)) >= 30");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req = null;

        foreach ($data as $artist) {
            $url = "https://api.spotify.com/v1/tracks/".$artist["id_artist"];
            $json = runCurl($url);
            $req = $db->prepare("UPDATE artists SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, followers=?, popularity=?, image=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['followers']['total']);
            $req->bindParam(3, $json['popularity']);
            $req->bindParam(4, $json['images'][0]);
            $req->execute();
        }

        $req = $db->prepare("SELECT * FROM albums WHERE ABS(julianday('now') - julianday(search_date)) >= 30");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req = null;

        foreach ($data as $album) {
            $url = "https://api.spotify.com/v1/tracks/".$album["id_album"];
            $json = runCurl($url);
            $req = $db->prepare("UPDATE artists SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, id_artist=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['artists'][0]['id']);
            $req->execute();
        }
    }

    function runCurl($url) {
        date_default_timezone_set('Europe/Paris');
        require_once 'verify_access_token.php';

        $token = get_latest_access_token('../wp-content/plugins/ld_spotify/ld_spotify.db');
        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer '.$token);

        curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        $result = curl_exec( $ch ); # run!
        curl_close($ch);
        return json_decode($result, true);
    }