<?php
require_once('../../config.php');
require_once('../../classes/UsersModel.php');

$usersModel = new UsersModel();

$user = null;
if(isset($_GET['id'])){
    $user = $usersModel->getUserById($_GET['id']);
}
?>
<div class="container-fluid">
    <form action="" id="user-form">
        <input type="hidden" name="id" value="<?php echo $user ? $user['id'] : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-border" placeholder="Enter Full Name" value="<?php echo $user ? htmlspecialchars($user['name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="control-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control form-control-border" placeholder="Enter Email Address" value="<?php echo $user ? htmlspecialchars($user['email']) : '' ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone" class="control-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control form-control-border" placeholder="Enter Phone Number" value="<?php echo $user ? htmlspecialchars($user['phone']) : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password" class="control-label"><?php echo $user ? 'New Password (leave blank to keep current)' : 'Password' ?></label>
                    <input type="password" name="password" id="password" class="form-control form-control-border" placeholder="Enter Password" <?php echo !$user ? 'required' : '' ?>>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="control-label">Address</label>
            <textarea rows="3" name="address" id="address" class="form-control form-control-border" placeholder="Enter Address"><?php echo $user ? htmlspecialchars($user['address']) : '' ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_admin" id="is_admin" class="form-check-input" value="1" <?php echo ($user && $user['is_admin']) ? 'checked' : '' ?>>
                        <label for="is_admin" class="form-check-label">Administrator</label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="status" id="status" class="form-check-input" value="1" <?php echo ($user && $user['status']) || !$user ? 'checked' : '' ?>>
                        <label for="status" class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal #user-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            
            // Prepare form data
            var formData = new FormData();
            formData.append('id', $('#user-form input[name="id"]').val());
            formData.append('name', $('#user-form input[name="name"]').val());
            formData.append('email', $('#user-form input[name="email"]').val());
            formData.append('phone', $('#user-form input[name="phone"]').val());
            formData.append('address', $('#user-form textarea[name="address"]').val());
            formData.append('password', $('#user-form input[name="password"]').val());
            formData.append('is_admin', $('#user-form input[name="is_admin"]').is(':checked') ? 1 : 0);
            formData.append('status', $('#user-form input[name="status"]').is(':checked') ? 1 : 0);

            $.ajax({
                url: '../../classes/UsersModel.php',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:function(err){
                    console.log(err)
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        alert_toast(resp.msg || 'User saved successfully', 'success');
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                        el.show('slow')
                    }
                    end_loader();
                }
            })
        })
    })
</script>