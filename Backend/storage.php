<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$servername = "localhost";
$username   = "root";   // DB username
$password   = "";       // DB password
$dbname     = "agri_inventory"; // Database নাম

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

// GET (Read)
if ($method == "GET") {
    $sql = "SELECT * FROM storage_conditions ORDER BY id DESC";
    $result = $conn->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
}

// POST (Create)
elseif ($method == "POST") {
    $warehouse_id = $data["warehouse_id"] ?? '';
    $location     = $data["location"] ?? '';
    $temperature  = $data["temperature"] ?? '';
    $humidity     = $data["humidity"] ?? '';

    $sql = "INSERT INTO storage_conditions (warehouse_id, location, temperature, humidity)
            VALUES ('$warehouse_id', '$location', '$temperature', '$humidity')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

// PUT (Update)
elseif ($method == "PUT") {
    parse_str($_SERVER['QUERY_STRING'], $params);
    $id = $params["id"] ?? 0;

    $warehouse_id = $data["warehouse_id"] ?? '';
    $location     = $data["location"] ?? '';
    $temperature  = $data["temperature"] ?? '';
    $humidity     = $data["humidity"] ?? '';

    $sql = "UPDATE storage_conditions 
            SET warehouse_id='$warehouse_id', location='$location', temperature='$temperature', humidity='$humidity'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

// DELETE (Remove)
elseif ($method == "DELETE") {
    parse_str($_SERVER['QUERY_STRING'], $params);
    $id = $params["id"] ?? 0;

    $sql = "DELETE FROM storage_conditions WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }
}

$conn->close();
?>
