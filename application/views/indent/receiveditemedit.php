<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4>Edit Received Item</h4>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo base_url('receiveditem/receiveditemupdate/' . $master->id); ?>">

                <div class="row">
                    <!-- Employee -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cst">Employee</label>
                            <input type="text" class="form-control" id="mysupplier-box"
                                   name="cst"
                                   value="<?= htmlspecialchars($master->employee_name) ?>"
                                   placeholder="Enter Employee Name or Mobile Number">
                        </div>
                    </div>

                    <!-- Invoice No -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Invoice No</label>
                            <input type="text" class="form-control"
                                   name="invocieno"
                                   value="<?= htmlspecialchars($master->order_no) ?>"
                                   placeholder="Invoice #">
                        </div>
                    </div>

                    <!-- Reference -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Reference</label>
                            <input type="text" class="form-control"
                                   name="refer"
                                   value="<?= htmlspecialchars($master->reference) ?>"
                                   placeholder="Reference #">
                        </div>
                    </div>

                    <!-- Billing Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Billing Date</label>
                            <input type="text" class="form-control required"
                                   name="invoicedate"
                                   value="<?= $master->order_date ?>">
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="text" class="form-control required"
                                   id="tsn_due"
                                   name="invocieduedate"
                                   value="<?= $master->due_date ?>">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="items">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Required Qty</th>
                                <th>Unit</th>
                                <th>Received Qty</th>
                                <th>Balance Qty</th>
                                <th>Supplier</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Amount</th>
								 <th><button type="button" class="btn btn-success btn-sm" id="addRow">+ Add More</button></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        if (!empty($items)) {
                            foreach ($items as $item) { ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control text-center"
                                               name="product_name[]"
                                               value="<?= htmlspecialchars($item->product_name) ?>">
											      <input type="hidden" class="product_id" name="product_id[]" value="<?=$item->product_id?>">
											      <input type="hidden" class="item_id" name="item_id[]" value="<?=$item->id?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control req amnt"
                                               name="required_qty[]"
                                               value="<?= number_format($item->required_qty, 2) ?>"
                                               readonly>
                                    </td>
                                    <td>
                                        <select class="form-control prounits" name="product_unit[]">
                                            <option value="<?= $item->unit ?>"><?= $item->unit ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control req"
                                               name="received_qty[]"
                                               value="<?= number_format($item->received_qty, 2) ?>">
											   
											     <button type="button" class="btn btn-primary updateRow">Update</button>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control req prc"
                                               name="balance_qty[]"
                                               value="<?= number_format($item->balance_qty, 2) ?>">
                                    </td>
                                    <td>
                                        <input type="hidden" name="supplier_id[]" value="<?= $item->supplier_id ?>">
                                        <input type="text" class="form-control supplier-input"
                                               name="supplier[]"
                                               value="<?= htmlspecialchars($item->supplier_name) ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control req prc"
                                               name="product_price[]"
                                               value="<?= number_format($item->price, 2) ?>">
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="product_description[]"><?= htmlspecialchars($item->description) ?></textarea>
                                    </td>
                                    <td>
                                        <strong><span class="ttlText"><?= number_format($item->amount, 2) ?></span></strong>
                                    </td>
									
									 <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                                </tr>
                        <?php } } ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-group mt-3">
                    <button type="button" id="saveNewItems" class="btn btn-success">Save New Items</button>
					    <button type="button" id="convertToChallan" class="btn btn-warning ml-2">Convert to Purchase Challan</button>
                    <a href="<?php echo base_url('receiveditem'); ?>" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>



<script>

$(document).on('click', '#convertToChallan', function () {
    if (!confirm('Are you sure you want to convert this received record into Purchase Challan(s)? This action cannot be undone.')) return;

    $.ajax({
        url: "<?= base_url('purchase/convert_to_challan/' . $master->id) ?>",
        type: "POST",
        dataType: "json",
        beforeSend: function() {
            $('#convertToChallan').prop('disabled', true).text('Processing...');
        },
        success: function(res) {
            if (res.status === 'success') {
                alert(res.message);

                // Disable all inputs and buttons after conversion
                $('form :input').prop('disabled', true);
                $('#addRow, .removeRow, #saveNewItems, #convertToChallan').hide();
                $('a.btn.btn-secondary').prop('disabled', false);
            } else {
                alert('Error: ' + res.message);
                $('#convertToChallan').prop('disabled', false).text('Convert to Purchase Challan');
            }
        },
        error: function(xhr, status, err) {
            alert('AJAX error: ' + err);
            $('#convertToChallan').prop('disabled', false).text('Convert to Purchase Challan');
        }
    });
});

