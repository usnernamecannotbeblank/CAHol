<?php
    header("Content-Type: application/json");
    try{
        $kapcsolat = new PDO("mysql:host=localhost;dbname=afp_cahol", "root", "");
        $kapcsolat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        // Képfeltöltés miatt itt a szokásos form inputjait használjuk
        if (!isset($_POST['rendszam'], $_POST['atipus'], $_FILES['foto_url'])) {
            echo json_encode(["error" => "Adja meg legalább a főbb adatokat a autónak (rendszám, típus) és töltös fel róla képet!"]);
            exit;
        }
        $rendszam = $_POST['rendszam'];
        $atipus = $_POST['atipus'];
        $uzemanyag = $_POST['uzemanyag'];
        $szin = $_POST['szin'];
        $beszerzes = $_POST['beszerzes'];
        $foto_url = $_FILES['foto_url'];

        // készítünk neki tároláshoz helyet
        $feltoltesHelye = 'kepek_db/';
        if (!is_dir($feltoltesHelye)) {
            mkdir($feltoltesHelye, 0777, true);
        }
        $fileNev = basename($foto_url['name']);
        $celUtvonal = $feltoltesHelye . uniqid() . "_" . $fileNev;
        // Megpróbáljuk áthelyezni a szerver ideiglenes tárhelyéről a végleges könyvtárjába
        if (move_uploaded_file($foto_url['tmp_name'], $celUtvonal)) {
            try {
                $query = "SELECT * FROM autok WHERE rendszam = ?";
                $muvelet = $kapcsolat->prepare($query);
                $muvelet->execute([$rendszam]);
                $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
    
                if ($eredmeny) {
                    echo json_encode(["msg" => "A megadott rendszámmal van autó!"]);
                } else {
                    $query = "INSERT INTO autok (rendszam, tip_id, uzemanyag, szin, beszerzes, foto_url) VALUES(?, ?, ?, ?, ?, ?)";
                    $muvelet = $kapcsolat->prepare($query);
                    $muvelet->execute([$rendszam, $atipus, $uzemanyag, $szin, $beszerzes, $celUtvonal]);
    
                    echo json_encode(["success" => "Sikeres hozzáadás!"]);
                }
            } catch(PDOException $e) {
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "A fájl feltöltése nem sikerült."]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "GET") {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $query = "SELECT autok.rendszam, autok.tip_id, autok.foto_url, autok.uzemanyag, autok.szin, autok.beszerzes,
                         auto_tipus.marka, auto_tipus.tipus, auto_tipus.felepitmeny, CONCAT(auto_tipus.marka , ' - ' , auto_tipus.tipus) markatipus,
                         (SELECT max(kinel_van.id) FROM kinel_van WHERE kinel_van.rendszam = autok.rendszam AND kinel_van.dolg_id = ?) AS kinelId
                  FROM autok INNER JOIN auto_tipus ON autok.tip_id = auto_tipus.tip_id";
        $muvelet = $kapcsolat->prepare($query);
        $muvelet->execute([$_SESSION['dolg_id']]);
        $eredmeny = $muvelet->fetchAll(PDO::FETCH_OBJ);
        if($eredmeny)
            echo json_encode(['success' => $eredmeny]);
        else echo json_encode(['msg' => "Nincs még autó!"]); 
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $rendszam = $adatok['rendszam'];
        $uzemanyag = $adatok['uzemanyag'];
        $atipusId = $adatok['atipusId'];
        try {
            $query = "UPDATE autok SET uzemanyag = ?, tip_id = ? WHERE rendszam = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$uzemanyag, $atipusId, $rendszam]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres frissítés!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval $rendszam nincs autó!"]);
                
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $rendszam = json_decode(file_get_contents("php://input"), true)['rendszam'];
        try {
            $query = "DELETE FROM autok WHERE rendszam = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$rendszam]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres törlés!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval $rendszam nincs autó!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
    }
?>