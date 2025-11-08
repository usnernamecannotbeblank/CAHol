<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="creator.css">
    <style>
        body {
            background-image: url('kepek/BTU_ceg.jpg');
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
    <?php require_once "menu.php"; ?>

    <h1>Regisztráció</h1>
    <?php
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
        if(isset($_SESSION['cahol_nev']) && isset($_SESSION['cahol_dolg_id'])) {
            echo "<p align='right'> <b> Bejelentkezve : " . $_SESSION['cahol_dolg_id'] . " &nbsp &nbsp &nbsp / &nbsp &nbsp &nbsp " . $_SESSION['cahol_nev'] . " &nbsp &nbsp &nbsp ( " . $_SESSION['cahol_jog_megjel'] .  " ) &nbsp &nbsp &nbsp </b><br>";
        }
    ?>    
    <div id="errDiv" style="color: red;"></div>
    <div id="msgDiv" style="font-weight: bolder;" ></div>
    <form id="regForm">
        <!-- Mindenki kérje be azokat a dolgokat, amiket a felhasználókhoz rendelt -->
        <!-- Submit előtt itt történjen meg a form ellenőzrése, amit megtalálnak a tinyurl-en szintén -->
        <table>
            <tr>
                <td><label for="dolg_id">Dolgozó azonosító: </label></td>
                <td><input type="number" id="dolg_id" pattern=".{1,10}" required></td>
                <td>Csak szám lehet, nincs hossz korlát (max 10 karakter), kötelező</td>
            </tr> 
            <tr>
                <td><label for="nev">Név:</label></td>
                <td><input type="text" id="nev" pattern=".{6,50}" required></td>
                <td>Csak hossz korlátozás van: 6..50, kötelező</td>
            </tr>
            <tr>
                <td><label for="email">E-mail:</label></td>
                <td><input type="text" id="email" pattern=".{6,50}"></td>
                <td>Csak hossz korlátozás van: 6..50, nem kötelező</td>
            </tr>
            <tr>
                <td><label for="osztaly">Osztály azonosító: </label></td>
                <td><input type="text" id="osztaly"></td>
                <td>Nincs feltétel és vizsgálat (létezés vizsgálat külön kellene).</td>
            </tr> 
            <tr>
                <td><label for="jelszo">Jelszó:</label></td>
                <td><input type="password" id="jelszo"pattern="(?=.*\d)(?=.*[A-Z])(?=.*\W).{6,10}" required></td>
                <td>Minimum 6, maximum 10 karakter hosszúság, nagybetűt és számot kell tartalmazzon és egy speciális karaktert!</td>
            </tr>
        </table>
        <input type="submit" value="Regisztráció" >
    </form>

    <script>
        document.getElementById('regForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const jsonData = JSON.stringify({
                dolg_id : document.getElementById('dolg_id').value,
                nev : document.getElementById('nev').value,
                jelszo : document.getElementById('jelszo').value,
                osztaly_id : document.getElementById('osztaly').value,
                email : document.getElementById('email').value,
                muvelet : "reg"
            });
            fetch("felhasznalok.php", {
                method: "POST",
                header: {"Content-Type" : "application/json"},
                body: jsonData
            })
            .then(response => response.json()) // JSON formátumba alakítja a választ
            .then(data => {
                if(data.error) {
                    document.getElementById('errDiv').innerText = data.error;
                }
                else if(data.success) {
                    window.location.href = "bejelentkezes.php";
                }
                else if(data.msg){
                    document.getElementById('msgDiv').innerText = data.msg;
                }
            })
        });
    </script>
</body>
</html>