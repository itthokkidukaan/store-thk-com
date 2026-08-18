 <style>


/* Container */
.container {
    width: 100%;
    padding: 8mm;
}

/* Heading */
h1, h2, h3 {
    margin: 0;
    padding-bottom: 2mm;
    font-weight: bold;
    text-align: center;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5mm;
    font-size: 10px;
}

table th, table td {
    border: 0.4px solid #555;
    padding: 4px 6px;
    vertical-align: middle;
    text-align: center;
}

/* Table Head */
table thead th {
    background-color: #e0e0e0;
    font-weight: bold;
    font-size: 10.5px;
    color: #333;
    padding: 5px 8px;
}

/* Product Name Left align */
td.product-name {
    text-align: left;
}

/* Signature section */
.signature-section {
    margin-top: 20mm;
    text-align: center;
}

.signature {
    width: 30%;
    display: inline-block;
    font-size: 10px;
    border-top: 0.5px solid #000;
    margin-top: 10mm;
}

/* Utilities */
.text-center {
    text-align: center;
}
.text-left {
    text-align: left;
}
.text-right {
    text-align: right;
}
.mt-10 {
    margin-top: 10mm;
}
.mt-5 {
    margin-top: 5mm;
}
</style>
 <?php $param = $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>
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
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-min-width dropdown-toggle mb-1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-print"></i> Print Indent
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                       href="<?= base_url('indent/printindent/' . $param); ?>" target="_blank">
                                        Print Product wise
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                       href="<?= base_url('indent/printstoreview/'.$param); ?>" target="_blank">
                                        Print Store Wise
                                    </a>
                                </div> &nbsp;
                               
                                <a href="<?= base_url('indent/view/' . $param); ?>">  
                                    <button type="button" class="btn btn-primary btn-min-width mb-1"
                                            aria-haspopup="true" aria-expanded="false"><i class="fa fa-eye"></i> view Product wise
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Company Details -->
                <div id="invoice-company-details" class="row mt-2">
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-left"><p></p>
                        <img src="<?php $loc = location($invoice['loc']); echo base_url('userfiles/company/' . $loc['logo']) ?>"
                             class="img-responsive p-1 m-b-2" style="max-height: 120px;">
                        <p class="ml-2"><?= $loc['cname'] ?></p>
                    </div>
                    <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                        <h2>Indent</h2>
                        <p class="pb-1"><?php echo 'CN ' . $chalan . '</p>
                            <p class="pb-1"><b>Total Challan: ' . $totalchalan . '</b></p>'; ?>
                    </div>
                </div>

                <!-- Invoice Items Details -->
                <div id="invoice-items-details" class="pt-2">
                    <div class="row">
                        <div class="table-responsive col-sm-12">
                     <table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Sr. No.</th>
            <th>Product</th>
            <?php foreach ($stores as $store): ?>
                <th>
                    <div style="word-wrap: break-word; max-width: 120px;">
                        <?php
                        $store_name = $store['username'];
                        if (strlen($store_name) > 20) {
                            echo nl2br(wordwrap($store_name, 20));
                        } else {
                            echo $store_name;
                        }
                        ?>
                    </div>
                </th>
            <?php endforeach; ?>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $srno = 1; 
        $storeTotals = []; // Store-wise & unit-wise total array

        foreach ($products as $pname => $pdetail): ?>
            <tr>
                <td><?php echo $srno++; ?></td>
                <td><?php echo $pname; ?></td>
                <?php 
                $totalQty = 0;
                foreach ($stores as $store): 
                    $qty = isset($pdetail['stores'][$store['id']]) ? $pdetail['stores'][$store['id']] : 0;
                    $totalQty += $qty;

                    // Store-wise & unit-wise total
                    $unit = $pdetail['unit'];
                    if (!isset($storeTotals[$store['id']][$unit])) {
                        $storeTotals[$store['id']][$unit] = 0;
                    }
                    $storeTotals[$store['id']][$unit] += $qty;
                ?>
                    <td>
                        <?php 
                        if($qty != 0){
                            echo round($qty, 2) . $pdetail['unit'];
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
                <td><?php echo round($totalQty, 2) . $pdetail['unit']; ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Final Total Row -->
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <td colspan="2">Store Totals</td>
            <?php foreach ($stores as $store): ?>
                <td>
                    <?php 
                    if (isset($storeTotals[$store['id']])) {
                        $totalsText = [];
                        foreach ($storeTotals[$store['id']] as $unit => $qty) {
                            $totalsText[] = round($qty, 2) . $unit;
                        }
                        echo implode(", ", $totalsText);
                    } else {
                        echo "0";
                    }
                    ?>
                </td>
            <?php endforeach; ?>
            <td></td> <!-- Overall total (optional) -->
        </tr>
    </tbody>
</table>

                            
                            <!-- Buttons for Exporting and Printing -->
                          
                        </div>
                    </div>
                </div>

            
            </div>
        </div>
    </div>
</div>

<?php
// PHP function to format the quantity (to be defined in your view or controller)
function formatQuantity($qty, $unit) {
    if ($qty == 0 || $qty == '0.00') {
        return '';  // Return empty if quantity is 0
}}

    // Format the quantity by removing
