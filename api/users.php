<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';

$pdo = db();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  switch ($action) {
    case 'list':
      $rows = $pdo->query("SELECT id,name,email,phone,address,is_admin,status,created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['success'=>true,'users'=>$rows]);
      break;
    case 'get':
      $id = (int)($_GET['id'] ?? 0);
      $st = $pdo->prepare("SELECT * FROM users WHERE id=?");
      $st->execute([$id]);
      echo json_encode(['success'=>true,'user'=>$st->fetch(PDO::FETCH_ASSOC)]);
      break;
    case 'create':
      $hash = password_hash($_POST['password'] ?? 'password', PASSWORD_BCRYPT);
      $st = $pdo->prepare("INSERT INTO users (name,email,phone,address,password_hash,is_admin,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())");
      $st->execute([
        trim($_POST['name'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['phone'] ?? ''),
        trim($_POST['address'] ?? ''),
        $hash,
        isset($_POST['is_admin'])?1:0,
        isset($_POST['status'])?1:0,
      ]);
      echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
      break;
    case 'update':
      $id = (int)($_POST['id'] ?? 0);
      $pass = $_POST['password'] ?? '';
      if ($pass !== '') {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $q = $pdo->prepare("UPDATE users SET name=?,email=?,phone=?,address=?,password_hash=?,is_admin=?,status=?,updated_at=NOW() WHERE id=?");
        $ok = $q->execute([
          trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''),
          $hash, isset($_POST['is_admin'])?1:0, isset($_POST['status'])?1:0, $id
        ]);
      } else {
        $q = $pdo->prepare("UPDATE users SET name=?,email=?,phone=?,address=?,is_admin=?,status=?,updated_at=NOW() WHERE id=?");
        $ok = $q->execute([
          trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''),
          isset($_POST['is_admin'])?1:0, isset($_POST['status'])?1:0, $id
        ]);
      }
      echo json_encode(['success'=>$ok]);
      break;
    case 'delete':
      $id = (int)($_POST['id'] ?? 0);
      $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
      echo json_encode(['success'=>true]);
      break;
    default:
      http_response_code(400);
      echo json_encode(['success'=>false,'message'=>'Unknown action']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

