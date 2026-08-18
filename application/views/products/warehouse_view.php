<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5 class="title"> 
                <?php echo $this->lang->line('Products') ?> 
                <a href="<?php echo base_url('products/add') ?>" class="btn btn-primary btn-sm rounded">
                    <?php echo $this->lang->line('Add new') ?>
                </a>
            </h5>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>

            <div class="card-body">

                <!-- 🔹 DATE FILTER AREA -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" id="start_date" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" id="end_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="filterReport" class="btn btn-success btn-block">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <!-- 🔹 END DATE FILTER AREA -->

                <!-- 🔹 PRODUCT TABLE -->
                <table id="productstable" class="table table-striped table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('Name') ?></th>
                            <th><?php echo $this->lang->line('Stock') ?></th>
                            <th>Stock value</th>
                            <th><?php echo $this->lang->line('Code') ?></th>
                            <th><?php echo $this->lang->line('Category') ?></th>
                            <th>Online Sell Price</th>
                            <th>Billing Price</th>
                            <th>Purchase Price</th>
                            <th>Updated Price</th>
                            <th><?php echo $this->lang->line('Settings') ?></th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('Name') ?></th>
                            <th id="totalStock">Stock</th>
                            <th id="totalStockValue">Stock value</th>
                            <th><?php echo $this->lang->line('Code') ?></th>
                            <th><?php echo $this->lang->line('Category') ?></th>
                            <th>Online Sell Price</th>
                            <th>Billing Price</th>
                            <th>Purchase Price</th>
                            <th>Updated Price</th>
                            <th><?php echo $this->lang->line('Settings') ?></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- 🔹 DATATABLE SCRIPT -->
<script type="text/javascript">
$(document).ready(function () {

    // Initialize DataTable
    var table = $('#productstable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [[1, "asc"]],
        "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
        "columnDefs": [
            { "targets": [2, 3, 6, 7, 8, 9], "orderable": true, "type": "num" },
            { "targets": [0, 10], "orderable": false }
        ],
        "ajax": {
            "url": "<?php echo site_url('products/warehouseproduct_list') . '?id=' . $_GET['id']; ?>",
            "type": "POST",
            "data": function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d['<?php echo $this->security->get_csrf_token_name(); ?>'] = crsf_hash;
            }
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();

            var intVal = function (i) {
                if (i === null || i === undefined || i === '') return 0;
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[^0-9.-]+/g, '')) :
                    typeof i === 'number' ? i : 0;
            };

            // Total for Stock (Column 2)
            var totalStock = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            // Total for Stock Value (Column 3)
            var totalStockValue = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            // Update footer
            $(api.column(2).footer()).html(totalStock.toFixed(2));
            $(api.column(3).footer()).html('₹' + totalStockValue.toFixed(2));
        }
    });

    // 🔹 SEARCH BUTTON CLICK
    $('#filterReport').on('click', function (e) {
        e.preventDefault();
        table.ajax.reload(); // Reload table with new date range
    });

    // 🔹 VIEW BUTTON ACTION
    $(document).on('click', ".view-object", function (e) {
        e.preventDefault();
        $('#view-object-id').val($(this).attr('data-object-id'));
        $('#view_model').modal({ backdrop: 'static', keyboard: false });

        var actionurl = $('#view-action-url').val();
        $.ajax({
            url: baseurl + actionurl,
            data: 'id=' + $('#view-object-id').val() + '&' + crsf_token + '=' + crsf_hash,
            type: 'POST',
            dataType: 'html',
            success: function (data) {
                $('#view_object').html(data);
            }
        });
    });

});
</script>

    <div id="delete_model" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title"><?php echo $this->lang->line('Delete') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p><?php echo $this->lang->line('delete this product') ?></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="object-id" value="">
                    <input type="hidden" id="action-url" value="products/delete_i">
                    <button type="button" data-dismiss="modal" class="btn btn-primary"
                            id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                    <button type="button" data-dismiss="modal"
                            class="btn"><?php echo $this->lang->line('Cancel') ?></button>
                </div>
            </div>
        </div>
    </div>  


	<div id="update_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Update Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="form-group">
                        <label for="sell-price">Sell Price</label>
                        <input type="number" id="sell-price" name="sell_price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="purchase-price">Purchase Price</label>
                        <input type="number" id="purchase-price" name="purchase_price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" class="form-control"  data.original="" required>
                    </div>  


					<div class="form-group">
                        <label for="stock">Wastage</label>
                        <input type="number" id="wastage" name="wastage" class="form-control" value="0">
                    </div>
              
                    <input type="hidden" id="product-id" name="product_id">
                    <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>	
<script>
$(document).ready(function () {
    const stockInput = $('#stock');
    const wastageInput = $('#wastage');

    // Jab modal open ho, original stock ko save karo
    stockInput.data('original', stockInput.val());

    // Stock input par change hone par wastage calculate karo
    stockInput.on('input', function () {
        const originalStock = parseFloat(stockInput.data('original')) || 0;
        const newStock = parseFloat($(this).val()) || 0;
        const wastage = originalStock - newStock;

        wastageInput.val(wastage.toFixed(2));
    });
});
</script>

<div id="sellreportspopup_model" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-titles">Sell Report</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <input type="hidden" id="product_id">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="date" id="from_date" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="to_date" class="form-control" placeholder="To Date">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-info" id="filter-btn">Filter</button>
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th>Perticular</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody id="sell_report_body">
                        <!-- AJAX data here -->
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


    <div id="view_model" class="modal  fade">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header">

                    <h4 class="modal-title"><?php echo $this->lang->line('View') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="view_object">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="view-object-id" value="">
                    <input type="hidden" id="view-action-url" value="products/view_over">

                    <button type="button" data-dismiss="modal"
                            class="btn"><?php echo $this->lang->line('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
	
	<script>
	
	
	function sellreportspopup(product_id, product_name, sell_price, purchase_price, stock) {
    $('#modal-titles').text('Sell Report - ' + product_name);
    $('#sellreportspopup_model').modal('show');
    $('#product_id').val(product_id);
    $('#from_date, #to_date').val('');
    loadSellReport(product_id, '', '');
}

function loadSellReport(product_id, from_date, to_date) {
    $.ajax({
        url: '<?=base_url()?>products/get_sell_report',
        type: 'POST',
        data: {
            product_id: product_id,
            from_date: from_date,
            to_date: to_date
        },
        success: function(response) {
            $('#sell_report_body').html(response);
        }
    });
}

$(document).on('click', '#filter-btn', function() {
    let product_id = $('#product_id').val();
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    loadSellReport(product_id, from_date, to_date);
});



	function updatePopup(id, name, sellPrice, purchasePrice, stock) {
 
    document.getElementById('modal-title').innerText = 'Update Product: ' + name;
    document.getElementById('sell-price').value = sellPrice;
    document.getElementById('purchase-price').value = purchasePrice;
    document.getElementById('stock').value = stock;
    document.getElementById('product-id').value = id;
	    const stockInput = $('#stock');
    stockInput.data('original', stockInput.val()); // Set original stock value
 
    $('#update_model').modal('show');
}

function confirmSubmit() {
    if (confirm("Are you sure you want to update this product?")) {
        submitForm();
    }
}

function submitForm() {
    let formData = $('#update-form').serialize();

    $.ajax({
        url: '<?=base_url()?>products/update_product',
        type: 'POST',
        data: formData,
        success: function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                alert(res.message);
                location.reload();
            } else {
                alert('An error occurred: ' + res.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the product.');
        }
    });
}


	</script>
