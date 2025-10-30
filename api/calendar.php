<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../classes/CalendarService.php';

$svc = new CalendarService();
$pdo = db();

$action = $_GET['action'] ?? ($_POST['action'] ?? 'status');

try {
  switch ($action) {
    case 'status':
      echo json_encode(['success'=>true,'configured'=>$svc->isConfigured()]);
      break;
    case 'list':
      $from = $_GET['from'] ?? date('Y-m-d').'T00:00:00+03:00';
      $to   = $_GET['to']   ?? date('Y-m-d', strtotime('+14 days')).'T23:59:59+03:00';
      echo json_encode(['success'=>true,'events'=>$svc->listEvents($from,$to,100)]);
      break;
    case 'create_manual':
      $summary = trim($_POST['summary'] ?? 'Event');
      $desc    = trim($_POST['description'] ?? '');
      $start   = $_POST['start'] ?? '';
      $end     = $_POST['end'] ?? '';
      $id = $svc->createManualEvent(['summary'=>$summary,'description'=>$desc,'start'=>$start,'end'=>$end]);
      echo json_encode(['success'=> (bool)$id, 'event_id'=>$id]);
      break;
    case 'create_from_appointment':
      $id = (int)($_POST['appointment_id'] ?? 0);
      $st = $pdo->prepare("SELECT * FROM appointments WHERE id=?"); $st->execute([$id]);
      $appt = $st->fetch(PDO::FETCH_ASSOC);
      if(!$appt) { echo json_encode(['success'=>false,'message'=>'Appointment not found']); break; }
      $eventId = $svc->createEvent($appt);
      if ($eventId) {
        $pdo->prepare("UPDATE appointments SET google_event_id=?, updated_at=NOW() WHERE id=?")->execute([$eventId,$id]);
        echo json_encode(['success'=>true,'event_id'=>$eventId]);
      } else {
        echo json_encode(['success'=>false,'message'=>'Create failed']);
      }
      break;
    case 'delete':
      $eid = $_POST['event_id'] ?? '';
      $ok = $svc->deleteEvent($eid);
      echo json_encode(['success'=>$ok]);
      break;
    default:
      http_response_code(400);
      echo json_encode(['success'=>false,'message'=>'Unknown action']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

