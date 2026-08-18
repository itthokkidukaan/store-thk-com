<div class="content-body">
    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>

                <div class="message"></div>
            </div>
            <div id="invoice-template" class="card-body">
                <div class="row wrapper white-bg page-heading">
                    <div class="col-lg-12">
                       
                        <div class="title-action">

                          <?php 
                          $date_seg = $this->uri->segment(3);
                          $chalan_seg = $this->uri->segment(4);
                          $totalchalan_seg = $this->uri->segment(5);

                          $date_param = !empty($date_seg) ? $date_seg : ($invoice['invoicedate'] ?? '');
                          $chalan_param = !empty($chalan_seg) ? $chalan_seg : ($invoice['tid'] ?? 0);
                          $totalchalan_param = !empty($totalchalan_seg) ? $totalchalan_seg : 1;

                          $param = $date_param . '/' . $chalan_param . '/' . $totalchalan_param;
                          $q_str = (!empty($id) && $id > 0) ? '?id=' . $id : '';
                          ?>



                             <div class="btn-group">
                                <button type="button" class="btn btn-success btn-min-width dropdown-toggle mb-1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-print"></i> Print Indent
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                       href="<?= base_url('indent/printindent/' . $param) . $q_str; ?>" target="_blank">
                                        Print Product wise
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                       href="<?= base_url('indent/printstoreview/'.$param) . $q_str; ?>" target="_blank">
                                        Print Store Wise
                                    </a>
                                </div> &nbsp;
                               
                                <a href="<?= base_url('indent/storeview/' . $param) . $q_str; ?>">  
                                    <button type="button" class="btn btn-primary btn-min-width mb-1"
                                            aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i> view Store wise
                                    </button>
                                </a> 

								<a href="<?= base_url('indent/cretequote/' . $param) . $q_str; ?>">  
                                    <button type="button" class="btn btn-danger btn-min-width mb-1"
                                            aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i> Convert to Quote
                                    </button>
                                </a>
								&nbsp;
								<a href="<?= base_url('indent/receiveditem/' . $param) . $q_str; ?>">  
                                    <button type="button" class="btn btn-amber btn-md t_tooltip"
                                            aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i> Received item
											
											
											
											
                                    </button>
                                </a>
                            </div>
                          

                        </div>
                    </div>
                </div>

                <?php if ($invoice['multi'] > 0) {

                    echo '<div class="tag tag-info text-xs-center mt-2">' . $this->lang->line('Payment currency is different') . '</div>';
                }
                ?>

                <!-- Invoice Company Details -->
                <div id="invoice-company-details" class="row mt-2">
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-left"><p></p>
                        <img src="<?php $loc = location($invoice['loc']);
                        echo base_url('userfiles/company/' . $loc['logo']) ?>"
                             class="img-responsive p-1 m-b-2" style="max-height: 120px;">
                        <p class="ml-2"><?= $loc['cname'] ?></p>
                    </div>
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                        <h2>Indent</h2>
                        <p class="pb-1"> <?php echo 'CN ' . $chalan . '</p>
                            <p class="pb-1"><b>Total Challan: ' . $totalchalan . '</b></p>'; ?>
                     
                    </div>
                </div>
                <!--/ Invoice Company Details -->

                <!-- Invoice Customer Details -->
              
                <!--/ Invoice Customer Details -->
                <?php if ($invoice['proposal'] != '') {
                    echo '<div id="invoice-customer-details" class="row pt-2">
                        <div class="col-sm-12 text-xs-center text-md-left">';

                    echo '<h5>' . $this->lang->line('Proposal') . '</h5>';
                    echo '<p>' . $invoice['proposal'] . '</p>';


                    echo '   </div></div>';
                } ?>
             
                <div id="invoice-items-details" class="pt-2">
                    <div class="row">
                        <div class="table-responsive col-sm-12">
                     



	
	<?php
	//print_r($quote_items);
