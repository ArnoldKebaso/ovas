<?php
// Get dashboard statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$total_services = $conn->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
$pending_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")->fetch_assoc()['count'];
$confirmed_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'confirmed'")->fetch_assoc()['count'];
$total_payments = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status = 'success'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'success'")->fetch_assoc()['total'];
$total_messages = $conn->query("SELECT COUNT(*) as count FROM message_list WHERE status = 0")->fetch_assoc()['count'];

// Get recent appointments
$recent_appointments = $conn->query("
    SELECT a.*, u.name as user_name, s.name as service_name, p.name as pet_name
    FROM appointments a
    LEFT JOIN users u ON a.user_id = u.id
    LEFT JOIN services s ON a.service_id = s.id
    LEFT JOIN pets p ON a.pet_id = p.id
    ORDER BY a.created_at DESC
    LIMIT 5
");
?>

<style>
    .stats-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    .stats-icon {
        font-size: 2.5rem;
    }
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
</style>

<div class="dashboard-header">
    <h1 class="mb-2">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Welcome to <?php echo $_settings->info('name') ?> - Admin Panel
    </h1>
    <p class="mb-0 opacity-75">
        Manage your veterinary clinic with ease. Monitor appointments, services, and revenue all in one place.
    </p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-primary text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon mr-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo number_format($total_users); ?></h4>
                    <small>Total Users</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-success text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon mr-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo number_format($total_appointments); ?></h4>
                    <small>Total Appointments</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-info text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon mr-3">
                    <i class="fas fa-th-list"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo number_format($total_services); ?></h4>
                    <small>Active Services</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card bg-gradient-warning text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon mr-3">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h4 class="mb-0">KES <?php echo number_format($total_revenue, 2); ?></h4>
                    <small>Total Revenue</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Overview -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h2 class="text-warning"><?php echo number_format($pending_appointments); ?></h2>
                <p class="mb-0">Pending Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h2 class="text-success"><?php echo number_format($confirmed_appointments); ?></h2>
                <p class="mb-0">Confirmed Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h2 class="text-danger"><?php echo number_format($total_messages); ?></h2>
                <p class="mb-0">Unread Messages</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock mr-2"></i>
                    Recent Appointments
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Code</th>
                                <th>Client</th>
                                <th>Pet</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($recent_appointments->num_rows > 0): ?>
                                <?php while($appointment = $recent_appointments->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($appointment['code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($appointment['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['pet_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($appointment['schedule_date'])); ?></td>
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
                                        <span class="badge badge-<?php echo $color; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td>KES <?php echo number_format($appointment['total_fee'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                                        <p class="text-muted">No appointments found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="<?php echo base_url; ?>admin/?page=appointments" class="btn btn-primary">
                    <i class="fas fa-eye mr-2"></i>View All Appointments
                </a>
            </div>
        </div>
    </div>
</div>
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-calendar-day"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Confirmed Request</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `appointment_list` where `status` = 1 ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-3">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-gradient-danger elevation-1"><i class="fas fa-calendar-day"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Cancelled Request</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `appointment_list` where `status` = 2 ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<hr>
<div class="card card-outline card-primary rounded-0 shadow">
    <div class="card-header rounded-0">
            <h4 class="card-title">Appointment Requests</h4>
    </div>
    <div class="card-body">
        <div id="appointmentCalendar"></div>
    </div>
</div>
<script>
    var calendar;
    var appointment = $.parseJSON('<?= json_encode($appoinment_arr) ?>') || {};
    start_loader();
    $(function(){
        var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()
        var Calendar = FullCalendar.Calendar;

        calendar = new Calendar(document.getElementById('appointmentCalendar'), {
            headerToolbar: {
                left  : false,
                center: 'title',
            },
            selectable: true,
            themeSystem: 'bootstrap',
            //Random default events
            events: [
                {
                    daysOfWeek: [0,1,2,3,4,5,6], // these recurrent events move separately
                    title:0,
                    allDay: true,
                    }
            ],
            validRange:{
                start: moment(date).format("YYYY-MM-DD"),
            },
            eventDidMount:function(info){
                // console.log(appointment)
                if(!!appointment[info.event.startStr]){
                    var available = parseInt(info.event.title) + parseInt(appointment[info.event.startStr]);
                     $(info.el).find('.fc-event-title.fc-sticky').text(available)
                }
                end_loader()
            },
            editable  : true
        });

    calendar.render();
    })
</script>