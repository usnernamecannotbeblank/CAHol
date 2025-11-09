<?php
    header("Content-Type: application/json");
    try{
        $kapcsolat = new PDO("mysql:host=localhost;dbname=afp_cahol", "root", "");
        $kapcsolat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        //$tipid = json_decode(file_get_contents("php://input"), true)['tipid'];
        $adatok = json_decode(file_get_contents("php://input"), true);
        if(isset( $adatok['tip_id'])){
                $tip_id = $adatok['tip_id'];
            }
            else{
                $tip_id = "";
            }
        $marka = $adatok['marka'];
        $tipus = $adatok['tipus'];
        $felep = $adatok['felep'];
        try {
            $query = "SELECT * FROM auto_tipus WHERE tip_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$tip_id]);
            $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
            if($eredmeny) {
                echo json_encode(["msg" => "A megadott azonosítóval már van autó típus!"]);
            }
            else {
                $query = "INSERT INTO auto_tipus (marka, tipus, felepitmeny) VALUES(? , ? , ?)";
                $muvelet = $kapcsolat->prepare($query);
                $muvelet->execute([$marka, $tipus, $felep]);
                echo json_encode(["success" => "Sikeres hozzáadás!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "GET") {
        $query = "SELECT at.*, CONCAT(at.marka , ' - ' , at.tipus) markatipus FROM auto_tipus at ORDER BY at.tip_id desc";     //Azért desc, mert így az utoljára felvitt, 
        // legnagyobb azonosítót veszi ki először az adatbázis, s teszi hozzá az átadásra kerülő eredmény string-hez, s így a megjelenítés megfordul.
        $muvelet = $kapcsolat->prepare($query);
        $muvelet->execute();
        $eredmeny = $muvelet->fetchAll(PDO::FETCH_OBJ);
        if($eredmeny)
            echo json_encode(['success' => $eredmeny]);
        else echo json_encode(['msg' => "Nincs még autó típus meghatározva!"]); 
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $tip_id = $adatok['tip_id'];
        $marka = $adatok['marka'];
        $tipus = $adatok['tipus'];
        $felep = $adatok['felep'];
        try {
            $query = "UPDATE auto_tipus SET marka = ?, tipus = ?, felepitmeny = ? WHERE tip_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$marka, $tipus, $felep, $tip_id]);
            
            if($muvelet->rowCount() >0) {
                echo json_encode(["success" => "Sikeres módosítás!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval $tip_id nincs autó típus!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $tip_id = json_decode(file_get_contents("php://input"), true)['tip_id'];
        try {
            $query = "DELETE FROM auto_tipus WHERE tip_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$tip_id]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres törlés!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval nincs autó típus!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
    }
?>