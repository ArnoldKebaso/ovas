<?php
// Get dashboard statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$total_services = $conn->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
$pending_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")->fetch_assoc()['count'];
$confirmed_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'confirmed'")->fetch_assoc()['count'];
$completed_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'")->fetch_assoc()['count'];
$total_payments = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status = 'completed'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'];
$total_messages = $conn->query("SELECT COUNT(*) as count FROM message_list WHERE status = 0")->fetch_assoc()['count'];

// Get recent appointments with better error handling
$recent_appointments_query = "
    SELECT a.*, 
           u.name as user_name, 
           s.name as service_name
    FROM appointments a
    LEFT JOIN users u ON a.user_id = u.id
    LEFT JOIN services s ON a.service_id = s.id
    ORDER BY a.created_at DESC
    LIMIT 5
";
$recent_appointments = $conn->query($recent_appointments_query);

// Monthly revenue
$current_month = date('Y-m');
$monthly_revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetch_assoc()['total'];
?>

<style>
    .stats-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    .stats-icon {
        font-size: 3rem;
        opacity: 0.8;
    }
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    .metric-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .quick-action-btn {
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="dashboard-header text-center">
    <h1 class="mb-3 font-weight-bold">
        <i class="fas fa-stethoscope mr-3"></i>
        <?php echo $_settings->info('name') ?> - Admin Dashboard
    </h1>
    <p class="mb-0 h5 font-weight-light">
        Complete veterinary clinic management at your fingertips
    </p>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-3">Quick Actions</h6>
                <a href="<?php echo base_url; ?>admin/?page=appointments" class="btn btn-primary quick-action-btn mx-2 mb-2">
                    <i class="fas fa-plus mr-2"></i>New Appointment
                </a>
                <a href="<?php echo base_url; ?>admin/?page=users" class="btn btn-success quick-action-btn mx-2 mb-2">
                    <i class="fas fa-user-plus mr-2"></i>Add Customer
                </a>
                <a href="<?php echo base_url; ?>admin/?page=services" class="btn btn-info quick-action-btn mx-2 mb-2">
                    <i class="fas fa-list-alt mr-2"></i>Manage Services
                </a>
                <a href="<?php echo base_url; ?>admin/?page=payments" class="btn btn-warning quick-action-btn mx-2 mb-2">
                    <i class="fas fa-credit-card mr-2"></i>Record Payment
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-primary text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_users); ?></h2>
                    <p class="mb-0 opacity-75">Total Customers</p>
                    <small class="opacity-50">Registered users</small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-success text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_appointments); ?></h2>
                    <p class="mb-0 opacity-75">Total Appointments</p>
                    <small class="opacity-50">All time</small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-info text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-0 font-weight-bold"><?php echo number_format($total_services); ?></h2>
                    <p class="mb-0 opacity-75">Active Services</p>
                    <small class="opacity-50">Available now</small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-th-list"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-warning text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-0 font-weight-bold">KES <?php echo number_format($total_revenue, 2); ?></h2>
                    <p class="mb-0 opacity-75">Total Revenue</p>
                    <small class="opacity-50">All time earnings</small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Overview Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card metric-card border-left-warning h-100">
            <div class="card-body text-center py-4">
                <div class="text-warning mb-2">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h3 class="text-warning font-weight-bold"><?php echo number_format($pending_appointments); ?></h3>
                <p class="text-muted mb-0">Pending Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card metric-card border-left-success h-100">
            <div class="card-body text-center py-4">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h3 class="text-success font-weight-bold"><?php echo number_format($confirmed_appointments); ?></h3>
                <p class="text-muted mb-0">Confirmed Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card metric-card border-left-info h-100">
            <div class="card-body text-center py-4">
                <div class="text-info mb-2">
                    <i class="fas fa-clipboard-check fa-2x"></i>
                </div>
                <h3 class="text-info font-weight-bold"><?php echo number_format($completed_appointments); ?></h3>
                <p class="text-muted mb-0">Completed Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card metric-card border-left-danger h-100">
            <div class="card-body text-center py-4">
                <div class="text-danger mb-2">
                    <i class="fas fa-envelope fa-2x"></i>
                </div>
                <h3 class="text-danger font-weight-bold"><?php echo number_format($total_messages); ?></h3>
                <p class="text-muted mb-0">Unread Messages</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-clock mr-3"></i>
                    Recent Appointments
                    <span class="badge badge-light ml-auto"><?php echo $recent_appointments->num_rows; ?> items</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0"><i class="fas fa-hashtag mr-2"></i>Code</th>
                                <th class="border-0"><i class="fas fa-user mr-2"></i>Client</th>
                                <th class="border-0"><i class="fas fa-concierge-bell mr-2"></i>Service</th>
                                <th class="border-0"><i class="fas fa-calendar mr-2"></i>Date</th>
                                <th class="border-0"><i class="fas fa-info-circle mr-2"></i>Status</th>
                                <th class="border-0"><i class="fas fa-dollar-sign mr-2"></i>Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($recent_appointments && $recent_appointments->num_rows > 0): ?>
                                <?php while($appointment = $recent_appointments->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary"><?php echo htmlspecialchars($appointment['code']); ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle mr-3 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <?php echo htmlspecialchars($appointment['user_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                    <td>
                                        <span class="text-muted">
                                            <?php echo date('M j, Y', strtotime($appointment['schedule_date'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_colors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $color = $status_colors[$appointment['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?php echo $color; ?> px-3 py-2">
                                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            KES <?php echo number_format($appointment['fee'], 2); ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                            <h6>No appointments found</h6>
                                            <p class="mb-0">Start by creating your first appointment</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="row align-items-center">
                    <div class="col">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing latest 5 appointments
                        </small>
                    </div>
                    <div class="col-auto">
                        <a href="<?php echo base_url; ?>admin/?page=appointments" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye mr-2"></i>View All Appointments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
