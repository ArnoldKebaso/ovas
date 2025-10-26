<?php
/**
 * Admin Notification Logs Page
 * View and manage notification delivery logs
 */

require_once '../../initialize.php';

// Check admin authentication
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get filter parameters
$filter_type = $_GET['type'] ?? 'all';
$filter_status = $_GET['status'] ?? 'all';
$filter_date = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Build WHERE clause for filters
$whereConditions = [];
$params = [];

if ($filter_type !== 'all') {
    $whereConditions[] = "nl.type = ?";
    $params[] = $filter_type;
}

if ($filter_status !== 'all') {
    $whereConditions[] = "nl.status = ?";
    $params[] = $filter_status;
}

if (!empty($filter_date)) {
    $whereConditions[] = "DATE(nl.sent_at) = ?";
    $params[] = $filter_date;
}

if (!empty($search)) {
    $whereConditions[] = "(nl.recipient LIKE ? OR a.code LIKE ? OR u.name LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get notification logs with related data
$sql = "
    SELECT 
        nl.*,
        a.code as appointment_code,
        u.name as user_name,
        u.phone as user_phone,
        s.name as service_name
    FROM notification_logs nl
    LEFT JOIN appointments a ON nl.appointment_id = a.id
    LEFT JOIN users u ON nl.user_id = u.id
    LEFT JOIN services s ON a.service_id = s.id
    $whereClause
    ORDER BY nl.sent_at DESC
    LIMIT 500
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$statsSql = "
    SELECT 
        COUNT(*) as total_notifications,
        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN type = 'confirmation' THEN 1 ELSE 0 END) as confirmation_count,
        SUM(CASE WHEN type = 'reminder' THEN 1 ELSE 0 END) as reminder_count,
        SUM(CASE WHEN type = 'cancellation' THEN 1 ELSE 0 END) as cancellation_count,
        SUM(CASE WHEN channel LIKE '%email%' THEN 1 ELSE 0 END) as email_count,
        SUM(CASE WHEN channel LIKE '%sms%' THEN 1 ELSE 0 END) as sms_count
    FROM notification_logs nl
    WHERE DATE(nl.sent_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)
";

$statsStmt = $conn->prepare($statsSql);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>

