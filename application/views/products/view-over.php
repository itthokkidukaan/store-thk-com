<h5><?php echo $product['name'] . ' (' . $product['title'] . ')'; ?></h5>

<table class="table" style="display:none;">
    <?php echo '<tr><td>' . $product['name'] . '</td><td> Balance Stock : ' . $product['stock'] . '<br><br><a href="' . base_url() . 'products/edit?id=' . $product['id'] . '" class="btn btn-primary btn-sm"><span class="icon-pencil"></span> ' . $this->lang->line('Edit') . '</a>  <div class="btn-group">
                                    <button type="button" class="btn btn-blue dropdown-toggle   btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i>  ' . $this->lang->line('Print') . '                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . base_url() . 'products/barcode?id=' . $product['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . '</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/posbarcode?id=' . $product['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . ' - Compact</a>
                                          <div class="dropdown-divider"></div>
                                             <a class="dropdown-item" href="' . base_url() . 'products/label?id=' . $product['id'] . '" target="_blank"> ' . $this->lang->line('Product') . ' Label</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/poslabel?id=' . $product['id'] . '" target="_blank"> Label - Compact</a>

                                    </div>
                                </div>   <a class="btn btn-pink  btn-sm" href="' . base_url() . 'products/report_product?id=' . $product['id'] . '" target="_blank"> <span class="icon-pie-chart2"></span> ' . $this->lang->line('Sales') . '</a> </td></tr>'; ?>
</table>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h4 class="mb-3 text-center">Stock Report</h4>
        
  <?php
$today = date('Y-m-d'); // Aaj ki date
$first_day = date('Y-m-01'); // Current month ka first date
?>

<form id="filterForm" class="row g-3">
    <div class="col-md-5">
        <label class="form-label">From Date:</label>
        <input type="date" id="from_dates" name="from_date" class="form-control" value="<?= $first_day ?>">
    </div>
    <div class="col-md-5">
        <label class="form-label">To Date:</label>
        <input type="date" id="to_dates" name="to_date" class="form-control" value="<?= $today ?>">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>
        
        <div class="table-responsive mt-4">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Sr No.</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Customer</th>
                        <th>DR Stock</th>
                        <th>CR Stock</th>
                        <th>C. Stock</th>
                        <th>P. Rate</th>
                        <th>S. Rate</th>
                        <th>P. Amount</th>
                        <th>S. Amount</th>
                      
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

$(document).ready(function () {
    function loadTable(from_date = '', to_date = '') {
        $.ajax({
            url: "<?= base_url('products/filter_over') ?>",
            type: "POST",
            data: { from_date: from_date, to_date: to_date, pid: "<?=$pid?>" },
            success: function (data) {
                $('#tableBody').html(data);
                // Extract final stock value after table loads
                extractFinalStock();
            }
        });
    }
    
    function extractFinalStock() {
        // Find the row with "Final Stock (After Wastage & Returns)"
        var finalStockRow = $('#tableBody tr').filter(function() {
            return $(this).text().indexOf('Final Stock (After Wastage') !== -1 || 
                   $(this).text().indexOf('Final Stock (After Wastage & Returns)') !== -1;
        });
        
        if (finalStockRow.length > 0) {
            // The row structure: <td colspan="4">Final Stock...</td><td colspan="2">30 KG</td>...
            // So the stock value is in the second td (index 1)
            var stockText = finalStockRow.find('td').eq(1).text().trim();
            // Extract number from text like "30 KG" -> "30"
            var stockMatch = stockText.match(/([\d.]+)/);
            if (stockMatch) {
                var stockValue = parseFloat(stockMatch[1]);
                // Store in localStorage with product ID as key
                localStorage.setItem('final_stock_<?=$pid?>', stockValue);
                console.log('Final stock extracted and stored: ' + stockValue + ' for product ID: <?=$pid?>');
            }
        }
    }
    
    loadTable();
    
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        var from_date = $('#from_dates').val();
        var to_date = $('#to_dates').val();
        loadTable(from_date, to_date);
    });
    
    // Also extract on initial page load if table is already rendered
    setTimeout(function() {
        extractFinalStock();
    }, 500);
    
    // Extract stock when Edit button is clicked (as backup, before navigation)
    $(document).on('click', '#view_object a[href*="products/edit"]', function(e) {
        extractFinalStock();
        // Don't prevent default - let the link work normally
        // localStorage is already updated from table load, this is just a backup
    });
});
</script>

