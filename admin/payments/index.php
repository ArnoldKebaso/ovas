<?php
require_once '../../classes/PaymentsModel.php';

$paymentsModel = new PaymentsModel();
$payments = $paymentsModel->getAllPaymentsWithDetails();
?>

<style>
    .status-badge {
        font-size: 0.8em;
        padding: 0.25rem 0.5rem;
    }
    .amount-text {
        font-weight: bold;
        font-size: 1.1em;
    }
    .payment-ref {
        font-family: monospace;
        font-size: 0.9em;
        background: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Payment Management</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-info" id="refreshPayments">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                    Filter by Status
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="?status=all">All</a>
                    <a class="dropdown-item" href="?status=success">Success</a>
                    <a class="dropdown-item" href="?status=initiated">Initiated</a>
                    <a class="dropdown-item" href="?status=failed">Failed</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Successful</span>
                            <span class="info-box-number">
                                <?php 
                                $successCount = 0;
                                foreach($payments as $payment) {
                                    if($payment['status'] == 'success') $successCount++;
                                }
                                echo $successCount;
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">
                                <?php 
                                $pendingCount = 0;
                                foreach($payments as $payment) {
                                    if($payment['status'] == 'initiated') $pendingCount++;
                                }
                                echo $pendingCount;
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Failed</span>
                            <span class="info-box-number">
                                <?php 
                                $failedCount = 0;
                                foreach($payments as $payment) {
                                    if($payment['status'] == 'failed') $failedCount++;
                                }
                                echo $failedCount;
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">
                                KES 
                                <?php 
                                $totalRevenue = 0;
                                foreach($payments as $payment) {
                                    if($payment['status'] == 'success') {
                                        $totalRevenue += $payment['amount'];
                                    }
                                }
                                echo number_format($totalRevenue, 2);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payments Table -->
            <table class="table table-hover table-striped" id="paymentsTable">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Reference</th>
                        <th width="20%">Appointment</th>
                        <th width="10%">Amount</th>
                        <th width="10%">Provider</th>
                        <th width="10%">Status</th>
                        <th width="15%">Date</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($payments as $payment): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td>
                            <?php if($payment['reference']): ?>
                                <span class="payment-ref"><?php echo htmlspecialchars($payment['reference']); ?></span>
                            <?php else: ?>
                                <em class="text-muted">No reference</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($payment['appointment_code']): ?>
                                <strong><?php echo htmlspecialchars($payment['appointment_code']); ?></strong>
                                <br><small class="text-muted"><?php echo htmlspecialchars($payment['user_name']); ?></small>
                            <?php else: ?>
                                <em class="text-muted">Direct Payment</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="amount-text text-success">
                                <?php echo htmlspecialchars($payment['currency']); ?> 
                                <?php echo number_format($payment['amount'], 2); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info text-uppercase">
                                <?php echo htmlspecialchars($payment['provider']); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusColors = [
                                'success' => 'success',
                                'initiated' => 'warning',
                                'failed' => 'danger'
                            ];
                            $color = $statusColors[$payment['status']] ?? 'secondary';
                            ?>
                            <span class="badge badge-<?php echo $color; ?> status-badge">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </td>
                        <td>
                            <small>
                                <?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item view_payment" href="javascript:void(0)" data-id="<?php echo $payment['id']; ?>">
                                        <span class="fa fa-eye text-dark"></span> View Details
                                    </a>
                                    <?php if($payment['raw_payload']): ?>
                                        <a class="dropdown-item view_payload" href="javascript:void(0)" data-id="<?php echo $payment['id']; ?>">
                                            <span class="fa fa-code text-info"></span> View Raw Data
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
    $('#paymentsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[ 6, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [7] }
        ]
    });

    $('#refreshPayments').click(function(){
        location.reload();
    });

    $('.view_payment').click(function(){
        var id = $(this).attr('data-id');
        uni_modal("Payment Details", "payments/view_payment.php?id=" + id, "mid-large");
    });

    $('.view_payload').click(function(){
        var id = $(this).attr('data-id');
        uni_modal("Raw Payment Data", "payments/view_payload.php?id=" + id, "large");
    });
});
</script>