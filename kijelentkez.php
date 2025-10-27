<?php
session_start();
if (isset($_SESSION['nev'])) {
    unset($_SESSION['nev']);
    if(isset($_SESSION['dolg_id'])) {
       unset($_SESSION['dolg_id']);
       }
    session_destroy();
    header("Location: bejelentkezes.php");
    exit();
} else {
    header("Location: bejelentkezes.php");
    exit();
}
?>