<?php
session_start();
if (isset($_SESSION['cahol_nev'])) {
    unset($_SESSION['cahol_nev']);
    if(isset($_SESSION['cahol_dolg_id'])) {
       unset($_SESSION['cahol_dolg_id']);
       }
    // latohu 20251116 -> Biztonságosabb lehet, ha minden használt session változó tartalma is törlésre kerül.
    if(isset($_SESSION['cahol_jogosultsag'])) {
        unset($_SESSION['cahol_jogosultsag']);
        }
    if(isset($_SESSION['cahol_jog_megjel'])) {
        unset($_SESSION['cahol_jog_megjel']);
        }
    session_destroy();
    header("Location: bejelentkezes.php");
    exit();
} else {
    header("Location: bejelentkezes.php");
    exit();
}
?>