<?php
require_once('../../config.php');
require_once('../../classes/ServicesModel.php');

$servicesModel = new ServicesModel();

$service = null;
if(isset($_GET['id'])){
    $service = $servicesModel->getServiceById($_GET['id']);
}
?>
<style>
    #cimg{
        object-fit:scale-down;
        object-position:center center;
        height:200px;
        width:200px;
    }
</style>
<div class="container-fluid">
    <form action="" id="service-form">
        <input type="hidden" name="id" value="<?php echo $service ? $service['id'] : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Service Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-border" placeholder="Enter Service Name" value="<?php echo $service ? htmlspecialchars($service['name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea rows="4" name="description" id="description" class="form-control form-control-border" placeholder="Enter service description here."><?php echo $service ? htmlspecialchars($service['description']) : '' ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fee" class="control-label">Fee (KSh)</label>
                    <input type="number" step="0.01" name="fee" id="fee" class="form-control form-control-border" placeholder="0.00" value="<?php echo $service ? $service['fee'] : '0.00' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="duration_min" class="control-label">Duration (Minutes)</label>
                    <input type="number" name="duration_min" id="duration_min" class="form-control form-control-border" placeholder="30" value="<?php echo $service ? $service['duration_min'] : '30' ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?php echo ($service && $service['is_active']) || !$service ? 'checked' : '' ?>>
                <label for="is_active" class="form-check-label">Active Service</label>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal #service-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            
            // Prepare form data
            var formData = new FormData();
            formData.append('id', $('#service-form input[name="id"]').val());
            formData.append('name', $('#service-form input[name="name"]').val());
            formData.append('description', $('#service-form textarea[name="description"]').val());
            formData.append('fee', $('#service-form input[name="fee"]').val());
            formData.append('duration_min', $('#service-form input[name="duration_min"]').val());
            formData.append('is_active', $('#service-form input[name="is_active"]').is(':checked') ? 1 : 0);

            $.ajax({
                url: '../../classes/ServicesModel.php',
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
                        alert_toast(resp.msg || 'Service saved successfully', 'success');
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
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>