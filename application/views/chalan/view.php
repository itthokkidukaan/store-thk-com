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
                        <?php
						
						
                        $validtoken = hash_hmac('ripemd160', 'q' . $invoice['iid'], $this->config->item('encryption_key'));

                        $link = base_url('billing/quoteview?id=' . $invoice['iid'] . '&token=' . $validtoken);
                        ?>
                        <div class="title-action">

                            <a href="<?php echo 'edit?id=' . $invoice['iid']; ?>" class="btn btn-warning mb-1"><i
                                        class="fa fa-pencil"></i> Edit Challan </a>

                            <a href="#pop_model" data-toggle="modal" data-remote="false"
                               class="btn btn-large btn-success mb-1" title="Change Status"
                            ><span class="fa fa-retweet"></span> <?php echo $this->lang->line('Change Status') ?> </a>
							<?php 
							
							if($invoice['convert_to_sell']==0){
						
							
							echo ' <a href="'.base_url().'chalan/invoicepre?id='.$invoice['iid'].'" 
                               class="btn btn-large btn-info mb-1" title="Convert to Invoice"
                            ><span class="fa fa-share"></span>'.$this->lang->line("Convert to Invoice").' </a>';
							}else{
								
								echo ' <a href="'.base_url().'invoices/edit?id='.$invoice['convert_to_sell'].'" 
                               class="btn btn-large btn-info mb-1" title="Convert to Invoice"
                            ><span class="fa fa-share"></span>'.$this->lang->line("Convert to Invoice").' </a>';
								
							}
							?>
                           
							
							
                     
							
								<?php 
							
							if($invoice['convert_purchase']==0){
							echo '<a href="#pop_model3" data-toggle="modal" data-remote="false"
                               class="btn btn-large btn-blue-grey mb-1" title="Convert to Purchase"
                            ><span class="fa fa-share"></span>'.$this->lang->line("Convert to Purchase").'</a>';
							}else{
								
								echo ' <a href="'.base_url().'purchase/edit?id='.$invoice['convert_purchase'].'" 
                               class="btn btn-large btn-info mb-1" title="Convert to Invoice"
                            ><span class="fa fa-share"></span>'.$this->lang->line("Convert to Purchase").' </a>';
								
							}
							?>

                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle mb-1"
                                        data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                            <span
                                    class="fa fa-envelope"></span> EMail
                                </button>
                                <div class="dropdown-menu"><a href="#sendEmail" data-toggle="modal"
                                                              data-remote="false" class="dropdown-item sendbill"
                                                              data-type="quote"><?php echo $this->lang->line('Send Proposal') ?></a>


                                </div>

                            </div>

                            <div class="btn-group">
                                <button type="button" class="btn btn-blue dropdown-toggle mb-1"
                                        data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                            <span
                                    class="fa fa-mobile"></span> SMS
                                </button>
                                <div class="dropdown-menu"><a href="#sendSMS" data-toggle="modal"
                                                              data-remote="false" class="dropdown-item sendsms"
                                                              data-type="quote"><?php echo $this->lang->line('Send Proposal') ?></a>


                                </div>

                            </div>

                            <div class="btn-group ">
                                <button type="button" class="btn btn-success btn-min-width dropdown-toggle mb-1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                            class="fa fa-print"></i> Print Challan
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                       href="<?= base_url('billing/printchalan?id=' . $invoice['iid'] . '&token=' . $validtoken); ?>"><?php echo $this->lang->line('Print') ?></a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                       href="<?= base_url('billing/printchalan?id=' . $invoice['iid'] . '&token=' . $validtoken); ?>&d=1"><?php echo $this->lang->line('PDF Download') ?></a>

                                </div>
                            </div>
                            <a href="<?php echo $link; ?>" class="btn btn-primary mb-1"><i
                                        class="fa fa-globe"></i> <?php echo $this->lang->line('Preview') ?>
                            </a> 



							<a href="<?= base_url("billing/recievchalan?id=".$invoice['iid']); ?>" class="btn btn-success mb-1"><i
                                        class="fa fa-sms"></i> Recieved Product
                            </a>
							
							
							<a href="<?= base_url("billing/chalanreport?id=".$invoice['iid']); ?>" class="btn btn-danger mb-1"><i
                                        class="fa fa-chart"></i> Challan Report
                            </a>

							<a href="javascript:void(0)"
   class="btn btn-blue mb-1 send-whatsapp"
   data-default-mobile="<?= $invoice['mobile'] ?>"
   data-default-name="<?= $invoice['name'] ?>"
   data-invoice-id="<?= $invoice['iid'] ?>">
   <i class="fa fa-whatsapp" aria-hidden="true"></i> Send Whatsapp