$(document).ready(function () {

 
    $(document).on("focus", ".product-input", function () {
        let $row = $(this).closest("tr");
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?= base_url('search_products/puchase_search'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: { name_startsWith: request.term },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item[0],
                                value: item[0],
                                units: item[6], // HTML <option> string
                                pid: item[2],
                                purchase: item[1]
                            };
                        }));
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
				    $(this).val(ui.item.label);
                $row.find(".product_id").val(ui.item.pid);
                $row.find(".product_unit").html(ui.item.units);

                // Default first unit select
                $row.find(".product_unit option:first").prop("selected", true);

                // Set price from selected unit
                let price = $row.find(".product_unit option:selected").attr("purchase");
                $row.find(".price").val(price);

                return false;
            }
        });
    });

    // ✅ On Unit Change → Update Price
    $(document).on("change", ".product_unit", function () {
        let $row = $(this).closest("tr");
        let price = $(this).find("option:selected").attr("purchase");
        $row.find(".price").val(price);
        calculateAllRows();
    });

    // ✅ Received Qty & Required Qty → Balance + Amount
    $(document).on("input", ".required_qty, .received_qty", function () {
        let $row = $(this).closest("tr");
        let required = parseFloat($row.find(".required_qty").val()) || 0;
        let received = parseFloat($row.find(".received_qty").val()) || 0;
        let balance = required - received;
        $row.find(".balance_qty").val(balance);

        calculateAllRows();
    });

    // ✅ Amount Calculation
    $(document).on("input", ".received_qty, .price", function () {
        calculateAllRows();
    });

    function calculateAllRows() {
        let grandTotal = 0;
        $("#items tbody tr").each(function () {
            let $row = $(this);
            let qty = parseFloat($row.find(".received_qty").val()) || 0;
            let price = parseFloat($row.find(".price").val()) || 0;
            let subtotal = qty * price;
            $row.find(".amount").text(subtotal.toFixed(2));
            $row.find(".subtotal").val(subtotal.toFixed(2));
            grandTotal += subtotal;
        });
        $("#invoiceyoghtml").val(grandTotal.toFixed(2));
    }

    // ✅ Add More Row (only if product selected in current row)
/*     $("#addRow").click(function () {
        let lastRow = $("#items tbody tr:last");
        let productName = lastRow.find(".product-input").val();
        if (productName === "") {
            alert("Please select a product first before adding new row.");
            return false;
        }

        let newRow = lastRow.clone();
        newRow.find("input, textarea").val("");
        newRow.find(".product_unit").html('<option value="">Select</option>');
        newRow.find(".amount").text("0.00");
        newRow.find(".subtotal").val("");
        $("#items tbody").append(newRow);
    }); */

    // ✅ Remove Row
    $(document).on("click", ".removeRow", function () {
        if ($("#items tbody tr").length > 1) {
            $(this).closest("tr").remove();
            calculateAllRows();
        }
    });

});
$(document).on("click", "#saveNewItems", function () {
    let formData = $("form").serialize(); // full form

    $.ajax({
        url: "<?= base_url('purchase/insert_received_items/'.$master->id) ?>",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                alert(res.message);
                location.reload(); // reload to see new rows in DB
            }
        }
    });
});


$(document).on("click", "#addRow", function () {
    let newRow = '<tr><td> <input type="text" class="form-control product-input" name="product_name[]"><input type="hidden" name="item_id[]" value="0"> </td> <td><input type="number" class="form-control" name="required_qty[]" value="0"></td> <td><select class="form-control product_unit" name="product_unit[]"><option value="">Select</option></select></td> <td><input type="number" class="form-control" name="received_qty[]" value="0"></td><td><input type="number" class="form-control" name="balance_qty[]" value="0"></td><td> <input type="hidden" name="supplier_id[]" value=""> <input type="text" class="form-control supplier-input" name="supplier[]"></td><td><input type="text" class="form-control" name="product_price[]" value="0.00"></td> <td><textarea class="form-control" name="product_description[]"></textarea></td> <td><input type="hidden" name="product_subtotal[]" value="0.00"><strong class="ttlText">0.00</strong></td> <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td> </tr>';
    $("#items tbody").append(newRow);

    $(".product-input").autocomplete({
        source: "<?= base_url('search_products/get_products') ?>",
        minLength: 1,
        select: function (event, ui) {
            $(this).val(ui.item.label);
            $(this).closest("tr").find("select[name='product_unit[]']").html(ui.item.units);
            $(this).closest("tr").find("input[name='product_price[]']").val(ui.item.price);
            return false;
        }
    });
});

$(document).on("click", ".updateRow", function () {
    let $row = $(this).closest("tr");

    let rowData = {
        id: $row.find("input[name='item_id[]']").val(), // hidden field with item id
        product_name: $row.find("input[name='product_name[]']").val(),
        required_qty: $row.find("input[name='required_qty[]']").val(),
        unit: $row.find("select[name='product_unit[]']").val(),
        received_qty: $row.find("input[name='received_qty[]']").val(),
        balance_qty: $row.find("input[name='balance_qty[]']").val(),
        supplier_id: $row.find("input[name='supplier_id[]']").val(),
        supplier: $row.find("input[name='supplier[]']").val(),
        price: $row.find("input[name='product_price[]']").val(),
        description: $row.find("textarea[name='product_description[]']").val(),
        amount: $row.find("input[name='product_subtotal[]']").val(),
    };

    $.ajax({
        url: "<?= base_url('purchase/update_received_item') ?>",
        type: "POST",
        data: rowData,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                $row.css("background-color", "#d4edda"); // green background
                setTimeout(() => {
                    $row.css("background-color", ""); // reset after 2s
                }, 2000);
            }
        }
    });
});

