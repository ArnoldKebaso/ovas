<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';

$pdo = db();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  switch ($action) {
    case 'list':
      $rows = $pdo->query("SELECT id,start_time,end_time,duration_min,max_appointments,is_active FROM time_slots ORDER BY start_time")->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['success'=>true,'data'=>$rows]);
      break;
    case 'get':
      $id = (int)($_GET['id'] ?? 0);
      $st = $pdo->prepare("SELECT * FROM time_slots WHERE id=?");
      $st->execute([$id]);
      echo json_encode(['success'=>true,'data'=>$st->fetch(PDO::FETCH_ASSOC)]);
      break;
    case 'create':
      $st = $pdo->prepare("INSERT INTO time_slots (start_time,end_time,duration_min,max_appointments,is_active) VALUES (?,?,?,?,?)");
      $st->execute([
        $_POST['start_time'] ?? '08:00:00',
        $_POST['end_time'] ?? '08:30:00',
        (int)($_POST['duration_min'] ?? 30),
        (int)($_POST['max_appointments'] ?? 1),
        isset($_POST['is_active']) ? 1 : 0,
      ]);
      echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
      break;
    case 'update':
      $id = (int)($_POST['id'] ?? 0);
      $st = $pdo->prepare("UPDATE time_slots SET start_time=?,end_time=?,duration_min=?,max_appointments=?,is_active=?,updated_at=CURRENT_TIMESTAMP WHERE id=?");
      $ok = $st->execute([
        $_POST['start_time'] ?? '08:00:00',
        $_POST['end_time'] ?? '08:30:00',
        (int)($_POST['duration_min'] ?? 30),
        (int)($_POST['max_appointments'] ?? 1),
        isset($_POST['is_active']) ? 1 : 0,
        $id
      ]);
      echo json_encode(['success'=>$ok]);
      break;
    case 'delete':
      $id = (int)($_POST['id'] ?? 0);
      $pdo->prepare("DELETE FROM time_slots WHERE id=?")->execute([$id]);
      echo json_encode(['success'=>true]);
      break;
    case 'toggle':
      $id = (int)($_POST['id'] ?? 0);
      $pdo->prepare("UPDATE time_slots SET is_active = NOT is_active, updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
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

