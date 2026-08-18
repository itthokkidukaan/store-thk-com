 <style>
body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px;
    color: #000;
    margin: 0;
    padding: 0;
}

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


   

<div class="content-body">
    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div id="invoice-template" class="card-body">
          

                <!-- Invoice Items Details -->
                <div id="invoice-items-details" class="pt-2">
                    <div class="row">
                        <div class="table-responsive col-sm-12">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Product</th>
                                        <?php 
										$n = 1;
										
										foreach ($stores as $store): ?>
                                            <th>
                                                <div style="word-wrap: break-word; max-width: 120px;">
                            <?php
							echo '<b style="color:red;">CRT'.$n.'</b><br/> ';
                            // Check if the store name exceeds 20 characters and split it into two lines
                            $store_name = $store['username'];

                            // Use wordwrap to split the name into two lines if it exceeds 20 characters
                            if (strlen($store_name) > 20) {
                                echo nl2br(wordwrap($store_name, 20));  // Split after 20 characters
                            } else {
                                echo $store_name;
                            }
							
                            ?>
							
                        </div>
                                            </th>
                                        <?php 
										$n++;
										endforeach; ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $srno = 1; foreach ($products as $pname => $pdetail): ?>
                                        <tr>
                                            <td><?php echo $srno++; ?></td>
                                            <td><?php echo $pname; ?></td>

                                            <?php $totalQty = 0; foreach ($stores as $store): ?>
                                                <td>
                                                    <?php
                                                        $qty = isset($pdetail['stores'][$store['id']]) ? $pdetail['stores'][$store['id']] : 0;
                                                        $totalQty += $qty;
                                                        // Call the PHP function formatQuantity() for formatting
                                                     //   echo round($qty,2). $pdetail['unit'];
														
														 if($qty !=0){
															
															
                                                        echo round($qty,2). $pdetail['unit'];
														}
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <td><?php echo round($totalQty,2).$pdetail['unit']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
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
