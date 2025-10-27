<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hozzám rendelt autók</title>
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
    <h1>Hozzám rendelt autó(k)</h1>
    <?php
        if(isset($_SESSION['nev']) && isset($_SESSION['dolg_id'])) {
            echo "<p align='right'> <b> Bejelentkezve : " . $_SESSION['dolg_id'] . " &nbsp &nbsp &nbsp / &nbsp &nbsp &nbsp " . $_SESSION['nev'] . " &nbsp &nbsp &nbsp </b><br>";
        }
    ?>
    <div id="errDiv" style="color: red;"></div>
    <div id="msgDiv" style="font-weight: bolder;" ></div>
    <div id="tableSection">
        <table id="contentTable">
            <tr>
                <th>Rendszám</th>
                <th>Típus Az</th>
                <th>Márka - Típus</th>
                <th>Kép</th>

                <th>Művelet(ek)</th>
            </tr>
        </table>
    </div>
    <div id="formSection" style="display: none;">
        <form id="updateForm">
            <table>
                <tr>
                    <td><label for="updateRendszam">Autó rendszáma: </label></td>
                    <td><input type="text" id="updateRendszam"></td>
                </tr>
                <tr>
                    <td><label for="atipusSelect">Típus: </label></td>
                    <td><select id="atipusSelect"></select></td>
                </tr>
                <tr>
                    <td><label for="markaSelect">Márka-tipus: </label></td>
                    <td><input type="text" id="markaSelect"></td>
                </tr>
            </table>
            <input type="hidden" id="updateId">
            
            
            <button type="submit">Mentés</button>
            <button type="button" id="cancelBtn">Mégse</button>
        </form>
    </div>
    <script>
        // Lekérjük az összes autót
        fetch("kiadva.php", {
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
                    var foto_urlTD = row.insertCell(3);
                    foto_urlTD.innerHTML = `<img src="${item.foto_url}" width="200px" height="125px">`;

                    //Művelet oszlop
                    if(item.kinelID) {
                        var returnTD = row.insertCell(4);
                        returnTD.innerHTML = `<button class="return" data-kinelId="${item.kinelID}">Visszaad</button>`;
                        console.log(item);
                    }
                    else {
                        var rentTD = row.insertCell(4);
                        rentTD.innerHTML = `<button class="rent" data-rendszam="${item.rendszam}">Átvesz</button>`;
                    }
                });
                // Miután betöltöttük, az oldalon lévő összes törlő és frissítő gombhoz hozzáadunk egy esemény-figyelőt
                document.querySelectorAll('.delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const rendszamId = this.dataset.rendszam;
                        console.log(rendszamId);
                        fetch("autok.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ rendszam: rendszamId })
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

                        const rendszamID = this.dataset.rendszam;
                        const markatipus = this.dataset.markatipus;
                        const row = this.closest('tr');
                        const rendszam = row.querySelector('td:first-child').innerText;

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
                                data.success.forEach(atipus => {
                                    if(atipus['markatipus'] == markatipus)
                                        selectElement.innerHTML += `<option value='${atipus['tip_id']}' selected>${atipus['markatipus']}</option>`;
                                    else
                                    selectElement.innerHTML += `<option value='${atipus['tip_id']}'>${atipus['megn']}</option>`;

                                })
                            }
                            document.getElementById('updateRendszam').value = rendszam;

                            document.getElementById('tableSection').style.display = 'none';
                            document.getElementById('formSection').style.display = 'block';
                        })
                        
                    });
                });
                document.querySelectorAll('.rent').forEach(button => {
                    button.addEventListener('click', function() {
                        const rendszam = this.dataset.rendszam;
                        console.log(rendszam);
                        fetch("kiadva.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ rendszam : rendszam })
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
                id : document.getElementById('updateId').value,
                rendszam : document.getElementById('updateRendszam').value,
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