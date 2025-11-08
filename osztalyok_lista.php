<!-- irok_fetcher.php, irok.php   ->   auto_tipus_lista.php, auto_tipus.php    ->   osztalyok_lista.php, osztalyok.php   -->
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osztályok</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="content.css">
    <style>
        body {
            background-image: url('kepek/burj_khalifa.webp');
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
    <h1>Osztályok</h1>
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
                <th>Azonosító</th>
                <th>Osztály megnevezése</th>
                <?php if($_SESSION['cahol_jogosultsag'] == 'admin'): ?>
                    <th>Művelet(ek)</th>
                <?php endif; ?>
            </tr>
        </table>
    </div>
    <div id="formSection" style="display: none;">
        <form id="updateForm">
            <table>
                <tr>
                    <td><label for="updateAz">Azonosító:</label></td>
                    <td><input type="text" id="updateAz"></td>
                </tr>
                <tr>
                    <td><label for="updateNev">Megnevezés:</label></td>
                    <td><input type="text" id="updateNev"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Mentés</button><button type="button" id="cancelBtn">Mégse</button></td>
                </tr>
            </table>
            <!-- Ez akkor kellene, ha az azonosító nem kerülne megjelenítésre és nem lehetne módosítani.
            <input type="hidden" id="updateTipId">
            -->
        </form>
    </div>
    <script>
        fetch("osztalyok.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                document.getElementById('errDiv').innerText = data.error;
            }
            else if(data.success) {
                var table = document.getElementById("contentTable");
                data.success.forEach(item => {
                    var row = table.insertRow(1);
                
                    var azTD = row.insertCell(0);
                    azTD.innerText = item['osztaly_id'];
                    var nevTD = row.insertCell(1);
                    nevTD.innerText = item['osztaly_nev'];
                    <?php if($_SESSION['cahol_jogosultsag'] == 'admin'): ?>
                    var muveletekTD = row.insertCell(2);
                    muveletekTD.innerHTML = `<button class="update" data-osztaly_id="${item.osztaly_id}">Módosítás</button>
                                             <button class="delete" data-osztaly_id="${item.osztaly_id}">Törlés</button>`;
                    <?php endif; ?>
                });
                document.querySelectorAll('.delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const azID = this.dataset.osztaly_id;
                    
                        fetch("osztalyok.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ osztaly_id : azID })
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
                        const azID = this.dataset.osztaly_id;
                        const row = this.closest('tr');
                        //https://stackoverflow.com/questions/7228800/how-to-select-first-and-last-td-in-a-row
                        //const nev = row.querySelector('td:first-child').innerText;
                        //const nev = row.querySelector('td:last-child').innerText;
                        const nev = row.querySelector('td:nth-child(2)').innerText;
                        //const nev = "haha";//this.dataset.osztaly_nev;

                        // Fill and show the form
                        document.getElementById('updateAz').value = azID;
                        document.getElementById('updateNev').value = nev;

                        // Switch views
                        document.getElementById('tableSection').style.display = 'none';
                        document.getElementById('formSection').style.display = 'block';
                    });
                });

                document.getElementById('cancelBtn').addEventListener('click', function () {
                    document.getElementById('formSection').style.display = 'none';
                    document.getElementById('tableSection').style.display = 'block';
                });
            }
            else if(data.msg){
                document.getElementById('msgDiv').innerText = data.msg;
            }
        });

        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const osztaly_id = document.getElementById('updateAz').value;
            const osztaly_nev = document.getElementById('updateNev').value;

            fetch("osztalyok.php", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ osztaly_id: osztaly_id, osztaly_nev: osztaly_nev })
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    document.getElementById('msgDiv').innerText = "Sikeres módosítás";
                
                    document.getElementById('formSection').style.display = 'none';
                    document.getElementById('tableSection').style.display = 'block';
                
                    location.reload();
                } else {
                    document.getElementById('errDiv').innerText = resp.error || "Hiba történt.";
                }
            });
        });
    </script>
</body>
</html>