</a>

							
							
<a href="<?= base_url("chalan/print_all_barcodes/".$invoice['iid']); ?>" target="_blank" class="btn btn-primary mb-1">
    <i class="fa fa-globe"></i> All Barcode Print
</a>


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
                        <h2>Challan</h2>
                        <p class="pb-1"> <?php echo 'CN' . $invoice['tid'] . '</p>
                            <p class="pb-1">' . $this->lang->line('Reference') . ':' . $invoice['refer'] . '</p>'; ?>
                        <ul class="px-0 list-unstyled">
                            <li><?php echo $this->lang->line('Gross Amount') ?></li>
                            <li class="lead text-bold-600"><?php echo amountExchange($invoice['total'], 0, $this->aauth->get_user()->loc) ?></li>
                        </ul>
                    </div>
                </div>
                <!--/ Invoice Company Details -->

                <!-- Invoice Customer Details -->
                <div id="invoice-customer-details" class="row">
                    <div class="col-sm-12 text-xs-center text-md-left">
                        <p class="text-muted"><?php echo $this->lang->line('Bill To') ?></p>
                    </div>
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                        <ul class="px-0 list-unstyled">


                            <li class="text-bold-600"><a
                                        href="<?php echo base_url('customers/view?id=' . $invoice['cid']) ?>"><strong
                                            class="invoice_a"><?php echo $invoice['username'] . '</strong></a></li><li>' . $invoice['company'] . '</li><li>' . $invoice['address'] . ', ' . $invoice['city'] . '</li><li>' . $this->lang->line('Phone') . ': ' . $invoice['mobile'] . '</li><li>' . $this->lang->line('Email') . ': ' . $invoice['email'];
                                        if ($invoice['taxid']) echo '</li><li>' . $this->lang->line('Tax') . ' ID: ' . $invoice['taxid']
                                        ?>
                            </li>
                        </ul>

                    </div>
                    <div class="offset-md-3 col-md-3 col-sm-12 text-xs-center text-md-left">
                        <?php echo '<p><span class="text-muted">Challan Date :</span> ' . dateformat($invoice['invoicedate']) . '</p> <p><span class="text-muted">' . $this->lang->line('Due Date') . ' :</span> ' . dateformat($invoice['invoiceduedate']) . '</p>  <p><span class="text-muted">' . $this->lang->line('Terms') . ' :</span> ' . $invoice['termtit'] . '</p>';
                        ?>
                    </div>
                </div>
                <!--/ Invoice Customer Details -->
                <?php if ($invoice['proposal'] != '') {
                    echo '<div id="invoice-customer-details" class="row pt-2">
                        <div class="col-sm-12 text-xs-center text-md-left">';

                    echo '<h5>' . $this->lang->line('Proposal') . '</h5>';
                    echo '<p>' . $invoice['proposal'] . '</p>';


                    echo '   </div></div>';
                } ?>
                <!-- Invoice Items Details -->
                <div id="invoice-items-details" class="pt-2">
                    <div class="row">
                        <div class="table-responsive col-sm-12">
                            <table class="table table-striped">

                                <thead>
                               
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Article</th>
                                        <th>UOM</th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Qty') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Tax') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Discount') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Amount') ?></th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $c = 1;
                                    $sub_t = 0;

                                    foreach ($products as $row) {
                                     //   $sub_t += $row['price'] * $row['qty'];
									 
									 if (!empty($row['variant_sku'])) {
										 $article = $row['variant_sku'];
									 } elseif($row['product_article'] !=""){
										 $article= $row['product_article'];
									 }else{
										  $article= $row['article'];
									 }
									 $price_val = !empty($row['variant_price']) ? $row['variant_price'] : $row['price'];
                       echo '<tr>
    <th scope="row">' . $c . '</th>
    <td>' . $row['product'] . '</td>                           
    <td>' . $article . '</td>                           
    <td>' . $row['unit'] . '</td>                           
    <td>' . sprintf('%g', $row['qty']) . ' </td>
    <td>' . amountExchange($row['totaltax'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['tax']) . '%)</td>
    <td>' . amountExchange($row['totaldiscount'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['discount']) . $this->lang->line($invoice['format_discount']) . ')</td>
    <td>' . amountExchange($row['subtotal'], 0, $this->aauth->get_user()->loc) . '</td>
    <td>
        <a href="#" class="btn btn-primary mb-1 printSingleBtn"
           data-pid="' . $row['pid'] . '" data-article="' . $article . '"
           data-name="' . htmlspecialchars($row['product'], ENT_QUOTES) . '"
           data-qty="' . $row['qty'] . '"
           data-price="' . $price_val . '"
           data-uom="' . htmlspecialchars($row['unit'], ENT_QUOTES) . '"
           data-seller="' . htmlspecialchars($row['seller_name'] ?? '', ENT_QUOTES) . '">
           <i class="fa fa-globe"></i> Barcode Print
        </a>
    </td>