<?php 

//print_r($product_warehouse);
$product_variations=0;
if ($product_variations==3) {

    echo '<h6>' . $this->lang->line('Products')  . '</h6>';
    ?>

    <table class="table table-striped table-bordered">
        <?php
    /*     foreach ($product_variations as $product_variation) {
            echo '<tr><td><a href="' . base_url() . 'products/edit?id=' . $product_variation['id'] . '" class="btn btn-primary btn-sm"><span class="icon-pencil"></span> ' . $this->lang->line('Edit') . '</a>  <div class="btn-group">
                                    <button type="button" class="btn btn-blue dropdown-toggle   btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i>  ' . $this->lang->line('Print') . '                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . base_url() . 'products/barcode?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . '</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/posbarcode?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . ' - Compact</a>
                                          <div class="dropdown-divider"></div>
                                             <a class="dropdown-item" href="' . base_url() . 'products/label?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('Product') . ' Label</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/poslabel?id=' . $product_variation['id'] . '" target="_blank"> Label - Compact</a>

                                    </div>
                                </div>   <a class="btn btn-pink  btn-sm" href="' . base_url() . 'products/report_product?id=' . $product_variation['id'] . '" target="_blank"> <span class="icon-pie-chart2"></span> ' . $this->lang->line('Sales') . '</a>  ' . $product_variation['product_name'] . '</td><td>Code : ' . $product_variation['product_code'] . '</td><td> ' . $this->lang->line('Stock') . ' : ' . $product_variation['qty'] . ' </td></tr>';
        } */ ?>
    </table>
<?php } ?>

<?php //if ($product_warehouse) {
   // echo '<h6> ' . $this->lang->line('Warehouse') . '</h6>';
    ?>
   <!-- <table class="table table-striped table-bordered">
        <?php
       /*  foreach ($product_warehouse as $product_variation) {
            echo '<tr><td><a href="' . base_url() . 'products/edit?id=' . $product_variation['pid'] . '" class="btn btn-primary btn-sm"><span class="icon-pencil"></span> ' . $this->lang->line('Edit') . '</a> <div class="btn-group">
                                    <button type="button" class="btn btn-blue dropdown-toggle   btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i>  ' . $this->lang->line('Print') . '                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . base_url() . 'products/barcode?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . '</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/posbarcode?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('BarCode') . ' - Compact</a>
                                          <div class="dropdown-divider"></div>
                                             <a class="dropdown-item" href="' . base_url() . 'products/label?id=' . $product_variation['id'] . '" target="_blank"> ' . $this->lang->line('Product') . ' Label</a>

                                        <div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/poslabel?id=' . $product_variation['id'] . '" target="_blank"> Label - Compact</a>

                                    </div>
                                </div>   <a class="btn btn-pink  btn-sm" href="' . base_url() . 'products/report_product?id=' . $product_variation['id'] . '" target="_blank"> <span class="icon-pie-chart2"></span> ' . $this->lang->line('Sales') . '</a> ' . $product_variation['product_name'] . '</td><td>Code : ' . $product_variation['product_code'] . '</td><td>' . $product_variation['title'] . '</td><td> ' . $this->lang->line('Stock') . ' : ' . $product_variation['qty'] . '  </td></tr>';
        } */ ?>
    </table> -->
<?php //} ?>
<hr>

