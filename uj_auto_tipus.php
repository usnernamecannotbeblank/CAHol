<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autó típus létrehozása</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="creator.css">
    <style>
        body {
            background-image: url('kepek/tipus.jpg');
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
    <h1>Autó típus létrehozása</h1>
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
    <form id="ujIroForm">
        <table>
            <tr>
                <td><label for="marka">Márka: </label></td>
                <td><input type="text" id="marka"/></td>
            </tr>
            <tr>
                <td><label for="tipus">Típus: </label></td>
                <td><input type="text" id="tipus"/></td>
            </tr>
            <tr>
                <td><label for="felep">Felépítmény: </label></td>
                <td><input type="text" id="felep"/></td>
            </tr>
        </table>
        <input type="submit" value="Hozzáadás" />
    </form>
    <script>
        document.getElementById('ujIroForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const jsonData = JSON.stringify({
                marka : document.getElementById('marka').value,
                tipus : document.getElementById('tipus').value,
                felep : document.getElementById('felep').value
            });
            fetch("auto_tipus.php", {
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