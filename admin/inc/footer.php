<?php /* Footer & required scripts only; no outside custom JS */ ?>
<footer class="main-footer text-sm">
  <strong>&copy; <?php echo date('Y') ?> <?php echo $_settings->info('short_name') ?: 'OVAS' ?>.</strong>
  <span class="text-muted ml-1">All rights reserved.</span>
  <div class="float-right d-none d-sm-inline-block">
    <span class="text-muted">Powered by AdminLTE</span>
  </div>
</footer>

<!-- Vendor JS -->
<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
<script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?php echo base_url ?>dist/js/adminlte.js"></script>

<script>
  // Image fallback (uses placeholders from header constants)
  (function(){
    var PH_LOGO   = <?php echo json_encode(defined('OVAS_PH_LOGO') ? OVAS_PH_LOGO : '') ?>;
    var PH_AVATAR = <?php echo json_encode(defined('OVAS_PH_AVATAR') ? OVAS_PH_AVATAR : '') ?>;
    document.querySelectorAll('img').forEach(function(img){
      img.addEventListener('error', function(){
        var isLogoHint = (img.className||'').indexOf('brand-image')>-1 || img.getAttribute('data-fallback')==='logo';
        img.src = isLogoHint ? PH_LOGO : PH_AVATAR;
      }, { once:true });
    });
  })();

  // Helpers
  function start_loader(){
    if(document.getElementById('preloader')) return;
    const el = document.createElement('div');
    el.id = 'preloader';
    el.style.cssText = "position:fixed;inset:0;z-index:2000;display:flex;align-items:center;justify-content:center;background:rgba(3,6,17,.55);backdrop-filter:blur(2px)";
    el.innerHTML = '<div class="spinner-border text-primary" role="status" aria-label="Loading"></div>';
    document.body.appendChild(el);
  }
  function end_loader(){ const el=document.getElementById('preloader'); if(el) el.remove(); }

  window.viewer_modal = function(src=''){
    if(!src) return; start_loader();
    const isVideo = /\.(mp4|webm|ogg)$/i.test(src);
    const view = isVideo ? $("<video>",{src,controls:true,autoplay:true,style:"max-width:100%;max-height:80vh"})
                         : $("<img>",{src,style:"max-width:100%;max-height:80vh"});
    $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove();
    $('#viewer_modal .modal-content').append(view);
    $('#viewer_modal').modal({show:true,backdrop:'static'});
    end_loader();
  }

  window.uni_modal = function(title='', url='', size='modal-md'){
    start_loader();
    $.ajax({
      url, dataType:'html',
      error: function(){ end_loader(); Swal.fire('Error','Failed to load content.','error'); },
      success: function(resp){
        $('#uni_modal .modal-title').text(title);
        $('#uni_modal .modal-body').html(resp);
        $('#uni_modal .modal-dialog').attr('class','modal-dialog '+size+' modal-dialog-centered');
        $('#uni_modal').modal({show:true,backdrop:'static',keyboard:false,focus:true});
        end_loader();
      }
    });
  }

  window._conf = function(msg='', func='', params=[]){
    $('#confirm_modal #confirm').attr('onclick', func+"("+params.join(',')+")");
    $('#confirm_modal .modal-body').html(msg);
    $('#confirm_modal').modal('show');
  }

  $(function(){
    if($.fn.select2) $('.select2').select2({ theme:'bootstrap4', width:'100%' });
    if($.fn.overlayScrollbars) $('body').overlayScrollbars({});
  });
</script>
