<?php
    session_start();
    if(!isset($_SESSION["id"])){
        header("Location: login");
        die();
    }
    session_destroy();
?>