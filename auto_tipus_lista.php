<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autó típus</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="content.css">
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
    <h1>Autó típusok</h1>
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
                <th>Márka</th>
                <th>Típus</th>
                <th>Felépítmény</th>
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
                    <td><label for="updateMarka">Márka:</label></td>
                    <td><input type="text" id="updateMarka"></td>
                </tr>
                <tr>
                    <td><label for="updateTipus">Tipus:</label></td>
                    <td><input type="text" id="updateTipus"></td>
                </tr>
                <tr>
                    <td><label for="updateFelep">Felépítmény:</label></td>
                    <td><input type="text" id="updateFelep"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Mentés</button><button type="button" id="cancelBtn">Mégse</button></td>
                </tr>
            </table>
            <input type="hidden" id="updateTipId">
        </form>
    </div>
    <script>
        fetch("auto_tipus.php", {
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
                
                    var markaTD = row.insertCell(0);
                    markaTD.innerText = item['marka'];
                    var tipusTD = row.insertCell(1);
                    tipusTD.innerText = item['tipus'];
                    var felepTD = row.insertCell(2);
                    felepTD.innerText = item['felepitmeny'];                    
                    <?php if($_SESSION['cahol_jogosultsag'] == 'admin'): ?>
                    var muveletekTD = row.insertCell(3);
                    muveletekTD.innerHTML = `<button class="update" data-tip_id="${item.tip_id}">Módosítás</button>
                                             <button class="delete" data-tip_id="${item.tip_id}">Törlés</button>`;
                    <?php endif; ?>
                });
                document.querySelectorAll('.delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const TipID = this.dataset.tip_id;
                    
                        fetch("auto_tipus.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ tip_id : TipID })
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
                        const TipID = this.dataset.tip_id;
                        const row = this.closest('tr');
                        const marka = row.querySelector('td:first-child').innerText;
                        const tipus = row.querySelector('td:nth-child(2)').innerText;
                        const felep = row.querySelector('td:nth-child(3)').innerText;

                        // Fill and show the form
                        document.getElementById('updateTipId').value = TipID;
                        document.getElementById('updateMarka').value = marka;
                        document.getElementById('updateTipus').value = tipus;
                        document.getElementById('updateFelep').value = felep;

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

            const tipid = document.getElementById('updateTipId').value;
            const marka = document.getElementById('updateMarka').value;
            const tipus = document.getElementById('updateTipus').value;
            const felep = document.getElementById('updateFelep').value;

            fetch("auto_tipus.php", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ tip_id: tipid, marka: marka, tipus : tipus, felep : felep })
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