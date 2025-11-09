<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['cahol_nev']))
        header("Location: views/autok_lista.php");
    else
        header("Location: views/bejelentkezes.php");
?>