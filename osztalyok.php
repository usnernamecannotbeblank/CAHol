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
        $osztaly_id = $adatok['osztaly_id'];
        $osztaly_nev = $adatok['osztaly_nev'];
        $osztaly_leiras = $adatok['osztaly_leiras'];
        $osztaly_vezeto = $adatok['osztaly_vezeto'];
        try {
            $query = "SELECT * FROM osztalyok WHERE osztaly_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$osztaly_id]);
            $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
            if($eredmeny) {
                echo json_encode(["msg" => "A megadott azonosítóval már van osztály!"]);
            }
            else {
                $query = "INSERT INTO osztalyok (osztaly_id, osztaly_nev, leiras, vezeto) VALUES(?, ?, ?, ?)";
                $muvelet = $kapcsolat->prepare($query);
                $muvelet->execute([$osztaly_id, $osztaly_nev, $osztaly_leiras, $osztaly_vezeto]);
                echo json_encode(["success" => "Sikeres hozzáadás!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "GET") {
        $query = "SELECT o.* FROM osztalyok o";
        $muvelet = $kapcsolat->prepare($query);
        $muvelet->execute();
        $eredmeny = $muvelet->fetchAll(PDO::FETCH_OBJ);
        if($eredmeny)
            echo json_encode(['success' => $eredmeny]);
        else echo json_encode(['msg' => "Nincs még osztály meghatározva!"]); 
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $osztaly_id = $adatok['osztaly_id'];
        $osztaly_nev = $adatok['osztaly_nev'];
        try {
            $query = "UPDATE osztalyok SET osztaly_nev = ? WHERE osztaly_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$osztaly_nev, $osztaly_id]);
            
            if($muvelet->rowCount() >0) {
                echo json_encode(["success" => "Sikeres módosítás!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval $osztaly_id nincs osztály!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $osztaly_id = json_decode(file_get_contents("php://input"), true)['osztaly_id'];
        try {
            $query = "DELETE FROM osztalyok WHERE osztaly_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$osztaly_id]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres törlés!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval nincs osztály!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
    }
?>