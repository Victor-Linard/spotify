<?php
add_action("admin_menu", "ld_spotify_add_admin_menu");

function ld_spotify_add_admin_menu()
{
    add_menu_page(
            'Spotify Administration', // Title of the page
            'Spotify Administration', // Text to show on the menu link
            'manage_options', // Capability requirement to see the link
            plugin_dir_path(__FILE__).'spotify-administration.php'
    );
}


// function that runs when shortcode is called
function ld_spotify_shortcode() {
    return '<h1>Je suis là \o/ !</h1>';
}

add_shortcode('ld_spotify_search_bar', 'ld_spotify_shortcode');