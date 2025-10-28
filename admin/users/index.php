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
        object-fit: cover;
    }
    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8em;
        margin: 0.1rem;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-info" id="refreshUsers">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped" id="usersTable">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Name</th>
                        <th width="20%">Email</th>
                        <th width="15%">Phone</th>
                        <th width="10%">Type</th>
                        <th width="10%">Status</th>
                        <th width="15%">Joined</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($users as $user): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($user['is_admin'] == 1): ?>
                                <span class="badge badge-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-info">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['status'] == 1): ?>
                                <span class="badge badge-success status-badge">Active</span>
                            <?php else: ?>
                                <span class="badge badge-secondary status-badge">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm action-btn dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
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