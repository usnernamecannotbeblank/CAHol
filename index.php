<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['nev']))
        header("Location: autok_lista.php");
    else
        header("Location: bejelentkezes.php");
?>