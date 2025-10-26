<?php
require_once 'initialize.php';
require_once 'classes/AppointmentsModel.php';
require_once 'classes/PetsModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$appointmentsModel = new AppointmentsModel();
$petsModel = new PetsModel();

// Get user's appointments
$appointments = $appointmentsModel->getUserAppointments($userId);
$upcomingAppointments = array_filter($appointments, function($apt) {
    return in_array($apt['status'], ['confirmed', 'paid']) && 
           strtotime($apt['schedule_date'] . ' ' . $apt['start_time']) > time();
});

$pastAppointments = array_filter($appointments, function($apt) {
    return in_array($apt['status'], ['completed']) || 
           (strtotime($apt['schedule_date'] . ' ' . $apt['start_time']) < time() && $apt['status'] !== 'cancelled');
});

// Get user's pets
$pets = $petsModel->getUserPets($userId);

// Get appointment statistics
$stats = [
    'total_appointments' => count($appointments),
    'upcoming_appointments' => count($upcomingAppointments),
    'completed_appointments' => count(array_filter($appointments, function($apt) { return $apt['status'] === 'completed'; })),
    'total_pets' => count($pets)
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - OVAS</title>
    
    <!-- Bootstrap CSS -->
    <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- AdminLTE -->
    <link href="libs/style.css" rel="stylesheet">
    
    <style>
        .dashboard-card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #2196F3 0%, #0b7dda 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        }
        .appointment-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .pet-card {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .pet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .quick-actions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .action-btn {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-paw"></i> OVAS Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointment.php">
                            <i class="fas fa-calendar-plus"></i> Book Appointment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_pets.php">
                            <i class="fas fa-dog"></i> My Pets
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Header -->
    <div class="welcome-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="mb-0">Manage your pet appointments and keep track of your furry friends' health.</p>
                </div>
                <div class="col-md-4 text-right">
                    <i class="fas fa-heart fa-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $stats['total_appointments']; ?></h3>
                            <p class="mb-0">Total Appointments</p>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $stats['upcoming_appointments']; ?></h3>
                            <p class="mb-0">Upcoming</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $stats['completed_appointments']; ?></h3>
                            <p class="mb-0">Completed</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $stats['total_pets']; ?></h3>
                            <p class="mb-0">Registered Pets</p>
                        </div>
                        <i class="fas fa-paw fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-3">
                <div class="dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body quick-actions">
                        <a href="appointment.php" class="btn btn-primary action-btn">
                            <i class="fas fa-calendar-plus"></i> Book New Appointment
                        </a>
                        <a href="my_pets.php?action=add" class="btn btn-success action-btn">
                            <i class="fas fa-plus"></i> Add New Pet
                        </a>
                        <a href="my_appointments.php" class="btn btn-info action-btn">
                            <i class="fas fa-list"></i> View All Appointments
                        </a>
                        <a href="profile.php" class="btn btn-secondary action-btn">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                    </div>
                </div>

                <!-- Recent Pets -->
                <div class="dashboard-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-paw"></i> My Pets</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pets)): ?>
                            <?php foreach (array_slice($pets, 0, 3) as $pet): ?>
                                <div class="pet-card">
                                    <div class="d-flex align-items-center">
                                        <div class="pet-avatar mr-3">
                                            <i class="fas fa-<?php echo strtolower($pet['species']) === 'cat' ? 'cat' : 'dog'; ?> fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($pet['name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($pet['species']); ?>
                                                <?php if ($pet['breed']): ?>
                                                    • <?php echo htmlspecialchars($pet['breed']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($pets) > 3): ?>
                                <div class="text-center">
                                    <a href="my_pets.php" class="btn btn-sm btn-outline-primary">
                                        View All <?php echo count($pets); ?> Pets
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-paw fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No pets registered yet</p>
                                <a href="my_pets.php?action=add" class="btn btn-sm btn-primary">
                                    Add Your First Pet
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Upcoming Appointments</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcomingAppointments)): ?>
                            <?php foreach (array_slice($upcomingAppointments, 0, 5) as $appointment): ?>
                                <div class="card appointment-card">
                                    <div class="card-body py-3">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($appointment['service_name']); ?>
                                                    <span class="badge badge-primary ml-2"><?php echo $appointment['code']; ?></span>
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-paw text-primary"></i>
                                                    <?php echo htmlspecialchars($appointment['pet_name']); ?>
                                                    (<?php echo htmlspecialchars($appointment['pet_species']); ?>)
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo date('l, F j, Y', strtotime($appointment['schedule_date'])); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div class="mb-2">
                                                    <strong><?php echo date('g:i A', strtotime($appointment['start_time'])); ?></strong>
                                                    <br>
                                                    <span class="badge badge-<?php echo $appointment['status'] === 'paid' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($appointment['status']); ?>
                                                    </span>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewAppointment(<?php echo $appointment['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if (strtotime($appointment['schedule_date'] . ' ' . $appointment['start_time']) > (time() + 24*3600)): ?>
                                                        <button class="btn btn-outline-warning btn-sm" onclick="rescheduleAppointment(<?php echo $appointment['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($upcomingAppointments) > 5): ?>
                                <div class="text-center">
                                    <a href="my_appointments.php" class="btn btn-outline-primary">
                                        View All Upcoming Appointments
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No upcoming appointments</h5>
                                <p class="text-muted">Book an appointment to keep your pets healthy and happy!</p>
                                <a href="appointment.php" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus"></i> Book Appointment
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="col-lg-3">
                <div class="dashboard-card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pastAppointments)): ?>
                            <?php foreach (array_slice($pastAppointments, 0, 5) as $appointment): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold">
                                            <?php echo htmlspecialchars($appointment['service_name']); ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($appointment['pet_name']); ?>
                                            • <?php echo date('M j', strtotime($appointment['schedule_date'])); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No recent activity</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- OVAS API Client -->
    <script src="assets/js/ovas-api.js"></script>
    
    <script>
    class DashboardManager {
        constructor() {
            this.api = new OvasAPI();
            this.ui = new OvasUI(this.api);
            this.init();
        }

        init() {
            this.bindEvents();
            this.setupAutoRefresh();
        }

        bindEvents() {
            // Add event listeners for dynamic actions
            document.addEventListener('click', (e) => {
                if (e.target.matches('[data-action="view-appointment"]')) {
                    this.viewAppointment(e.target.dataset.appointmentId);
                }
                
                if (e.target.matches('[data-action="reschedule-appointment"]')) {
                    this.rescheduleAppointment(e.target.dataset.appointmentId);
                }
                
                if (e.target.matches('[data-action="cancel-appointment"]')) {
                    this.cancelAppointment(e.target.dataset.appointmentId);
                }
                
                if (e.target.matches('[data-action="refresh-stats"]')) {
                    this.refreshDashboardStats();
                }
            });
        }

        async viewAppointment(appointmentId) {
            try {
                // Load appointment details and show in modal
                const appointments = await this.api.getUserAppointments();
                const appointment = appointments.find(apt => apt.id == appointmentId);
                
                if (appointment) {
                    this.showAppointmentModal(appointment);
                } else {
                    this.ui.showToast('Appointment not found', 'error');
                }
            } catch (error) {
                console.error('Error viewing appointment:', error);
                this.ui.showToast('Failed to load appointment details', 'error');
            }
        }

        async rescheduleAppointment(appointmentId) {
            // For now, redirect to reschedule page
            // In future, could implement inline reschedule modal
            window.location.href = `reschedule.php?id=${appointmentId}`;
        }

        async cancelAppointment(appointmentId) {
            if (!confirm('Are you sure you want to cancel this appointment?')) {
                return;
            }

            try {
                const result = await this.api.cancelAppointment(appointmentId, 'Cancelled by user');
                
                if (result.success) {
                    this.ui.showToast('Appointment cancelled successfully', 'success');
                    this.refreshAppointments();
                } else {
                    this.ui.showToast(result.message || 'Failed to cancel appointment', 'error');
                }
            } catch (error) {
                console.error('Error cancelling appointment:', error);
                this.ui.showToast('Failed to cancel appointment', 'error');
            }
        }

        async refreshDashboardStats() {
            try {
                const stats = await this.api.getDashboardStats();
                
                if (stats.success) {
                    this.updateStatsDisplay(stats.data);
                    this.ui.showToast('Dashboard refreshed', 'success');
                }
            } catch (error) {
                console.error('Error refreshing stats:', error);
                this.ui.showToast('Failed to refresh dashboard', 'error');
            }
        }

        updateStatsDisplay(stats) {
            // Update statistics cards with new data
            const statElements = {
                'total_appointments': document.querySelector('[data-stat="total_appointments"]'),
                'upcoming_appointments': document.querySelector('[data-stat="upcoming_appointments"]'),
                'completed_appointments': document.querySelector('[data-stat="completed_appointments"]'),
                'total_pets': document.querySelector('[data-stat="total_pets"]')
            };

            Object.keys(statElements).forEach(key => {
                if (statElements[key] && stats[key] !== undefined) {
                    statElements[key].textContent = stats[key];
                }
            });
        }

        async refreshAppointments() {
            try {
                const appointments = await this.api.getUserAppointments();
                // Update appointments display (would need to restructure HTML for dynamic updates)
                location.reload(); // For now, just reload the page
            } catch (error) {
                console.error('Error refreshing appointments:', error);
            }
        }

        showAppointmentModal(appointment) {
            // Create and show appointment details modal
            const modalHtml = `
                <div class="modal fade" id="appointmentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Appointment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <tr><th>Appointment Code:</th><td>${appointment.code}</td></tr>
                                    <tr><th>Service:</th><td>${appointment.service_name}</td></tr>
                                    <tr><th>Pet:</th><td>${appointment.pet_name}</td></tr>
                                    <tr><th>Date:</th><td>${this.ui.formatDate(appointment.schedule_date)}</td></tr>
                                    <tr><th>Time:</th><td>${this.ui.formatTime(appointment.start_time)}</td></tr>
                                    <tr><th>Status:</th><td><span class="badge badge-${appointment.status === 'confirmed' ? 'success' : 'warning'}">${appointment.status}</span></td></tr>
                                    <tr><th>Fee:</th><td>${this.ui.formatCurrency(appointment.fee)}</td></tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('appointmentModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to DOM and show
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            modal.show();
        }

        setupAutoRefresh() {
            // Refresh dashboard data every 5 minutes
            setInterval(() => {
                this.refreshDashboardStats();
            }, 300000);
        }
    }

    // Initialize dashboard when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        window.dashboardManager = new DashboardManager();
    });

    // Legacy functions for backward compatibility
    function viewAppointment(appointmentId) {
        if (window.dashboardManager) {
            window.dashboardManager.viewAppointment(appointmentId);
        }
    }
    
    function rescheduleAppointment(appointmentId) {
        if (window.dashboardManager) {
            window.dashboardManager.rescheduleAppointment(appointmentId);
        }
    }
    </script>
</body>
</html>