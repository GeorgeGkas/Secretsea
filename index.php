<?php
    # DO NOT DISPLAY ERRORS TO USER
    ini_set('display_errors', 0);

    session_start();
    header('Content-type: text/html; charset=utf-8');

    require_once 'classes/userAuth.class.php';

    $USER = new userAuth();
    
    # REDIRECT USER IF LOGIN CREDENTIAL EXISTS
    if($USER->isLogin()) {
        header('Location: home.php');
    }
    else {
        # GET THE INDEX HTML FILE
        echo file_get_contents("html/index.html");
    }
?>