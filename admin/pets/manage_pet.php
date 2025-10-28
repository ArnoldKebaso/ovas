<?php
require_once('../../config.php');
require_once('../../classes/PetsModel.php');
require_once('../../classes/UsersModel.php');

$petsModel = new PetsModel();
$usersModel = new UsersModel();

$pet = null;
if(isset($_GET['id'])){
    $pet = $petsModel->getPetById($_GET['id']);
}

// Get users for dropdown (only non-admin users)
$users = array_filter($usersModel->getAllUsers(), function($user) {
    return !$user['is_admin'];
});
?>
<div class="container-fluid">
    <form action="" id="pet-form">
        <input type="hidden" name="id" value="<?php echo $pet ? $pet['id'] : '' ?>">
        
        <div class="form-group">
            <label for="user_id" class="control-label">Pet Owner</label>
            <select name="user_id" id="user_id" class="form-control form-control-border" required>
                <option value="">Select Pet Owner</option>
                <?php foreach($users as $user): ?>
                    <option value="<?php echo $user['id'] ?>" <?php echo ($pet && $pet['user_id'] == $user['id']) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($user['name']) ?> (<?php echo htmlspecialchars($user['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="control-label">Pet Name</label>
                    <input type="text" name="name" id="name" class="form-control form-control-border" placeholder="Enter Pet Name" value="<?php echo $pet ? htmlspecialchars($pet['name']) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="species" class="control-label">Species</label>
                    <select name="species" id="species" class="form-control form-control-border" required>
                        <option value="">Select Species</option>
                        <option value="Dog" <?php echo ($pet && $pet['species'] == 'Dog') ? 'selected' : '' ?>>Dog</option>
                        <option value="Cat" <?php echo ($pet && $pet['species'] == 'Cat') ? 'selected' : '' ?>>Cat</option>
                        <option value="Bird" <?php echo ($pet && $pet['species'] == 'Bird') ? 'selected' : '' ?>>Bird</option>
                        <option value="Rabbit" <?php echo ($pet && $pet['species'] == 'Rabbit') ? 'selected' : '' ?>>Rabbit</option>
                        <option value="Fish" <?php echo ($pet && $pet['species'] == 'Fish') ? 'selected' : '' ?>>Fish</option>
                        <option value="Reptile" <?php echo ($pet && $pet['species'] == 'Reptile') ? 'selected' : '' ?>>Reptile</option>
                        <option value="Other" <?php echo ($pet && $pet['species'] == 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="breed" class="control-label">Breed</label>
                    <input type="text" name="breed" id="breed" class="form-control form-control-border" placeholder="Enter Breed" value="<?php echo $pet ? htmlspecialchars($pet['breed']) : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="gender" class="control-label">Gender</label>
                    <select name="gender" id="gender" class="form-control form-control-border">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($pet && $pet['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?php echo ($pet && $pet['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                        <option value="Unknown" <?php echo ($pet && $pet['gender'] == 'Unknown') ? 'selected' : '' ?>>Unknown</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="age_years" class="control-label">Age (Years)</label>
                    <input type="number" name="age_years" id="age_years" class="form-control form-control-border" placeholder="0" value="<?php echo $pet ? $pet['age_years'] : '0' ?>" min="0" max="30">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="age_months" class="control-label">Age (Months)</label>
                    <input type="number" name="age_months" id="age_months" class="form-control form-control-border" placeholder="0" value="<?php echo $pet ? $pet['age_months'] : '0' ?>" min="0" max="11">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="weight_kg" class="control-label">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" id="weight_kg" class="form-control form-control-border" placeholder="0.0" value="<?php echo $pet ? $pet['weight_kg'] : '0.0' ?>" min="0">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="color" class="control-label">Color/Markings</label>
            <input type="text" name="color" id="color" class="form-control form-control-border" placeholder="Enter Color or Markings" value="<?php echo $pet ? htmlspecialchars($pet['color']) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="medical_notes" class="control-label">Medical Notes</label>
            <textarea rows="3" name="medical_notes" id="medical_notes" class="form-control form-control-border" placeholder="Enter any medical notes, allergies, or special conditions"><?php echo $pet ? htmlspecialchars($pet['medical_notes']) : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?php echo ($pet && $pet['is_active']) || !$pet ? 'checked' : '' ?>>
                <label for="is_active" class="form-check-label">Active Pet Record</label>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal #pet-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            
            // Prepare form data
            var formData = new FormData();
            formData.append('id', $('#pet-form input[name="id"]').val());
            formData.append('user_id', $('#pet-form select[name="user_id"]').val());
            formData.append('name', $('#pet-form input[name="name"]').val());
            formData.append('species', $('#pet-form select[name="species"]').val());
            formData.append('breed', $('#pet-form input[name="breed"]').val());
            formData.append('gender', $('#pet-form select[name="gender"]').val());
            formData.append('age_years', $('#pet-form input[name="age_years"]').val());
            formData.append('age_months', $('#pet-form input[name="age_months"]').val());
            formData.append('weight_kg', $('#pet-form input[name="weight_kg"]').val());
            formData.append('color', $('#pet-form input[name="color"]').val());
            formData.append('medical_notes', $('#pet-form textarea[name="medical_notes"]').val());
            formData.append('is_active', $('#pet-form input[name="is_active"]').is(':checked') ? 1 : 0);

            $.ajax({
                url: '../../classes/PetsModel.php',
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
                        alert_toast(resp.msg || 'Pet saved successfully', 'success');
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