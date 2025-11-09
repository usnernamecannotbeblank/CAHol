<?php
session_start();
if (isset($_SESSION['cahol_nev'])) {
    unset($_SESSION['cahol_nev']);
    if(isset($_SESSION['cahol_dolg_id'])) {
       unset($_SESSION['cahol_dolg_id']);
       }
    session_destroy();
    header("Location: bejelentkezes.php");
    exit();
} else {
    header("Location: bejelentkezes.php");
    exit();
}
?>