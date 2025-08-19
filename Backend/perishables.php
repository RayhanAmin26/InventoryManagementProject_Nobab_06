<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM perishables ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['name','storage','product_type','category','expiry_date']);
  $stmt = $pdo->prepare("INSERT INTO perishables (name,storage,product_type,category,expiry_date) VALUES (?,?,?,?,?)");
  $stmt->execute([$b['name'],$b['storage'],$b['product_type'],$b['category'],$b['expiry_date']]);
  respond(['message'=>'Perishable added']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE perishables SET name=?,storage=?,product_type=?,category=?,expiry_date=? WHERE id=?");
  $stmt->execute([$b['name'],$b['storage'],$b['product_type'],$b['category'],$b['expiry_date'],$id]);
  respond(['message'=>'Perishable updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM perishables WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
