<div class="content-body">
    <div class="card">
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div class="card-body">
                <form method="post" id="data_form">


                    <div class="row">

                        <div class="col-sm-4">

                        </div>

                        <div class="col-sm-3"></div>

                        <div class="col-sm-2"></div>

                        <div class="col-sm-3">

                        </div>

                    </div>

                    <div class="row">


                        <div class="col-sm-6 cmp-pnl">
                            <div id="customerpanel" class="inner-cmp-pnl">
                                <div class="form-group row">
                                    <div class="fcol-sm-12">
                                        <h3 class="title">
                                          Received Item  From
											</h3>
                                    </div>
                                </div>

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
							
                        </div>
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">


                                <div class="form-group row">

                                    <div class="col-sm-12"><h3
                                                class="title">Received item</h3>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Order Number') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Invoice #"
                                                   name="invocieno"
                                                   value="">
                                        </div>
                                    </div>
									<?php 
									$last = $this->uri->total_segments();
									$reference = $this->uri->segment($last-1); ?>
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Reference') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Reference #"
                                                   name="refer" value="<?=$reference?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-sm-6"><label for="invociedate"
                                                                 class="caption"><?php echo $this->lang->line('Order Date') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar4"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required"
                                                   placeholder="Billing Date" name="invoicedate"
                                                   data-toggle="datepicker"
                                                   autocomplete="false">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieduedate"
                                                                 class="caption"><?php echo $this->lang->line('Order Due Date') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required" id="tsn_due"
                                                   name="invocieduedate"
                                                   placeholder="Due Date" data-toggle="datepicker" autocomplete="false">
                                        </div>
                                    </div>
                                </div>

                               
                             
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 
							 

                            </div>
                        </div>

                    </div>


                    <div id="saman-row">
                        <table class="table-responsive ">
                            <thead>

                            <tr class="item_header bg-gradient-directional-amber">
                                <th width="20%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="5%" class="text-center">Required <?php echo $this->lang->line('Quantity') ?></th>
                                <th width="10%" class="text-center">Unit</th>
                                <th width="8%" class="text-center">Received <?php echo $this->lang->line('Quantity') ?></th>
                                <th width="8%" class="text-center">Balance <?php echo $this->lang->line('Quantity') ?></th>
                                <th width="8%" class="text-center">Supplier </th>
                                <th width="8%" class="text-center"> Fill <?php echo $this->lang->line('Rate') ?></th>
								<th width="25%" class="text-center">
                                    Description
                                </th>
                                <th width="8%" class="text-center">
                                    <?php echo $this->lang->line('Amount') ?>
                                    (<?php echo $this->config->item('currency'); ?>)
                                </th>
								
                              
                            </tr>
                            </thead>
                            <tbody>
							
							   <?php 
        $i = 1;
		$subtotal=0;
        foreach ($quote_items as $item) {
			
			
			 $converted = convert_to_base_unit($item['unit']);


        $prqty = $converted['qty'] * $item['total_qty'];
		
		$baseunit = $converted['unit'];
		
			$original_unit = $item['unit'];
			
            $unit = $baseunit ?? 'N/A';
       
			$subtotal = $subtotal+ $item['total_purchase'];
        ?>
		
                            <tr>
                                <td><input type="text" class="form-control text-center" name="product_name[]"
                                           placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                                           id='productname-<?=$i?>' value="<?= htmlspecialchars($item['productname']) ?>">
                                </td>
                                <td><input type="text" class="form-control req amnt" name="required_qty[]" id="required_qty-<?=$i?>"
                                           readonly 
                                           autocomplete="off" value="<?= number_format($prqty, 2)?>"></td>
        <td><select class="form-control prounits" id="prounit-<?=$i?>" name="product_unit[]"><option value="<?= $unit ?>"><?= $unit ?></option></select></td>
		
		<td><input type="text" class="form-control req" name="received_qty[]" id="receivedqty-<?=$i?>"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off" value="<?= number_format($prqty, 2)?>"></td>
										   
										   
                              


										   <td><input type="text" class="form-control req prc" name="balance_qty[]" id="balance-<?=$i?>"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off" placeholder="Balance Qty"></td> 
<td>
    <input type="hidden" name="supplier_id[]" id="supplier_id-<?= $i ?>">
    <input type="text" class="form-control supplier-input" 
           data-row="<?= $i ?>" 
           id="supplier-<?= $i ?>" 
           placeholder="Search Supplier">
</td>

									  <td><input type="text" class="form-control req prc" name="product_price[]" id="price-<?=$i?>"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off"></td>
                            
                               
                          
										    <td><textarea id="dpid-<?=$i?>" class="form-control" name="product_description[]"
                                                          placeholder="<?php echo $this->lang->line('Enter Product description'); ?>"
                                                          autocomplete="off"></textarea><br></td>
														  
                                <td><span class="currenty"><?= currency($this->aauth->get_user()->loc); ?></span>
                                    <strong><span class='ttlText' id="result-<?=$i?>">0</span></strong></td>
									
														  
                             
                               
                            </tr>
                           
   <?php
$i++;

   } ?>
                          
                          

                         

                          
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"></td>
                                <td colspan="2">
                                   
                                </td>
                                <td align="right" colspan="4"><input type="submit" class="btn btn-success sub-btn"
                                                                     value="Submit"
                                                                     id="submit-data" data-loading-text="Creating...">

                                </td>
                            </tr>


                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" value="indent/submitreceiveditem" id="action-url">
           

                </form>
            </div>

        </div>
    </div>
</div>



<script>
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
                <input type="hidden" name="employee_phone" value="${supplierPhone}">  
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