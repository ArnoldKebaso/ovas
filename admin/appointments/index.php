<?php
require_once '../../classes/AppointmentsModel.php';

$appointmentsModel = new AppointmentsModel();
$appointments = $appointmentsModel->getAllAppointmentsWithDetails();
?>

<style>
    .status-badge {
        font-size: 0.8em;
        padding: 0.25rem 0.5rem;
    }
    .appointment-info {
        line-height: 1.3;
    }
    .payment-status {
        font-size: 0.75em;
    }
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8em;
    }
</style>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Appointment Management</h3>
		<div class="card-tools">
			<div class="btn-group">
				<button type="button" class="btn btn-sm btn-info" id="refreshAppointments">
					<i class="fas fa-sync-alt"></i> Refresh
				</button>
				<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
					<i class="fas fa-filter"></i> Filter
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item filter-status" href="#" data-status="all">All Appointments</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item filter-status" href="#" data-status="pending">Pending</a>
					<a class="dropdown-item filter-status" href="#" data-status="confirmed">Confirmed</a>
					<a class="dropdown-item filter-status" href="#" data-status="paid">Paid</a>
					<a class="dropdown-item filter-status" href="#" data-status="completed">Completed</a>
					<a class="dropdown-item filter-status" href="#" data-status="cancelled">Cancelled</a>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="appointmentsTable">
				<thead>
					<tr>
						<th width="5%">#</th>
						<th width="12%">Code</th>
						<th width="20%">Client & Pet</th>
						<th width="15%">Service</th>
						<th width="12%">Schedule</th>
						<th width="10%">Fee</th>
						<th width="8%">Status</th>
						<th width="8%">Payment</th>
						<th width="10%">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($appointments)): ?>
						<?php foreach ($appointments as $index => $appointment): ?>
							<tr data-status="<?php echo strtolower($appointment['status']); ?>">
								<td class="text-center"><?php echo $index + 1; ?></td>
								<td>
									<strong><?php echo htmlspecialchars($appointment['code']); ?></strong>
									<br><small class="text-muted">
										<?php echo date('M j, Y', strtotime($appointment['created_at'])); ?>
									</small>
								</td>
								<td class="appointment-info">
									<strong><?php echo htmlspecialchars($appointment['user_name']); ?></strong>
									<br><span class="text-muted"><?php echo htmlspecialchars($appointment['user_phone']); ?></span>
									<br><small class="text-info">
										<?php echo htmlspecialchars($appointment['pet_name']); ?> 
										(<?php echo htmlspecialchars($appointment['pet_species']); ?>)
									</small>
								</td>
								<td>
									<div><?php echo htmlspecialchars($appointment['service_name']); ?></div>
									<small class="text-muted"><?php echo $appointment['duration_minutes']; ?> mins</small>
								</td>
								<td>
									<div><?php echo date('M j, Y', strtotime($appointment['schedule_date'])); ?></div>
									<small class="text-muted">
										<?php echo date('g:i A', strtotime($appointment['start_time'])); ?> - 
										<?php echo date('g:i A', strtotime($appointment['end_time'])); ?>
									</small>
								</td>
								<td class="text-right">
									<strong>KSh <?php echo number_format($appointment['total_fee'], 2); ?></strong>
								</td>
								<td class="text-center">
									<?php
									$statusColors = [
										'pending' => 'warning',
										'confirmed' => 'info',
										'paid' => 'success',
										'completed' => 'primary',
										'cancelled' => 'danger',
										'no_show' => 'secondary'
									];
									$statusColor = $statusColors[$appointment['status']] ?? 'secondary';
									?>
									<span class="badge status-badge badge-<?php echo $statusColor; ?>">
										<?php echo ucfirst($appointment['status']); ?>
									</span>
								</td>
								<td class="text-center">
									<?php
									$paymentColors = [
										'pending' => 'warning',
										'completed' => 'success',
										'failed' => 'danger',
										'refunded' => 'info'
									];
									$paymentColor = $paymentColors[$appointment['payment_status']] ?? 'secondary';
									?>
									<span class="badge payment-status badge-<?php echo $paymentColor; ?>">
										<?php echo ucfirst($appointment['payment_status']); ?>
									</span>
								</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
											<i class="fas fa-cog"></i>
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item view_appointment" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-eye text-info"></i> View Details
											</a>
											
											<?php if ($appointment['status'] === 'pending'): ?>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item confirm_appointment" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-check text-success"></i> Confirm
											</a>
											<?php endif; ?>
											
											<?php if (in_array($appointment['status'], ['confirmed', 'paid'])): ?>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item complete_appointment" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-flag-checkered text-primary"></i> Mark Complete
											</a>
											<?php endif; ?>
											
											<?php if (!in_array($appointment['status'], ['completed', 'cancelled'])): ?>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item cancel_appointment" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-times text-danger"></i> Cancel
											</a>
											<?php endif; ?>
											
											<div class="dropdown-divider"></div>
											<a class="dropdown-item send_notification" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-envelope text-warning"></i> Send Notification
											</a>
											
											<?php if ($appointment['status'] === 'cancelled'): ?>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item delete_appointment" href="#" data-id="<?php echo $appointment['id']; ?>">
												<i class="fa fa-trash text-danger"></i> Delete
											</a>
											<?php endif; ?>
										</div>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="9" class="text-center py-4">
								<div class="text-muted">
									<i class="fas fa-calendar-alt fa-2x mb-2"></i>
									<p>No appointments found.</p>
								</div>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// Initialize DataTable
	var table = $('#appointmentsTable').DataTable({
		columnDefs: [
			{ orderable: false, targets: [8] }, // Actions column not sortable
			{ searchable: false, targets: [0, 8] } // # and Actions not searchable
		],
		order: [[4, 'desc']], // Sort by schedule date (newest first)
		responsive: true,
		pageLength: 25,
		language: {
			emptyTable: "No appointments available",
			search: "Search appointments:",
			lengthMenu: "Show _MENU_ appointments per page"
		}
	});

	// Refresh appointments
	$('#refreshAppointments').click(function(){
		location.reload();
	});

	// Filter by status
	$('.filter-status').click(function(e){
		e.preventDefault();
		var status = $(this).data('status');
		
		if (status === 'all') {
			table.search('').columns().search('').draw();
		} else {
			table.column(6).search(status).draw(); // Search in status column
		}
		
		// Update active filter
		$('.filter-status').removeClass('active');
		$(this).addClass('active');
	});

	// View appointment details
	$('.view_appointment').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		uni_modal("Appointment Details", "appointments/view_details.php?id=" + appointmentId, 'large');
	});

	// Confirm appointment
	$('.confirm_appointment').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		_conf("Confirm this appointment?", "confirm_appointment", [appointmentId]);
	});

	// Complete appointment
	$('.complete_appointment').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		_conf("Mark this appointment as completed?", "complete_appointment", [appointmentId]);
	});

	// Cancel appointment
	$('.cancel_appointment').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		uni_modal("Cancel Appointment", "appointments/cancel_form.php?id=" + appointmentId, 'mid-large');
	});

	// Send notification
	$('.send_notification').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		uni_modal("Send Notification", "appointments/send_notification.php?id=" + appointmentId, 'mid-large');
	});

	// Delete appointment
	$('.delete_appointment').click(function(e){
		e.preventDefault();
		var appointmentId = $(this).data('id');
		_conf("Are you sure to delete this appointment permanently?", "delete_appointment", [appointmentId]);
	});

	// Style table cells
	$('.table td, .table th').addClass('py-1 px-2 align-middle');
});