</tr>';


                                        echo '<tr><td colspan=5>' . $row['product_des'] . '</td></tr>';
                                        $c++;
                                    } ?>

                                    </tbody>
                             
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
                            <p class="lead"><?php echo $this->lang->line('Total Due') ?></p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td><?php echo $this->lang->line('Sub Total') ?></td>
                                        <td class="text-xs-right"> <?php echo amountExchange($sub_t, 0, $this->aauth->get_user()->loc) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('Tax') ?></td>
                                        <td class="text-xs-right"><?php echo amountExchange($invoice['tax'], 0, $this->aauth->get_user()->loc) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('Discount') ?></td>
                                        <td class="text-xs-right"><?php echo amountExchange($invoice['discount'], 0, $this->aauth->get_user()->loc) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $this->lang->line('Shipping') ?></td>
                                        <td class="text-xs-right"><?php echo amountExchange($invoice['shipping'], 0, $this->aauth->get_user()->loc) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-bold-800"><?php echo $this->lang->line('Total') ?></td>
                                        <td class="text-bold-800 text-xs-right"> <?php echo amountExchange($invoice['total'], 0, $this->aauth->get_user()->loc) ?></td>
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

                <!-- Invoice Footer -->

                <div id="invoice-footer">


                    <div class="row">

                        <div class="col-md-7 col-sm-12">

                            <h6><?php echo $this->lang->line('Terms & Condition') ?></h6>
                            <p> <?php

                                echo '<strong>' . $invoice['termtit'] . '</strong><br>' . $invoice['terms'];
                                ?></p>
                        </div>

                    </div>

                </div>
                <!--/ Invoice Footer -->
                <hr>
                <pre><?php echo $this->lang->line('Public Access URL') ?>: <?php
                    echo $link ?></pre>

                <div class="row">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('Files') ?></th>


                        </tr>
                        </thead>
                        <tbody id="activity">
                        <?php foreach ($attach as $row) {

                            echo '<tr><td><a data-url="' . base_url() . 'quote/file_handling?op=delete&name=' . $row['col1'] . '&invoice=' . $invoice['iid'] . '" class="aj_delete"><i class="btn-danger btn-lg fa fa-trash"></i></a> <a class="n_item" href="' . base_url() . 'userfiles/attach/' . $row['col1'] . '"> ' . $row['col1'] . ' </a></td></tr>';
                        } ?>

                        </tbody>
                    </table>
                </div>
                <div class="card">
                    <pre>Allowed: gif, jpeg, png, docx, docs, txt, pdf, xls </pre>
                    <br>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <div class="btn btn-success fileinput-button display-block">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Select files...</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileupload" type="file" name="files[]" multiple>
                    </div>
                </div>
                <!-- The global progress bar -->
                <div id="progress" class="progress">
                    <div class="progress-bar progress-bar-success"></div>
                </div>
                <!-- The container for the uploaded files -->
                <table id="files" class="files"></table>
                <br>

            </div>
        </div>
    </div>
</div>


<div id="barcodePopup" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= base_url('chalan/print_single_barcode'); ?>" target="_blank">
        <div class="modal-header">
          <h4 class="modal-title" id="productName"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="pid" id="productId">
          <input type="hidden" name="article" id="article">
          <input type="hidden" name="name" id="productNameInput">
          <input type="hidden" name="price" id="productPriceInput">
          <input type="hidden" name="uom" id="productUomInput">
          <input type="hidden" name="seller" id="productSellerInput">
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="qty" id="productQty" class="form-control" min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Print</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal HTML -->

