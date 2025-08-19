<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  // সব inventory record বের করবে
  $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  // নতুন inventory record insert
  $b = read_json_body();
  required($b, ['item_name']);
  $stmt = $pdo->prepare("INSERT INTO inventory (item_name, category, quantity, location, status) VALUES (?,?,?,?,?)");
  $stmt->execute([
    $b['item_name'],
    $b['category'] ?? null,
    $b['quantity'] ?? null,
    $b['location'] ?? null,
    $b['status'] ?? null
  ]);
  respond(['message' => 'Inventory record created']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE inventory SET item_name=?, category=?, quantity=?, location=?, status=? WHERE id=?");
  $stmt->execute([
    $b['item_name'] ?? null,
    $b['category'] ?? null,
    $b['quantity'] ?? null,
    $b['location'] ?? null,
    $b['status'] ?? null,
    $id
  ]);
  respond(['message'=>'Inventory record updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM inventory WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
