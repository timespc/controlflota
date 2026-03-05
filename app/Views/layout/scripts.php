<!-- Jquery Page Js -->
<script src="<?= base_url() ?>/assets/js/theme.js"></script>
<!-- Plugin Js -->
<script src="<?= base_url() ?>/assets/js/bundle/dataTables.bundle.js"></script>
<script>
(function() {
  if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && jQuery.fn.dataTable.defaults) {
    jQuery.extend(true, jQuery.fn.dataTable.defaults, {
      language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
      pageLength: 10,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
      processing: true,
      responsive: true
    });
  }
})();
</script>
<script src="<?= base_url() ?>/assets/js/bundle/apexcharts.bundle.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/jquery.smartWizard.min.js"></script>
<script src="<?= base_url() ?>/assets/js/waitMe.min.js"></script>
<script src="<?= base_url() ?>/assets/jquery-ui.min.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/toastr.bundle.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/sweetalert2.bundle.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/jquery.dataTables.yadcf.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/dataTables.fixedColumns.min.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/jquery.barrating.min.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/select2.bundle.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/jqueryknob.bundle.js"></script>
<script src="<?= base_url() ?>/assets/js/bundle/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>/assets/js/jquery.validation/es.js"></script>
<!-- Jquery Page Js -->
<script>
  let ancho = screen.width;
  let alto = screen.height;

  let ancho_total = (ancho / 100) * 80;
  let alto_total = (alto / 100) * 50;
  let alto_overflow = (alto / 100) * 60;

$(document).ready(function() {
  // Submenú Parametros: no agregar # al URL al hacer clic en el padre
  $(document).on('click', '.menu-list a.m-link[data-bs-target="#submenu-parametros"]', function(e) {
    if (this.getAttribute('href') === '#') e.preventDefault();
  });
  // Sidebar en móvil: backdrop y cierre al tocar fuera
  var $sidebar = $('.sidebar');
  var $backdrop = $('#sidebar-backdrop');
  function syncBackdrop() {
    var isOpen = $sidebar.hasClass('open');
    $backdrop.toggleClass('show', isOpen).attr('aria-hidden', isOpen ? 'false' : 'true');
  }
  $('.menu-toggle').on('click', function() {
    setTimeout(syncBackdrop, 0);
  });
  $backdrop.on('click', function() {
    $sidebar.removeClass('open');
    $backdrop.removeClass('show').attr('aria-hidden', 'true');
  });
  $(window).on('resize', function() {
    if (window.innerWidth >= 1200) {
      $sidebar.removeClass('open');
      $backdrop.removeClass('show').attr('aria-hidden', 'true');
    }
  });

  // CSRF: enviar token del meta en todas las peticiones POST (Security $regenerate = false para que siga válido)
  var metaVal = document.querySelector('meta[name="csrf-token-value"]');
  var csrfVal = metaVal ? metaVal.getAttribute('content') : '';
  if (csrfVal && typeof $.ajaxSetup === 'function') {
    $.ajaxSetup({
      beforeSend: function(xhr, settings) {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        if (settings.type && settings.type.toUpperCase() === 'POST') {
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfVal);
        }
      }
    });
  }
  $(".modal_ancho").css({
    width: ancho_total + "px"
  });

  $(".modal_alto").css({
    "height": alto_total + "px"
  });

  $(".modal_alto_overflow").css({
    "height": alto_overflow + "px"
  });

  $(".modal_overflow").css({
    "overflow-x": "hidden",
    "overflow-y": "visible"
  });

  var notifCountPrev = 0;
  var notifPushUrl = '<?= site_url('notificaciones') ?>';
  var notifEstadoUrl = '<?= site_url('notificaciones/estado-push') ?>';

  function actualizarBadgeYPush() {
    $.get(notifEstadoUrl, function(res) {
      var n = res.count || 0;
      var $b = $('#notif-badge');
      if (n > 0) {
        $b.text(n > 99 ? '99+' : n).show();
      } else {
        $b.hide();
      }
      if (n > notifCountPrev && notifCountPrev > 0 && res.ultima) {
        var titulo = res.ultima.titulo || 'Nueva notificación';
        var mensaje = res.ultima.mensaje || '';
        var urlNotif = res.ultima.url || notifPushUrl;
        if (typeof toastr !== 'undefined') {
          var body = (mensaje ? mensaje.substring(0, 120) + (mensaje.length > 120 ? '...' : '') + ' ' : '') +
            '<a href="' + urlNotif + '" class="text-primary fw-bold">Ver notificaciones</a>';
          toastr.info(body, titulo, { timeOut: 8000, closeButton: true, positionClass: 'toast-top-right', newestOnTop: true, enableHtml: true });
        }
        if (res.push_browser_activo && typeof Notification !== 'undefined' && Notification.permission === 'granted') {
          try {
            var not = new Notification(titulo, { body: mensaje ? mensaje.substring(0, 100) : 'Ver notificaciones', icon: '/img/logo/logo-itdelfos.ico' });
            not.onclick = function() { window.focus(); window.location.href = res.ultima.url || notifPushUrl; };
          } catch (e) {}
        }
      }
      notifCountPrev = n;
    });
  }

  if ($('#notif-badge').length) {
    actualizarBadgeYPush();
    setInterval(actualizarBadgeYPush, 45000);
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
      Notification.requestPermission();
    }
    // Ejecutar recordatorios cada 1 minuto (envío de emails por no leídas según config de cada admin)
    var recordatoriosUrl = '<?= site_url('notificaciones/procesar-recordatorios') ?>';
    setInterval(function() {
      $.get(recordatoriosUrl).fail(function() { /* silenciar error de red */ });
    }, 60000);
  }
});
</script>




