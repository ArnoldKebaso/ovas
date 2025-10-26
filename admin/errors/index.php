<?php
require_once '../initialize.php';
require_once '../inc/sess_auth.php';
require_once '../classes/ErrorLogger.php';

// Ensure admin is logged in
if (!is_admin_logged_in()) {
    redirect('../login.php');
}

$page_title = "Error Logs Dashboard";
$logger = new ErrorLogger();

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_recent_errors':
            $level = $_GET['level'] ?? null;
            $limit = intval($_GET['limit'] ?? 50);
            echo json_encode($logger->getRecentErrors($limit, $level));
            exit;
            
        case 'get_error_stats':
            $days = intval($_GET['days'] ?? 7);
            echo json_encode($logger->getErrorStats($days));
            exit;
            
        case 'clear_old_logs':
            $days = intval($_POST['days'] ?? 30);
            $cleared = $logger->clearOldLogs($days);
            echo json_encode(['success' => true, 'cleared' => $cleared]);
            exit;
    }
}

// Get data for dashboard
$recent_errors = $logger->getRecentErrors(20);
$error_stats = $logger->getErrorStats(7);

// Count errors by level
$error_counts = [
    'emergency' => 0,
    'critical' => 0,
    'error' => 0,
    'warning' => 0,
    'info' => 0
];

