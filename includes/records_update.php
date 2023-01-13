<?php
    function getNbRecords() {
        /*The getNbRecords() function connects to the SQLite database and retrieves the total number of records
        in the tracks, artists, and albums tables.*/
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
        /*The getNbUpToDateRecords() function connects to the SQLite database and retrieves the total number of records
         in the tracks, artists, and albums tables that have been searched in the last 30 days (delay for a record to be outdated).*/
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
        /*The getNbOutdatesRecords() function connects to the SQLite database and retrieves the total number of records
         in the tracks, artists, and albums tables that have not been searched in the last 30 days (delay for a record to be outdated).*/
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
        /*The updateRecords() function updates the records in the tracks table that have not been searched
         in the last 30 days by making an API request to Spotify, updating the data and storing it in the database.*/
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
            $duration = intval(intval($json['duration_ms'])/1000);
            $req = $db->prepare("UPDATE tracks SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, id_artist=?, image=?, duration=? WHERE id_track=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['artists'][0]['id']);
            $req->bindParam(3, $json['album']['images'][0]['url']);
            $req->bindParam(4, $duration);
            $req->bindParam(5, $track["id_track"]);
            $req->execute();
        }

        $req = $db->prepare("SELECT * FROM artists WHERE ABS(julianday('now') - julianday(search_date)) >= 30");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req = null;

        foreach ($data as $artist) {
            $url = "https://api.spotify.com/v1/artists/".$artist["id_artist"];
            $json = runCurl($url);
            $req = $db->prepare("UPDATE artists SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, followers=?, popularity=?, image=? WHERE id_artist=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['followers']['total']);
            $req->bindParam(3, $json['popularity']);
            $req->bindParam(4, $json['images'][0]['url']);
            $req->bindParam(5, $artist["id_artist"]);
            $req->execute();
        }

        $req = $db->prepare("SELECT * FROM albums WHERE ABS(julianday('now') - julianday(search_date)) >= 30");
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req = null;

        foreach ($data as $album) {
            $url = "https://api.spotify.com/v1/albums/".$album["id_album"];
            $json = runCurl($url);
            $req = $db->prepare("UPDATE albums SET search_date=datetime(CURRENT_TIMESTAMP, '+1 hour'), name=?, id_artist=?, image=? WHERE id_album=?;");
            $req->bindParam(1, $json['name']);
            $req->bindParam(2, $json['artists'][0]['id']);
            $req->bindParam(3, $json['images'][0]['url']);
            $req->bindParam(4, $album["id_album"]);
            $req->execute();
        }
    }

    function runCurl($url) {
        /*The runCurl() function is used to make a cURL request to a specified URL. */
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