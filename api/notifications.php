<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../initialize.php';
require_once __DIR__ . '/../classes/NotificationService.php';

$svc = new NotificationService();
$action = $_GET['action'] ?? ($_POST['action'] ?? 'status');

try {
  switch ($action) {
    case 'status':
      echo json_encode(['success'=>true]);
      break;
    case 'send_email':
      // generic test email
      $to = trim($_POST['to'] ?? '');
      $name = trim($_POST['name'] ?? 'Recipient');
      $sub = trim($_POST['subject'] ?? 'Test Email');
      $html= $_POST['html'] ?? '<p>Hello from OVAS</p>';
      // Add a generic send method if available; else configure PHPMailer here
      // Reuse service instance
      $ref = new ReflectionClass($svc);
      if ($ref->hasMethod('sendGeneralEmail')) {
        $ok = $svc->sendGeneralEmail($to,$name,$sub,$html);
      } else {
        // Fallback: simple PHPMailer send
        $ok = false;
        try {
          $r = new ReflectionProperty($svc, 'mailer');
          $r->setAccessible(true);
          $m = $r->getValue($svc);
          $m->clearAddresses(); $m->addAddress($to,$name); $m->Subject=$sub; $m->Body=$html; $ok = $m->send();
        } catch (Throwable $e) { $ok=false; }
      }
      echo json_encode(['success'=>$ok]);
      break;
    case 'send_sms':
      $phone = trim($_POST['phone'] ?? '');
      $msg   = trim($_POST['message'] ?? 'Test SMS from OVAS');
      $ref = new ReflectionClass($svc);
      $ok=false;
      if ($ref->hasMethod('sendGeneralSMS')) {
        $ok = $svc->sendGeneralSMS($phone,$msg);
      } else {
        // Try to call private sendSMS via reflection (for test only)
        try {
          $m = $ref->getMethod('sendSMS'); $m->setAccessible(true); $ok = $m->invoke($svc,$phone,$msg);
        } catch (Throwable $e) { $ok=false; }
      }
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