$(document).ready(function () {

    // Balance Calculation
    $(document).on('input', '[id^=receivedqty-]', function () {
        var row = $(this).attr('id').split('-')[1];
        var required = parseFloat($("#required_qty-" + row).val()) || 0;
        var received = parseFloat($(this).val()) || 0;
        var balance = required - received;
        $("#balance-" + row).val(balance.toFixed(2));
    });

    // Supplier Autocomplete
    $(document).on('focus', '.supplier-input', function () {
        var row = $(this).data('row');

        $(this).autocomplete({
            source: function (request, response) {
                $.getJSON("<?= base_url('search_products/get_suppliers'); ?>", { term: request.term }, function (data) {
                    response(data);
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $("#supplier_id-" + row).val(ui.item.id);
                $(this).val(ui.item.value);
                return false;
            }
        });
    });

});
</script>


<script>
    let rowCount = 1;
    function addRow() {
        const fields = ['item_name', 'quantity', 'unit', 'fill_quantity', 'fill_rate', 'description', 'amount'];
        let html = "<tr>";
        fields.forEach(field => {
            html += `<td><input type="text" name="items[${rowCount}][${field}]"></td>`;
        });
        html += "</tr>";
        $('#itemsTable tbody').append(html);
        rowCount++;
    }

    $('#orderForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("indent/store_puquote") ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#result').html('<p style="color:green;">Order saved successfully!</p>');
                $('#orderForm')[0].reset();
                $('#itemsTable tbody').html(''); // Clear item rows
                addRow(); // Add default row back
            },
            error: function(xhr) {
                $('#result').html('<p style="color:red;">Something went wrong!</p>');
            }
        });
    });
</script>

<script>
$(document).ready(function () {
    // Call on page load
    calculateAllRows();

    // When any quantity or price is changed
    $(document).on('input', 'input[name="received_qty[]"], input[name="product_price[]"]', function () {
        calculateAllRows();
    });

    function calculateAllRows() {
        let grandTotal = 0;

        $('tbody tr').each(function () {
            const $row = $(this);
            const qty = parseFloat($row.find('input[name="received_qty[]"]').val()) || 0;
            const rate = parseFloat($row.find('input[name="product_price[]"]').val()) || 0;
            const amount = qty * rate;

            $row.find('.ttlText').text(amount.toFixed(2));
            $row.find('input[name="product_subtotal[]"]').val(amount.toFixed(2));

            if (!isNaN(amount)) {
                grandTotal += amount;
            }
        });

        // Update grand total
        $('#invoiceyoghtml').val(grandTotal.toFixed(2));
    }
});
</script>


<script>

$("#mysupplier-box").keyup(function () {
    $.ajax({
        type: "GET",
        url: baseurl + 'search_products/myemployee',
        data: 'keyword=' + $(this).val() + '&' + crsf_token + '=' + crsf_hash,
        beforeSend: function () {
            $("#mysupplier-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
        },
        success: function (data) {
            $("#mysupplier-box-result").show();
            $("#mysupplier-box-result").html(data);
            $("#mysupplier-box").css("background", "none");
        }
    });
});

// Handle multiple selection
$(document).on('click', '.select-employee', function () {
	
	
    let supplierId = $(this).data('id');
    let supplierName = $(this).data('name');
    let supplierPhone = $(this).data('phone');
    let supplierAddress = $(this).data('address');

    // Avoid duplicates
    if ($("#supplier-" + supplierId).length === 0) {
        let html = `
            <div class="col-sm-4 selected-supplier" id="supplier-${supplierId}">
                <input type="hidden" name="employee_ids" value="${supplierId}">
                <input type="hidden" name="employee_phone[]" value="${supplierPhone}">  
                <input type="hidden" name="employee_name" value="${supplierName}">
                <strong>${supplierName}</strong><br>
                ${supplierPhone}<br>
                ${supplierAddress}<br>
                <button type="button" class="btn btn-danger btn-sm remove-supplier" data-id="${supplierId}">Remove</button>
                <hr>
            </div>
        `;
        $("#customer").html(html);
    }

    $("#mysupplier-box-result").hide();
    $("#mysupplier-box").val('');
});

// Remove supplier
$(document).on('click', '.remove-supplier', function () {
    let id = $(this).data('id');
    $("#supplier-" + id).remove();
});

</script>

<style>
#selected-suppliers {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.selected-supplier {
    flex: 1 1 calc(50% - 15px); /* Two columns with gap */
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px 15px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    position: relative;
}

.selected-supplier strong {
    display: block;
    font-size: 16px;
    color: #333;
}

.selected-supplier .btn {
    margin-top: 10px;
    background-color: #ff5c5c;
    color: white;
    font-size: 12px;
    padding: 5px 10px;
}

.selected-supplier hr {
    margin: 8px 0;
    border: 0;
    border-top: 1px solid #eee;
}

</style>