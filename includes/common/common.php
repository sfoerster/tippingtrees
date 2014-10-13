<?php

// Common scripts, functions, and classes to include with every page

    // ensure a session is started
    $a = session_id();
    if(empty($a)){
        session_start();
        $a = session_id();
    }
    
    include_once('../includes/functions/autoload.php');
    
    //include_once('../includes/functions/link_functions.php');
    //include_once('../includes/functions/login_functions.inc.php');
    include_once('../includes/functions/redirect_user.php');
    //include_once('../includes/functions/display_tips.php');

    include_once('../includes/libraries/WDC.php');
    include_once('../includes/libraries/ttlib.php');
    
    $path = '/var/php';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    include_once "Mail.php";
    $path = '/var/php/Mail';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    include_once "mime.php";

?>