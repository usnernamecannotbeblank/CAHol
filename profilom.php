<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil kezelés</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="creator.css">
    <style>
        body {
            background-image: url('kepek/profil.jpg');
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
    <h1>Profil módosítása</h1>
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
    <form id="profilForm">
        <table>
            <tr>
                <td><label for="dolg_id">Dolgozó azonosító: </label></td>
                <td><input type="text" id="dolg_id" value="<?php echo $_SESSION['cahol_dolg_id'] ?>"></td>
                <td>Kötelező, nem kerül módosításra</td>
            </tr>
            <tr>
                <td><label for="nev">Név: </label></td>
                <td><input type="text" id="nev" value="<?php echo $_SESSION['cahol_nev'] ?>" pattern=".{6,50}" required></td>
                <td>Kötelező, módosításra kerül, nem ez alapján történik az azonosítás</td>
                <td>Csak hossz korlátozás van: 6..50, kötelező</td>
            </tr>
            <tr>
                <td><label for="email">E-mail:</label></td>
                <td><input type="text" id="email" value="<?php echo $_SESSION['email'] ?>" pattern=".{6,50}"></td>
                <td>Csak hossz korlátozás van: 6..50, nem kötelező</td>
            </tr>
            <tr>
                <td><label for="osztaly_id">Osztály azonosító: </label></td>
                <td><input type="text" id="osztaly_id" value="<?php echo $_SESSION['osztaly_id'] ?>"></td>
                <td><?php echo $_SESSION['osztaly_nev'] ?></td>
                <td>Nincs feltétel és vizsgálat (létezés vizsgálat külön kellene).</td>
            </tr> 
            <tr>
                <td><label for="jelszo">Jelszó: </label></td>
                <td><input type="password" id="jelszo" pattern="(?=.*\d)(?=.*[A-Z])(?=.*\W).{6,10}"></td>
                <td></td>
                <td>Minimum 6, maximum 10 karakter hosszúság, nagybetűt és számot kell tartalmazzon és egy speciális karaktert!</td>
            </tr>
        </table>
        <input type="hidden" id="id" value="<?php echo $_SESSION['cahol_dolg_id'] ?>">
        <input type="submit" value="Módosít" />
    </form>
    <script>
        document.getElementById('profilForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var jelszo = document.getElementById('jelszo').value;
            var jsonData;
            if(jelszo) {
                jsonData = JSON.stringify({
                    dolg_id: document.getElementById('dolg_id').value,
                    nev : document.getElementById('nev').value,
                    osztaly_id : document.getElementById('osztaly_id').value,
                    email : document.getElementById('email').value,
                    jelszo: jelszo
                });
            }
            else {
                jsonData = JSON.stringify({
                    dolg_id: document.getElementById('dolg_id').value,
                    nev : document.getElementById('nev').value,
                    osztaly_id : document.getElementById('osztaly_id').value,
                    email : document.getElementById('email').value
                });
            }
            console.log(jsonData);
            fetch("felhasznalok.php", {
                method: "PUT",
                headers: {"Content-Type" : "application/json"},
                body: jsonData
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    document.getElementById('errDiv').innerText = data.error;
                }
                else if(data.success) {
                    window.location.href = "auto_tipus_lista.php";
                }
                else if(data.msg){
                    document.getElementById('msgDiv').innerText = data.msg;
                }
            })
        });
    </script>
</body>
</html>