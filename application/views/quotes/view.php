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
                        $is_chalan_doc = !empty($is_chalan);
                        $validtoken = hash_hmac('ripemd160', ($is_chalan_doc ? 'c' : 'q') . $invoice['iid'], $this->config->item('encryption_key'));
                        $link = $is_chalan_doc ? '' : base_url('billing/quoteview?id=' . $invoice['iid'] . '&token=' . $validtoken);
                        $print_url = $is_chalan_doc ? base_url('billing/printchalan?id=' . $invoice['iid']) : base_url('billing/printquote?id=' . $invoice['iid'] . '&token=' . $validtoken);
                        $update_status_url = $is_chalan_doc ? 'chalan/update_status' : 'quote/update_status';
                        $file_handling_url = $is_chalan_doc ? 'chalan/file_handling' : 'quote/file_handling';
                        $doc_label = $is_chalan_doc ? 'Chalan' : $this->lang->line('Quote');
                        ?>
                        <div class="title-action">

                            <a href="<?php echo 'edit?id=' . $invoice['iid']; ?>" class="btn btn-warning mb-1"><i
                                        class="fa fa-pencil"></i> <?php echo $is_chalan_doc ? 'Edit Chalan' : $this->lang->line('Edit Quote') ?> </a>

                            <a href="#pop_model" data-toggle="modal" data-remote="false"
                               class="btn btn-large btn-success mb-1" title="Change Status"
                            ><span class="fa fa-retweet"></span> <?php echo $this->lang->line('Change Status') ?> </a>

                            <?php if (empty($is_chalan_doc)) { ?>
                            <a href="#pop_model2" data-toggle="modal" data-remote="false"
                               class="btn btn-large btn-info mb-1" title="Convert to Invoice"
                            ><span class="fa fa-share"></span> <?php echo $this->lang->line('Convert to Invoice') ?>
                            </a>
                               <a href="#pop_model3" data-toggle="modal" data-remote="false"
                               class="btn btn-large btn-blue-grey mb-1" title="Convert to Purchase"
                            ><span class="fa fa-share"></span> <?php echo $this->lang->line('Convert to Purchase') ?>
                            </a>
                            <?php } ?>

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
                                            class="fa fa-print"></i> <?php echo $is_chalan_doc ? 'Print Chalan' : $this->lang->line('Print Quote') ?>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                       href="<?= $print_url; ?>"><?php echo $this->lang->line('Print') ?></a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                       href="<?= $print_url; ?>&d=1"><?php echo $this->lang->line('PDF Download') ?></a>

                                </div>
                            </div>
                            <?php if (empty($is_chalan_doc)) { ?>
                            <a href="<?php echo $link; ?>" class="btn btn-primary mb-1"><i
                                        class="fa fa-globe"></i> <?php echo $this->lang->line('Preview') ?>
                            </a>
                            <?php } ?>


                        </div>
                    </div>
                </div>

                <?php if ($invoice['multi'] > 0) {

                    echo '<div class="tag tag-info text-xs-center mt-2">' . $this->lang->line('Payment currency is different') . '</div>';
                }
                ?>

                <!-- Invoice Company Details -->
                <div id="invoice-company-details" class="row mt-2">
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                        <p></p>
                        <img src="<?php $loc = invoice_company_details($invoice['loc']);
                        echo base_url($loc['logo_path']) ?>"
                             class="img-responsive p-1 m-b-2" style="max-height: 120px;">
                        <p class="ml-2">
                            <strong><?= htmlspecialchars($loc['cname']) ?></strong><br>
                            <?php
                            $company_addr_bits = array_filter([
                                trim((string)$loc['address']),
                                trim((string)$loc['city']),
                                trim((string)$loc['region'])
                            ]);
                            $company_addr = implode(', ', $company_addr_bits);
                            if ($company_addr !== '') echo htmlspecialchars($company_addr) . '<br>';

                            $company_country_bits = array_filter([
                                trim((string)$loc['country']),
                                trim((string)$loc['postbox'])
                            ]);
                            $company_country = implode(', ', $company_country_bits);
                            if ($company_country !== '') echo htmlspecialchars($company_country) . '<br>';

                            if (!empty($loc['phone'])) echo $this->lang->line('Phone') . ': ' . htmlspecialchars($loc['phone']) . '<br>';
                            if (!empty($loc['email'])) echo $this->lang->line('Email') . ': ' . htmlspecialchars($loc['email']) . '<br>';
                            if (!empty($loc['taxid'])) echo $this->lang->line('Tax') . ' ID: ' . htmlspecialchars($loc['taxid']) . '<br>';
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                        <h2><?php echo $doc_label ?></h2>
                        <p class="pb-1 text-bold-600"> <?php echo $is_chalan_doc ? 'CH#' . $invoice['tid'] : prefix(1) . $invoice['tid']; ?></p>
                        <p class="pb-1"><?= $this->lang->line('Reference') ?>: <?= $invoice['refer'] ?></p>
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
                            <?php
                            $customer_name = !empty($invoice['name']) ? $invoice['name'] : (!empty($invoice['username']) ? $invoice['username'] : '');
                            $customer_phone = !empty($invoice['phone']) ? $invoice['phone'] : (!empty($invoice['mobile']) ? $invoice['mobile'] : '');
                            $customer_email = !empty($invoice['email']) ? $invoice['email'] : '';
                            $customer_company = !empty($invoice['company']) ? $invoice['company'] : '';
                            $customer_taxid = !empty($invoice['taxid']) ? $invoice['taxid'] : '';

                            $address_bits = array_filter([
                                trim((string)(!empty($invoice['address']) ? $invoice['address'] : '')),
                                trim((string)(!empty($invoice['street']) ? $invoice['street'] : '')),
                                trim((string)(!empty($invoice['area']) ? $invoice['area'] : '')),
                                trim((string)(!empty($invoice['city']) ? $invoice['city'] : '')),
                                trim((string)(!empty($invoice['region']) ? $invoice['region'] : ''))
                            ]);
                            $address_line = implode(', ', $address_bits);

                            $country_bits = array_filter([
                                trim((string)(!empty($invoice['country']) ? $invoice['country'] : '')),
                                trim((string)(!empty($invoice['postbox']) ? $invoice['postbox'] : '')),
                                trim((string)(!empty($invoice['pincode']) ? $invoice['pincode'] : ''))
                            ]);
                            $country_line = implode(', ', $country_bits);
                            ?>
                            <?php if ($customer_name !== '') { ?>
                            <li class="text-bold-600">
                                <a href="<?php echo base_url('customers/view?id=' . $invoice['cid']) ?>">
                                    <strong class="invoice_a"><?php echo htmlspecialchars($customer_name); ?></strong>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if ($customer_company !== '') { ?><li><?php echo htmlspecialchars($customer_company); ?></li><?php } ?>
                            <?php if ($address_line !== '') { ?><li><?php echo htmlspecialchars($address_line); ?></li><?php } ?>
                            <?php if ($country_line !== '') { ?><li><?php echo htmlspecialchars($country_line); ?></li><?php } ?>
                            <?php if ($customer_phone !== '') { ?><li><?php echo $this->lang->line('Phone') . ': ' . htmlspecialchars($customer_phone); ?></li><?php } ?>
                            <?php if ($customer_email !== '') { ?><li><?php echo $this->lang->line('Email') . ': ' . htmlspecialchars($customer_email); ?></li><?php } ?>
                            <?php if ($customer_taxid !== '') { ?><li><?php echo $this->lang->line('Tax') . ' ID: ' . htmlspecialchars($customer_taxid); ?></li><?php } ?>
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                        <?php
                        $date_label = $is_chalan_doc ? 'Chalan Date' : $this->lang->line('Quote Date');
                        $due_label = $is_chalan_doc ? 'Validity Date' : $this->lang->line('Due Date');
                        echo '<p class="mb-1"><span class="text-muted">' . $date_label . ' :</span> ' . dateformat($invoice['invoicedate']) . '</p>';
                        echo '<p class="mb-1"><span class="text-muted">' . $due_label . ' :</span> ' . dateformat($invoice['invoiceduedate']) . '</p>';
                        echo '<p class="mb-1"><span class="text-muted">' . $this->lang->line('Terms') . ' :</span> ' . htmlspecialchars((string)$invoice['termtit']) . '</p>';
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
                                <?php if ($invoice['taxstatus'] == 'cgst'){ ?>

                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('Description') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('HSN') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('Rate') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('Qty') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('Discount') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('CGST') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('SGST') ?></th>
                                    <th class="text-xs-left"><?php echo $this->lang->line('Amount') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $c = 1;
                                $sub_t = 0;

                                foreach ($products as $row) {
                                    $sub_t += $row['price'] * $row['qty'];
                                    $gst = $row['totaltax'] / 2;
                                    $rate = $row['tax'] / 2;
                                    echo '<tr>
<th scope="row">' . $c . '</th>
                            <td>' . $row['product'] . '</td> 
                            <td>' . $row['code'] . '</td>                          
                            <td>' . amountExchange($row['price'], 0, $this->aauth->get_user()->loc) . '</td>
                             <td>' . amountFormat_general($row['qty']) . $row['unit'] . '</td>
                              <td>' . amountExchange($row['totaldiscount'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['discount']) . $this->lang->line($invoice['format_discount']) . ')</td>
                            <td>' . amountExchange($gst, 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($rate) . '%)</td>
                             <td>' . amountExchange($gst, 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($rate) . '%)</td>                           
                            <td>' . amountExchange($row['subtotal'], 0, $this->aauth->get_user()->loc) . '</td>
                        </tr>';

                                    echo '<tr><td colspan=5>' . $row['product_des'] . '</td></tr>';
                                    $c++;
                                } ?>

                                </tbody>
                                <?php

                                } elseif ($invoice['taxstatus'] == 'igst') {
                                    ?>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('Description') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('HSN') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Rate') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Qty') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Discount') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('IGST') ?></th>

                                        <th class="text-xs-left"><?php echo $this->lang->line('Amount') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $c = 1;
                                    $sub_t = 0;

                                    foreach ($products as $row) {
                                        $sub_t += $row['price'] * $row['qty'];

                                        echo '<tr>
<th scope="row">' . $c . '</th>
                            <td>' . $row['product'] . '</td> 
                            <td>' . $row['code'] . '</td>                          
                            <td>' . amountExchange($row['price'], 0, $this->aauth->get_user()->loc) . '</td>
                             <td>' . amountFormat_general($row['qty']) . $row['unit'] . '</td>
                              <td>' . amountExchange($row['totaldiscount'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['discount']) . $this->lang->line($invoice['format_discount']) . ')</td>
                            <td>' . amountExchange($row['totaltax'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['tax']) . '%)</td>
                                            
                            <td>' . amountExchange($row['subtotal'], 0, $this->aauth->get_user()->loc) . '</td>
                        </tr>';

                                        echo '<tr><td colspan=5>' . $row['product_des'] . '</td></tr>';
                                        $c++;
                                    } ?>

                                    </tbody>
                                    <?php
                                } else {
                                    ?>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('Description') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Rate') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Qty') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Tax') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Discount') ?></th>
                                        <th class="text-xs-left"><?php echo $this->lang->line('Amount') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $c = 1;
                                    $sub_t = 0;

                                    foreach ($products as $row) {
                                        $sub_t += $row['price'] * $row['qty'];
                                        echo '<tr>
<th scope="row">' . $c . '</th>
                            <td>' . $row['product'] . '</td>                           
                            <td>' . amountExchange($row['price'], 0, $this->aauth->get_user()->loc) . '</td>
                             <td>' . amountFormat_general($row['qty']) . $row['unit'] . '</td>
                            <td>' . amountExchange($row['totaltax'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['tax']) . '%)</td>
                            <td>' . amountExchange($row['totaldiscount'], 0, $this->aauth->get_user()->loc) . ' (' . amountFormat_s($row['discount']) . $this->lang->line($invoice['format_discount']) . ')</td>
                            <td>' . amountExchange($row['subtotal'], 0, $this->aauth->get_user()->loc) . '</td>
                        </tr>';

                                        echo '<tr><td colspan=5>' . $row['product_des'] . '</td></tr>';
                                        $c++;
                                    } ?>

                                    </tbody>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <p></p>
                    <?php if (!empty($is_chalan)) { ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Barcodes</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="chalan-barcode-items">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th>Seller</th>
                                            <th class="text-xs-right">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $bc = 1;
                                        foreach ($products as $prow) {
                                            $pid = isset($prow['pid']) ? (int)$prow['pid'] : 0;
                                            $pname = isset($prow['product']) ? (string)$prow['product'] : '';
                                            $seller = isset($seller_by_pid[$pid]) ? $seller_by_pid[$pid] : [];
                                            $seller_name = isset($seller['store_name']) ? trim((string)$seller['store_name']) : '';
                                            $seller_addr_bits = array_filter([
                                                isset($seller['address']) ? trim((string)$seller['address']) : '',
                                                isset($seller['street']) ? trim((string)$seller['street']) : '',
                                                isset($seller['area']) ? trim((string)$seller['area']) : '',
                                                isset($seller['city']) ? trim((string)$seller['city']) : '',
                                                isset($seller['pincode']) ? trim((string)$seller['pincode']) : ''
                                            ]);
                                            $seller_addr = implode(', ', $seller_addr_bits);

                                            $all_url = base_url('chalan/print_variant_barcodes?invoice_id=' . (int)$invoice['iid'] . '&pid=' . $pid . '&all=1&qty=1');
                                            echo '<tr>
                                                <td>' . $bc . '</td>
                                                <td>' . htmlspecialchars($pname) . '</td>
                                                <td>' . htmlspecialchars($seller_name) . ($seller_addr !== '' ? '<br>' . htmlspecialchars($seller_addr) : '') . '</td>
                                                <td class="text-xs-right">
                                                    <a class="btn btn-info btn-sm" target="_blank" href="' . htmlspecialchars($all_url, ENT_QUOTES, 'UTF-8') . '">Print All</a>
                                                </td>
                                            </tr>';

                                            $variants = isset($barcode_variants_by_pid[$pid]) ? $barcode_variants_by_pid[$pid] : [];
                                            if (!empty($variants)) {
                                                echo '<tr><td colspan="4"><div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Variant</th><th>Article</th><th>UOM</th><th>Weight</th><th>Price</th><th>Discount Price</th><th>Qty</th><th class="text-xs-right">Action</th></tr></thead><tbody>';
                                                foreach ($variants as $v) {
                                                    $vname = isset($v['print_name']) ? (string)$v['print_name'] : $pname;
                                                    $varticle = '';
                                                    if (!empty($v['article_no'])) $varticle = (string)$v['article_no'];
                                                    elseif (!empty($v['hpnumber'])) $varticle = (string)$v['hpnumber'];
                                                    $vuom = isset($v['UOM']) ? (string)$v['UOM'] : '';
                                                    $vweight = isset($v['weight']) ? (string)$v['weight'] : '';
                                                    $vprice = isset($v['price']) ? (float)$v['price'] : 0.0;
                                                    $vdisc = isset($v['discount_price']) ? (float)$v['discount_price'] : 0.0;
                                                    $vqty = 1;
                                                    $single_url = base_url('chalan/print_variant_barcodes?invoice_id=' . (int)$invoice['iid'] . '&pid=' . $pid . '&article=' . rawurlencode($varticle) . '&name=' . rawurlencode($vname) . '&qty=' . $vqty);
                                                    echo '<tr>
                                                        <td>' . htmlspecialchars($vname) . '</td>
                                                        <td>' . htmlspecialchars($varticle) . '</td>
                                                        <td>' . htmlspecialchars($vuom) . '</td>
                                                        <td>' . htmlspecialchars($vweight) . '</td>
                                                        <td>' . ($vprice > 0 ? number_format($vprice, 2) : '') . '</td>
                                                        <td>' . ($vdisc > 0 ? number_format($vdisc, 2) : '') . '</td>
                                                        <td>' . $vqty . '</td>
                                                        <td class="text-xs-right"><a class="btn btn-info btn-sm" target="_blank" href="' . htmlspecialchars($single_url, ENT_QUOTES, 'UTF-8') . '">Print</a></td>
                                                    </tr>';
                                                }
                                                echo '</tbody></table></div></td></tr>';
                                            }
                                            $bc++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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
                                <?php
                                $sign = isset($employee['sign']) ? (string)$employee['sign'] : '';
                                $sign_path = $sign !== '' ? FCPATH . 'userfiles/employee_sign/' . $sign : '';
                                if ($sign !== '' && $sign_path !== '' && file_exists($sign_path)) {
                                    echo '<img src="' . base_url('userfiles/employee_sign/' . $sign) . '" alt="signature" class="height-100"/>';
                                }
                                echo '<h6>(' . htmlspecialchars((string)$employee['name']) . ')</h6>';
                                echo '<p class="text-muted">' . user_role($employee['roleid']) . '</p>';
                                ?>
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
                <?php if (empty($is_chalan_doc)) { ?>
                    <pre><?php echo $this->lang->line('Public Access URL') ?>: <?php echo $link ?></pre>
                <?php } ?>

                <div class="row">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('Files') ?></th>


                        </tr>
                        </thead>
                        <tbody id="activity">
                        <?php foreach ($attach as $row) {

                            echo '<tr><td><a data-url="' . base_url() . $file_handling_url . '?op=delete&name=' . $row['col1'] . '&invoice=' . $invoice['iid'] . '" class="aj_delete"><i class="btn-danger btn-lg fa fa-trash"></i></a> <a class="n_item" href="' . base_url() . 'userfiles/attach/' . $row['col1'] . '"> ' . $row['col1'] . ' </a></td></tr>';
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

<?php if (!empty($is_chalan) && false) { ?>
    <div id="chalanBarcodeModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Barcode Print</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="chalanBarcodeVariants">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="chalanBarcodeSelectAll"></th>
                                <th>Variant</th>
                                <th>Article</th>
                                <th>UOM</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Discount Price</th>
                                <th>Seller</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <input type="hidden" id="chalanBarcodeInvoiceId" value="">
                    <input type="hidden" id="chalanBarcodePid" value="">
                    <input type="hidden" id="chalanBarcodeName" value="">
                    <input type="hidden" id="chalanBarcodeArticle" value="">
                    <input type="hidden" id="chalanBarcodeUom" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="chalanBarcodePrintBtn">Print</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        (function () {
            function dsBuildPostForm(url, fields, target) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.target = target || '_blank';
                for (var k in fields) {
                    if (!Object.prototype.hasOwnProperty.call(fields, k)) continue;
                    var v = fields[k];
                    if (Array.isArray(v)) {
                        for (var i = 0; i < v.length; i++) {
                            var inputArr = document.createElement('input');
                            inputArr.type = 'hidden';
                            inputArr.name = k + '[]';
                            inputArr.value = v[i];
                            form.appendChild(inputArr);
                        }
                    } else {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = k;
                        input.value = v;
                        form.appendChild(input);
                    }
                }
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }

            function dsSafeInt(v, def) {
                var n = parseInt(v, 10);
                if (!isFinite(n) || isNaN(n)) return def;
                return n;
            }

            function dsRenderVariants(rows, defaultQty) {
                var $tbody = $('#chalanBarcodeVariants tbody');
                $tbody.empty();

                if (!rows || !rows.length) {
                    var fallbackName = $('#chalanBarcodeName').val() || '';
                    var fallbackArticle = $('#chalanBarcodeArticle').val() || '';
                    var fallbackUom = $('#chalanBarcodeUom').val() || '';
                    if (!fallbackArticle) {
                        $tbody.append('<tr><td colspan="9" class="text-center">No variants found</td></tr>');
                        return;
                    }
                    rows = [{
                        print_name: fallbackName,
                        article_no: fallbackArticle,
                        UOM: fallbackUom,
                        weight: '',
                        price: 0,
                        discount_price: 0,
                        store_name: ''
                    }];
                }

                rows.forEach(function (r) {
                    var name = r.print_name || r.name || '';
                    var article = r.article_no || r.hpnumber || '';
                    var uom = r.UOM || r.uom || '';
                    var weight = r.weight || '';
                    var price = r.price || r.variant_price || 0;
                    var discountPrice = r.discount_price || r.variant_special_price || 0;
                    var seller = r.store_name || r.seller || '';
                    var qty = defaultQty || 1;
                    var rowHtml = '<tr>' +
                        '<td><input type="checkbox" class="chalanBarcodePick" checked></td>' +
                        '<td class="chalanBarcodeVariantName">' + $('<div>').text(name).html() + '</td>' +
                        '<td><span class="chalanBarcodeArticle">' + $('<div>').text(article).html() + '</span></td>' +
                        '<td>' + $('<div>').text(uom).html() + '</td>' +
                        '<td>' + $('<div>').text(weight).html() + '</td>' +
                        '<td>' + $('<div>').text(price ? Number(price).toFixed(2) : '').html() + '</td>' +
                        '<td>' + $('<div>').text(discountPrice ? Number(discountPrice).toFixed(2) : '').html() + '</td>' +
                        '<td>' + $('<div>').text(seller).html() + '</td>' +
                        '<td><input type="text" class="form-control form-control-sm chalanBarcodeQty" value="' + qty + '"></td>' +
                        '</tr>';
                    $tbody.append(rowHtml);
                });
            }

            $(document).on('click', '.chalan-barcode-btn', function () {
                var pid = $(this).data('pid');
                var name = $(this).data('name') || '';
                var article = $(this).data('article') || '';
                var uom = $(this).data('uom') || '';
                var invoiceId = $(this).data('invoice');
                var qty = dsSafeInt($(this).data('qty'), 1);

                $('#chalanBarcodePid').val(pid);
                $('#chalanBarcodeName').val(name);
                $('#chalanBarcodeArticle').val(article);
                $('#chalanBarcodeUom').val(uom);
                $('#chalanBarcodeInvoiceId').val(invoiceId);
                $('#chalanBarcodeSelectAll').prop('checked', true);
                $('#chalanBarcodeVariants tbody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');
                $('#chalanBarcodeModal').modal('show');

                $.ajax({
                    url: baseurl + 'chalan/get_variation_articles',
                    type: 'POST',
                    dataType: 'json',
                    data: {product_id: pid, [crsf_token]: crsf_hash},
                    success: function (data) {
                        dsRenderVariants(data, qty);
                    },
                    error: function () {
                        dsRenderVariants([], qty);
                    }
                });
            });

            $(document).on('change', '#chalanBarcodeSelectAll', function () {
                var checked = $(this).prop('checked');
                $('.chalanBarcodePick').prop('checked', checked);
            });

            $(document).on('click', '#chalanBarcodePrintBtn', function () {
                var invoiceId = $('#chalanBarcodeInvoiceId').val();
                var pid = $('#chalanBarcodePid').val();
                var baseName = $('#chalanBarcodeName').val();

                var pids = [];
                var articles = [];
                var names = [];
                var qtys = [];

                $('#chalanBarcodeVariants tbody tr').each(function () {
                    var $tr = $(this);
                    var pick = $tr.find('.chalanBarcodePick').prop('checked');
                    if (!pick) return;
                    var article = ($tr.find('.chalanBarcodeArticle').text() || '').trim();
                    if (!article) return;
                    var vname = ($tr.find('.chalanBarcodeVariantName').text() || '').trim();
                    var qty = dsSafeInt($tr.find('.chalanBarcodeQty').val(), 1);
                    if (qty < 1) qty = 1;
                    pids.push(pid);
                    articles.push(article);
                    names.push(vname || baseName);
                    qtys.push(qty);
                });

                if (!articles.length) {
                    return;
                }

                var fields = {
                    invoice_id: invoiceId,
                    pid: pids,
                    article: articles,
                    name: names,
                    qty: qtys
                };
                fields[crsf_token] = crsf_hash;
                dsBuildPostForm(baseurl + 'chalan/print_variant_barcodes', fields, '_blank');
            });
        })();
    </script>
<?php } ?>


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
                        <input type="hidden" id="action-url" value="<?php echo $update_status_url; ?>">
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
                        <div class="col mb-1"><?php echo $this->lang->line('quote as invoice') ?>


                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="<?php echo $invoice['iid'] ?>">
                         <input type="hidden" class="form-control required"
                               name="type" id="type" value="0">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                        <input type="hidden" id="action-url" value="quote/convert">
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
                        <input type="hidden" id="action-url" value="quote/convert_po">
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
<script src="<?php echo assets_url('assets/myjs/jquery.ui.widget.js') ?>"></script>
<script src="<?php echo assets_url('assets/myjs/jquery.fileupload.js') ?>"></script>
<script>
    /*jslint unparam: true */
    /*global window, $ */
    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = '<?php echo base_url() ?><?php echo $file_handling_url ?>?id=<?php echo $invoice['iid'] ?>';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            formData: {'<?=$this->security->get_csrf_token_name()?>': crsf_hash},
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('#files').append('<tr><td><a data-url="<?php echo base_url() ?><?php echo $file_handling_url ?>?op=delete&name=' + file.name + '&invoice=<?php echo $invoice['iid'] ?>" class="aj_delete red"><i class="btn-sm fa fa-trash"></i></a> ' + file.name + ' </td></tr>');
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
