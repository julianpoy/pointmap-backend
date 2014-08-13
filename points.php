<?php

 // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

   

include 'Slim/Slim.php';

$app = new Slim();

$app->get('/points', 'getPoints');
$app->get('/points/:id', 'getPoint');
$app->post('/points', 'savePoint');
$app->put('/points/:id', 'updatePoint');
$app->delete('/points/:id', 'deletePoint');

$app->run();

function getPoints() {
    $request = Slim::getInstance()->request();
    $requestjson = json_decode($request->getBody());

    //Get All Points
    $sql = "SELECT

        *

        FROM points ORDER BY id LIMIT 10000";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $response = $stmt->fetchObject();
        $db = null;
        echo json_encode($response);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        exit;
    }
}

function getPoint($id) {
    $request = Slim::getInstance()->request();
    $requestjson = json_decode($request->getBody());

    $sql = "SELECT

        *

        FROM points WHERE id=:id LIMIT 1";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $response = $stmt->fetchObject();
        $db = null;
        echo json_encode($response);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function savePoint() {
    $request = Slim::getInstance()->request();
    $requestjson = json_decode($request->getBody());

    $sql = "INSERT INTO

        points

        (name, address, city, state, zip,
            lat, lon, details, notes, db_link)

        VALUES

        (:name, :address, :city, :state, :zip,
            :lat, :lon, :details, :notes, :db_link)

        ";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $requestjson->name);
        $stmt->bindParam("address", $requestjson->address);
        $stmt->bindParam("city", $requestjson->city);
        $stmt->bindParam("state", $requestjson->state);
        $stmt->bindParam("zip", $requestjson->zip);
        $stmt->bindParam("lat", $requestjson->lat);
        $stmt->bindParam("lon", $requestjson->lon);
        $stmt->bindParam("details", $requestjson->details);
        $stmt->bindParam("notes", $requestjson->notes);
        $stmt->bindParam("db_link", $requestjson->db_link);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updatePoint($id) {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $requestjson = json_decode($body);

    $sql = "UPDATE points SET

        name=:name, address=:address,
        city=:city, state=:state,
        zip=:zip, lat=:lat, lon=:lon,
        details=:details, notes=:notes,
        db_link=:db_link

        WHERE id=:id LIMIT 1";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->bindParam("name", $requestjson->name);
        $stmt->bindParam("address", $requestjson->address);
        $stmt->bindParam("city", $requestjson->city);
        $stmt->bindParam("state", $requestjson->state);
        $stmt->bindParam("zip", $requestjson->zip);
        $stmt->bindParam("lat", $requestjson->lat);
        $stmt->bindParam("lon", $requestjson->lon);
        $stmt->bindParam("details", $requestjson->details);
        $stmt->bindParam("notes", $requestjson->notes);
        $stmt->bindParam("db_link", $requestjson->db_link);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function deletePoint($id) {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $requestjson = json_decode($body);

    $sql = "DELETE FROM points WHERE id=:id LIMIT 1";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getConnection() {
    $dbhost="kondeo.com";
    $dbuser="pointmap_admin";
    $dbpass="pointmap_admin";
    $dbname="pointmap";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);  
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

?>
