<?php
session_start();
//$_SESSION['nev'] és $_SESSION['dolg_id'] van használva, de együtt, így bármelyik vizsgálata is elég, ezért nem lett módosítva a példa
if (!isset($_SESSION['nev'])) {
    header("Location: bejelentkezes.php");
    exit();
}
?>