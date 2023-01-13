<?php
    try {
        $db = new PDO("sqlite:../ld_spotify.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $pe) {
        die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
    }

    function verify_exit_record($table, $id){
        try {
            $db = new PDO("sqlite:../ld_spotify.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $pe) {
            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
        }

        $field_name = "id_".substr($table, 0, -1);
        $req = $db->prepare("SELECT 1 FROM ".$table." WHERE ".$field_name." = '".$id."';");
        $req->execute();
        $req_artists = $req->fetchAll();
        return $req_artists;
    }

    function create_modal($type, $id, $tab = null){
        $modal_html = '
              <div class="modal fade" id="modal' .$tab. $id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                            <div class="modal-body">
                                <iframe style="border-radius:12px" src="https://open.spotify.com/embed/'.$type.'/' . $id . '?utm_source=generator" width="100%" height="380" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>';
        return $modal_html;
    }

    function create_info_card($type, $info, $save){
        $html_return = '';
        $content='';

        foreach ($info as $i => $value) {
            if ($type=='artist'){
                $content ='<p class="small text-muted mb-0">
                                    <span class="fe fe-users"></span> ' . $value[2] . '
                                    <span class="fe fe-trending-up"></span> ' . $value[4] . '
                                  </p>';
            }elseif ($type=='album' && $save=='True'){

                try {
                    $db = new PDO("sqlite:../ld_spotify.db");
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
                catch (PDOException $pe) {
                    die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
                }
                $req = $db->prepare("SELECT name FROM artists WHERE id_artist = '".$value[2]."';");
                $req->execute();
                $req_artists = $req->fetchAll();
                $req_artists = $req_artists[0]['name'];

                $content = '<p class="small text-muted mb-0">
                                    <span class="fe fe-user"></span> ' . $req_artists . '
                                  </p>';

            }elseif ($type == 'track'){
                //var_dump($value[4]);
                $content ='<p class="small text-muted mb-0">
                                    <span class="fe fe-user"></span> ' . $value[4] . '
                                    <span class="fe fe-clock"></span> ' . $value[5]." min ".$value[6]." sec" . '
                                  </p>';
            }
            if ($i == 0) {
                $html_return .= '<div class="row align-items-center">';
            }
            if ($i % 2 == 0 && $i!=0) {
                $html_return .= '</div><div class="row align-items-center">';
            }
            $html_return .= '<!-- Accordion basic -->
                  <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                    
                          <!-- List group -->
                          <div class="list-group list-group-flush list-group-focus">
                              <div class="row align-items-center">
                                <div class="col-auto">
                    
                                  <!-- Avatar -->
                                  <div class="avatar">
                                    <img src="' . $value[3] . '" alt="..." class="avatar-img rounded">
                                  </div>
                                  
                                   
                                </div>
                                <div class="col ms-n2">
                    
                                  <!-- Title -->
                                  <h4 class="text-body text-focus mb-1 name">
                                    <a data-bs-toggle="modal" data-bs-target="#tab' . $value[0] . '" >' . $value[1] . '</a>
                                  </h4>
                    
                                  '.$content.'
                                  
                                  
                    
                                </div>
                              </div> <!-- / .row -->
                          </div>
                        </div>
                    </div>
              </div>';
        }
        $html_return.='</div>';
        return $html_return;
    }


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once 'verify_access_token.php';

    $token = get_latest_access_token('../ld_spotify.db');

    //echo $token;
    //$token = 'BQBwnc4KBzIDnq3wnkX3p40fVszU6ym15WX5ZdAe3DbHW9j6_DCWAROyCzzSc0Ig7SEYf7nyTKK3oIArGQthKBzCGvEyK2n6h4f418k0NH7o99LigRGNrKi300Xurobum9Gw8daa5jRCgy8GA77U6OQD6foZIH3ScW3tUBJnYbJG';

    $img_default = "../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png";
    $search_bar_content = $_POST["search_bar_content"];
    $search_bar_content = str_replace("'","''", $search_bar_content);
    $run_api = !empty($_POST["reload"]);
    //var_dump($_POST["btn-selected"]);
    //var_dump($_POST["reload"]);
    //var_dump($search_bar_content);

    /* Recherche en premier dans la base de données */
    date_default_timezone_set('Europe/Paris');
    //var_dump(scandir("./"));


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
            $req = $db->prepare("SELECT name FROM artists WHERE id_artist = '".$value['id_artist']."';");
            $req->execute();
            $req_artists_name = $req->fetchAll();
            $req_artists_name = $req_artists_name[0][0] ?? '';
            //var_dump($req_artists_name);
            $minutes = floor($value['duration'] / 60);
            $seconds = $value['duration'] % 60;
            $array_tracks_id[] = [$value['id_track'], $value['name'], $value['id_artist'], $value['image'], $req_artists_name, $minutes, $seconds];
        }
    }

    if(count($req_artists)!=0){
        $array_artists_id = array();
        foreach ($req_artists as $key => $value) {
            $array_artists_id[] = [$value["id_artist"], $value["name"],  $value["followers"], $value["image"], $value['popularity']];
            //var_dump($array_artists_id);

        }
    }

if((!isset($array_artists_id) && !isset($array_tracks_id) && !isset($array_albums_id)) || $run_api) {
    $run_api = true;
    $url = "https://api.spotify.com/v1/search?q=" . $search_bar_content . "&type=track%2Cartist%2Cplaylist%2Calbum";
    $ch = curl_init();
    $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $token);
    include_once 'manage_options.php';
    //$save = 'True';
    $save = get_sqlite_option('ld_spotify_save_result', '../ld_spotify.db');

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
        $array_artists_id[] = [$id_artist, $name, $followers, $image, $popularity];

        /* Envoie des données dans la base de données TODO */
        if($save == 'True' && !verify_exit_record("artists", $id_artist)){
            $sql = 'INSERT INTO artists(name,id_artist,followers,image,popularity) '
                . 'VALUES(:name,:id_artist,:followers,:image,:popularity)';

            $stmt = $db->prepare($sql);
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
        $artist_name = $track['artists'][0]['name'];
        $id_artist = $track['artists'][0]['id'];
        $album = $track['album']['name'];
        $id_track = $track['id'];
        $duration = floor($track['duration_ms'] / 1000);
        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        $image = isset($track['album']['images'][0]) ? $track['album']['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';
        $url_tracks = $track['href'];
        $array_tracks_id[] = [$id_track, $name, $id_artist, $image, $artist_name, $minutes, $seconds];
        /* Envoie des données dans la base de données TODO */
        if($save == 'True' && !verify_exit_record("tracks", $id_track)){
            $sql = 'INSERT INTO tracks(id_track,name,duration,id_artist,image) '
                . 'VALUES(:id_track,:name,:duration,:id_artist,:image)';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_track' => $id_track,
                ':name' => $name,
                ':duration' => $duration,
                ':id_artist' => $id_artist,
                ':image' => $image,
            ]);
        }
        if($save == 'True' && !verify_exit_record("artists", $id_artist)){
            $url = "https://api.spotify.com/v1/artists/".$id_artist;
            $ch = curl_init();
            $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $token);

            curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); # custom headers, see above
            $result = curl_exec($ch); # run!
            curl_close($ch);
            $json_2 = json_decode($result, true);
            $image = isset($json['images'][0]) ? $json['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';

            $sql = 'INSERT INTO artists(name,id_artist,followers,image,popularity) '
                . 'VALUES(:name,:id_artist,:followers,:image,:popularity)';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $json_2['name'],
                ':id_artist' => $id_artist,
                ':followers' => $json_2['followers']['total'],
                ':image' => $image,
                ':popularity' => $json_2['popularity'],
            ]);



        }
    }
    //var_dump($json);
    foreach ($json['albums']['items'] as $i => $album) {
        //$search_bar_content = $album['id'];
        if(!isset($array_albums_id)){
            $array_albums_id = array();
        }
        $album_id = $album['id'];
        $artist_id = $album['artists'][0]['id'];
        $name = $album['name'];
        $image = isset($album['images'][0]) ? $album['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';


        $array_albums_id[] = [$album_id, $name, $artist_id, $image];

        if($save == 'True' && !verify_exit_record("albums", $album_id)){
            $sql = 'INSERT INTO albums(id_album,name,id_artist,image) '
                . 'VALUES(:album_id,:name,:artist_id,:image)';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':album_id' => $album_id,
                ':name' => $name,
                ':artist_id' => $artist_id,
                ':image' => $image,
            ]);
        }
        if($save == 'True' && !verify_exit_record("artists", $artist_id)){
            $url = "https://api.spotify.com/v1/artists/".$artist_id;
            $ch = curl_init();
            $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $token);

            curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); # custom headers, see above
            $result = curl_exec($ch); # run!
            curl_close($ch);
            $json_2 = json_decode($result, true);
            $image = isset($json['images'][0]) ? $json['images'][0]['url'] : '../../../../wp-content/plugins/ld_spotify/Finder/img/Spotify_no_image_users.png';

            $sql = 'INSERT INTO artists(name,id_artist,followers,image,popularity) '
                . 'VALUES(:name,:id_artist,:followers,:image,:popularity)';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $json_2['name'],
                ':id_artist' => $artist_id,
                ':followers' => $json_2['followers']['total'],
                ':image' => $image,
                ':popularity' => $json_2['popularity'],
            ]);



        }
    }

}
$message = $run_api ? "Données chargées depuis Spotify" : "Données chargées depuis la base de données local";
$bouton = $run_api ? '' : '<a class="reload" id="reload" onclick="search_request(true)">From spotify</a>';
$HTML_var = '
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Player</button>
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

