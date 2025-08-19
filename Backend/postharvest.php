<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$host = "localhost";
$user = "root";   // তোমার DB username বসাও
$pass = "";       // তোমার DB password বসাও
$dbname = "agri_inventory";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Helper: parse input JSON
function getInput() {
    return json_decode(file_get_contents("php://input"), true);
}

$method = $_SERVER['REQUEST_METHOD'];

// ------------------- READ (GET) -------------------
if ($method === "GET") {
    $result = $conn->query("SELECT * FROM postharvest ORDER BY id DESC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
}

// ------------------- CREATE (POST) -------------------
elseif ($method === "POST") {
    $data = getInput();
    $monitor_date = $data["monitor_date"] ?? null;
    $temperature = $data["temperature"] ?? null;
    $humidity = $data["humidity"] ?? null;
    $notes = $data["notes"] ?? null;

    $stmt = $conn->prepare("INSERT INTO postharvest (monitor_date, temperature, humidity, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $monitor_date, $temperature, $humidity, $notes);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }
}

// ------------------- UPDATE (PUT) -------------------
elseif ($method === "PUT") {
    if (!isset($_GET["id"])) {
        echo json_encode(["error" => "Missing ID"]);
        exit;
    }
    $id = intval($_GET["id"]);
    $data = getInput();

    $monitor_date = $data["monitor_date"] ?? null;
    $temperature = $data["temperature"] ?? null;
    $humidity = $data["humidity"] ?? null;
    $notes = $data["notes"] ?? null;

    $stmt = $conn->prepare("UPDATE postharvest SET monitor_date=?, temperature=?, humidity=?, notes=? WHERE id=?");
    $stmt->bind_param("ssssi", $monitor_date, $temperature, $humidity, $notes, $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }
}

// ------------------- DELETE (DELETE) -------------------
elseif ($method === "DELETE") {
    if (!isset($_GET["id"])) {
        echo json_encode(["error" => "Missing ID"]);
        exit;
    }
    $id = intval($_GET["id"]);
    $stmt = $conn->prepare("DELETE FROM postharvest WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $stmt->error]);
    }
}

// ------------------- OPTIONS -------------------
elseif ($method === "OPTIONS") {
    http_response_code(200);
}

$conn->close();