/**
 * Confirm appointment
 */
function confirm_appointment(appointmentId) {
	start_loader();
	
	$.ajax({
		url: _base_url_ + "api/admin_appointments.php?action=confirm",
		method: "POST",
		data: {
			id: appointmentId,
			csrf_token: $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		error: function(err) {
			console.log(err);
			alert_toast("An error occurred while confirming the appointment.", 'error');
			end_loader();
		},
		success: function(resp) {
			if (resp.success) {
				alert_toast("Appointment confirmed successfully!", 'success');
				setTimeout(function() {
					location.reload();
				}, 1500);
			} else {
				alert_toast(resp.message || "Failed to confirm appointment.", 'error');
			}
			end_loader();
		}
	});
}

/**
 * Complete appointment
 */
function complete_appointment(appointmentId) {
	start_loader();
	
	$.ajax({
		url: _base_url_ + "api/admin_appointments.php?action=complete",
		method: "POST",
		data: {
			id: appointmentId,
			csrf_token: $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		error: function(err) {
			console.log(err);
			alert_toast("An error occurred while completing the appointment.", 'error');
			end_loader();
		},
		success: function(resp) {
			if (resp.success) {
				alert_toast("Appointment marked as completed!", 'success');
				setTimeout(function() {
					location.reload();
				}, 1500);
			} else {
				alert_toast(resp.message || "Failed to complete appointment.", 'error');
			}
			end_loader();
		}
	});
}

/**
 * Delete appointment
 */
function delete_appointment(appointmentId) {
	start_loader();
	
	$.ajax({
		url: _base_url_ + "api/admin_appointments.php?action=delete",
		method: "POST",
		data: {
			id: appointmentId,
			csrf_token: $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		error: function(err) {
			console.log(err);
			alert_toast("An error occurred while deleting the appointment.", 'error');
			end_loader();
		},
		success: function(resp) {
			if (resp.success) {
				alert_toast("Appointment deleted successfully!", 'success');
				setTimeout(function() {
					location.reload();
				}, 1500);
			} else {
				alert_toast(resp.message || "Failed to delete appointment.", 'error');
			}
			end_loader();
		}
	});
}
</script>