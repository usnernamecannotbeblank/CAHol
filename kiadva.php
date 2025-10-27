<?php
    header("Content-Type: application/json");
    try{
        $kapcsolat = new PDO("mysql:host=localhost;dbname=afp_cahol", "root", "");
        $kapcsolat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $rendszam = json_decode(file_get_contents("php://input"), true)['rendszam'];
        try {
            $query = "INSERT INTO kinel_van (rendszam, dolg_id) VALUES(?, ?)";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$rendszam, $_SESSION['dolg_id']]);
            echo json_encode(["success" => "Sikeres hozzáadás!"]);
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "GET") {
        try{
            $query = "SELECT kinel_van.*, autok.*, auto_tipus.*, CONCAT(auto_tipus.marka , ' - ' , auto_tipus.tipus) markatipus, kinel_van.id kinelID
                      FROM kinel_van 
                      INNER JOIN autok ON autok.rendszam = kinel_van.rendszam 
                      INNER JOIN auto_tipus ON auto_tipus.tip_id = autok.tip_id 
                      WHERE dolg_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$_SESSION['dolg_id']]);
            $eredmeny = $muvelet->fetchAll(PDO::FETCH_OBJ);
            if($eredmeny)
                echo json_encode(['success' => $eredmeny]);
            else echo json_encode(['msg' => "Nincs még általad átvett autó!"]); 
        }
        catch(PDOException $e)  {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $id = $adatok['kinelid'];
        $rendszam = $adatok['rendszam'];
        $dolg_id = $_SESSION['dolg_id'];
        try {
            $query = "UPDATE kinel_van SET dolg_id = ?, rendszam = ? WHERE id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$dolg_id, $rendszam, $id]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres frissítés!"]);
            }
            else {
                echo json_encode(["error" => "Sikertelen módosítás"]);
                
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $id = $adatok['kinelId'];
        try {
            $query = "DELETE FROM kinel_van WHERE id = ?";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$id]);
            if($muvelet->rowCount() > 0) {
                echo json_encode(["success" => "Sikeres törlés!"]);
            }
            else {
                echo json_encode(["error" => "A törlés nem járt sikerrel!"]);
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
    }
?>