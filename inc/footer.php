<script>
  $(document).ready(function(){
    $('.list-group').each(function(){
      if(String($(this).text()).trim() == ""){
        $(this).html("")
      }
    })
    
     window.viewer_modal = function($src = ''){
      start_loader()
      var t = $src.split('.')
      t = t[1]
      if(t =='mp4'){
        var view = $("<video src='"+$src+"' controls autoplay></video>")
      }else{
        var view = $("<img src='"+$src+"' />")
      }
      $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
      $('#viewer_modal .modal-content').append(view)
      $('#viewer_modal').modal({
              show:true,
              backdrop:'static',
              keyboard:false,
              focus:true
            })
            end_loader()  

  }
    window.uni_modal = function($title = '' , $url='',$size=""){
        start_loader()
        $.ajax({
            url:$url,
            error:err=>{
                console.log()
                alert("An error occured")
            },
            success:function(resp){
                if(resp){
                    $('#uni_modal .modal-title').html($title)
                    $('#uni_modal .modal-body').html(resp)
                    if($size != ''){
                        $('#uni_modal .modal-dialog').addClass($size+'  modal-dialog-centered')
                    }else{
                        $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
                    }
                    $('#uni_modal').modal({
                      show:true,
                      backdrop:'static',
                      keyboard:false,
                      focus:true
                    })
                    end_loader()
                }
            }
        })
    }
    window._conf = function($msg='',$func='',$params = []){
       $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
       $('#confirm_modal .modal-body').html($msg)
       $('#confirm_modal').modal('show')
    }
  })
</script>
<footer class="main-footer text-sm">
  <div class="container">
        <strong>Copyright © <?php echo date('Y') ?>. 
        <!-- <a href=""></a> -->
        </strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
         
        </div>
      </div>
      </footer>
    </div>
    <!-- ./wrapper -->
<div id="libraries">
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/fullcalendar/main.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>
  </div>   
    <div class="daterangepicker ltr show-ranges opensright">
      <div class="ranges">
        <ul>
          <li data-range-key="Today">Today</li>
          <li data-range-key="Yesterday">Yesterday</li>
          <li data-range-key="Last 7 Days">Last 7 Days</li>
          <li data-range-key="Last 30 Days">Last 30 Days</li>
          <li data-range-key="This Month">This Month</li>
          <li data-range-key="Last Month">Last Month</li>
          <li data-range-key="Custom Range">Custom Range</li>
        </ul>
      </div>
      <div class="drp-calendar left">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-calendar right">
        <div class="calendar-table"></div>
        <div class="calendar-time" style="display: none;"></div>
      </div>
      <div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn btn btn-sm btn-default" type="button">Cancel</button><button class="applyBtn btn btn-sm btn-primary" disabled="disabled" type="button">Apply</button> </div>
    </div>
    <div class="jqvmap-label" style="display: none; left: 1093.83px; top: 394.361px;"></div>