<style>
    .notification-card {
        border-left: 4px solid #007bff;
        margin-bottom: 1rem;
    }
    .notification-card.failed {
        border-left-color: #dc3545;
    }
    .notification-card.sent {
        border-left-color: #28a745;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    }
    .stat-card.danger {
        background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
    }
    .stat-card.info {
        background: linear-gradient(135deg, #2196F3 0%, #0b7dda 100%);
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .metadata-badge {
        font-size: 0.75em;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Notification Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notification Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?php echo number_format($stats['total_notifications']); ?></h4>
                            <p class="mb-0">Total Notifications</p>
                            <small>Last 7 days</small>
                        </div>
                        <i class="fas fa-bell fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?php echo number_format($stats['sent_count']); ?></h4>
                            <p class="mb-0">Successfully Sent</p>
                            <small><?php echo $stats['total_notifications'] > 0 ? round(($stats['sent_count'] / $stats['total_notifications']) * 100, 1) : 0; ?>% success rate</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?php echo number_format($stats['failed_count']); ?></h4>
                            <p class="mb-0">Failed</p>
                            <small><?php echo $stats['total_notifications'] > 0 ? round(($stats['failed_count'] / $stats['total_notifications']) * 100, 1) : 0; ?>% failure rate</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?php echo number_format($stats['email_count']); ?></h4>
                            <p class="mb-0">Email Notifications</p>
                            <small><?php echo number_format($stats['sms_count']); ?> SMS sent</small>
                        </div>
                        <i class="fas fa-envelope fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Notifications</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" id="exportLogs">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-sm btn-success" id="testNotifications">
                        <i class="fas fa-flask"></i> Test System
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-2">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control form-control-sm">
                            <option value="all" <?php echo $filter_type === 'all' ? 'selected' : ''; ?>>All Types</option>
                            <option value="confirmation" <?php echo $filter_type === 'confirmation' ? 'selected' : ''; ?>>Confirmation</option>
                            <option value="reminder" <?php echo $filter_type === 'reminder' ? 'selected' : ''; ?>>Reminder</option>
                            <option value="cancellation" <?php echo $filter_type === 'cancellation' ? 'selected' : ''; ?>>Cancellation</option>
                            <option value="reschedule" <?php echo $filter_type === 'reschedule' ? 'selected' : ''; ?>>Reschedule</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="sent" <?php echo $filter_status === 'sent' ? 'selected' : ''; ?>>Sent</option>
                            <option value="failed" <?php echo $filter_status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?php echo $filter_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm" 
                               placeholder="Code, email, name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-sm btn-primary mr-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="?" class="btn btn-sm btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notification Logs -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Notifications</h3>
                <div class="card-tools">
                    <span class="badge badge-info"><?php echo count($logs); ?> records</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($logs)): ?>
                    <div class="row">
                        <?php foreach ($logs as $log): ?>
                            <div class="col-12">
                                <div class="card notification-card <?php echo $log['status']; ?>">
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-<?php echo $log['type'] === 'reminder' ? 'clock' : ($log['type'] === 'confirmation' ? 'check' : 'times'); ?> mr-2"></i>
                                                    <div>
                                                        <strong><?php echo ucfirst($log['type']); ?> Notification</strong>
                                                        <?php if ($log['appointment_code']): ?>
                                                            <span class="badge badge-secondary ml-2"><?php echo $log['appointment_code']; ?></span>
                                                        <?php endif; ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            To: <?php echo htmlspecialchars($log['recipient']); ?>
                                                            <?php if ($log['user_name']): ?>
                                                                (<?php echo htmlspecialchars($log['user_name']); ?>)
                                                            <?php endif; ?>
                                                            | Via: <?php echo ucfirst($log['channel']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="badge badge-<?php echo $log['status'] === 'sent' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($log['status']); ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y g:i A', strtotime($log['sent_at'])); ?>
                                                </small>
                                                <?php if (!empty($log['metadata'])): ?>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-info mt-1" onclick="showMetadata('<?php echo htmlspecialchars($log['metadata']); ?>')">
                                                        <i class="fas fa-info-circle"></i> Details
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($log['service_name']): ?>
                                            <div class="mt-1">
                                                <small class="text-info">
                                                    <i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($log['service_name']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No notification logs found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Metadata Modal -->
<div class="modal fade" id="metadataModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="metadataContent" class="bg-light p-3 rounded"></pre>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-refresh every 30 seconds
    setInterval(function() {
        if (!$('.modal').hasClass('show')) {
            location.reload();
        }
    }, 30000);

    // Export logs
    $('#exportLogs').click(function() {
        var url = 'notification_export.php?' + $('form').serialize();
        window.open(url, '_blank');
    });

    // Test notifications system
    $('#testNotifications').click(function() {
        if (confirm('This will send test notifications. Continue?')) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
            
            $.ajax({
                url: '../../api/test_notifications.php',
                method: 'POST',
                data: {
                    csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert_toast('Test notifications sent successfully!', 'success');
                    } else {
                        alert_toast('Test failed: ' + response.message, 'error');
                    }
                    $('#testNotifications').prop('disabled', false).html('<i class="fas fa-flask"></i> Test System');
                },
                error: function() {
                    alert_toast('Test failed due to server error', 'error');
                    $('#testNotifications').prop('disabled', false).html('<i class="fas fa-flask"></i> Test System');
                }
            });
        }
    });
});

function showMetadata(metadata) {
    try {
        var parsed = JSON.parse(metadata);
        $('#metadataContent').text(JSON.stringify(parsed, null, 2));
    } catch (e) {
        $('#metadataContent').text(metadata);
    }
    $('#metadataModal').modal('show');
}
</script>