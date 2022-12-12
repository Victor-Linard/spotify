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

function spotify_administration() {
    include_once(plugin_dir_path(__FILE__) . 'spotify-administration.php');
}

function ld_spotify_shortcode(): string {
    return '<h1>Je suis là \o/ !</h1>';
}

add_shortcode('ld_spotify_search_bar', 'ld_spotify_shortcode');