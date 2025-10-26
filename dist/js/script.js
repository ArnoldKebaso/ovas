/**
 * General OVAS Script Functions
 */

// Initialize when document is ready
$(document).ready(function() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize DataTables if present
    if ($.fn.DataTable) {
        $('.table-datatable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }
    
    // Form validation
    $('.needs-validation').on('submit', function(e) {
        if (this.checkValidity() === false) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Confirm delete actions
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const message = $(this).data('message') || 'Are you sure you want to delete this item?';
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        } else {
            if (confirm(message)) {
                window.location.href = url;
            }
        }
    });
});

// Alert toast function
function alert_toast(msg, type) {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type || 'info',
            title: msg
        });
    } else {
        alert(msg);
    }
}

// Loading state functions
function start_load() {
    $('body').prepend('<div id="preloader2"></div>');
}

function end_load() {
    $('#preloader2').remove();
}