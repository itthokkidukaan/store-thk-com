</div>
</div>
</div>


<div class="modal fade " id='media-upload-modal' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 main-content">
                    <div class="content-area p-4">
                        <div class="card-innr">
                            <div class="gaps-1-5x"></div>
                            <input type='hidden' name='media_type' id='media_type' value='image'>
                            <input type='hidden' name='current_input'>
                            <input type='hidden' name='remove_state'>
                            <input type='hidden' name='multiple_images_allowed_state'>
                            <div class="col-md-12 mt-3 mb-3 mb-5">
                                <!-- Change /upload-target to your upload address -->
                                <div id="dropzone" class="dropzone"></div>
                                <br>
                                <a href="" id="upload-files-btn" class="btn btn-success float-right">Upload</a>
                            </div>
                            <div class="alert alert-warning">Select media and click choose media</div>
                            <div id="toolbar">
                                <button id='upload-media' class="btn btn-danger">
                                    <i class="fa fa-plus"></i> Choose Media
                                </button>
                            </div>
                            <?php 
                            // Fix Mixed Content: Ensure media/fetch URL uses HTTPS if page is HTTPS
                            $media_url = base_url('media/fetch');
                            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") || 
                                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https")) {
                                $media_url = str_replace('http://', 'https://', $media_url);
                            }
                            ?>
                            <table class='table-striped' data-toolbar="#toolbar" id='media-upload-table' data-page-size="5" data-toggle="table" data-url="<?= $media_url ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="asc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-query-params="mediaParams">
                                <thead>
                                    <tr>
                                        <th data-field="state" data-checkbox="true"></th>
                                        <th data-field="id" data-sortable="true" data-visible='false'>ID</th>
                                        <th data-field="image" data-sortable="false">Image</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="size" data-sortable="false">Size</th>
                                        <th data-field="extension" data-sortable="false" data-visible='false'>Extension</th>
                                        <th data-field="sub_directory" data-sortable="false" data-visible='false'>Sub directory</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
/*     $('[data-toggle="mydatepicker"]').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>'
    });
    $('[data-toggle="mydatepicker"]').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');  
 */

    // Calculate 2 days ago
    var today = new Date();
    var twoDaysAgo = new Date();
    twoDaysAgo.setDate(today.getDate() - 2);

    // Format function: dd-mm-yyyy
    function formatDate(d) {
        let day = ('0' + d.getDate()).slice(-2);
        let month = ('0' + (d.getMonth() + 1)).slice(-2);
        let year = d.getFullYear();
        return `${day}-${month}-${year}`;
    }

    // Initialize datepicker
    $('[data-toggle="mydatepicker"]').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>',
        startDate: twoDaysAgo
    });

    // Set today's date or any default date (optional)
    $('[data-toggle="mydatepicker"]').datepicker('setDate', formatDate(today));

    // Prevent manual typing
    $('[data-toggle="mydatepicker"]').on('keydown paste', function (e) {
        e.preventDefault();
    });

	$('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>'
    });
    $('[data-toggle="datepicker"]').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');
    $('#dashend_date').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');

    $('#sdate').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('#sdate').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');
    $('.date30').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('.date30').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');

    $('.date30_plus').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('.date30_plus').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('+30 days', strtotime(date('Y-m-d'))))); ?>');


 $('#dashstart_date').datepicker('setDate', '<?php echo dateformat(date('01-m-Y')); ?>');
</script>
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/unslider-min.js"></script>
<script src="<?= assets_url() ?>app-assets/vendors/js/timeline/horizontal-timeline.js"></script>
<script src="<?= assets_url() ?>assets/admin/ekko-lightbox/ekko-lightbox.min.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url() ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<script src="<?php echo assets_url(); ?>assets/myjs/jquery-ui.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/jszip.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"></script>

<script type="text/javascript">var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');
</script>
<script src="<?php echo assets_url('assets/myjs/custom.js') . '?v=' . (file_exists(FCPATH . 'assets/myjs/custom.js') ? filemtime(FCPATH . 'assets/myjs/custom.js') : APPVER); ?>"></script>
<script src="<?php echo assets_url('assets/myjs/basic.js') . APPVER; ?>"></script>
<script src="<?php echo assets_url('assets/myjs/control.js') . '?v=' . (file_exists(FCPATH . 'assets/myjs/control.js') ? filemtime(FCPATH . 'assets/myjs/control.js') : APPVER); ?>"></script>

