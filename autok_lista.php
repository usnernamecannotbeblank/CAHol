<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autók</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="content.css">
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
    <h1>Autók</h1>
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
    <div id="tableSection">
        <table id="contentTable">
            <tr>
                <th>Rendszám</th>
                <th>Tipus Az.</th>
                <th>Márka-tipus</th>
                <th>Üzemanyag</th>
                <th>Szín</th>
                <th>Beszerzés</th>
                <th>Kép</th>
                <th>Művelet(ek)</th>
            </tr>
        </table>
    </div>
    <div id="formSection" style="display: none;">
        <form id="updateForm">
            <table>
                <tr>
                    <td><label for="updateRendszam">Rendszám: </label></td>
                    <td><input type="text" id="updateRendszam"></td>
                </tr>
                <tr>
                    <td><label for="atipusSelect">Típus Az: </label></td>
                    <td><select id="atipusSelect"></select></td>
                </tr>
                <tr>
                    <td><label for="markaSelect">Márka-tipus: </label></td>
                    <td><input type="text" id="markaSelect" readonly></td>
                </tr>
                <tr>
                    <td><label for="updateUzemanyag">Üzemanyag: </label></td>
                    <td><input type="text" id="updateUzemanyag"></td>
                </tr>
                <tr>
                    <td><label for="updateSzin">Szín: </label></td>
                    <td><input type="text" id="updateSzin"></td>
                </tr>
                <tr>
                    <td><label for="updateBeszerzes">Beszerzes: </label></td>
                    <td><input type="text" id="updateBeszerzes"></td>
                </tr>

            </table>
            <input type="hidden" id="updateId">

            &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
            &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
            <button type="submit" align="Center">Mentés</button>
            <button type="button" id="cancelBtn" align="Center">Mégse</button>

        </form>
    </div>
    <script>
        // Lekérjük az összes könyvet
        fetch("autok.php", {
                method: "GET",
                header: {"Content-Type" : "application/json"}
        })
        .then(response => response.json()) 
        .then(data => {
            if(data.error) {
                document.getElementById('errDiv').innerText = data.error;
            }
            else if(data.success) {
                // Beérkező könyveket soronként beletöltjük a táblázatba
                var table = document.getElementById("contentTable");
                data.success.forEach(item => {
                    var row = table.insertRow(1);
                
                    var rendszamTD = row.insertCell(0);
                    rendszamTD.innerText = item.rendszam;
                    var atipusTD = row.insertCell(1);
                    atipusTD.innerText = item.tip_id;
                    var markatipusTD = row.insertCell(2);
                    markatipusTD.innerText = item.markatipus;
                    var uzemaTD = row.insertCell(3);
                    uzemaTD.innerText = item.uzemanyag;
                    var szinTD = row.insertCell(4);
                    szinTD.innerText = item.szin;
                    var beszerzesTD = row.insertCell(5);
                    beszerzesTD.innerText = item.beszerzes;
                    var foto_urlTD = row.insertCell(6);
                    foto_urlTD.innerHTML = `<img src="${item.foto_url}" width="200px" height="125px">`;

                    // Csak akkor módosíthassunk, ha adminok vagyunk (következő a műveletek oszlop, ide a megfelelő nyomógomb elhelyezése)
                    <?php if($_SESSION['cahol_jogosultsag'] == "admin"): ?>
                        var muveletekTD = row.insertCell(7);
                        muveletekTD.innerHTML = `<button class="update" data-rendszam="${item.rendszam}">Módosítás</button> 
                                                 <button class="delete" data-rendszam="${item.rendszam}">Törlés</button>`;                   
                    <?php endif; ?>
                
                    // Ha user, akkor a művelet a visszadás, vagy átvétel lehet
                    <?php if($_SESSION['cahol_jogosultsag'] == "user"): ?>
                        if(item.kinelId) {
                            var returnTD = row.insertCell(7);
                            returnTD.innerHTML = `<button class="return" data-kinelId="${item.kinelId}">Visszaad</button>`;
                            console.log(item);
                        }
                        else {
                            var rentTD = row.insertCell(7);
                            rentTD.innerHTML = `<button class="rent" data-rendszam="${item.rendszam}">Átvesz</button>`;
                        }
                    <?php endif; ?>
                });
                // Miután betöltöttük, az oldalon lévő összes törlő és frissítő gombhoz hozzáadunk egy esemény-figyelőt
                document.querySelectorAll('.delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const autoId = this.dataset.rendszam;
                        console.log(autoId);
                        fetch("autok.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ rendszam : autoId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('msgDiv').innerText = data.success;
                                this.closest('tr').remove();
                            } else {
                                document.getElementById('errDiv').innerText = data.error;
                            }
                        });
                    })
                });
                document.querySelectorAll('.update').forEach(button => {
                    button.addEventListener('click', function () {
                        // Szedjük ki a tag adattárolójában található értékeket
                        // Az oldalon található update form-ot tegyük láthatóvá, a táblázatot rejtsük el
                        // majd kérjük le az összes szerzőt és töltsük be a select-be

                        const autoID = this.dataset.rendszam;
                        //const atipus = this.dataset.atipus;
                        //const markatipus = this.dataset.markatipus;
                        //const uzemanyag = this.dataset.uzemanyag;
                        //const szin = this.dataset.szin;
                        //const beszerzes = this.dataset.beszerzes;
                        const row = this.closest('tr');
                        //const auto = row.querySelector('td:first-child').innerText;
                        const atipus = row.querySelector('td:nth-child(2)').innerText;
                        const markatipus = row.querySelector('td:nth-child(3)').innerText;
                        const uzemanyag = row.querySelector('td:nth-child(4)').innerText;
                        const szin = row.querySelector('td:nth-child(5)').innerText;
                        const beszerzes = row.querySelector('td:nth-child(6)').innerText;

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
                            else if(data.success) {
                                const selectElement = document.getElementById('atipusSelect');
                                data.success.forEach(atip => {
                                    if(atip['tip_id'] == atipus)
                                        selectElement.innerHTML += `<option value='${atip['tip_id']}' selected>${atip['markatipus']}</option>`;
                                    else
                                    selectElement.innerHTML += `<option value='${atip['tip_id']}'>${atip['markatipus']}</option>`;

                                })
                            }
                            document.getElementById('updateRendszam').value = autoID;
                            document.getElementById('markaSelect').value = markatipus;
                            document.getElementById('updateUzemanyag').value = uzemanyag;
                            document.getElementById('updateSzin').value = szin;
                            document.getElementById('updateBeszerzes').value = beszerzes;

                            document.getElementById('tableSection').style.display = 'none';
                            document.getElementById('formSection').style.display = 'block';
                        })
                        
                    });
                });
                document.querySelectorAll('.rent').forEach(button => {
                    button.addEventListener('click', function() {
                        const autoId = this.dataset.rendszam;
                        console.log(autoId);
                        fetch("kiadva.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ rendszam : autoId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                document.getElementById('errDiv').innerText = data.error;
                            }
                        });
                    })
                });
                document.querySelectorAll('.return').forEach(button => {
                    button.addEventListener('click', function() {
                        const kinelId = this.dataset.kinelid;
                        console.log(kinelId);
                        fetch("kiadva.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ kinelId : kinelId })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                document.getElementById('errDiv').innerText = data.error;
                            }
                        });
                    })
                });
                document.getElementById('cancelBtn').addEventListener('click', function () {
                    document.getElementById('formSection').style.display = 'none';
                    document.getElementById('tableSection').style.display = 'block';
                });
            }
            else if(data.msg){
                document.getElementById('msgDiv').innerText = data.msg;
            }
        })
        // Ha elküldtük a formunkat, hajtsuk végre a frissítést!
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const jsonData = JSON.stringify({
                rendszam : document.getElementById('updateRendszam').value,
                uzemanyag : document.getElementById('updateUzemanyag').value,
                atipusId : document.getElementById('atipusSelect').value
            });
            fetch("autok.php", {
                method: "PUT",
                header: {"Content-Type" : "application/json"},
                body: jsonData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('msgDiv').innerText = "Sikeres módosítás";
                
                    document.getElementById('formSection').style.display = 'none';
                    document.getElementById('tableSection').style.display = 'block';
                
                    location.reload();
                } else {
                    document.getElementById('errDiv').innerText = data.error || "Hiba történt.";
                }
            })
        });
    </script>
</body>
</html>