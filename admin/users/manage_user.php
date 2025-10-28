<?php
require_once('../../config.php');
require_once('../../classes/UsersModel.php');

$usersModel = new UsersModel();

$user = null;
if(isset($_GET['id'])){
    $user = $usersModel->getUserById($_GET['id']);
}
?>

<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #007bff;
    }
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-<?php echo $user ? 'edit' : 'plus' ?> mr-2"></i>
                        <?php echo $user ? 'Edit User' : 'Add New User' ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form action="" id="user-form">
                        <input type="hidden" name="id" value="<?php echo $user ? $user['id'] : '' ?>">
                        
                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user-circle mr-2 text-primary"></i>
                                Personal Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label font-weight-bold">
                                            <i class="fas fa-user mr-1"></i>Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" value="<?php echo $user ? htmlspecialchars($user['name']) : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="control-label font-weight-bold">
                                            <i class="fas fa-envelope mr-1"></i>Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" value="<?php echo $user ? htmlspecialchars($user['email']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="control-label font-weight-bold">
                                            <i class="fas fa-phone mr-1"></i>Phone Number
                                        </label>
                                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number" value="<?php echo $user ? htmlspecialchars($user['phone']) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="control-label font-weight-bold">
                                            <i class="fas fa-lock mr-1"></i>
                                            <?php echo $user ? 'New Password' : 'Password' ?>
                                            <?php if(!$user): ?><span class="text-danger">*</span><?php endif; ?>
                                        </label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $user ? 'Leave blank to keep current password' : 'Enter password' ?>" <?php echo !$user ? 'required' : '' ?>>
                                        <?php if($user): ?>
                                        <small class="text-muted">Leave blank to keep current password</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="control-label font-weight-bold">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Address
                                </label>
                                <textarea rows="3" name="address" id="address" class="form-control" placeholder="Enter address"><?php echo $user ? htmlspecialchars($user['address']) : '' ?></textarea>
                            </div>
                        </div>

                        <!-- Account Settings Section -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-cogs mr-2 text-success"></i>
                                Account Settings
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_admin" id="is_admin" class="custom-control-input" value="1" <?php echo ($user && $user['is_admin']) ? 'checked' : '' ?>>
                                            <label for="is_admin" class="custom-control-label font-weight-bold">
                                                <i class="fas fa-user-shield mr-1"></i>Administrator Privileges
                                            </label>
                                        </div>
                                        <small class="text-muted">Give this user admin access to the system</small>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="status" id="status" class="custom-control-input" value="1" <?php echo (!$user || $user['status']) ? 'checked' : '' ?>>
                                            <label for="status" class="custom-control-label font-weight-bold">
                                                <i class="fas fa-toggle-on mr-1"></i>Account Active
                                            </label>
                                        </div>
                                        <small class="text-muted">Enable/disable user account</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                <?php echo $user ? 'Update User' : 'Create User' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#user-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        
        $.ajax({
            url: _base_url_ + "classes/UsersModel.php?f=save_user",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    alert_toast(resp.msg, "success");
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (resp.status == 'failed' && !!resp.msg) {
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").scrollTop(0);
                } else {
                    alert_toast("An error occurred", 'error');
                }
                end_loader();
            }
        });
    });
});
</script>