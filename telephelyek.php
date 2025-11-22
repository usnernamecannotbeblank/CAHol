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
        $tlph_id = $adatok['tlph_id'];
        $nev = $adatok['nev'];
        $cim = $adatok['cim'];
        try {
            $query = "SELECT * FROM telephely WHERE tlph_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$tlph_id]);
            $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
            if($eredmeny) {
                echo json_encode(["msg" => "A megadott azonosítóval már van telephely!"]);
            }
            else {
                $query = "INSERT INTO telephely (tlph_id, telephely_nev, cim) VALUES(? , ? , ?)";
                $muvelet = $kapcsolat->prepare($query);
                $muvelet->execute([$tlph_id, $nev, $cim]);
                echo json_encode(["success" => "Sikeres hozzáadás!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "GET") {
        $query = "SELECT t.* FROM telephely t ORDER BY t.tlph_id desc";
        $muvelet = $kapcsolat->prepare($query);
        $muvelet->execute();
        $eredmeny = $muvelet->fetchAll(PDO::FETCH_OBJ);
        if($eredmeny)
            echo json_encode(['success' => $eredmeny]);
        else echo json_encode(['msg' => "Nincs még autó típus meghatározva!"]); 
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $tlph_id = $adatok['tlph_id'];
        $nev = $adatok['telephely_nev'];
        $cim = $adatok['cim'];
        try {
            $query = "UPDATE telephely SET telephely_nev = ?, cim = ? WHERE tlph_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$nev, $cim, $tlph_id]);
            
            if($muvelet->rowCount() >0) {
                echo json_encode(["success" => "Sikeres módosítás!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval $tlph_id nincs telephely!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $tlph_id = json_decode(file_get_contents("php://input"), true)['tlph_id'];
        try {
            $query = "DELETE FROM telephely WHERE tlph_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$tlph_id]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres törlés!"]);
            }
            else {
                echo json_encode(["error" => "A megadott azonosítóval nincs telephely!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
    }
?>