<?php
require_once('../../config.php');
require_once('../../classes/AppointmentsModel.php');
require_once('../../classes/UsersModel.php');
require_once('../../classes/ServicesModel.php');

$appointmentsModel = new AppointmentsModel();
$usersModel = new UsersModel();
$servicesModel = new ServicesModel();

$appointment = null;
if(isset($_GET['id'])){
    $appointment = $appointmentsModel->getAppointmentById($_GET['id']);
}

// Get users and services for dropdowns
$users = $usersModel->getAllUsers();
$services = $servicesModel->getAllServices();
?>
<div class="container-fluid">
    <form action="" id="appointment-form">
        <input type="hidden" name="id" value="<?php echo $appointment ? $appointment['id'] : '' ?>">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="user_id" class="control-label">Customer</label>
                    <select name="user_id" id="user_id" class="form-control form-control-border" required>
                        <option value="">Select Customer</option>
                        <?php foreach($users as $user): ?>
                            <?php if(!$user['is_admin']): // Only show regular users ?>
                                <option value="<?php echo $user['id'] ?>" <?php echo ($appointment && $appointment['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($user['name']) ?> (<?php echo htmlspecialchars($user['email']) ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="service_id" class="control-label">Service</label>
                    <select name="service_id" id="service_id" class="form-control form-control-border" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $service): ?>
                            <?php if($service['is_active']): // Only show active services ?>
                                <option value="<?php echo $service['id'] ?>" data-fee="<?php echo $service['fee'] ?>" data-duration="<?php echo $service['duration_min'] ?>" <?php echo ($appointment && $appointment['service_id'] == $service['id']) ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($service['name']) ?> - KSh <?php echo number_format($service['fee'], 2) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="schedule_date" class="control-label">Appointment Date</label>
                    <input type="date" name="schedule_date" id="schedule_date" class="form-control form-control-border" value="<?php echo $appointment ? $appointment['schedule_date'] : '' ?>" required min="<?php echo date('Y-m-d') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_time" class="control-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control form-control-border" value="<?php echo $appointment ? $appointment['start_time'] : '' ?>" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fee" class="control-label">Fee (KSh)</label>
                    <input type="number" step="0.01" name="fee" id="fee" class="form-control form-control-border" placeholder="0.00" value="<?php echo $appointment ? $appointment['fee'] : '0.00' ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="form-control form-control-border" required>
                        <option value="pending" <?php echo ($appointment && $appointment['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?php echo ($appointment && $appointment['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                        <option value="paid" <?php echo ($appointment && $appointment['status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                        <option value="completed" <?php echo ($appointment && $appointment['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?php echo ($appointment && $appointment['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="notes" class="control-label">Notes</label>
            <textarea rows="3" name="notes" id="notes" class="form-control form-control-border" placeholder="Enter any additional notes or comments"><?php echo $appointment ? htmlspecialchars($appointment['notes']) : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        // Auto-fill fee when service is selected
        $('#service_id').change(function(){
            var selectedOption = $(this).find('option:selected');
            var fee = selectedOption.data('fee') || 0;
            $('#fee').val(fee);
        });
        
        // Trigger fee update if editing existing appointment
        if ($('#service_id').val()) {
            $('#service_id').trigger('change');
        }
        
        $('#uni_modal #appointment-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            
            // Prepare form data
            var formData = new FormData();
            formData.append('id', $('#appointment-form input[name="id"]').val());
            formData.append('user_id', $('#appointment-form select[name="user_id"]').val());
            formData.append('service_id', $('#appointment-form select[name="service_id"]').val());
            formData.append('schedule_date', $('#appointment-form input[name="schedule_date"]').val());
            formData.append('start_time', $('#appointment-form input[name="start_time"]').val());
            formData.append('fee', $('#appointment-form input[name="fee"]').val());
            formData.append('status', $('#appointment-form select[name="status"]').val());
            formData.append('notes', $('#appointment-form textarea[name="notes"]').val());

            $.ajax({
                url: '../../classes/AppointmentsModel.php',
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
                        alert_toast(resp.msg || 'Appointment saved successfully', 'success');
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