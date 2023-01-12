<?php
    function get_sqlite_option($option) {
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("SELECT option_value FROM options WHERE option_name = '".$option."';");
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data['option_value'] ?? false;
    }

    function update_sqlite_option($option_value, $option_name) {
        try {
            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $req = $db->prepare("UPDATE options SET option_value=? WHERE option_name=?;");
        $req->bindParam(1, $option_value);
        $req->bindParam(2, $option_name);
        $req->execute();
    }