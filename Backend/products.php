<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/config.php';
require __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
  respond($stmt->fetchAll());
}

if ($method === 'POST') {
  $b = read_json_body();
  required($b, ['name','category','seed_type','sowing_date','harvest_date','storage','shelf_life','packaging']);
  $stmt = $pdo->prepare("INSERT INTO products (name, category, seed_type, sowing_date, harvest_date, storage, shelf_life, packaging) VALUES (?,?,?,?,?,?,?,?)");
  $stmt->execute([$b['name'],$b['category'],$b['seed_type'],$b['sowing_date'],$b['harvest_date'],$b['storage'],$b['shelf_life'],$b['packaging']]);
  respond(['message' => 'Product created']);
}

if ($method === 'PUT') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $b = read_json_body();
  $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, seed_type=?, sowing_date=?, harvest_date=?, storage=?, shelf_life=?, packaging=? WHERE id=?");
  $stmt->execute([$b['name'],$b['category'],$b['seed_type'],$b['sowing_date'],$b['harvest_date'],$b['storage'],$b['shelf_life'],$b['packaging'],$id]);
  respond(['message'=>'Product updated']);
}

if ($method === 'DELETE') {
  if(!$id) respond(['error'=>'Missing id'],400);
  $stmt=$pdo->prepare("DELETE FROM products WHERE id=?");
  $stmt->execute([$id]);
  respond(['message'=>'Deleted']);
}
