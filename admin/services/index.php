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
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .fee-badge {
        font-size: 1rem;
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }
</style>

<div class="card service-card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="card-title mb-0">
                    <i class="fas fa-th-list mr-2"></i>
                    Services Management
                </h3>
                <p class="mb-0 mt-1 opacity-75">Manage veterinary services and pricing</p>
            </div>
            <div class="col-auto">
                <button type="button" id="create_new" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add New Service
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="servicesTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="border-0">#</th>
                        <th width="25%" class="border-0">Service</th>
                        <th width="30%" class="border-0">Description</th>
                        <th width="10%" class="border-0">Duration</th>
                        <th width="15%" class="border-0">Fee</th>
                        <th width="10%" class="border-0">Status</th>
                        <th width="5%" class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
					<?php if (!empty($services)): ?>
						<?php foreach ($services as $index => $service): ?>
							<tr>
								<td class="text-center font-weight-bold text-muted"><?php echo $index + 1; ?></td>
								<td>
									<div class="d-flex align-items-center">
										<div class="service-icon bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
											<i class="fas fa-stethoscope"></i>
										</div>
										<div>
											<strong class="d-block"><?php echo htmlspecialchars($service['name']); ?></strong>
											<?php if (!empty($service['category'])): ?>
												<small class="text-muted">
													<i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($service['category']); ?>
												</small>
											<?php endif; ?>
										</div>
									</div>
								</td>
								<td>
									<div class="service-description" title="<?php echo htmlspecialchars($service['description']); ?>">
										<small class="text-muted"><?php echo htmlspecialchars($service['description']); ?></small>
									</div>
								</td>
								<td>
									<span class="badge badge-info px-3 py-2">
										<i class="fas fa-clock mr-1"></i><?php echo $service['duration_minutes']; ?> mins
									</span>
								</td>
								<td>
									<span class="fee-badge badge badge-success">
										KSh <?php echo number_format($service['fee'], 2); ?>
									</span>
								</td>
								<td>
									<span class="badge status-badge <?php echo $service['is_active'] ? 'badge-success' : 'badge-secondary'; ?> px-3 py-2">
										<i class="fas fa-<?php echo $service['is_active'] ? 'check-circle' : 'times-circle'; ?> mr-1"></i>
										<?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
									</span>
								</td>
								<td class="text-center">
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
											<i class="fas fa-cog"></i>
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $service['id']; ?>">
												<i class="fa fa-eye text-info"></i> View Details
											</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $service['id']; ?>">
												<i class="fa fa-edit text-primary"></i> Edit Service
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