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
        $feltoltesHelye = __DIR__ . '/../kepek/db/';
        if (!is_dir($feltoltesHelye)) {
            mkdir($feltoltesHelye, 0777, true);
        }
        $fileNev = basename($foto_url['name']);
        $ujNev = uniqid() . "_" . $fileNev;
        $celUtvonal = $feltoltesHelye . $ujNev;
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
                    $muvelet->execute([$rendszam, $atipus, $uzemanyag, $szin, $beszerzes, $ujNev]);
    
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
        $query = "SELECT a.rendszam, a.tip_id, a.foto_url, a.uzemanyag, a.szin, a.beszerzes,
                 at.marka, at.tipus, at.felepitmeny,
                 CONCAT(at.marka, ' - ', at.tipus) AS markatipus,
                 kv_me.id AS kinelId
          FROM autok a
          INNER JOIN auto_tipus at ON a.tip_id = at.tip_id
          LEFT JOIN kinel_van kv_any ON kv_any.rendszam = a.rendszam
          LEFT JOIN kinel_van kv_me  ON kv_me.rendszam = a.rendszam AND kv_me.dolg_id = ?
          WHERE kv_any.id IS NULL OR kv_me.id IS NOT NULL";
        $muvelet = $kapcsolat->prepare($query);
        $muvelet->execute([$_SESSION['cahol_dolg_id']]);
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
        $szin = $adatok['szin'];
        $beszerzes = $adatok['beszerzes'];
        try {
            $query = "UPDATE autok SET uzemanyag = ?, tip_id = ?, szin = ?, beszerzes = ? WHERE rendszam = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$uzemanyag, $atipusId, $szin, $beszerzes, $rendszam]);
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