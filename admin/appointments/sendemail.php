<?php
// admin/sendemail.php
declare(strict_types=1);

/**
 * Lightweight email sender. If PHPMailer is available (files in your project),
 * we’ll use it. Otherwise we fallback to PHP mail().
 */
function sendAppointmentEmail(array $row, string $action = 'confirmed'): bool
{
  $to   = $row['email'] ?? '';
  if (!$to) return false;

  $subject = 'Your Appointment ' . ucfirst($action);
  $body    = sprintf(
    "Hello %s,\n\nYour appointment (%s) for %s on %s %s has been %s.\n\nRegards,\nVAP",
    $row['client_name'] ?? ($row['name'] ?? 'Client'),
    $row['code'] ?? ('APPT-' . ($row['id'] ?? '')),
    $row['service_name'] ?? ($row['service'] ?? 'service'),
    substr($row['appt_date'] ?? ($row['appointment_date'] ?? ''), 0, 10),
    $row['time_slot'] ?? '',
    $action
  );

  // Try PHPMailer if present
  $phpMailerAvailable = is_file(__DIR__ . '/PHPMailer.php') && is_file(__DIR__ . '/SMTP.php');
  if ($phpMailerAvailable) {
    require_once __DIR__ . '/PHPMailer.php';
    require_once __DIR__ . '/SMTP.php';
    try {
      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      // Configure as needed for your environment
      $mail->isSMTP();
      $mail->Host       = 'smtp.gmail.com';
      $mail->Port       = 587;
      $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
      $mail->SMTPAuth   = true;
      // TODO: put your SMTP creds here
      $mail->Username   = 'no-reply@example.com';
      $mail->Password   = 'password';

      $mail->setFrom('no-reply@example.com', 'VAP');
      $mail->addAddress($to);
      $mail->Subject = $subject;
      $mail->Body    = $body;
      $mail->send();
      return true;
    } catch (\Throwable $e) {
      // fall back to mail()
    }
  }

  // Fallback
  return @mail($to, $subject, $body);
}