<div id="pop_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Change Status') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="form_model">


                    <div class="row">
                        <div class="col mb-1"><label
                                    for="pmethod"><?php echo $this->lang->line('Mark As') ?></label>
                            <select name="status" class="form-control mb-1">
                                <option value="pending"><?php echo $this->lang->line('Pending') ?></option>
                                <option value="accepted"><?php echo $this->lang->line('Accepted') ?></option>
                                <option value="rejected"><?php echo $this->lang->line('Rejected') ?></option>
                            </select>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="<?php echo $invoice['iid'] ?>">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                        <input type="hidden" id="action-url" value="quote/update_status">
                        <button type="button" class="btn btn-primary"
                                id="submit_model"><?php echo $this->lang->line('Change Status') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="pop_model2" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Change Status') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="form_model2">


                    <div class="row">
                        <div class="col mb-1">Convert Challan as invoice


                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="<?php echo $invoice['iid'] ?>">
                         <input type="hidden" class="form-control required"
                               name="type" id="type" value="0">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                        <input type="hidden" id="action-url" value="chalan/convert">
                        <button type="button" class="btn btn-primary"
                                id="submit_model2"><?php echo $this->lang->line('Yes') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="pop_model3" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Change Status') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="form_model3">


                    <div class="row">
                        <div class="col mb-1"><?php echo $this->lang->line('Convert to Purchase') ?>


                        </div>
                    </div>
   <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Supplier') ?> </label>
                                        <input type="text" class="form-control" name="cst" id="supplier-box"
                                               placeholder="Enter Supplier Name or Mobile Number to search"
                                               autocomplete="off"/>

                                        <div id="supplier-box-result"></div>
                                    </div>

                                </div>
                    <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Supplier Details') ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                                        <div id="customer_name"></div>
                                    </div>
                                    <div class="clientinfo">

                                        <div id="customer_address1"></div>
                                    </div></div>
                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="<?php echo $invoice['iid'] ?>">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                        <input type="hidden" id="action-url" value="chalan/convert_po">
                        <button type="button" class="btn btn-primary"
                                id="submit_model3"><?php echo $this->lang->line('Yes') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal HTML -->
<div id="sendEmail" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Email</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div id="request">
                <div id="ballsWaveG">
                    <div id="ballsWaveG_1" class="ballsWaveG"></div>
                    <div id="ballsWaveG_2" class="ballsWaveG"></div>
                    <div id="ballsWaveG_3" class="ballsWaveG"></div>
                    <div id="ballsWaveG_4" class="ballsWaveG"></div>
                    <div id="ballsWaveG_5" class="ballsWaveG"></div>
                    <div id="ballsWaveG_6" class="ballsWaveG"></div>
                    <div id="ballsWaveG_7" class="ballsWaveG"></div>
                    <div id="ballsWaveG_8" class="ballsWaveG"></div>
                </div>
            </div>
            <div class="modal-body" id="emailbody" style="display: none;">
                <form id="sendbill">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="icon-envelope-o"
                                                                     aria-hidden="true"></span></div>
                                <input type="text" class="form-control" placeholder="Email" name="mailtoc"
                                       value="<?php echo $invoice['email'] ?>">
                            </div>

                        </div>

                    </div>


                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Customer Name') ?></label>
                            <input type="text" class="form-control"
                                   name="customername" value="<?php echo $invoice['name'] ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Subject') ?></label>
                            <input type="text" class="form-control"
                                   name="subject" id="subject">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Message') ?></label>
                            <textarea name="text" class="summernote" id="contents" title="Contents"></textarea></div>
                    </div>

                    <input type="hidden" class="form-control"
                           id="invoiceid" name="tid" value="<?php echo $invoice['iid'] ?>">
                    <input type="hidden" class="form-control"
                           id="emailtype" value="">


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                <button type="button" class="btn btn-primary"
                        id="sendM"><?php echo $this->lang->line('Send') ?></button>
            </div>
        </div>
    </div>