foreach ($error_stats as $stat) {
    $level = strtolower($stat['level']);
    if (isset($error_counts[$level])) {
        $error_counts[$level] += $stat['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?> | OVAS Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <style>
        .error-level-emergency { background-color: #dc3545; color: white; }
        .error-level-critical { background-color: #fd7e14; color: white; }
        .error-level-error { background-color: #ffc107; color: black; }
        .error-level-warning { background-color: #0dcaf0; color: black; }
        .error-level-info { background-color: #198754; color: white; }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .error-details {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .error-context {
            font-family: 'Courier New', monospace;
            font-size: 0.8em;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include_once('../inc/topBarNav.php') ?>
        
        <!-- Sidebar -->
        <?php include_once('../inc/navigation.php') ?>
        
        <!-- Main content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php echo $page_title; ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Dashboard</a></li>
                                <li class="breadcrumb-item active">Error Logs</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3><?php echo $error_counts['emergency'] + $error_counts['critical']; ?></h3>
                                    <p>Critical Issues</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h3><?php echo $error_counts['error']; ?></h3>
                                    <p>Errors</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3><?php echo $error_counts['warning']; ?></h3>
                                    <p>Warnings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3><?php echo $error_counts['info']; ?></h3>
                                    <p>Info</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Log Management</h5>
                                    <button class="btn btn-warning btn-sm" id="clearOldLogs">
                                        <i class="fas fa-trash"></i> Clear Old Logs
                                    </button>
                                    <button class="btn btn-primary btn-sm" id="refreshData">
                                        <i class="fas fa-sync"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filter Options</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="levelFilter">Error Level:</label>
                                    <select id="levelFilter" class="form-control">
                                        <option value="">All Levels</option>
                                        <option value="emergency">Emergency</option>
                                        <option value="critical">Critical</option>
                                        <option value="error">Error</option>
                                        <option value="warning">Warning</option>
                                        <option value="info">Info</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="limitFilter">Records Limit:</label>
                                    <select id="limitFilter" class="form-control">
                                        <option value="20">20</option>
                                        <option value="50" selected>50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label><br>
                                    <button id="applyFilters" class="btn btn-primary">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Logs Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Error Logs</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="errorLogsTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Level</th>
                                            <th>Message</th>
                                            <th>File</th>
                                            <th>User</th>
                                            <th>IP</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="errorTableBody">
                                        <!-- Data loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </section>
        </div>
    </div>

    <!-- Error Details Modal -->
    <div class="modal fade" id="errorDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Error Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="errorDetailsContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            let dataTable;
            
            // Initialize DataTable
            function initDataTable() {
                if (dataTable) {
                    dataTable.destroy();
                }
                
                dataTable = $('#errorLogsTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 25,
                    responsive: true,
                    columnDefs: [
                        { width: "15%", targets: 0 },
                        { width: "10%", targets: 1 },
                        { width: "30%", targets: 2 },
                        { width: "20%", targets: 3 },
                        { width: "10%", targets: 4 },
                        { width: "10%", targets: 5 },
                        { width: "5%", targets: 6 }
                    ]
                });
            }
            
            // Load error data
            function loadErrorData() {
                const level = $('#levelFilter').val();
                const limit = $('#limitFilter').val();
                
                $.ajax({
                    url: '?action=get_recent_errors',
                    data: { level: level, limit: limit },
                    success: function(data) {
                        let tbody = '';
                        
                        data.forEach(function(error) {
                            const levelClass = `error-level-${error.level.toLowerCase()}`;
                            const truncatedMessage = error.message.length > 50 ? 
                                error.message.substring(0, 50) + '...' : error.message;
                            const fileLine = error.file ? `${error.file}:${error.line}` : 'N/A';
                            
                            tbody += `
                                <tr>
                                    <td>${error.created_at}</td>
                                    <td><span class="badge ${levelClass}">${error.level}</span></td>
                                    <td class="error-details" title="${error.message}">${truncatedMessage}</td>
                                    <td>${fileLine}</td>
                                    <td>${error.user_id || 'Guest'}</td>
                                    <td>${error.ip_address}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-error='${JSON.stringify(error).replace(/'/g, "&apos;")}'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#errorTableBody').html(tbody);
                        initDataTable();
                    },
                    error: function() {
                        alert('Failed to load error data');
                    }
                });
            }
            
            // Apply filters
            $('#applyFilters').click(function() {
                loadErrorData();
            });
            
            // Refresh data
            $('#refreshData').click(function() {
                location.reload();
            });
            
            // Clear old logs
            $('#clearOldLogs').click(function() {
                if (confirm('Clear logs older than 30 days?')) {
                    $.post('?action=clear_old_logs', { days: 30 }, function(response) {
                        if (response.success) {
                            alert(`Cleared ${response.cleared} old log entries`);
                            loadErrorData();
                        }
                    });
                }
            });
            
            // View error details
            $(document).on('click', '.view-details', function() {
                const errorData = JSON.parse($(this).attr('data-error').replace(/&apos;/g, "'"));
                
                let contextHtml = '';
                if (errorData.context) {
                    try {
                        const context = JSON.parse(errorData.context);
                        contextHtml = `<pre class="error-context">${JSON.stringify(context, null, 2)}</pre>`;
                    } catch (e) {
                        contextHtml = `<div class="error-context">${errorData.context}</div>`;
                    }
                }
                
                const detailsHtml = `
                    <table class="table table-striped">
                        <tr><th>Level:</th><td><span class="badge error-level-${errorData.level.toLowerCase()}">${errorData.level}</span></td></tr>
                        <tr><th>Message:</th><td>${errorData.message}</td></tr>
                        <tr><th>File:</th><td>${errorData.file || 'N/A'}</td></tr>
                        <tr><th>Line:</th><td>${errorData.line || 'N/A'}</td></tr>
                        <tr><th>User ID:</th><td>${errorData.user_id || 'Guest'}</td></tr>
                        <tr><th>IP Address:</th><td>${errorData.ip_address}</td></tr>
                        <tr><th>User Agent:</th><td style="word-break: break-all;">${errorData.user_agent || 'N/A'}</td></tr>
                        <tr><th>Request URI:</th><td>${errorData.request_uri || 'N/A'}</td></tr>
                        <tr><th>Timestamp:</th><td>${errorData.created_at}</td></tr>
                    </table>
                    ${contextHtml ? '<h6>Context:</h6>' + contextHtml : ''}
                `;
                
                $('#errorDetailsContent').html(detailsHtml);
                $('#errorDetailsModal').modal('show');
            });
            
            // Initial load
            loadErrorData();
        });
    </script>
</body>
</html>