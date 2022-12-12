<?php
    date_default_timezone_set('Europe/Paris');
?>

<div>
    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/libs.bundle.css">
    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/theme.bundle.css">

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
                    <input type="text" class="fuzzy-search" />
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-datetime">#</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-name">Name</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idartist">ID Artist</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-image">Image</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-followers">Followers</a></th>
                                <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-popularity">Popularity</a></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                        <?php
                            try {
                                $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
                            }
                            catch (PDOException $pe) {
                                die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
                            }

                            $sql = 'SELECT * FROM artists;';
                            $req = $db->prepare($sql);
                            $req->execute();
                            $str = '';
                            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                                $str = '<tr>
                                    <th scope="row" class="tables-datetime">'.$data['search_date'].'</th>
                                    <td class="tables-name">'.$data['name'].'</td>
                                    <td class="tables-idartist">'.$data['id_artist'].'</td>
                                    <td class="tables-image">'.$data['image'].'</td>
                                    <td class="tables-followers">'.$data['followers'].'</td>
                                    <td class="tables-popularity">'.$data['popularity'].'</td>
                                </tr>';
                            }
                            echo $str;
                        ?>
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
                    <input type="text" class="fuzzy-search" />
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-datetime">#</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-name">Name</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idtrack">ID Track</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-idartist">ID Artist</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-image">Image</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-previewurl">Preview URL</a></th>
                            <th scope="col"><a href="#" class="text-muted list-sort" data-sort="tables-uri">URI</a></th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        <?php
                        try {
                            $db = new PDO("sqlite:../wp-content/plugins/ld_spotify/ld_spotify.db");
                        }
                        catch (PDOException $pe) {
                            die("<br>Erreur de connexion sur ld_spotify :" . $pe->getMessage());
                        }

                        $sql = 'SELECT * FROM tracks;';
                        $req = $db->prepare($sql);
                        $req->execute();
                        $str = '';
                        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                            $str = '<tr>
                                    <th scope="row" class="tables-datetime">'.$data['search_date'].'</th>
                                    <td class="tables-name">'.$data['name'].'</td>
                                    <td class="tables-idartist">'.$data['id_artist'].'</td>
                                    <td class="tables-image">'.$data['image'].'</td>
                                    <td class="tables-followers">'.$data['followers'].'</td>
                                    <td class="tables-popularity">'.$data['popularity'].'</td>
                                </tr>';
                        }
                        echo $str;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/theme.bundle.js"></script>
    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/vendor.bundle.js"></script>
</div>