</div>
<!--sms-->
<!-- Modal HTML -->
<div id="sendSMS" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Send'); ?> SMS</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div id="request_sms">
                <div id="ballsWaveG1">
                    <div id="ballsWaveG_1" class="ballsWaveG"></div>
                    <div id="ballsWaveG_2" class="ballsWaveG"></div>
                    <div id="ballsWaveG_3" class="ballsWaveG"></div>
                    <div id="ballsWaveG_4" class="ballsWaveG"></div>
                    <div id="ballsWaveG_5" class="ballsWaveG"></div>
                    <div id="ballsWaveG_6" class="ballsWaveG"></div>
                    <div id="ballsWaveG_7" class="ballsWaveG"></div>
                    <div id="ballsWaveG_8" class="ballsWaveG"></div>
                </div>
            </div>
            <div class="modal-body" id="smsbody" style="display: none;">
                <form id="sendsms">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="icon-envelope-o"
                                                                     aria-hidden="true"></span></div>
                                <input type="text" class="form-control" placeholder="SMS" name="mobile"
                                       value="<?php echo $invoice['phone'] ?>">
                            </div>

                        </div>

                    </div>


                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Customer Name'); ?></label>
                            <input type="text" class="form-control"
                                   value="<?php echo $invoice['name'] ?>"></div>
                    </div>

                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Message'); ?></label>
                            <textarea class="form-control" name="text_message" id="sms_tem" title="Contents"
                                      rows="3"></textarea></div>
                    </div>


                    <input type="hidden" class="form-control"
                           id="smstype" value="">


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
                <button type="button" class="btn btn-primary"
                        id="submitSMS"><?php echo $this->lang->line('Send'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="whatsappModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="whatsappForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Whatsapp</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="invoice_id" id="invoice_id">

          <!-- Default number -->
          <div class="form-group">
            <label>Default Name & Mobile</label>
            <div class="input-group">
              <input type="text" class="form-control" id="default_name" readonly>
              <input type="text" class="form-control" id="default_mobile" readonly>
              <div class="input-group-append">
                <div class="input-group-text">
                  <input type="checkbox" id="skip_default">
                </div>
              </div>
            </div>
            <small>Tick to skip sending to default number</small>
          </div>

          <!-- New number (always visible) -->
          <div class="form-group">
            <label>New Name & Mobile (optional)</label>
            <input type="text" class="form-control mb-2" name="new_name" placeholder="Name">
            <input type="text" class="form-control" name="new_mobile" placeholder="Mobile Number">
            <small>If you enter a new number, message will be sent there as well.</small>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Send</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script src="<?php echo assets_url('assets/myjs/jquery.ui.widget.js') ?>"></script>
<script src="<?php echo assets_url('assets/myjs/jquery.fileupload.js') ?>"></script>
<script>
$(document).ready(function(){

  // Open modal with default data
  $(document).on('click', '.send-whatsapp', function(){
    $('#invoice_id').val($(this).data('invoice-id'));
    $('#default_mobile').val($(this).data('default-mobile'));
    $('#default_name').val($(this).data('default-name'));
    $('#skip_default').prop('checked', false);
    $('input[name="new_name"]').val('');
    $('input[name="new_mobile"]').val('');
    $('#whatsappModal').modal('show');
  });

  // Form submit via AJAX
  $('#whatsappForm').submit(function(e){
    e.preventDefault();

    let sendNumbers = [];

    // If skip default is NOT checked, add default number
    if (!$('#skip_default').is(':checked') && $('#default_mobile').val().trim() !== '') {
      sendNumbers.push({
        name: $('#default_name').val(),
        mobile: $('#default_mobile').val()
      });
    }

    // If new number is entered
    if ($('input[name="new_mobile"]').val().trim() !== '') {
      sendNumbers.push({
        name: $('input[name="new_name"]').val(),
        mobile: $('input[name="new_mobile"]').val()
      });
    }

    if (sendNumbers.length === 0) {
      alert('Please select at least one recipient.');
      return;
    }

    $.ajax({
      url: '<?= base_url("billing/send_whatsapp") ?>',
      type: 'POST',
      data: {
        invoice_id: $('#invoice_id').val(),
        recipients: sendNumbers
      },
      dataType: 'json',
      success: function(res){
        if (res.status === 'success') {
          alert('Message sent!');
          $('#whatsappModal').modal('hide');
        } else {
          alert('Error: ' + res.message);
        }
      }
    });
  });

});



$(document).on('click', '.printSingleBtn', function() {
    $('#article').val($(this).data('article'));
    $('#productId').val($(this).data('pid'));
    $('#productName').text($(this).data('name'));
    $('#productNameInput').val($(this).data('name'));
    $('#productQty').val($(this).data('qty'));
    $('#productPriceInput').val($(this).data('price'));
    $('#productUomInput').val($(this).data('uom'));
    $('#productSellerInput').val($(this).data('seller'));
    $('#barcodePopup').modal('show');
});

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
