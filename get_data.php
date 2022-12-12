<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $search_bar_content = $_POST["search_bar_content"];
    $url = "https://api.spotify.com/v1/search?q=".$search_bar_content."&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer BQDLO77v5muH8LOLN5kZn54OHOiyFxDU7oXXslf3RQWnF8ETNLp-YuK6hxx9yZbnITM4DV0IEk9nLXbEG3t-mTHSznAzEb-ImCSqsIIGpRHeZ7mM2TjQsQY4eIpn-ohM_1S85Y6Eh7oG0iIA7i-2eFKt5YtmsLyWPOoWQItSjr1r');

    curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
    $result = curl_exec( $ch ); # run!
    curl_close($ch);
    $json = json_decode($result, true);
    //var_dump($json);
    //var_dump($json['artists']);
    //var_dump($json['artists']['items'][0]['name']);
    $HTML_var = '';
    foreach($json['artists']['items'] as $key => $value) {
        $HTML_var = $HTML_var.'<div class="card card-light card-hover card-horizontal mb-4">
              <div class="card-img-top" style="background-image: url('.$value['images'][0]['url'].');"></div>
              <div class="card-body">
                <h3 class="h6 mb-1"><a class="nav-link-light" href="car-finder-single.html">'.$value['name'].'</a></h3>
                <div class="text-primary fw-bold mb-1"><i class="fi-users"></i>'.$value['followers']['total'].'</div>
              </div>
            </div>';
    }

    foreach($json['tracks']['items'] as $key => $value) {
        $input = $value['duration_ms'];
        $input = floor($input / 1000);
        $seconds = $input % 60;
        $input = floor($input / 60);
        $minutes = $input % 60;
        $input = floor($input / 60);
        $HTML_var = $HTML_var.'<div class="card bg-secondary card-hover">
              <div class="card-body">
                <h3 class="h6 card-title pt-1 mb-3">
                  <a href="#" class="text-nav stretched-link text-decoration-none">'.$value['name'].'</a>
                </h3>
                <div class="fs-sm">
                  <span class="text-nowrap me-3">
                    <i class="fi-user"> </i>
                    '.$value['artists'][0]['name'].'
                  </span>
                  <span class="text-nowrap me-3">
                    <i class="fi-folder"></i>
                    '.$value['album']['name'].'
                  </span>
                  <span class="text-nowrap me-3">
                    <i class="fi-play"></i>
                    '.$minutes.' min '.$seconds.' s'.'
                  </span>
                </div>
              </div>
            </div>';
    }

    echo $HTML_var;



