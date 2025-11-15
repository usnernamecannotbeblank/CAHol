<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autó létrehozása</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="creator.css">
    <style>
        body {
            background-image: url('kepek/BTU_auto.jpg');
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
    <h1>Autó létrehozása</h1>
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

    <form id="ujAutoForm" enctype="multipart/form-data">
        <table>
            <tr>
                <td><label for="rendszam">Rendszám</label></td>
                <td><input type="text" id="rendszam"/></td>
            </tr>
            <tr>
                <td><label for="atipusSelect">Típus</label></td>
                <td>
                    <select id="atipusSelect">
                        <option disabled>Válasszon típust!</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="uzemanyag">Üzemanyag</label></td>
                <td><input type="text" id="uzemanyag"/></td>
            </tr>
            <tr>
                <td><label for="szin">Szín</label></td>
                <td><input type="text" id="szin"/></td>
            </tr>
            <tr>
                <td><label for="beszerzes">Beszerzés</label></td>
                <td><input type="date" id="beszerzes"/></td>
            </tr>

            <tr>
                <td><label for="foto_url">Kép</label></td>
                <td><input type="file" id="foto_url" accept="image/*" required /></td>
            </tr>
        </table>
        
        
        
        <input type="submit" value="Hozzáadás" id="submitBtn"/>
    </form>


    <script>
        // Lekérjük az autó típusokat, hogy betölthessük őket a kiválasztáshoz
        fetch("auto_tipus.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                document.getElementById('errDiv').innerText = data.error;
            }
            else if(data.msg){
                document.getElementById('msgDiv').innerText = data.msg;

            }
            // Ha sikeres volt, töltsük be őket option tagek használatával a select-ünkbe!
            else if(data.success) {
                const selectElement = document.getElementById('atipusSelect');
                data.success.forEach(atip => {
                    // foreach-csel végigmegyek a beérkező autó típusokon, jelenlegi elemre markatipus néven hivatkozok és abból lesz tip_id tárolva
                    selectElement.innerHTML += `<option value='${atip['tip_id']}'>${atip['markatipus']}</option>`;
                })
            }
        })
        
        document.getElementById('ujAutoForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Mivel képet töltünk fel, itt FormData-t kell használnunk, JSON-ként nem fogjuk tudni feltölteni
            const formData = new FormData();
            formData.append('rendszam', document.getElementById('rendszam').value);
            formData.append('atipus', document.getElementById('atipusSelect').value);
            formData.append('uzemanyag', document.getElementById('uzemanyag').value);
            formData.append('szin', document.getElementById('szin').value);
            formData.append('beszerzes', document.getElementById('beszerzes').value);
            formData.append('foto_url', document.getElementById('foto_url').files[0]);

            fetch("autok.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('errDiv').innerText = data.error;
                } else if (data.success) {
                    window.location.href = "autok_lista.php";
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