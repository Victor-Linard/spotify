<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
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

    var_dump($json);