<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  // সব perishable products বের করবে
  $stmt = $pdo->query("SELECT * FROM perishables ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  // নতুন perishable insert
  $b = read_json_body();
  required($b, ['product_name']);
  $stmt = $pdo->prepare("INSERT INTO perishables (product_name, expiry_date, storage_temp, quantity, status) VALUES (?,?,?,?,?)");
  $stmt->execute([
    $b['product_name'],
    $b['expiry_date'] ?? null,
    $b['storage_temp'] ?? null,
    $b['quantity'] ?? null,
    $b['status'] ?? null
  ]);
  respond(['message' => 'Perishable product created']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE perishables SET product_name=?, expiry_date=?, storage_temp=?, quantity=?, status=? WHERE id=?");
  $stmt->execute([
    $b['product_name'] ?? null,
    $b['expiry_date'] ?? null,
    $b['storage_temp'] ?? null,
    $b['quantity'] ?? null,
    $b['status'] ?? null,
    $id
  ]);
  respond(['message'=>'Perishable product updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM perishables WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
