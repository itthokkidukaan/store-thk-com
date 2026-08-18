<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>'
    });
    $('[data-toggle="datepicker"]').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');
    $('#sdate').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('#sdate').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');
    $('.date30').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('.date30').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');
</script>
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/unslider-min.js"></script>
<script src="<?= assets_url() ?>app-assets/vendors/js/timeline/horizontal-timeline.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url() ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<script src="<?php echo assets_url(); ?>assets/myjs/jquery-ui.js"></script>
<script src="<?php echo assets_url(); ?>assets/myjs/jquery.dataTables.min.js"></script>
<script src="<?= assets_url('assets/admin/js/select2.full.min.js') ?>"></script>
<script type="text/javascript">var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');
    ;</script>
<script src="<?php echo assets_url('assets/myjs/custom.js') . APPVER; ?>"></script>
<script type="text/javascript">var base_url = '<?php echo base_url() ?>';
      
    </script>
    <script src="<?= assets_url() ?>assets/admin/js/sweetalert2.min.js"></script>
	
<?php
$pos_controller = $this->uri->segment(1);
$pos_action = $this->uri->segment(2);

$edit_pos_path = FCPATH . 'assets/admin/custom/edit_pos.js';
$edit_pos_version = file_exists($edit_pos_path) ? '?v=' . filemtime($edit_pos_path) : APPVER;

$pos_path = FCPATH . 'assets/admin/custom/pos.js';
$pos_version = file_exists($pos_path) ? '?v=' . filemtime($pos_path) : APPVER;

$basic_js_path = FCPATH . 'assets/myjs/basic.js';
$basic_js_version = file_exists($basic_js_path) ? '?v=' . filemtime($basic_js_path) : APPVER;

$control_js_path = FCPATH . 'assets/myjs/control.js';
$control_js_version = file_exists($control_js_path) ? '?v=' . filemtime($control_js_path) : APPVER;

if ($pos_controller === 'pos_invoices' && $pos_action === 'create') {
?>
<script src="<?= assets_url('assets/admin/custom/edit_pos.js') . $edit_pos_version ?>"></script>
<?php
} elseif ($pos_action === 'create') {
?>
<script src="<?= assets_url('assets/admin/custom/pos.js') . $pos_version ?>"></script>
<?php
} else {
?>
<script src="<?= assets_url('assets/admin/custom/edit_pos.js') . $edit_pos_version ?>"></script>
<?php
}
?>
<script src="<?php echo assets_url('assets/myjs/basic.js') . $basic_js_version; ?>"></script>
<script src="<?= assets_url('assets/admin/js/iziToast.min.js') ?>"></script>
<script src="<?php echo assets_url('assets/myjs/control.js') . $control_js_version; ?>"></script>
<script src="<?= assets_url() ?>app-assets/js/scripts/pages/chat-application.js"></script>
<script type="text/javascript">
    $.ajax({
        url: baseurl + 'manager/pendingtasks',
        dataType: 'json',
        success: function (data) {
            $('#tasklist').html(data.tasks);
            $('#taskcount').html(data.tcount);
        },
        error: function (data) {
            $('#response').html('Error')
        }
    });
    if (localStorage.show == 'no') {
        $("#pos0").fadeOut();
        $("body").css('padding-top', '0rem');
        $(".content-wrapper").css('padding-top', '0rem');
        $('#hide_header').attr('id', 'show_header');
    }
    $(document).on('click', "#show_header", function (e) {
        $("#pos0").fadeIn();
        $("body").css('padding-top', '4rem');
        $(".content-wrapper").css('padding-top', '1rem');
        localStorage.setItem("show", "yes");
        $(this).attr('id', 'hide_header');
    });
    $(document).on('click', "#hide_header", function (e) {
        $("#pos0").fadeOut();
        $("body").css('padding-top', '0rem');
        $(".content-wrapper").css('padding-top', '0rem');
        $(this).attr('id', 'show_header');
        localStorage.setItem("show", "no");
    });

</script>

</body>
</html>