$albums_result = '';
$artists_result = '';
$tracks_result = '';

if(!isset($array_artists_id) && !isset($array_albums_id) && !isset($array_tracks_id)){
    $HTML_var.= '<div class="alert alert-warning" role="alert">
                  Pas de résultats, merci de refaire une recherche
                </div>';
}else{
    $albums_result .= !isset($array_albums_id) ? '<div class="alert alert-warning" role="alert">
                  Pas de résultats
                </div>' : '';
    $artists_result .= !isset($array_artists_id) ? '<div class="alert alert-warning" role="alert">
                  Pas de résultats
                </div>' : '';
    $albums_result .= !isset($array_tracks_id) ? '<div class="alert alert-warning" role="alert">
                  Pas de résultats
                </div>' : '';
}




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
            <a data-bs-toggle="modal" data-bs-target="#' . $artist[0] . '" >' . $artist[1] . '</a>
            
            <p class="small text-muted mb-0">
                <span class="fe fe-users"></span> ' . $artist[2] . '
                <span class="fe fe-trending-up"></span> ' . $artist[4] . '
            </p>
            
            
            <button class="btn btn-white btn-sm btn-rounded-circle" data-bs-toggle="modal" data-bs-target="#modal' . $artist[0] . '">
                <span class="fe fe-play"></span>
            </button>
            

          </div>
        </div>
        
        
        
        '.create_modal("artist", $artist[0]);
            
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
                            <a data-bs-toggle="modal" data-bs-target="#' . $artist[0] . '" >' . $artist[1] . '</a>
                          </h4>
            
                          <p class="small text-muted mb-0">
                            <span class="fe fe-users"></span> ' . $artist[2] . '
                          </p>
                          
                          <p class="small text-muted mb-0">
                            <span class="fe fe-trending-up"></span> ' . $artist[4] . '
                          </p>
            
                        </div>
                        <button class="btn btn-white btn-sm btn-rounded-circle" data-bs-toggle="modal" data-bs-target="#modal' . $artist[0] . '">
                            <span class="fe fe-play"></span>
                        </button>
                      </div> <!-- / .row -->
                  </div>
            
                </div>
              </div>
              '.create_modal("artist", $artist[0]);
        }

        $best_results .= '</div>';
    }

   $artists_result.= create_info_card("artist", $array_artists_id, $save);

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
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/'.$track[0].'?utm_source=generator" width="100%" height="100" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
              </div>
    ';
    }
    $tracks_result.= create_info_card("track", $array_tracks_id, $save);
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
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/' . $album[0] . '?utm_source=generator" width="100%" height="380" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                    </div>
              
    ';
    }
    $albums_result .= create_info_card('album', $array_albums_id, $save);

}
    $HTML_var.= '<div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">'.$best_results.'</div>
              <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">'.$artists_result.'</div>
              <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">'.$albums_result.'</div>
              <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">'.$tracks_result.'</div>
            </div>';
    echo $HTML_var;


