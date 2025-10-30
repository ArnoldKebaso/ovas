<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../classes/MpesaService.php';

$svc = new MpesaService();
$action = $_GET['action'] ?? ($_POST['action'] ?? 'status');

try {
  switch ($action) {
    case 'status':
      echo json_encode(['success'=>true]);
      break;
    case 'stk_push':
      $phone = trim($_POST['phone'] ?? '');
      $amount= (float)($_POST['amount'] ?? 0);
      $ref   = trim($_POST['account_ref'] ?? 'OVAS');
      $desc  = trim($_POST['desc'] ?? 'Booking Fee');
      $res = $svc->stkPush($phone,$amount,$ref,$desc);
      echo json_encode($res);
      break;
    default:
      http_response_code(400);
      echo json_encode(['success'=>false,'message'=>'Unknown action']);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

