<?php
    require_once 'includes/verify_access_token.php';
    $token = get_latest_access_token('ld_spotify.db');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $img_default = "../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png";
    $search_bar_content = $_POST["search_bar_content"];
    $url = "https://api.spotify.com/v1/search?q=".$search_bar_content."&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer '.$token);

    curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
    $result = curl_exec( $ch ); # run!
    curl_close($ch);
    $json = json_decode($result, true);
    //3
    //var_dump($json);
    //var_dump($json['artists']);
    //var_dump($json['artists']['items'][0]['name']);

/* **************************************** */
$HTML_var = '<div class="row">';

foreach($json['artists']['items'] as $i => $artist) {
    if($i >= 5) break; // Stop after 5 artists

    $image = isset($artist['images'][0]) ? $artist['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';
    $name = $artist['name'];
    $followers = $artist['followers']['total'];

    if ($i == 0) {
        $HTML_var .= '<div class="col-12">';
    } else {
        if ($i % 2 == 1) {
            $HTML_var .= '</div><div class="row">';
        }
        $HTML_var .= '<div class="col-6">';
    }

    $HTML_var .= '
        <div class="card card-light card-hover card-horizontal mb-4">
            <div class="card-img-top" style="background-image: url('. $image .');"></div>
            <div class="card-body">
                <h3 class="h6 mb-1"><a class="nav-link-light" href="car-finder-single.html">' . $name . '</a></h3>
                <div class="text-primary fw-bold mb-1"><i class="fi-users"></i>' . $followers . '</div>
              </div>
        </div>
    ';

    $HTML_var .= '</div>';
}

$HTML_var .= '</div>';


    /* ******************************************** */
$HTML_var = $HTML_var.'</div>';
foreach($json['tracks']['items'] as $i => $track) {
    if($i >= 5) break; // Stop after 5 tracks

    $name = $track['name'];
    $artist = $track['artists'][0]['name'];
    $album = $track['album']['name'];
    $duration = floor($track['duration_ms'] / 1000);
    $minutes = floor($duration / 60);
    $seconds = $duration % 60;

    $HTML_var .= '<!-- Accordion basic -->
        <div class="card bg-secondary card-hover">
            <div class="card-body">
                <h3 class="h6 card-title pt-1 mb-3">
                    <a href="#" class="text-nav stretched-link text-decoration-none">'.$name.'</a>
                </h3>
                <div class="fs-sm">
                    <span class="text-nowrap me-3">
                        <i class="fi-user"> </i>
                        '.$artist.'
                    </span>
                    <span class="text-nowrap me-3">
                        <i class="fi-folder"></i>
                        '.$album.'
                    </span>
                    <span class="text-nowrap me-3">
                        <i class="fi-play"></i>
                        '.$minutes.' min '.$seconds.' s'.'
                    </span>
                </div>
            </div>
        </div>
    ';
}

$indice = 0;
    foreach ($json['albums']['items'] as $key => $value) {
        if($indice<1){
            $search_bar_content = $value['id'];
            $url_2 = "https://api.spotify.com/v1/albums/".$search_bar_content;
            //var_dump($url_2);
            $headers_2 = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer '.$token);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_2); # URL to post to
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_2 ); # custom headers, see above
            $result_2 = curl_exec( $ch ); # run!
            curl_close($ch);
            $json_2 = json_decode($result_2, true);
            var_dump($json_2['artists'][0]['name']);
            var_dump($json_2['name']);
            $image = $json_2['images'][0]['url'] ?? $img_default;
            $HTML_var = $HTML_var.'<!-- Card based accordion -->
                <div id="accordionCards">
                  <!-- Card -->
                  <div class="card bg-secondary mb-2" data-bs-toggle="collapse" data-bs-target="#cardCollapse1">
                    <div class="card-body">
                      <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                          <img class="me-2" src="'.$image.' "width="24px">
                          <span class="fs-sm text-dark opacity-80 ps-1">'.$json_2['artists'][0]['name'].'</span>
                        </div>
                      </div>
                      <h3 class="h6 card-title pt-1 mb-0">'.$json_2['name'].'</h3>
                    </div>
                    <div class="collapse show" id="cardCollapse1" data-bs-parent="#accordionCards">
                      <div class="card-body mt-n1 pt-0">';
            foreach ($json_2['tracks']['items'] as $key => $value_2){
                //var_dump($value_2['name']);
                $HTML_var = $HTML_var.'<p class="fs-sm">'.$value_2['name'].'</p>';
            }
        }
        $indice+=1;
        $HTML_var = $HTML_var.'<div class="d-flex align-items-center justify-content-between"> 
                          </div>
                      </div>
                    </div>
                  </div>';
    }



    echo $HTML_var.'<div class="accordion" id="accordionExample">

  <!-- Accordion item -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Accordion Item #1</button>
    </h2>
    <div class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample" id="collapseOne">
      <div class="accordion-body">Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum, quaerat. Corporis pariatur cum dolorem ullam at nulla ex doloribus, ratione quos repellendus aliquid aspernatur obcaecati adipisci maxime id, sed cupiditate.</div>
    </div>
  </div>

  <!-- Accordion item -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Accordion Item #2</button>
    </h2>
    <div class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" id="collapseTwo">
      <div class="accordion-body">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Cumque dicta enim cupiditate natus dolorum distinctio, impedit tenetur nisi laboriosam ut animi delectus quod quos ipsum corporis magnam, nobis neque mollitia.</div>
    </div>
  </div>

  <!-- Accordion item -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingThree">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Accordion Item #3</button>
    </h2>
    <div class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample" id="collapseThree">
      <div class="accordion-body">Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero ut accusantium ea a ipsa, aliquam nemo aperiam porro deserunt aspernatur sequi amet voluptatibus, fugiat nobis. Atque voluptatibus quibusdam placeat voluptas?</div>
    </div>
  </div>
</div>';



