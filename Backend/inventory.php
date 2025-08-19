<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['product_name','category','stock_level','usage_rate','quantity','procurement_schedule']);
  $stmt = $pdo->prepare("INSERT INTO inventory (product_name,category,stock_level,usage_rate,quantity,procurement_schedule) VALUES (?,?,?,?,?,?)");
  $stmt->execute([$b['product_name'],$b['category'],$b['stock_level'],$b['usage_rate'],$b['quantity'],$b['procurement_schedule']]);
  respond(['message'=>'Inventory record added']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE inventory SET product_name=?,category=?,stock_level=?,usage_rate=?,quantity=?,procurement_schedule=? WHERE id=?");
  $stmt->execute([$b['product_name'],$b['category'],$b['stock_level'],$b['usage_rate'],$b['quantity'],$b['procurement_schedule'],$id]);
  respond(['message'=>'Inventory record updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM inventory WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