<!-- Modern Footer -->
<footer class="modern-footer bg-dark text-white py-5 mt-5">
  <div class="container">
    <div class="row g-4">
      <!-- About Column -->
      <div class="col-lg-4 col-md-6">
        <div class="d-flex align-items-center mb-3">
          <img src="<?= validate_image($_settings->info('logo')) ?>" alt="Logo" class="rounded-circle me-3" style="height: 50px; width: 50px; object-fit: cover;">
          <h5 class="mb-0 fw-bold"><?= $_settings->info('short_name') ?></h5>
        </div>
        <p class="text-white-50 mb-3">
          Professional veterinary care for your beloved pets. We provide comprehensive health services with compassion and expertise.
        </p>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="fab fa-whatsapp"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links Column -->
      <div class="col-lg-2 col-md-6">
        <h6 class="fw-bold mb-3">Quick Links</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none hover-primary"><i class="fas fa-chevron-right me-2 small"></i>Home</a></li>
          <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none hover-primary"><i class="fas fa-chevron-right me-2 small"></i>Services</a></li>
          <li class="mb-2"><a href="#appointment" class="text-white-50 text-decoration-none hover-primary"><i class="fas fa-chevron-right me-2 small"></i>Book Appointment</a></li>
          <li class="mb-2"><a href="#about" class="text-white-50 text-decoration-none hover-primary"><i class="fas fa-chevron-right me-2 small"></i>About Us</a></li>
          <li class="mb-2"><a href="#contact" class="text-white-50 text-decoration-none hover-primary"><i class="fas fa-chevron-right me-2 small"></i>Contact</a></li>
        </ul>
      </div>

      <!-- Services Column -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-bold mb-3">Our Services</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><span class="text-white-50"><i class="fas fa-syringe me-2 text-primary"></i>Vaccination</span></li>
          <li class="mb-2"><span class="text-white-50"><i class="fas fa-pills me-2 text-success"></i>Deworming</span></li>
          <li class="mb-2"><span class="text-white-50"><i class="fas fa-cut me-2 text-info"></i>Grooming</span></li>
          <li class="mb-2"><span class="text-white-50"><i class="fas fa-tooth me-2 text-warning"></i>Dental Care</span></li>
          <li class="mb-2"><span class="text-white-50"><i class="fas fa-heartbeat me-2 text-danger"></i>Surgery</span></li>
        </ul>
      </div>

      <!-- Contact Column -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-bold mb-3">Contact Info</h6>
        <ul class="list-unstyled">
          <li class="mb-3 d-flex align-items-start">
            <i class="fas fa-map-marker-alt me-3 mt-1 text-primary"></i>
            <span class="text-white-50 small"><?= $_settings->info('address') ?></span>
          </li>
          <li class="mb-3 d-flex align-items-center">
            <i class="fas fa-phone me-3 text-success"></i>
            <a href="tel:<?= $_settings->info('contact') ?>" class="text-white-50 text-decoration-none hover-primary small"><?= $_settings->info('contact') ?></a>
          </li>
          <li class="mb-3 d-flex align-items-center">
            <i class="fas fa-envelope me-3 text-info"></i>
            <a href="mailto:<?= $_settings->info('email') ?>" class="text-white-50 text-decoration-none hover-primary small"><?= $_settings->info('email') ?></a>
          </li>
          <li class="mb-3 d-flex align-items-start">
            <i class="fas fa-clock me-3 mt-1 text-warning"></i>
            <span class="text-white-50 small">Mon - Sat: 8AM - 6PM<br>Sunday: Emergency Only</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bottom Bar -->
    <hr class="border-secondary my-4">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start">
        <p class="mb-0 text-white-50 small">
          &copy; <?= date('Y') ?> <?= $_settings->info('name') ?>. All rights reserved.
        </p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <p class="mb-0 text-white-50 small">
          <a href="#" class="text-white-50 text-decoration-none hover-primary me-3">Privacy Policy</a>
          <a href="#" class="text-white-50 text-decoration-none hover-primary me-3">Terms of Service</a>
          <a href="./admin" class="text-white-50 text-decoration-none hover-primary">Admin</a>
        </p>
      </div>
    </div>
  </div>
</footer>

<style>
.modern-footer {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
}

.modern-footer .hover-primary:hover {
  color: #2563eb !important;
  transform: translateX(4px);
  transition: all 0.3s ease;
}

.modern-footer .btn-outline-light:hover {
  background: #2563eb;
  border-color: #2563eb;
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

.modern-footer h6 {
  position: relative;
  padding-bottom: 0.5rem;
}

.modern-footer h6::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 40px;
  height: 2px;
  background: linear-gradient(90deg, #2563eb, #10b981);
}
</style>

<script>
  $(function(){
    $('.wrapper>.content-wrapper').css("min-height",$(window).height() - $('#mainNav').height() - $("footer.modern-footer").height())
  })
</script>