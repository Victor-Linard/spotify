<?php
add_action("admin_menu", "ld_spotify_add_admin_menu");

function ld_spotify_add_admin_menu() {
    add_menu_page(
        'Spotify Preferences',
        'Spotify Preferences',
        'manage_options',
        'menuparent',
        'spotify_preferences'
    );

    add_submenu_page(
        'menuparent',
        'Titre page option 2',
        'Spotify Administration',
        'manage_options',
        'menuenfant1',
        'spotify_administration'
    );
}

function spotify_preferences() {
    include_once(plugin_dir_path(__FILE__) . 'spotify-preferences.php');
}

function spotify_getdata() {
    include_once(plugin_dir_path(__FILE__) . 'get_data2.php');
}

function spotify_administration() {
    include_once(plugin_dir_path(__FILE__) . 'spotify-administration.php');
}
// function that runs when shortcode is called
function ld_spotify_shortcode() {
    $short_code = "
    
    <link rel='stylesheet' media='screen' href='../../../../wp-content/plugins/ld_spotify/Dashkit/css/libs.bundle.css' importance='high'>
    <link rel='stylesheet' media='screen' href='../../../../wp-content/plugins/ld_spotify/Dashkit/css/theme.bundle.css' importance='high'>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js'></script>
    <script type='text/javascript' src='../../../../wp-content/plugins/ld_spotify/spotify_query.js'></script>
    <label for='name'>Search:</label>
    
    <input type='text' id='search_bar_spotify' name='search_bar_spotify' required
           minlength='1' maxlength='50' size='30'>
    <button class='search_spotify btn btn-primary' id='search_spotify' onclick='search_request()'>Search</button>
    
    <div id='results'>
    </div>
   
    <script src='../../../../wp-content/plugins/ld_spotify/Dashkit/js/theme.bundle.js'></script>
    <script src='../../../../wp-content/plugins/ld_spotify/Dashkit/js/vendor.bundle.js'></script>
    ";
    return $short_code;
}

add_shortcode('ld_spotify_search_bar', 'ld_spotify_shortcode');