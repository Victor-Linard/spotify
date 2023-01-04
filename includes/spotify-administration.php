<?php
    date_default_timezone_set('Europe/Paris');

    try {
        $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }
    catch (PDOException $pe) {
        die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
    }

    function get_spotify_local_data($db, $table) : string {
        $req = $db->prepare("SELECT * FROM {$table};");
        $req->execute();
        $str = '';
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            if ($table == 'artists')
                $str .= '<tr>
                            <th scope="row" class="tables-datetime">'.$data['search_date'].'</th>
                            <td class="tables-name">'.$data['name'].'</td>
                            <td class="tables-idartist">'.$data['id_artist'].'</td>
                            <td class="tables-followers">'.$data['followers'].'</td>
                            <td class="tables-popularity">'.$data['popularity'].'</td>
                            <td><button onclick="delete_single_row(\'artists\', \''.$data['id_artist'].'\')" type="submit" name="artist" class="btn btn-outline-danger btn-sm">DELETE</button></td>
                        </tr>';
            elseif ($table == 'tracks')
                $str .= '<tr>
                            <th scope="row" class="tables-datetime">'.$data['search_date'].'</th>
                            <td class="tables-name">'.$data['name'].'</td>
                            <td class="tables-idartist">'.$data['id_artist'].'</td>
                            <td class="tables-idtrack">'.$data['id_track'].'</td>
                            <td class="tables-uri">'.$data['uri'].'</td>
                            <td><button onclick="delete_single_row(\'tracks\', \''.$data['id_track'].'\')" type="button" name="track" class="btn btn-outline-danger btn-sm">DELETE</button></td>
                        </tr>';
        }
        $req = null;
        $db = null;
        return $str;
    }
?>

<div>
    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/libs.bundle.css">
    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/theme.bundle.css">
    <script src="../wp-content/plugins/ld_spotify/delete_spotify_local.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <h3>Spotify Plugin Admnistration</h3>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Artists
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="table-responsive" data-list='{"valueNames": ["tables-datetime", "tables-name", "tables-idartist", "tables-image", "tables-followers", "tables-popularity"]}'>
                    <label for="search-artists">Search : </label>
                    <input type="text" id="search-artists" name="search-artists" class="fuzzy-search" />
                    <button onclick="delete_all_rows('tracks')" type="submit" name="artists" class="btn btn-outline-danger btn-sm">DELETE ALL</button>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-datetime">#</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-name">Name</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idartist">ID Artist</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-followers">Followers</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-popularity">Popularity</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" >Action</a></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                        <form method="post">
                            <?php echo get_spotify_local_data($db, 'artists'); ?>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Tracks
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="table-responsive" data-list='{"valueNames": ["tables-datetime", "tables-name", "tables-id", "tables-image", "tables-previewurl", "tables-uri"]}'>
                    <label for="search-tracks">Search : </label>
                    <input type="text" id="search-tracks" name="search-tracks" class="fuzzy-search" />
                    <button onclick="delete_all_rows('tracks')" type="button" class="btn btn-outline-danger btn-sm">DELETE ALL</button>
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-datetime">#</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-name">Name</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idtrack">ID Track</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idartist">ID Artist</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-uri">URI</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" >Action</a></th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        <form method="post">
                            <?php echo get_spotify_local_data($db, 'tracks'); ?>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/theme.bundle.js"></script>
    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/vendor.bundle.js"></script>
</div>
