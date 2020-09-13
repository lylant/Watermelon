<?php
    require_once("nocache.php"); // prevent the cache
    session_start(); // start the session

    session_destroy(); // shut the session down
    header("location: search.php"); // redirect to search.php
?>