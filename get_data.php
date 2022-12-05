<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $search_bar_content = $_POST["search_bar_content"];
    $url = "https://api.spotify.com/v1/search?q=".$search_bar_content."&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer BQD9aPOD0Fa-jIG16SwiOdOnBrQQDpgHvKxd6tvrPUNBIVD4OU9Mwsk-11Qex-_YN-he7gGmIj9uauCjzizUf4mKv3w35hUMpIuxKYEZnxA6UHTYbIGLe10ao0aWI5H_YXAUH6JfXGb5RlAOhMfEdJk3iBxS7J4yz4zbZSjc0nBg');

    curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
    $result = curl_exec( $ch ); # run!
    curl_close($ch);
    $json = json_decode($result, true);
    //var_dump($json);

    var_dump($json['artists']);
