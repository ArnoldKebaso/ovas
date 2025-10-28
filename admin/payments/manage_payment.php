<?php
require_once('../../config.php');
require_once('../../classes/PaymentsModel.php');
require_once('../../classes/AppointmentsModel.php');

$paymentsModel = new PaymentsModel();
$appointmentsModel = new AppointmentsModel();

$payment = null;
if(isset($_GET['id'])){
    $payment = $paymentsModel->getPaymentById($_GET['id']);
}

// Get appointments for dropdown
$appointments = $appointmentsModel->getAllAppointmentsWithDetails();
?>
<div class="container-fluid">
    <form action="" id="payment-form">
        <input type="hidden" name="id" value="<?php echo $payment ? $payment['id'] : '' ?>">
        
        <div class="form-group">
            <label for="appointment_id" class="control-label">Appointment</label>
            <select name="appointment_id" id="appointment_id" class="form-control form-control-border" required>
                <option value="">Select Appointment</option>
                <?php foreach($appointments as $appt): ?>
                    <option value="<?php echo $appt['id'] ?>" 
                            data-fee="<?php echo $appt['fee'] ?>"
                            <?php echo ($payment && $payment['appointment_id'] == $appt['id']) ? 'selected' : '' ?>>
                        #<?php echo $appt['code'] ?> - <?php echo htmlspecialchars($appt['customer_name']) ?> 
                        (<?php echo $appt['schedule_date'] ?> at <?php echo $appt['start_time'] ?>)
                        - <?php echo htmlspecialchars($appt['service_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="control-label">Amount (KSh)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-border" placeholder="0.00" value="<?php echo $payment ? $payment['amount'] : '0.00' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_method" class="control-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-control form-control-border" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash" <?php echo ($payment && $payment['payment_method'] == 'cash') ? 'selected' : '' ?>>Cash</option>
                        <option value="mpesa" <?php echo ($payment && $payment['payment_method'] == 'mpesa') ? 'selected' : '' ?>>M-Pesa</option>
                        <option value="card" <?php echo ($payment && $payment['payment_method'] == 'card') ? 'selected' : '' ?>>Card</option>
                        <option value="bank_transfer" <?php echo ($payment && $payment['payment_method'] == 'bank_transfer') ? 'selected' : '' ?>>Bank Transfer</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status" class="control-label">Payment Status</label>
                    <select name="status" id="status" class="form-control form-control-border" required>
                        <option value="pending" <?php echo ($payment && $payment['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?php echo ($payment && $payment['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="failed" <?php echo ($payment && $payment['status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                        <option value="refunded" <?php echo ($payment && $payment['status'] == 'refunded') ? 'selected' : '' ?>>Refunded</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="transaction_ref" class="control-label">Transaction Reference</label>
                    <input type="text" name="transaction_ref" id="transaction_ref" class="form-control form-control-border" placeholder="Enter transaction reference" value="<?php echo $payment ? htmlspecialchars($payment['transaction_ref']) : '' ?>">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="notes" class="control-label">Notes</label>
            <textarea rows="3" name="notes" id="notes" class="form-control form-control-border" placeholder="Enter any payment notes or comments"><?php echo $payment ? htmlspecialchars($payment['notes']) : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        // Auto-fill amount when appointment is selected
        $('#appointment_id').change(function(){
            var selectedOption = $(this).find('option:selected');
            var fee = selectedOption.data('fee') || 0;
            $('#amount').val(fee);
        });
        
        // Trigger amount update if editing existing payment
        if ($('#appointment_id').val()) {
            $('#appointment_id').trigger('change');
        }
        
        $('#uni_modal #payment-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            
            // Prepare form data
            var formData = new FormData();
            formData.append('id', $('#payment-form input[name="id"]').val());
            formData.append('appointment_id', $('#payment-form select[name="appointment_id"]').val());
            formData.append('amount', $('#payment-form input[name="amount"]').val());
            formData.append('payment_method', $('#payment-form select[name="payment_method"]').val());
            formData.append('status', $('#payment-form select[name="status"]').val());
            formData.append('transaction_ref', $('#payment-form input[name="transaction_ref"]').val());
            formData.append('notes', $('#payment-form textarea[name="notes"]').val());

            $.ajax({
                url: '../../classes/PaymentsModel.php',
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
                        alert_toast(resp.msg || 'Payment saved successfully', 'success');
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