<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telephely létrehozása</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="creator.css">
    <style>
        body {
            background-size: cover;
            background-position: center; 
            background-repeat: no-repeat;
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.7); 
            z-index: -1; 
        }
    </style>

</head>
<body>
    <?php require_once "ellenoriz.php"; ?>
    <?php require_once "menu.php"; ?>
    <h1>Telephely létrehozása</h1>
    <?php
        if(isset($_SESSION['cahol_nev']) && isset($_SESSION['cahol_dolg_id'])) {
            // latohu 20251116 -> Jobb a feltétel vizsgálaton belül elhelyezni, bár itt ezek már élnek, mert csak bejelentkezés után érhető el az oldal. (BUG-001 alapján máshol ide került)
            if($_SESSION['cahol_jogosultsag'] == 'admin') {
                $_SESSION['cahol_jog_megjel'] = 'Adminisztrátor';
            }
            if($_SESSION['cahol_jogosultsag'] == 'user') {
                $_SESSION['cahol_jog_megjel'] = 'Felhasználó';
            }
            if($_SESSION['cahol_jogosultsag'] == 'suser') {
                $_SESSION['cahol_jog_megjel'] = 'Szuper Felhasználó';
            }
            if($_SESSION['cahol_jogosultsag'] == 'reg') {
                $_SESSION['cahol_jog_megjel'] = 'Regisztrált - csak';
            }
            echo "<p align='right'> <b> Bejelentkezve : " . $_SESSION['cahol_dolg_id'] . " &nbsp &nbsp &nbsp / &nbsp &nbsp &nbsp " . $_SESSION['cahol_nev'] . " &nbsp &nbsp &nbsp ( " . $_SESSION['cahol_jog_megjel'] .  " ) &nbsp &nbsp &nbsp </b><br>";
        }
    ?>
    <div id="errDiv" style="color: red;"></div>
    <div id="msgDiv" style="font-weight: bolder;" ></div>

    <form id="ujAutoForm" enctype="multipart/form-data">
        <table>
            <tr>
                <td><label for="azonosito">Azonosító</label></td>
                <td><input type="text" id="azonosito"/></td>
            </tr>
            <tr>
                <td><label for="telephely_nev">Telephely megnevezése</label></td>
                <td><input type="text" id="telephely_nev"/></td>
            </tr>
            <tr>
                <td><label for="cim">Cím</label></td>
                <td><input type="text" id="cim"/></td>
            </tr>
        </table>
        <input type="submit" value="Hozzáadás" id="submitBtn"/>
    </form>


    <script>
    </script>
</body>
</html>