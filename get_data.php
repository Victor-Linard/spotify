<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once 'includes/verify_access_token.php';

    //$token = get_latest_access_token();

    //echo $token;
    $token = 'BQDhIsK_Uw6M3we4JUqR0mSRC7Erkt_sKCxvuV7zUzYPQbrDUHedZOAS-RSgVmVWERaT0ijqecVarGiK5pzJqMxW7b4IkeH1lXxUVX9tfCJV9JqBkloBkhQos1OGxZd7K4nYg0TdOVHha4cUO-fNeucisTceeapn6tbvehRIgQRB';

    $img_default = "../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png";
    $search_bar_content = $_POST["search_bar_content"];
    $search_bar_content = str_replace("'","''", $search_bar_content);
    $run_api = !empty($_POST["reload"]);
    //var_dump($_POST["btn-selected"]);
    var_dump($_POST["reload"]);
    //var_dump($search_bar_content);

    /* Recherche en premier dans la base de données */
    date_default_timezone_set('Europe/Paris');
    //var_dump(scandir("./"));
    try {
        $db = new PDO("sqlite:ld_spotify.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $pe) {
        die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
    }

    $req = $db->prepare("SELECT * FROM albums WHERE name like '%".$search_bar_content."%';");
    $req->execute();
    $req_albums = $req->fetchAll();

    $req = $db->prepare("SELECT * FROM artists WHERE name like '%".$search_bar_content."%';");
    $req->execute();
    $req_artists = $req->fetchAll();

    $req = $db->prepare("SELECT * FROM tracks WHERE name like '%".$search_bar_content."%';");
    $req->execute();
    $req_tracks = $req->fetchAll();


    if(count($req_albums)!=0){
        $array_albums_id = array();
        foreach ($req_albums as $key => $value) {
            $array_albums_id[] = [$value['id_album'], $value['name'], $value['id_artist'], $value['image']];
        }
    }

    if(count($req_tracks)!=0){
        $array_tracks_id = array();
        foreach ($req_tracks as $key => $value) {
            $req = $db->prepare("SELECT name FROM artists WHERE name like '%".$value['id_artist']."%';");
            $req->execute();
            $req_artists_name = $req->fetchAll();
            var_dump($req_artists_name);
            $minutes = floor($value['duration'] / 60);
            $seconds = $value['duration'] % 60;
            $array_tracks_id[] = [$value['id_track'], $value['name'], $req_artists_name, $minutes, $seconds, $value['id_artist'], $value['image']];
        }
    }

    if(count($req_artists)!=0){
        $array_artists_id = array();
        foreach ($req_artists as $key => $value) {
            $array_artists_id[] = [$value["name"], $value["id_artist"], $value["followers"], $value["image"], $value['popularity']];
            //var_dump($array_artists_id);
        }
    }

if((!isset($array_artists_id) && !isset($array_tracks_id) && !isset($array_albums_id)) || $run_api) {
    $run_api = true;
    $url = "https://api.spotify.com/v1/search?q=" . $search_bar_content . "&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $token);

    curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); # custom headers, see above
    $result = curl_exec($ch); # run!
    curl_close($ch);
    $json = json_decode($result, true);

    foreach ($json['artists']['items'] as $i => $artist) {
        if(!isset($array_artists_id)){
            $array_artists_id = array();
        }
        $image = isset($artist['images'][0]) ? $artist['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';
        $name = $artist['name'];
        $followers = $artist['followers']['total'];
        $popularity = $artist['popularity'];
        $id_artist = $artist['id'];
        $array_artists_id[] = [$name, $id_artist, $followers, $image, $popularity];

        /* Envoie des données dans la base de données TODO */
        if($save == 'True'){
            $sql = 'INSERT INTO artist(name,id_artist,followers,image,popularity) '
                . 'VALUES(:name,:id_artist,:followers,:image,:popularity)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':id_artist' => $id_artist,
                ':followers' => $followers,
                ':image' => $image,
                ':popularity' => $popularity,
            ]);
        }

    }
    //var_dump($array_artists_id);

    foreach ($json['tracks']['items'] as $i => $track) {
        if(!isset($array_tracks_id)){
            $array_tracks_id = array();
        }
        $name = $track['name'];
        $artist = $track['artists'][0]['name'];
        $id_artist = $track['artists'][0]['id'];
        $album = $track['album']['name'];
        $id_track = $track['id'];
        $duration = floor($track['duration_ms'] / 1000);
        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        $image = isset($track['album']['images'][0]) ? $track['album']['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';
        $url_tracks = $track['href'];
        $array_tracks_id[] = [$id_track, $name, $artist, $minutes, $seconds, $id_artist, $image];

        /* Envoie des données dans la base de données TODO */
    }

    foreach ($json['albums']['items'] as $i => $album) {
        //$search_bar_content = $album['id'];
        if(!isset($array_album_id)){
            $array_album_id = array();
        }
        $album_id = $album['id'];
        $artist_id = $album['artists'][0]['id'];
        $name = $album['name'];
        $image = isset($album['images'][0]) ? $album['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';


        $array_albums_id[] = [$album_id, $name, $artist_id, $image];
    }

}
$message = $run_api ? "Données chargées depuis Spotify" : "Données chargées depuis la base de données local";
$bouton = $run_api ? '' : '<a class="reload" id="reload" onclick="search_request(true)">From spotify</a>';
$HTML_var = '
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Top results</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Artists</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Albums</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">Tracks</button>
              </li>
            </ul>
            
            <div class="row">
                <div class="card card-fill">
                    <div class="card-header card-header-flush">
                        <!-- Title -->
                        <h4 class="card-header-title">
                          '.$message.'
                        </h4>
                        '.$bouton.'
                    </div>
                </div>
            </div>';
//var_dump($json);
 //3
    //var_dump($json);
    //var_dump($json['artists']);
    //var_dump($json['artists']['items'][0]['name']);



/* **************************************** */
$best_results = '<div class="row">';
if(isset($array_artists_id)) {
    //var_dump($array_artists_id);
    foreach ($array_artists_id as $i => $artist) {
        //var_dump($artist);
        if ($i >= 5) break; // Stop after 5 artists

        if ($i == 0) {
            $best_results .= '<div class="col-12">
        <div class="card">
          <div class="card-body text-center">
            <!-- Image -->
            <div class="card-avatar avatar avatar-lg mx-auto">
              <img src="' . $artist[3] . '" alt="" class="avatar-img rounded">
            </div>
            

            <!-- Heading -->
            <a data-bs-toggle="modal" data-bs-target="#' . $artist[1] . '" >' . $artist[0] . '</a>
            
            <p class="small text-muted mb-0">
                <span class="fe fe-users"></span> ' . $artist[2] . '
                <span class="fe fe-trending-up"></span> ' . $artist[4] . '
            </p>
            
            
            <button class="btn btn-white btn-sm btn-rounded-circle" data-bs-toggle="modal" data-bs-target="#modal' . $artist[1] . '">
                <span class="fe fe-play"></span>
            </button>
            

          </div>
        </div>
        
        
        
        <div class="modal fade" id="modal' . $artist[1] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                            <div class="modal-body">
                                <iframe style="border-radius:12px" src="https://open.spotify.com/embed/artist/' . $artist[1] . '?utm_source=generator" width="100%" height="380" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>';
        } else {
            if ($i % 2 == 1) {
                $best_results .= '</div><div class="row">';
            }
            $best_results .= '<div class="col-6">
            <div class="card">
                <div class="card-body">
            
                  <!-- List group -->
                  <div class="list-group list-group-flush list-group-focus">
                      <div class="row align-items-center">
                        <div class="col-auto">
            
                          <!-- Avatar -->
                          <div class="avatar">
                            <img src="' . $artist[3] . '" alt="..." class="avatar-img rounded">
                          </div>
                          
            
                        </div>
                        <div class="col ms-n2">
            
                          <!-- Title -->
                          <h4 class="text-body text-focus mb-1 name">
                            <a data-bs-toggle="modal" data-bs-target="#' . $artist[1] . '" >' . $artist[0] . '</a>
                          </h4>
            
                          <p class="small text-muted mb-0">
                            <span class="fe fe-users"></span> ' . $artist[2] . '
                          </p>
                          
                          <p class="small text-muted mb-0">
                            <span class="fe fe-trending-up"></span> ' . $artist[4] . '
                          </p>
            
                        </div>
                        <button class="btn btn-white btn-sm btn-rounded-circle" data-bs-toggle="modal" data-bs-target="#modal' . $artist[1] . '">
                            <span class="fe fe-play"></span>
                        </button>
                      </div> <!-- / .row -->
                  </div>
            
                </div>
              </div>
              <div class="modal fade" id="modal' . $artist[1] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                            <div class="modal-body">
                                <iframe style="border-radius:12px" src="https://open.spotify.com/embed/artist/' . $artist[1] . '?utm_source=generator" width="100%" height="380" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>';
        }

        $best_results .= '</div>';
    }
}
$best_results .= '</div>';


    /* ******************************************** */
if(isset($array_tracks_id)) {
    foreach ($array_tracks_id as $i => $track) {
        //var_dump($track);
        if ($i >= 6) break; // Stop after 6 tracks

        if ($i == 0) {
            $best_results .= '<div class="row align-items-center">';
        }
        if ($i % 2 == 0 && $i!=0) {
            $best_results .= '</div><div class="row align-items-center">';
        }
        $best_results .= '<!-- Accordion basic -->
                  <div class="col-6">
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/'.$track.'?utm_source=generator" width="100%" height="100" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
              </div>
    ';
    }
    $best_results .= '</div>';
}


$indice = 0;
if(isset($array_albums_id)) {
    foreach ($array_albums_id as $i => $album) {
        if ($i >= 5) break;
        //$search_bar_content = $album['id'];
        /*$url_2 = "https://api.spotify.com/v1/albums/" . $search_bar_content;
        //var_dump($url_2);
        $headers_2 = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $token);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_2); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_2); # custom headers, see above
        $result_2 = curl_exec($ch); # run!
        curl_close($ch);

        $json_2 = json_decode($result_2, true);*/
        //var_dump($json_2['artists'][0]['name']);
        //var_dump($json_2['name']);
        //$image = $json_2['images'][0]['url'] ?? $img_default;
        //$image = isset($json_2['images'][0]) ? $json_2['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';
        $best_results .= '
           <!-- Accordion basic -->
                  <div class="col-12">
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/' . $album . '?utm_source=generator" width="100%" height="380" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    </div>
              
    ';
    }
    $album_result = '';
    foreach ($array_albums_id as $i => $album) {
        $album_result .=   $album ;
    }

}
    $HTML_var.= '<div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">'.$best_results.'</div>
              <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">Profile</div>
              <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">'.$album_result.'</div>
              <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">Disabled</div>
            </div>';
    echo $HTML_var;


