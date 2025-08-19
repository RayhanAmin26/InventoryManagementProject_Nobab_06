<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM storage_conditions ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['warehouse_id','location','temperature','humidity']);
  $stmt = $pdo->prepare("INSERT INTO storage_conditions (condition_name, temperature, humidity, description) VALUES (?,?,?,?)");
  $stmt->execute([$b['warehouse_id'], $b['temperature'], $b['humidity'], $b['location']]);
  respond(['message' => 'Storage record created']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE storage_conditions SET condition_name=?, temperature=?, humidity=?, description=? WHERE id=?");
  $stmt->execute([$b['warehouse_id'],$b['temperature'],$b['humidity'],$b['location'],$id]);
  respond(['message'=>'Storage record updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM storage_conditions WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
