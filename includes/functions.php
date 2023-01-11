<?php
add_action("admin_menu", "ld_spotify_add_admin_menu");

function ld_spotify_add_admin_menu()
{
    add_menu_page(
            'Spotify Preference', // Title of the page
            'Spotify Preference', // Text to show on the menu link
            'manage_options', // Capability requirement to see the link
        plugin_dir_path(__FILE__) . 'spotify-preferences.php'
    );
}


// function that runs when shortcode is called
function ld_spotify_shortcode() {
    $short_code = '
    <link rel="stylesheet" media="screen" href="../../../../wp-content/plugins/ld_spotify/Finder/css/theme.min.css" importance="high">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script type="text/javascript" src="../../../../wp-content/plugins/ld_spotify/spotify_query.js"></script>
    <label for="name">Search:</label>
    
    <input type="text" id="search_bar_spotify" name="search_bar_spotify" required
           minlength="1" maxlength="50" size="30">
    <button class="search_spotify" id="search_spotify" onclick="search_request()">Search</button>
    <style></style>
    <div id="results">
        
    </div>
    ';
    return $short_code;
}

add_shortcode('ld_spotify_search_bar', 'ld_spotify_shortcode');