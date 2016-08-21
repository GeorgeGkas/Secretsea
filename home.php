<?php
    # DO NOT DISPLAY ERRORS TO USER
    ini_set('display_errors', 0);
    
    session_start();
    header('Content-type: text/html; charset=utf-8');

    require_once 'classes/HomeEnviroment.class.php';

    $USER = new HomeEnviroment();

    # REDIRECT USER IF LOGIN CREDENTIAL DO NOT EXIST
    if(!$USER->isLogin()) {
        header('Location: index.php');
    }
    $USER->prepareEnviromentVariables();

    # GET THE MAIN APPLICATION HTML FILE
    echo file_get_contents('html/home.html');  
?>