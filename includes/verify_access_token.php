<?php

    function get_latest_access_token() {
        date_default_timezone_set('Europe/Paris');
        try {
            $db = new PDO("sqlite:../ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT access_token, expires_date FROM access_token WHERE CURRENT_TIMESTAMP < expires_date;");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['access_token'] ?? renew_access_token();
    }

    function renew_access_token() {
        date_default_timezone_set('Europe/Paris');
        $client_id = '6a48f249630f482caaf9ad05bcf57836';
        $client_secret = 'ded5654dafe6413b87797a8c3985f117';
        $url = 'https://accounts.spotify.com/api/token';
        $authorization = 'Authorization: Basic '.base64_encode($client_id.':'.$client_secret);

        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded', $authorization);

        curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $result = curl_exec( $ch ); # run!
        curl_close($ch);
        $json = json_decode($result, true);

        if (isset($json["access_token"])) {
            try {
                $db = new PDO("sqlite:../ld_spotify.db");
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $pe) {
                die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
            }

            $date = new DateTime();
            $date->add(new DateInterval('PT'.$json["expires_in"].'S'));

            $req = $db->prepare("INSERT INTO access_token (access_token, expires_date) VALUES ('".$json["access_token"]."', '".$date->format('Y-m-d H:i:s')."')");
            $req->execute();
            return $json["access_token"] ?? false;
        }
    }