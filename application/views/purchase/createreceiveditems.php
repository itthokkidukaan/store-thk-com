<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4>Add Received Item</h4>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo base_url('purchase/receiveditemsave'); ?>">

                <div class="row">
                    <!-- Employee -->
                    <div class="col-md-6">
                             <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption">Search Employee</label>
                                        <input type="text" class="form-control" name="cst" id="mysupplier-box"
                                               placeholder="Enter Employee Name or Mobile Number to search"
                                               autocomplete="off"/>
                                        <div id="mysupplier-box-result"></div>
                                    </div>

                                </div>
          <div id="customer" class="row">
    <div class="clientinfo col-sm-10">
        <label style="font-weight: bold;">Selected Employee:</label><br>
        <div id="selected-suppliers" >
           
        </div>
    </div>
</div>
                    </div>

                    <!-- Invoice No -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Invoice No</label>
                            <input type="text" class="form-control"
                                   name="invocieno"
                                  
                                   placeholder="Invoice #">
                        </div>
                    </div>

                    <!-- Reference -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Reference</label>
                            <input type="text" class="form-control"
                                   name="refer"
                                   
                                   placeholder="Reference #">
                        </div>
                    </div>

                    <!-- Billing Date -->
					 <div class="col-md-3"></div>
					 <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Billing Date</label>
                            <input type="text" class="form-control required"
                                   name="invoicedate" data-toggle="datepicker"
                                  >
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="text" class="form-control required"
                                   id="tsn_due"
                                   name="invocieduedate" data-toggle="datepicker"
                                   >
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
                        <th><button type="button" class="btn btn-success btn-sm" id="addRow">+ Add</button></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td>
                            <input type="text" class="form-control product-input" name="product_name[]">
                            <input type="hidden" class="product_id" name="product_id[]">
                        </td>
                        <td><input type="number" class="form-control required_qty" name="required_qty[]" min="0"></td>
                        <td>
                            <select class="form-control product_unit" name="product_unit[]">
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td><input type="number" class="form-control received_qty" name="received_qty[]" min="0"></td>
                        <td><input type="number" class="form-control balance_qty" name="balance_qty[]" readonly></td>
                        <td>
                            <input type="hidden" class="supplier_id" name="supplier_id[]">
                            <input type="text" class="form-control supplier-input" name="supplier[]">
                        </td>
                        <td><input type="text" class="form-control price" name="product_price[]" readonly></td>
                        <td><textarea class="form-control" name="product_description[]"></textarea></td>
                        <td><strong><span class="amount">0.00</span></strong>
                            <input type="hidden" name="product_subtotal[]" class="subtotal">
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
 <div class="form-group mt-3">
            <label>Grand Total</label>
            <input type="text" id="invoiceyoghtml" class="form-control" readonly>
        </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?php echo base_url('receiveditem'); ?>" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>



<script>

$(document).ready(function () {
    $("form").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                if (res.status === "Success") {
                    alert(res.message); // या नीचे div में show करो
                    $("#items tbody").html(""); // clear table rows
                    $("#invoiceyoghtml").val("0.00");
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function () {
                alert("Something went wrong!");
            }
        });
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
    $("#addRow").click(function () {
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
    });

    // ✅ Remove Row
    $(document).on("click", ".removeRow", function () {
        if ($("#items tbody tr").length > 1) {
            $(this).closest("tr").remove();
            calculateAllRows();
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