$total_summary = [];
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Article</th>
            <th>Purchase Rate</th>
            <th>Supplier</th>
            <th>Total Qty</th>
            <th>Total Purchase</th>
            <th>Available Stock</th>
            <th>Need Qty</th>
            <th>Need Purchase</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i = 1;
        $subtotal = 0;
        $total_summary = [];

        foreach ($quote_items as $item) {
            $converted = convert_to_base_unit($item['unit']);
            $conversion_qty = $converted['qty'];
            $baseunit = $converted['unit'] ?? $item['unit'];

            $prqty = $conversion_qty * $item['total_qty'];

            // Totals group by unit
            $unit = $baseunit;

            $total_summary[$unit]['total_qty'] = ($total_summary[$unit]['total_qty'] ?? 0) + $prqty;
            $total_summary[$unit]['total_purchase'] = ($total_summary[$unit]['total_purchase'] ?? 0) + ($item['total_purchase'] ?? 0);
            $total_summary[$unit]['available_stock'] = ($total_summary[$unit]['available_stock'] ?? 0) + ($item['available_stock'] ?? 0);
            $total_summary[$unit]['need_qty'] = ($total_summary[$unit]['need_qty'] ?? 0) + ($item['need_qty'] ?? 0);
            $total_summary[$unit]['need_purchase'] = ($total_summary[$unit]['need_purchase'] ?? 0) + ($item['need_purchase'] ?? 0);

            $subtotal += $item['total_purchase'];
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($item['productname']) ?></td>
                <td><?= !empty($item['article']) ? $item['article'] : 'N/A' ?></td>
                <td><?= number_format($item['last_purchase_rate'], 2) ?></td>
                <td><?= htmlspecialchars($item['last_supplier']) ?></td>
                <td><?= number_format($prqty, 2) . ' ' . $unit ?></td>
                <td><?= number_format($item['total_purchase'], 2) ?></td>
                <td><?= number_format($item['available_stock'], 2) . ' ' . $unit ?></td>
                <td><?= number_format($item['need_qty'], 2) . ' ' . $unit ?></td>
                <td><?= number_format($item['need_purchase'], 2) ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <?php foreach ($total_summary as $unit => $totals) { ?>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="5">Total (<?= htmlspecialchars($unit) ?>)</td>
                <td><?= number_format($totals['total_qty'], 2) . ' ' . $unit ?></td>
                <td><?= number_format($totals['total_purchase'], 2) ?></td>
                <td><?= number_format($totals['available_stock'], 2) . ' ' . $unit ?></td>
                <td><?= number_format($totals['need_qty'], 2) . ' ' . $unit ?></td>
                <td><?= number_format($totals['need_purchase'], 2) ?></td>
            </tr>
        <?php } ?>
    </tfoot>
</table>


                          
                        </div>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-md-7 col-sm-12 text-xs-center text-md-left">


                            <div class="row">
                                <div class="col-md-8">
                                    <p class="lead"><?php echo $this->lang->line('Status') ?>: <u><strong
                                                    id="pstatus"><?php echo $this->lang->line(ucwords($invoice['status'])) ?></strong></u>
                                    </p>
                                    <p class="lead mt-1"><br><?php echo $this->lang->line('Note') ?>:</p>
                                    <code>
                                        <?php echo $invoice['notes'] ?>
                                    </code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <p class="lead"><?php echo $this->lang->line('Total') ?></p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td><?php echo $this->lang->line('Sub Total') ?></td>
                                        <td class="text-xs-right"> <?php echo amountExchange($subtotal, 0, $this->aauth->get_user()->loc) ?></td>
                                    </tr>
                                  
                                  
                                   
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-xs-center">
                                <p><?php echo $this->lang->line('Authorized person') ?></p>
                                <?php echo '<img src="' . base_url('userfiles/employee_sign/' . $employee['sign']) . '" alt="signature" class="height-100"/>
                                    <h6>(' . $employee['name'] . ')</h6>
                                    <p class="text-muted">' . user_role($employee['roleid']) . '</p>'; ?>
                            </div>
                        </div>
                    </div>
                </div>

             

            </div>
        </div>
    </div>
</div>
<script src="<?php echo assets_url('assets/myjs/jquery.ui.widget.js') ?>"></script>
<script src="<?php echo assets_url('assets/myjs/jquery.fileupload.js') ?>"></script>
<script>
    /*jslint unparam: true */
    /*global window, $ */
    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = '<?php echo base_url() ?>chalan/file_handling?id=<?php echo $invoice['iid'] ?>';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            formData: {'<?=$this->security->get_csrf_token_name()?>': crsf_hash},
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('#files').append('<tr><td><a data-url="<?php echo base_url() ?>chalan/file_handling?op=delete&name=' + file.name + '&invoice=<?php echo $invoice['iid'] ?>" class="aj_delete red"><i class="btn-sm fa fa-trash"></i></a> ' + file.name + ' </td></tr>');
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });

    $(document).on('click', ".aj_delete", function (e) {
        e.preventDefault();
        var aurl = $(this).attr('data-url');
        var obj = $(this);
        jQuery.ajax({
            url: aurl,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                obj.closest('tr').remove();
                obj.remove();
            }
        });

    });
</script>
<script type="text/javascript">
    $(function () {
        $('.summernote').summernote({
            height: 100,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['fullscreen', ['fullscreen']],
                ['codeview', ['codeview']]
            ]
        });

        $('#sendM').on('click', function (e) {
            e.preventDefault();

            sendBill($('.summernote').summernote('code'));

        });
    });


</script>
