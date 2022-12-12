<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $search_bar_content = $_POST["search_bar_content"];
    $url = "https://api.spotify.com/v1/search?q=".$search_bar_content."&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer BQAK9C4FVmt607Hv9FGuQsED5srCnZ624jmZ0PcTs2QMegzDprJyowsKUBsbSwjqqOnnrH_-zXPOQxo_Bw4uo-Z1cSpRfxgoyJVWmtL_gOppSGXGv1SWuHEiWX_NUnx-TD5ZudhFjMhXLm4ZO61nt2z8rF8_0pZ_80KZ-TItihbI');

    curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
    $result = curl_exec( $ch ); # run!
    curl_close($ch);
    $json = json_decode($result, true);
    //var_dump($json);
    //$date_jour = $json->{'response'}->{'features'}->{'artists'};
    //var_dump($json['artists']);
    var_dump($json['artists']['items'][0]['name']);
    //foreach($json['artists'] as $key => $value) {
      //  echo $key . " => " . $value['name'] . "<br>";
    //}
