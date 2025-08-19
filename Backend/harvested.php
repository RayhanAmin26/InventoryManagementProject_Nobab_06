<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM harvested_crops ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['temperature','humidity','category','harvest_date','crop_name','quantity','storage','processing_unit']);
  $stmt = $pdo->prepare("INSERT INTO harvested_crops (temperature, humidity, category, harvest_date, crop_name, quantity, storage, processing_unit) VALUES (?,?,?,?,?,?,?,?)");
  $stmt->execute([$b['temperature'],$b['humidity'],$b['category'],$b['harvest_date'],$b['crop_name'],$b['quantity'],$b['storage'],$b['processing_unit']]);
  respond(['message' => 'Harvested crop added']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE harvested_crops SET temperature=?, humidity=?, category=?, harvest_date=?, crop_name=?, quantity=?, storage=?, processing_unit=? WHERE id=?");
  $stmt->execute([$b['temperature'],$b['humidity'],$b['category'],$b['harvest_date'],$b['crop_name'],$b['quantity'],$b['storage'],$b['processing_unit'],$id]);
  respond(['message'=>'Harvested crop updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM harvested_crops WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
