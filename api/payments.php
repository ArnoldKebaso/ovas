<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../classes/PaymentsModel.php';

$payments = new PaymentsModel();
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  switch ($action) {
    case 'list':
      // Basic list (success + initiated + failed)
      $pdo = db();
      $rows = $pdo->query("SELECT p.id,p.user_id,p.appointment_id,p.amount,p.payment_method,p.status,p.transaction_ref,p.created_at,p.updated_at, u.name as user_name FROM payments p LEFT JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode(['success'=>true,'payments'=>$rows]);
      break;
    case 'get':
      $id = (int)($_GET['id'] ?? 0);
      echo json_encode(['success'=>true,'payment'=>$payments->find($id)]);
      break;
    case 'create':
      echo json_encode($payments->createPayment([
        'appointment_id' => (int)($_POST['appointment_id'] ?? 0),
        'amount' => (float)($_POST['amount'] ?? 0),
        'payment_method' => $_POST['payment_method'] ?? 'mpesa',
        'status' => $_POST['status'] ?? 'initiated',
        'transaction_ref' => $_POST['transaction_ref'] ?? '',
        'notes' => $_POST['notes'] ?? ''
      ]));
      break;
    case 'update':
      $id = (int)($_POST['id'] ?? 0);
      echo json_encode($payments->updatePayment($id,[
        'appointment_id' => (int)($_POST['appointment_id'] ?? 0),
        'amount' => (float)($_POST['amount'] ?? 0),
        'payment_method' => $_POST['payment_method'] ?? 'mpesa',
        'status' => $_POST['status'] ?? 'initiated',
        'transaction_ref' => $_POST['transaction_ref'] ?? '',
        'notes' => $_POST['notes'] ?? ''
      ]));
      break;
    case 'delete':
      $id = (int)($_POST['id'] ?? 0);
      echo json_encode($payments->deletePayment($id));
      break;
    default:
      http_response_code(400);
      echo json_encode(['success'=>false,'message'=>'Unknown action']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

