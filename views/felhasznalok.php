<?php
    header("Content-Type: application/json");
    try{
        $kapcsolat = new PDO("mysql:host=localhost;dbname=afp_cahol", "root", "");
        $kapcsolat->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $dolg_id = $adatok['dolg_id'];
        $nev = $adatok['nev'];
        $jelszo = md5($adatok['jelszo']);
        $osztaly_id = $adatok['osztaly_id'];
        $email = $adatok['email'];
        $muvelet = $adatok['muvelet'];

        switch($muvelet) {
            case "reg":
                try {
                    $query = "SELECT * FROM felhasznalok WHERE dolg_id = ?";
                    $muvelet = $kapcsolat->prepare($query);
                    $muvelet->execute([$dolg_id]);
                    $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
                    if($eredmeny) {
                        echo json_encode(["msg" => "A megadott dolgozó azonosítóval már van az adatbázisban rekord!"]);
                        exit();
                    }
                    else {
                        $query = "INSERT INTO felhasznalok (dolg_id, nev, osztaly_id, email, jelszo) VALUES (?, ?, ?, ?, ?)";
                        $muvelet = $kapcsolat->prepare($query);
                        $muvelet->execute([$dolg_id, $nev, $osztaly_id, $email, $jelszo]);
                        $msg = 'Sikeres regisztráció!';
                        setcookie('msg', $msg, time() + 60);
                        echo json_encode(["success" => $msg]);
                        exit();
                    }
                } catch(PDOException $e) {
                    echo json_encode(["error" => $e->getMessage()]);
                    exit();
                }
                break;
            case "log":
                $query = "SELECT f.*, (SELECT o.osztaly_nev FROM osztalyok o WHERE o.osztaly_id = f.osztaly_id ) osztaly_nev 
                            FROM felhasznalok f 
                            WHERE f.dolg_id is not null AND (f.dolg_id = ? OR f.nev = ?) AND f.jelszo = ?";
                try{
                    $muvelet = $kapcsolat->prepare($query);
                    $muvelet->execute([$dolg_id, $nev, $jelszo]);
                    $eredmeny = $muvelet->fetch(PDO::FETCH_OBJ);
                    if($eredmeny) {
                        session_start();
                        if(isset($_SESSION['cahol_dolg_id'])) {
                            unset($_SESSION['cahol_dolg_id']);
                        }
                        $_SESSION['cahol_dolg_id'] = $eredmeny->dolg_id;
                        $_SESSION['cahol_nev'] = $eredmeny->nev;
                        $_SESSION['cahol_jogosultsag'] = $eredmeny->jogosultsag;
                        $_SESSION['osztaly_id'] = $eredmeny->osztaly_id;
                        $_SESSION['osztaly_nev'] = $eredmeny->osztaly_nev;
                        $_SESSION['email'] = $eredmeny->email;
                        $msg = "Sikeres bejelentkezés, kedves ( $dolg_id - $nev ) !";
                        setcookie('msg', $msg, time() + 60);
                        echo json_encode(["success" => $msg]);
                        exit();
                    } 
                    else {
                        echo json_encode(["msg" => "A megadott doglozó azonosítóval / névvel és jelszóval nem szerepel rekord az adatbázisban!"]);
                        exit();
                    }
                }
                catch(PDOException $e) {
                    echo json_encode(["error" => $e->getMessage()]);
                    exit();
                }
                break;
            default:
            echo json_encode(["error" => "Hibás művelet!"]);
                break;
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "PUT") {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $adatok = json_decode(file_get_contents("php://input"), true);
        if(!isset($adatok['nev']) || !isset($adatok['dolg_id']))
        {
            echo json_encode(['error' => "Adjon meg dolgozó azonosítót és nevet!"]);
            exit();
        }
        $dolg_id = $adatok['dolg_id'];
        $nev = $adatok['nev'];
        $osztaly_id = $adatok['osztaly_id'];
        $email = $adatok['email'];
        try{
            $query = "UPDATE felhasznalok SET nev = ?, osztaly_id = ?, email = ?";
            if(isset($adatok['jelszo'])) {
                $jelszo = $adatok['jelszo'];
                $query .= ", jelszo = ?";
            }
            $query .= " WHERE dolg_id = ?";
            $muvelet = $kapcsolat->prepare($query);
            if(isset($adatok['jelszo']))
                $muvelet->execute([$nev, $osztaly_id, $email, md5($jelszo), $dolg_id]);
            else
                $muvelet->execute([$nev, $osztaly_id, $email, $dolg_id]);

            if($muvelet->rowCount() > 0) {
                //minden mező, mely megjelenik és használjuk fent a session alapján, azoknak a módosítás miatt be kell ismét állítani a session, még ha tényleg nem is változott.
                $_SESSION['cahol_nev'] = $nev;
                $_SESSION['cahol_dolg_id'] = $dolg_id;
                $_SESSION['osztaly_id'] = $osztaly_id;
                $_SESSION['email'] = $email;
                $msg = "Sikeres felhasználó módosítás! ($dolg_id - $nev)";
                setcookie('msg', $msg, time() + 60);
                echo json_encode(["success" => $msg]);
                exit();
            }
            else {
                echo json_encode(["msg" => "Nincsen a megadott ID-vel (dolgozó azonosító) felhasználó"]);
                exit();
            }

        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
            exit();
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
        $adatok = json_decode(file_get_contents("php://input"), true);
        $dolg_id = $adatok['dolg_id'];

        try{
            $query = "DELETE FROM felhasznalok WHERE dolg_id = ? AND jogosultsag != 'admin'";
            $muvelet = $kapcsolat->prepare($query);
            $muvelet->execute([$dolg_id]);
            if($muvelet->rowCount() > 0) {
                $msg = "Sikeres törlés!";
                setcookie('msg', $msg, time() + 60);
                echo json_encode(["success" => $msg]);
                exit();
            }
            else {
                echo json_encode(["error" => "Nem törölhető ilyen ID-vel (dolgozó azonosító) felhasználó!"]);
                exit();
            }
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
            exit();
        }
    }
    else {
        echo json_encode(["error" => "Hibás kérés!"]);
        exit();
    }
?>