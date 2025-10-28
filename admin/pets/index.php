<?php
require_once '../../classes/PetsModel.php';

$petsModel = new PetsModel();
$pets = $petsModel->getAllPets();
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
    .pet-info {
        line-height: 1.3;
    }
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8em;
    }
</style>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">Pets Management</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-sm btn-primary">
				<span class="fas fa-plus"></span> Register New Pet
			</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="petsTable">
				<thead>
					<tr>
						<th width="5%">#</th>
						<th width="15%">Pet Name</th>
						<th width="15%">Owner</th>
						<th width="10%">Species</th>
						<th width="10%">Breed</th>
						<th width="8%">Age</th>
						<th width="8%">Weight</th>
						<th width="10%">Status</th>
						<th width="19%">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					<?php foreach($pets as $pet): ?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td>
								<div class="pet-info">
									<strong><?php echo htmlspecialchars($pet['name']) ?></strong><br>
									<small class="text-muted">
										<?php echo htmlspecialchars($pet['color']) ?> <?php echo htmlspecialchars($pet['gender']) ?>
									</small>
								</div>
							</td>
							<td>
								<div>
									<strong><?php echo htmlspecialchars($pet['owner_name']) ?></strong><br>
									<small class="text-muted"><?php echo htmlspecialchars($pet['owner_email']) ?></small>
								</div>
							</td>
							<td><?php echo htmlspecialchars($pet['species']) ?></td>
							<td><?php echo htmlspecialchars($pet['breed']) ?></td>
							<td>
								<?php 
								$age = '';
								if ($pet['age_years'] > 0) {
									$age .= $pet['age_years'] . 'y ';
								}
								if ($pet['age_months'] > 0) {
									$age .= $pet['age_months'] . 'm';
								}
								echo $age ?: 'N/A';
								?>
							</td>
							<td><?php echo $pet['weight_kg'] ? $pet['weight_kg'] . ' kg' : 'N/A' ?></td>
							<td class="text-center">
								<?php if($pet['is_active']): ?>
									<span class="badge badge-success status-badge">Active</span>
								<?php else: ?>
									<span class="badge badge-secondary status-badge">Inactive</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $pet['id'] ?>">
										<span class="fa fa-edit text-primary"></span> Edit
									</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item toggle_status" href="javascript:void(0)" data-id="<?php echo $pet['id'] ?>">
										<span class="fa fa-power-off text-warning"></span> 
										<?php echo $pet['is_active'] ? 'Deactivate' : 'Activate' ?>
									</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $pet['id'] ?>">
										<span class="fa fa-trash text-danger"></span> Delete
									</a>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		// Initialize DataTable
		$('#petsTable').dataTable({
			"responsive": true,
			"autoWidth": false,
			"order": [[ 0, "desc" ]]
		});
		
		// Create new pet
		$('#create_new').click(function(){
			uni_modal("Register New Pet","pets/manage_pet.php","mid-large");
		});
		
		// Edit pet
		$('.edit_data').click(function(){
			uni_modal("Update Pet Information","pets/manage_pet.php?id="+$(this).attr('data-id'),"mid-large");
		});
		
		// Toggle status
		$('.toggle_status').click(function(){
			_conf("Are you sure you want to change this pet's status?","toggle_pet_status",[$(this).attr('data-id')]);
		});
		
		// Delete pet
		$('.delete_data').click(function(){
			_conf("Are you sure you want to delete this pet record permanently?","delete_pet",[$(this).attr('data-id')]);
		});
		
		// Reload modal
		$('#uni_modal').on('hide.bs.modal',function(){
			location.reload();
		});
	});
	
	function toggle_pet_status($id){
		start_loader();
		$.ajax({
			url: _base_url_+"classes/PetsModel.php",
			method: "POST",
			data: {action: 'toggle_status', id: $id},
			dataType: "json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp){
				if(typeof resp == 'object' && resp.status == 'success'){
					location.reload();
				} else {
					alert_toast("An error occurred.", 'error');
					end_loader();
				}
			}
		});
	}
	
	function delete_pet($id){
		start_loader();
		$.ajax({
			url: _base_url_+"classes/PetsModel.php",
			method: "POST",
			data: {action: 'delete', id: $id},
			dataType: "json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp){
				if(typeof resp == 'object' && resp.status == 'success'){
					location.reload();
				} else {
					alert_toast("An error occurred.", 'error');
					end_loader();
				}
			}
		});
	}
</script>