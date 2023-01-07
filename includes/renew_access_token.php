<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'verify_access_token.php';

    if (!get_latest_access_token())
        echo renew_access_token();
    else
        echo get_latest_access_token();


