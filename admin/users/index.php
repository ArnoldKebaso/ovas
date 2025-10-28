<?php
require_once '../../classes/UsersModel.php';

$usersModel = new UsersModel();
$users = $usersModel->getAllUsers();
?>

<style>
    .status-badge {
        font-size: 0.8em;
        padding: 0.25rem 0.5rem;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #6f42c1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 10px;
    }
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8em;
        margin: 0.1rem;
    }
    .user-info {
        display: flex;
        align-items: center;
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="card-title mb-0">
                    <i class="fas fa-users mr-2"></i>
                    User Management
                </h3>
                <p class="mb-0 mt-1 opacity-75">Manage customers and admin users</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-light btn-sm" id="refreshUsers">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="uni_modal('Add New User', '<?php echo base_url ?>admin/users/manage_user.php', 'large')">
                        <i class="fas fa-plus mr-1"></i> Add User
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="border-0">#</th>
                        <th width="25%" class="border-0">User</th>
                        <th width="20%" class="border-0">Contact</th>
                        <th width="15%" class="border-0">Address</th>
                        <th width="10%" class="border-0">Type</th>
                        <th width="10%" class="border-0">Status</th>
                        <th width="10%" class="border-0">Joined</th>
                        <th width="5%" class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($users as $user): 
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold text-muted"><?php echo $i++; ?></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <strong class="d-block"><?php echo htmlspecialchars($user['name']); ?></strong>
                                    <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-envelope text-info mr-1"></i>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <?php if (!empty($user['phone'])): ?>
                            <div class="mt-1">
                                <i class="fas fa-phone text-success mr-1"></i>
                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not provided'; ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($user['is_admin'] == 1): ?>
                                <span class="badge badge-danger px-3 py-2">
                                    <i class="fas fa-user-shield mr-1"></i>Admin
                                </span>
                            <?php else: ?>
                                <span class="badge badge-primary px-3 py-2">
                                    <i class="fas fa-user mr-1"></i>Customer
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['status'] == 1): ?>
                                <span class="badge badge-success status-badge">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary status-badge">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="uni_modal('Edit User', '<?php echo base_url ?>admin/users/manage_user.php?id=<?php echo $user['id']; ?>', 'large')">
                                        <i class="fa fa-edit text-primary"></i> Edit
                                    </a>
                                    <a class="dropdown-item view_user" href="#" data-id="<?php echo $user['id']; ?>">
                                        <i class="fa fa-eye text-info"></i> View Details
                                    </a>
                                    <?php if ($user['is_admin'] == 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item toggle_status" href="#" data-id="<?php echo $user['id']; ?>" data-status="<?php echo $user['status']; ?>">
                                        <?php if ($user['status'] == 1): ?>
                                            <i class="fa fa-ban text-warning"></i> Deactivate
                                        <?php else: ?>
                                            <i class="fa fa-check text-success"></i> Activate
                                        <?php endif; ?>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_user" href="#" data-id="<?php echo $user['id']; ?>">
                                        <i class="fa fa-trash text-danger"></i> Delete
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $user['id']; ?>">
                                        <span class="fa fa-eye text-dark"></span> View
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <?php if ($user['status'] == 1): ?>
                                        <a class="dropdown-item toggle_status" href="javascript:void(0)" data-id="<?php echo $user['id']; ?>" data-status="0">
                                            <span class="fa fa-ban text-warning"></span> Disable
                                        </a>
                                    <?php else: ?>
                                        <a class="dropdown-item toggle_status" href="javascript:void(0)" data-id="<?php echo $user['id']; ?>" data-status="1">
                                            <span class="fa fa-check text-success"></span> Enable
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($user['is_admin'] == 0): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $user['id']; ?>">
                                            <span class="fa fa-trash text-danger"></span> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
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
    $('#usersTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[ 6, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [7] }
        ]
    });

    $('#refreshUsers').click(function(){
        location.reload();
    });

    $('.view_data').click(function(){
        var id = $(this).attr('data-id');
        uni_modal("User Details", "users/view_user.php?id=" + id, "mid-large");
    });

    $('.toggle_status').click(function(){
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        var statusText = status == '1' ? 'enable' : 'disable';
        
        _conf("Are you sure to " + statusText + " this user?", "toggle_user_status", [id, status]);
    });

    $('.delete_data').click(function(){
        var id = $(this).attr('data-id');
        _conf("Are you sure to delete this user permanently?", "delete_user", [id]);
    });
});

function toggle_user_status(id, status) {
    start_load();
    $.ajax({
        url: 'users/manage_user.php',
        data: {id: id, status: status},
        method: 'POST',
        dataType: 'json',
        error: function(err) {
            console.log(err);
            alert_toast("An error occurred", 'error');
            end_load();
        },
        success: function(resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                alert_toast(resp.msg, 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert_toast(resp.msg || "An error occurred", 'error');
            }
            end_load();
        }
    });
}

function delete_user(id) {
    start_load();
    $.ajax({
        url: 'users/manage_user.php',
        data: {id: id, delete: 1},
        method: 'POST',
        dataType: 'json',
        error: function(err) {
            console.log(err);
            alert_toast("An error occurred", 'error');
            end_load();
        },
        success: function(resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                alert_toast(resp.msg, 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert_toast(resp.msg || "An error occurred", 'error');
            }
            end_load();
        }
    });
}

function _conf(msg, func, params) {
    $('#confirm_modal #confirm').attr('onclick', func + "(" + params.join(',') + ")");
    $('#confirm_modal .modal-body').html(msg);
    $('#confirm_modal').modal('show');
}
</script>