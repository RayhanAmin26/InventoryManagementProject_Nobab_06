<?php
function respond($data, $status=200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function read_json_body() {
    $input = file_get_contents("php://input");
    return json_decode($input, true) ?? [];
}

function required($arr, $fields) {
    foreach ($fields as $f) {
        if (!isset($arr[$f]) || $arr[$f] === "") {
            respond(["error" => "Missing field: $f"], 400);
        }
    }
}
?>
