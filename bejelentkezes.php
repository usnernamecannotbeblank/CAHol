<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
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
    <h1>Bejelentkezés</h1>
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
    <form id="loginForm">
        <table>
            <tr>
                <td><label for="dolg_id">Dolgozó azonosító: </label></td>
                <td><input type="text" id="dolg_id"></td>
            </tr> 
            <tr>
                <td><label for="nev">Név: </label></td>
                <td><input type="text" id="nev"></td>
            </tr> 
            <tr>
                <td><label for="jelszo">Jelszó: </label></td>
                <td><input type="password" id="jelszo"></td>
            </tr>
        </table>
        <input type="submit" value="Bejelentkezés" />
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const jsonData = JSON.stringify({
                dolg_id : document.getElementById('dolg_id').value,
                nev : document.getElementById('nev').value,
                jelszo : document.getElementById('jelszo').value,
                osztaly_id : "",                                            //üresen is meg kell adni, különben a felhasznalok.php értékadás rossz lesz: $osztaly_id = $adatok['osztaly_id'];
                email : "",
                muvelet : "log"
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
                    window.location.href = "autok_lista.php";
                }
                else if(data.msg){
                    document.getElementById('msgDiv').innerText = data.msg;
                }
            })
        });
    </script>

    <br>
    <br>
    <br>
    <table>
        <tr>
            <th></th><th>Felhasználó név &nbsp &nbsp &nbsp &nbsp</th><th>Dolgozó azonosító &nbsp </th><th>Jelszó &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</th><th>Megjegyzés</th>
        </tr>
        <tr>
            <td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>
                <td>Admin</td><td>1</td><td>123ABC-</td><td>Csak neki van admin jogosultsága, mindenki más csak sima user.</td>
        </tr>
        <tr>
            <td></td><td>Tóth László</td><td>2 </td><td>123ABC-</td>
        </tr>
        <tr>
            <td></td><td>Tóth Ádám</td><td>5 </td><td>123ABC-</td>
        </tr>
        <tr>
            <td></td><td> ... </td>
        </tr>
    </table>

</body>
</html>