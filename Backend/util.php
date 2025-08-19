<?php
function read_json_body() {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function respond($data, int $code = 200) {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function required($arr, $keys) {
  foreach ($keys as $k) {
    if (!isset($arr[$k]) || $arr[$k] === '') {
      respond(['error' => "Missing field: $k"], 422);
    }
  }
}
