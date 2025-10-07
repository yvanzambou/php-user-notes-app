<?php
    session_start();
    $_SESSION["flash_message"] = "Sie sind jetzt abgemeldet!";
    header("Location: index.php");
    exit;
