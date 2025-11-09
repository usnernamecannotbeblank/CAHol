<!-- irok_fetcher.php, irok.php   ->   auto_tipus_lista.php, auto_tipus.php    ->   osztalyok_lista.php, osztalyok.php   ->  telephelyek_lista.php, telephelyek.php -->
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telephelyek</title>
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/content.css">
    <style>
        body {
            background-image: url('../kepek/burj_khalifa.webp');
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
    <h1>Telephelyek</h1>
    <div id="errDiv" style="color: red;"></div>
    <div id="msgDiv" style="font-weight: bolder;" ></div>
    <div id="tableSection">
        <table id="contentTable">
            <tr>
                <th>Azonosító</th>
                <th>Megnevezés</th>
                <th>Cím</th>
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
                    <td><label for="updateCim">Cím:</label></td>
                    <td><input type="text" id="updateCim"></td>
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
        fetch("telephelyek.php", {
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
                    azTD.innerText = item['tlph_id'];
                    var nevTD = row.insertCell(1);
                    nevTD.innerText = item['telephely_nev'];
                    var cimTD = row.insertCell(2);
                    cimTD.innerText = item['cim'];                    
                    <?php if($_SESSION['cahol_jogosultsag'] == 'admin'): ?>
                    var muveletekTD = row.insertCell(3);
                    muveletekTD.innerHTML = `<button class="update" data-tlph_id="${item.tlph_id}">Módosítás</button>
                                             <button class="delete" data-tlph_id="${item.tlph_id}">Törlés</button>`;
                    <?php endif; ?>
                });
                document.querySelectorAll('.delete').forEach(button => {
                    button.addEventListener('click', function() {
                        const azID = this.dataset.tlph_id;
                    
                        fetch("telephelyek.php", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ tlph_id : azID })
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
                        const azID = this.dataset.tlph_id;
                        const row = this.closest('tr');
                        //const nev = row.querySelector('td:first-child').innerText;
                        const nev = row.querySelector('td:nth-child(2)').innerText;
                        const cim = row.querySelector('td:nth-child(3)').innerText;

                        // Fill and show the form
                        document.getElementById('updateAz').value = azID;
                        document.getElementById('updateNev').value = nev;
                        document.getElementById('updateCim').value = cim;

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

            const azID = document.getElementById('updateAz').value;
            const nev = document.getElementById('updateNev').value;
            const cim = document.getElementById('updateCim').value;

            fetch("auto_tipus.php", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ tlph_id: azID, telephely_nev: nev, cim : cim })
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