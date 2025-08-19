<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM postharvest ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['product_name','category','batch_number','expiry_date','storage_condition','location','quantity','stock_status']);
  $stmt = $pdo->prepare("INSERT INTO postharvest (product_name,category,batch_number,expiry_date,storage_condition,location,quantity,stock_status) VALUES (?,?,?,?,?,?,?,?)");
  $stmt->execute([$b['product_name'],$b['category'],$b['batch_number'],$b['expiry_date'],$b['storage_condition'],$b['location'],$b['quantity'],$b['stock_status']]);
  respond(['message'=>'Postharvest record added']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE postharvest SET product_name=?,category=?,batch_number=?,expiry_date=?,storage_condition=?,location=?,quantity=?,stock_status=? WHERE id=?");
  $stmt->execute([$b['product_name'],$b['category'],$b['batch_number'],$b['expiry_date'],$b['storage_condition'],$b['location'],$b['quantity'],$b['stock_status'],$id]);
  respond(['message'=>'Postharvest record updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM postharvest WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
