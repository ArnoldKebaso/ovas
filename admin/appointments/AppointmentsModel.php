<?php
// admin/AppointmentsModel.php
declare(strict_types=1);

// Ensure DB constants and db() helper are available
require_once __DIR__ . '/../../initialize.php';

final class AppointmentsModel
{
  private \PDO $pdo;
  private string $table = 'appointments';
  private array $cols = [];

  public function __construct()
  {
    // Get PDO via global db() helper defined in initialize.php
    $this->pdo = db();
    $this->cols = $this->describe();
  }

  /** Describe table columns to build INSERT/UPDATE safely (works across slight schema differences) */
  private function describe(): array
  {
    $stmt = $this->pdo->query("DESCRIBE {$this->table}");
    $cols = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $cols[$row['Field']] = true;
    }
    return $cols;
  }

  /** Map incoming data to real columns (only keep allowed keys) */
  private function filter(array $data): array
  {
    $allowed = array_intersect_key($data, $this->cols);
    // defaults if missing
    if (!isset($allowed['status']) && isset($this->cols['status'])) $allowed['status'] = 'Pending';
    return $allowed;
  }

  /** Generate APPT code if column exists */
  private function nextCode(): string
  {
    try {
      $stmt = $this->pdo->query("SELECT MAX(id) as max_id FROM {$this->table}");
      $id = (int)($stmt->fetch(PDO::FETCH_ASSOC)['max_id'] ?? 0) + 1;
      return 'APPT-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
    } catch (\Throwable $e) {
      return 'APPT-' . strtoupper(bin2hex(random_bytes(2)));
    }
  }

  public function all(): array
  {
    // Flexible SELECT that tolerates different column names
    $sql = "
      SELECT
        a.id,
        IFNULL(a.code, CONCAT('APPT-', LPAD(a.id,4,'0')))      AS code,
        IFNULL(a.client_name, a.name)                         AS client,
        IFNULL(a.pet_name,  '')                               AS pet,
        IFNULL(a.service_name, (SELECT s.name FROM services s WHERE s.id = a.service_id)) AS service,
        IFNULL(a.appt_date, a.appointment_date)               AS appt_date,
        IFNULL(a.time_slot, a.slot)                           AS time_slot,
        IFNULL(a.status, 'Pending')                           AS status,
        IFNULL(a.email, a.client_email)                       AS email,
        IFNULL(a.phone, a.client_phone)                       AS phone,
        IFNULL(a.notes, a.remarks)                            AS notes,
        a.created_at
      FROM {$this->table} a
      ORDER BY IFNULL(a.appt_date, a.appointment_date) DESC, a.id DESC
    ";
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public function find(int $id): ?array
  {
    $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public function create(array $data): int
  {
    $data = $this->filter($data);

    // Ensure code exists if column present
    if (isset($this->cols['code']) && empty($data['code'])) {
      $data['code'] = $this->nextCode();
    }

    $fields = array_keys($data);
    $place  = array_map(fn($k) => ':' . $k, $fields);
    $sql    = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->table, implode(',', $fields), implode(',', $place));
    $stmt   = $this->pdo->prepare($sql);
    $stmt->execute($data);
    return (int)$this->pdo->lastInsertId();
  }

  public function update(int $id, array $data): bool
  {
    $data = $this->filter($data);
    if (!$data) return false;

    $sets = [];
    foreach ($data as $k => $v) $sets[] = "$k = :$k";
    $data['id'] = $id;

    $sql = sprintf("UPDATE %s SET %s WHERE id = :id", $this->table, implode(', ', $sets));
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($data);
  }

  public function delete(int $id): bool
  {
    $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
    return $stmt->execute([':id' => $id]);
  }

  public function setStatus(int $id, string $status): bool
  {
    $allowed = ['Pending','Confirmed','Completed','Cancelled'];
    if (!in_array($status, $allowed, true)) $status = 'Pending';
    if (!isset($this->cols['status'])) return false;
    $stmt = $this->pdo->prepare("UPDATE {$this->table} SET status = :s WHERE id = :id");
    return $stmt->execute([':s' => $status, ':id' => $id]);
  }
}
