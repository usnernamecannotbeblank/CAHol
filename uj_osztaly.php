<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osztály létrehozása</title>
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
        textarea[id="leiras"]{
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
    </style>

</head>
<body>
    <?php require_once "ellenoriz.php"; ?>
    <?php require_once "menu.php"; ?>
    <h1>Osztály létrehozása</h1>
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

    <form id="ujOsztalyForm" enctype="multipart/form-data">
        <table>
            <tr>
                <td><label for="azonosito">Azonosító</label></td>
                <td><input type="text" id="azonosito"/></td>
            </tr>
            <tr>
                <td><label for="osztal_nev">Osztály megnevezése</label></td>
                <td><input type="text" id="osztaly_nev"/></td>
            </tr>
            <tr>
                <td><label for="leiras">Leírás</label></td>
                <td><textarea id="leiras"></textarea></td>
            </tr>
            <tr>
                <td><label for="vezeto">Vezető neve</label></td>
                <td><input type="text" id="vezeto"/></td>
            </tr>
        </table>
        <input type="submit" value="Hozzáadás" id="submit"/>
    </form>


    <script>
        
        document.getElementById('ujOsztalyForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const jsonData = JSON.stringify({
                osztaly_id : document.getElementById('azonosito').value,
                osztaly_nev : document.getElementById('osztaly_nev').value,
                osztaly_leiras : document.getElementById('leiras').value,
                osztaly_vezeto : document.getElementById('vezeto').value
            });

            fetch("osztalyok.php", {
                method: "POST",
                header: {"Content-Type" : "application/json"},
                body: jsonData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('errDiv').innerText = data.error;
                } else if (data.success) {
                    window.location.href = "osztalyok_lista.php";
                } else if (data.msg) {
                    document.getElementById('msgDiv').innerText = data.msg;
                }
            })
            .catch(err => {
                document.getElementById('errDiv').innerText = "Hiba történt a feltöltés során.";
                console.error(err);
            });
        });
    </script>
</body>
</html>