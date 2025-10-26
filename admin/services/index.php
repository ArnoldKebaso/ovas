<?php
require_once '../../classes/ServicesModel.php';

$servicesModel = new ServicesModel();
$services = $servicesModel->getAllServices();
?>

<style>
    .img-thumb-path{
        width:100px;
        height:80px;
        object-fit:scale-down;
        object-position:center center;
    }
    .status-badge {
        font-size: 0.8em;
        padding: 0.25rem 0.5rem;
    }
    .service-description {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">List of Services</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-sm btn-primary">
				<span class="fas fa-plus"></span> Add New Service
			</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped" id="servicesTable">
				<thead>
					<tr>
						<th width="5%">#</th>
						<th width="20%">Service Name</th>
						<th width="25%">Description</th>
						<th width="15%">Duration</th>
						<th width="10%">Fee (KSh)</th>
						<th width="10%">Status</th>
						<th width="15%">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($services)): ?>
						<?php foreach ($services as $index => $service): ?>
							<tr>
								<td class="text-center"><?php echo $index + 1; ?></td>
								<td>
									<strong><?php echo htmlspecialchars($service['name']); ?></strong>
									<?php if (!empty($service['category'])): ?>
										<br><small class="text-muted"><?php echo htmlspecialchars($service['category']); ?></small>
									<?php endif; ?>
								</td>
								<td>
									<div class="service-description" title="<?php echo htmlspecialchars($service['description']); ?>">
										<?php echo htmlspecialchars($service['description']); ?>
									</div>
								</td>
								<td>
									<?php echo $service['duration_minutes']; ?> minutes
								</td>
								<td class="text-right">
									<?php echo number_format($service['fee'], 2); ?>
								</td>
								<td>
									<span class="badge status-badge <?php echo $service['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
										<?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
									</span>
								</td>
								<td align="center">
									<div class="btn-group">
										<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
											Action
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<div class="dropdown-menu" role="menu">
											<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $service['id']; ?>">
												<span class="fa fa-eye text-dark"></span> View
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $service['id']; ?>">
												<span class="fa fa-edit text-primary"></span> Edit
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item toggle_status" href="javascript:void(0)" 
											   data-id="<?php echo $service['id']; ?>" 
											   data-status="<?php echo $service['is_active'] ? '0' : '1'; ?>">
												<span class="fa fa-<?php echo $service['is_active'] ? 'eye-slash' : 'eye'; ?> text-warning"></span>
												<?php echo $service['is_active'] ? 'Deactivate' : 'Activate'; ?>
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $service['id']; ?>">
												<span class="fa fa-trash text-danger"></span> Delete
											</a>
										</div>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="7" class="text-center py-4">
								<div class="text-muted">
									<i class="fas fa-info-circle fa-2x mb-2"></i>
									<p>No services found. <a href="javascript:void(0)" id="create_first">Create your first service</a></p>
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
	$('#servicesTable').DataTable({
		columnDefs: [
			{ orderable: false, targets: [6] }, // Actions column not sortable
			{ searchable: false, targets: [0, 6] } // # and Actions not searchable
		],
		order: [[1, 'asc']], // Sort by service name by default
		responsive: true,
		pageLength: 25,
		language: {
			emptyTable: "No services available",
			search: "Search services:",
			lengthMenu: "Show _MENU_ services per page"
		}
	});

	// Create new service
	$('#create_new, #create_first').click(function(){
		uni_modal("Add New Service", "services/manage_service.php", 'mid-large');
	});

	// Edit service
	$('.edit_data').click(function(){
		uni_modal("Update Service Details", "services/manage_service.php?id=" + $(this).attr('data-id'), 'mid-large');
	});

	// View service
	$('.view_data').click(function(){
		uni_modal("Service Details", "services/view_service.php?id=" + $(this).attr('data-id'), 'mid-large');
	});

	// Toggle service status
	$('.toggle_status').click(function(){
		var serviceId = $(this).attr('data-id');
		var newStatus = $(this).attr('data-status');
		var actionText = newStatus == '1' ? 'activate' : 'deactivate';
		
		_conf("Are you sure you want to " + actionText + " this service?", "toggle_service_status", [serviceId, newStatus]);
	});

	// Delete service
	$('.delete_data').click(function(){
		_conf("Are you sure to delete this service permanently?", "delete_service", [$(this).attr('data-id')]);
	});

	// Style table cells
	$('.table td, .table th').addClass('py-1 px-2 align-middle');
});

/**
 * Delete service function
 */
function delete_service(serviceId) {
	start_loader();
	
	$.ajax({
		url: _base_url_ + "api/services.php?action=delete",
		method: "POST",
		data: {
			id: serviceId,
			csrf_token: $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		error: function(err) {
			console.log(err);
			alert_toast("An error occurred while deleting the service.", 'error');
			end_loader();
		},
		success: function(resp) {
			if (resp.success) {
				alert_toast("Service deleted successfully!", 'success');
				setTimeout(function() {
					location.reload();
				}, 1500);
			} else {
				alert_toast(resp.message || "Failed to delete service.", 'error');
			}
			end_loader();
		}
	});
}

/**
 * Toggle service status
 */
function toggle_service_status(serviceId, newStatus) {
	start_loader();
	
	$.ajax({
		url: _base_url_ + "api/services.php?action=toggle_status",
		method: "POST",
		data: {
			id: serviceId,
			is_active: newStatus,
			csrf_token: $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		error: function(err) {
			console.log(err);
			alert_toast("An error occurred while updating service status.", 'error');
			end_loader();
		},
		success: function(resp) {
			if (resp.success) {
				var statusText = newStatus == '1' ? 'activated' : 'deactivated';
				alert_toast("Service " + statusText + " successfully!", 'success');
				setTimeout(function() {
					location.reload();
				}, 1500);
			} else {
				alert_toast(resp.message || "Failed to update service status.", 'error');
			}
			end_loader();
		}
	});
}
</script>