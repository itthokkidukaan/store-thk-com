<?php
$invoice_loc = isset($invoice['loc']) ? $invoice['loc'] : 0;
$loc = $invoice_loc ? location($invoice_loc) : [];
?>

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <?php if (empty($invoice)) { ?>
                <div class="alert alert-warning mb-0">Quote not found.</div>
            <?php } else { ?>
                <?php if (!empty($invoice['multi']) && (int)$invoice['multi'] > 0) { ?>
                    <div class="alert alert-info">Payment currency is different.</div>
                <?php } ?>

                <div class="row align-items-start">
                    <div class="col-md-7">
                        <?php if (!empty($loc['logo'])) { ?>
                            <img src="<?= base_url('userfiles/company/' . $loc['logo']) ?>" alt="Logo" style="max-height: 90px;">
                        <?php } ?>
                        <div class="mt-2 font-weight-bold"><?= isset($loc['cname']) ? $loc['cname'] : '' ?></div>
                        <div class="text-muted small">
                            <?= isset($loc['address']) ? $loc['address'] : '' ?>
                            <?= isset($loc['city']) ? ', ' . $loc['city'] : '' ?>
                            <?= isset($loc['country']) ? ', ' . $loc['country'] : '' ?>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                        <h3 class="mb-1">Quote</h3>
                        <div class="text-muted">#<?= isset($invoice['tid']) ? $invoice['tid'] : '' ?></div>
                        <?php if (!empty($invoice['refer'])) { ?>
                            <div class="text-muted">Reference: <?= $invoice['refer'] ?></div>
                        <?php } ?>
                        <div class="mt-2">
                            <div><span class="text-muted">Quote Date:</span> <?= isset($invoice['invoicedate']) ? dateformat($invoice['invoicedate']) : '' ?></div>
                            <div><span class="text-muted">Due Date:</span> <?= isset($invoice['invoiceduedate']) ? dateformat($invoice['invoiceduedate']) : '' ?></div>
                            <?php if (!empty($invoice['termtit'])) { ?>
                                <div><span class="text-muted">Terms:</span> <?= $invoice['termtit'] ?></div>
                            <?php } ?>
                        </div>
                        <div class="mt-3">
                            <div class="text-muted">Gross Amount</div>
                            <div class="h5 mb-0"><?= amountExchange($invoice['total'], 0, $invoice_loc) ?></div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-7">
                        <div class="text-muted mb-1">Bill To</div>
                        <div class="font-weight-bold"><?= isset($invoice['name']) ? $invoice['name'] : '' ?></div>
                        <?php if (!empty($invoice['company'])) { ?><div><?= $invoice['company'] ?></div><?php } ?>
                        <div class="text-muted small">
                            <?= isset($invoice['address']) ? $invoice['address'] : '' ?>
                            <?= isset($invoice['city']) ? ', ' . $invoice['city'] : '' ?>
                            <?= isset($invoice['region']) ? ', ' . $invoice['region'] : '' ?>
                            <?= isset($invoice['country']) ? ', ' . $invoice['country'] : '' ?>
                            <?= isset($invoice['postbox']) ? ', ' . $invoice['postbox'] : '' ?>
                        </div>
                        <div class="text-muted small">
                            <?php if (!empty($invoice['phone'])) { ?>Phone: <?= $invoice['phone'] ?><?php } ?>
                            <?php if (!empty($invoice['email'])) { ?><?= !empty($invoice['phone']) ? ' | ' : '' ?>Email: <?= $invoice['email'] ?><?php } ?>
                        </div>
                        <?php if (!empty($invoice['taxid'])) { ?>
                            <div class="text-muted small">Tax ID: <?= $invoice['taxid'] ?></div>
                        <?php } ?>
                    </div>
                </div>

                <?php if (!empty($invoice['proposal'])) { ?>
                    <hr>
                    <div>
                        <div class="font-weight-bold mb-1">Proposal</div>
                        <div><?= $invoice['proposal'] ?></div>
                    </div>
                <?php } ?>

                <hr>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                        <?php if (isset($invoice['taxstatus']) && $invoice['taxstatus'] == 'cgst') { ?>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Description</th>
                                <th class="text-right">HSN</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">CGST</th>
                                <th class="text-right">SGST</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        <?php } elseif (isset($invoice['taxstatus']) && $invoice['taxstatus'] == 'igst') { ?>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Description</th>
                                <th class="text-right">HSN</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">IGST</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Description</th>
                                <th class="text-right">Rate</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        <?php } ?>
                        </thead>
                        <tbody>
                        <?php
                        $c = 1;
                        $products = isset($products) && is_array($products) ? $products : [];
                        foreach ($products as $row) {
                            if (isset($invoice['taxstatus']) && $invoice['taxstatus'] == 'cgst') {
                                $gst = (float)$row['totaltax'] / 2;
                                $rate = (float)$row['tax'] / 2;
                                ?>
                                <tr>
                                    <td><?= $c ?></td>
                                    <td><?= $row['product'] ?></td>
                                    <td class="text-right"><?= $row['code'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['price'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountFormat_general($row['qty']) . $row['unit'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['totaldiscount'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountExchange($gst, 0, $invoice_loc) ?> (<?= amountFormat_s($rate) ?>%)</td>
                                    <td class="text-right"><?= amountExchange($gst, 0, $invoice_loc) ?> (<?= amountFormat_s($rate) ?>%)</td>
                                    <td class="text-right"><?= amountExchange($row['subtotal'], 0, $invoice_loc) ?></td>
                                </tr>
                                <?php if (!empty($row['product_des'])) { ?>
                                    <tr><td></td><td colspan="8" class="text-muted small"><?= $row['product_des'] ?></td></tr>
                                <?php } ?>
                                <?php
                            } elseif (isset($invoice['taxstatus']) && $invoice['taxstatus'] == 'igst') {
                                ?>
                                <tr>
                                    <td><?= $c ?></td>
                                    <td><?= $row['product'] ?></td>
                                    <td class="text-right"><?= $row['code'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['price'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountFormat_general($row['qty']) . $row['unit'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['totaldiscount'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountExchange($row['totaltax'], 0, $invoice_loc) ?> (<?= amountFormat_s($row['tax']) ?>%)</td>
                                    <td class="text-right"><?= amountExchange($row['subtotal'], 0, $invoice_loc) ?></td>
                                </tr>
                                <?php if (!empty($row['product_des'])) { ?>
                                    <tr><td></td><td colspan="7" class="text-muted small"><?= $row['product_des'] ?></td></tr>
                                <?php } ?>
                                <?php
                            } else {
                                ?>
                                <tr>
                                    <td><?= $c ?></td>
                                    <td><?= $row['product'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['price'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountFormat_general($row['qty']) . $row['unit'] ?></td>
                                    <td class="text-right"><?= amountExchange($row['totaltax'], 0, $invoice_loc) ?> (<?= amountFormat_s($row['tax']) ?>%)</td>
                                    <td class="text-right"><?= amountExchange($row['totaldiscount'], 0, $invoice_loc) ?></td>
                                    <td class="text-right"><?= amountExchange($row['subtotal'], 0, $invoice_loc) ?></td>
                                </tr>
                                <?php if (!empty($row['product_des'])) { ?>
                                    <tr><td></td><td colspan="6" class="text-muted small"><?= $row['product_des'] ?></td></tr>
                                <?php } ?>
                                <?php
                            }
                            $c++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-3">
                    <div class="col-md-5">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Subtotal</td>
                                <td class="text-right"><?= amountExchange($invoice['subtotal'], 0, $invoice_loc) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax</td>
                                <td class="text-right"><?= amountExchange($invoice['tax'], 0, $invoice_loc) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount</td>
                                <td class="text-right"><?= amountExchange($invoice['discount'], 0, $invoice_loc) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Shipping</td>
                                <td class="text-right"><?= amountExchange($invoice['shipping'], 0, $invoice_loc) ?></td>
                            </tr>
                            <?php if (!empty($invoice['ship_tax'])) { ?>
                                <tr>
                                    <td class="text-muted">Shipping Tax</td>
                                    <td class="text-right"><?= amountExchange($invoice['ship_tax'], 0, $invoice_loc) ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td class="text-right"><?= amountExchange($invoice['total'], 0, $invoice_loc) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