<script type="text/javascript">
var base_url= '<?=base_url()?>';
var media_base_url = '<?= (defined("MEDIA_BASE_URL") && MEDIA_BASE_URL) ? rtrim(MEDIA_BASE_URL, "/") . "/" : base_url(); ?>';
var csrfName = "<?= $this->security->get_csrf_token_name() ?>";
var csrfHash = "<?= $this->security->get_csrf_hash() ?>";
// Fix Mixed Content: Ensure base_url uses HTTPS if page is HTTPS
(function() {
    if (window.location.protocol === 'https:' && base_url.indexOf('http://') === 0) {
        base_url = base_url.replace('http://', 'https://');
    }
    if (typeof baseurl !== 'undefined' && baseurl) {
        if (window.location.protocol === 'https:' && baseurl.indexOf('http://') === 0) {
            baseurl = baseurl.replace('http://', 'https://');
        }
    }
})();

    $.ajax({

        url: (typeof baseurl !== 'undefined' ? baseurl : base_url) + 'manager/pendingtasks',
        dataType: 'json',
        success: function (data) {
            $('#tasklist').html(data.tasks);
            $('#taskcount').html(data.tcount);

        },
        error: function (data) {
            $('#response').html('Error')
        }

    });
</script>

<script src="<?= assets_url('assets/admin/chart.js/Chart.min.js') ?>"></script>
<!-- Sparkline -->
<script src="<?= assets_url('assets/admin/js/sparkline.js') ?>"></script>
<!-- JQVMap -->
<script src="<?= assets_url('assets/admin/js/jquery.vmap.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/jquery.vmap.usa.js') ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= assets_url('assets/admin/js/jquery.knob.min.js') ?>"></script>
<!-- daterangepicker -->
<script src="<?= assets_url('assets/admin/js/moment.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/daterangepicker.js') ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= assets_url('assets/admin/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<!-- Toastr -->
<script src="<?= assets_url('assets/admin/js/iziToast.min.js') ?>"></script>
<!-- Select -->
<script src="<?= assets_url('assets/admin/js/select2.full.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= assets_url('assets/admin/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= assets_url('assets/admin/dist/js/adminlte.js') ?>"></script>
<!-- Bootstrap Switch -->
<script src="<?= assets_url('assets/admin/js/bootstrap-switch.min.js') ?>"></script>
<!-- Bootstrap Table -->
<script src="<?= assets_url('assets/admin/js/bootstrap-table.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/tableExport.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/bootstrap-table-export.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<!-- Jquery Fancybox -->
<script src="<?= assets_url('assets/admin/js/jquery.fancybox.min.js') ?>"></script>
<!-- Sweeta Alert 2 -->
<script src="<?= assets_url('assets/admin/js/sweetalert2.min.js') ?>"></script>
<!-- Block UI -->
<script src="<?= assets_url('assets/admin/js/jquery.blockUI.js') ?>"></script>
<!-- JS tree -->
<script src="<?= assets_url('assets/admin/js/jstree.min.js') ?>"></script>
<!-- Chartist -->
<script src="<?= assets_url('assets/admin/js/chartist.js') ?>"></script>
<!-- Tool Tip -->
<script src="<?= assets_url('assets/admin/js/tooltip.js') ?>"></script>
<!-- Loader Js -->
<script type="text/javascript" src="<?= assets_url('assets/admin/js/loader.js') ?>"></script>
<!-- Dropzone -->
<script type="text/javascript" src="<?= assets_url('assets/admin/js/dropzone.js') ?>"></script>

<script type="text/javascript" src="<?= assets_url('assets/admin/js/tagify.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/moment.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/js/bootstrap.bundle.min.js') ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= assets_url('assets/admin/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= assets_url('assets/admin/jquery-ui/jquery-ui.min.js') ?>"></script>
<?php 
// Dynamic cache-busting for custom.js - uses file modification time
$custom_js_path = FCPATH . 'assets/admin/custom/custom.js';
$custom_js_version = file_exists($custom_js_path) ? '?v=' . filemtime($custom_js_path) : APPVER;
?>
<script src="<?= assets_url('assets/admin/custom/custom.js') . $custom_js_version ?>"></script>

<!-- Demo -->
<script src="<?= assets_url('assets/admin/dist/js/demo.js') ?>"></script>
</body>
</html>
