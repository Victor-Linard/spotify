<?php
add_action("admin_menu", "vl_p3_Add_My_Admin_Link");

function vl_p3_Add_My_Admin_Link()
{
    add_menu_page(
            'Ma Belle Page Plugin3', // Title of the page
            'Plugin 3', // Text to show on the menu link
            'manage_options', // Capability requirement to see the link
            plugin_dir_path(__FILE__).'vl-admin-page.php'
    );
}


// function that runs when shortcode is called
function vl_p3_shortcode() {
    // Things that you want to do.
    $message = 'Je suis là \o/ !';

    // Output needs to be return
    return $message;
}

function vl_p3_shortcode2($atts, $content = null) {
    $default = array(
        'link' => '#',
    );
    $a = shortcode_atts($default, $atts);
    $content = do_shortcode($content);
    return 'Voici un lien <a href="'.($a['link']).'" style="color: blue">'.$content.'</a>';
}

add_shortcode('coucou', 'vl_p3_shortcode');
add_shortcode('coucou2', 'vl_p3_shortcode2');