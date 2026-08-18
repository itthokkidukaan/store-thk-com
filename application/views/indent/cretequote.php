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
                                            <?php echo $this->lang->line('Bill From') ?> <a href='#'
                                                                                            class="btn btn-primary btn-sm rounded"
                                                                                            data-toggle="modal"
                                                                                            data-target="#addCustomer">
                                                <?php echo $this->lang->line('Add Supplier') ?>
                                            </a>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Supplier') ?> </label>
                                        <input type="text" class="form-control" name="cst" id="mysupplier-box"
                                               placeholder="Enter Supplier Name or Mobile Number to search"
                                               autocomplete="off"/>

                                        <div id="mysupplier-box-result"></div>
                                    </div>

                                </div>
          <div id="customer" class="row">
    <div class="clientinfo col-sm-10">
        <label style="font-weight: bold;">Selected Suppliers:</label><br>
        <div id="selected-suppliers" >
            <!-- These .selected-supplier divs will be inserted via JavaScript -->
        </div>
    </div>
</div>




                            </div>
							 <div class="form-group row">
							  <div class="frmSearch col-sm-6"><label for="cst"
                                                                            class="caption">Select Seller</label>
                                        <select id="s_sellers" class="selectpicker form-control">
                                       
										<?php foreach ($sellers as $row) {
                                            echo '<option value="' . $row['seller_id'] . '">' . $row['seller_name'] . '('.$row['mobile'] .')</option>';
                                        } ?>

                                    </select>

                                        <div id="mysupplier-box-result"></div>
                                    </div>
                                    </div>
                        </div>
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">


                                <div class="form-group row">

                                    <div class="col-sm-12"><h3
                                                class="title"> Convert Quote</h3>
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

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="taxformat"
                                               class="caption"><?php echo $this->lang->line('Tax') ?> </label>
                                        <select class="form-control round"
                                                onchange="changeTaxFormat(this.value)"
                                                id="taxformat">
                                            <?php echo $taxlist; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">

                                        <div class="form-group">
                                            <label for="discountFormat"
                                                   class="caption"><?php echo $this->lang->line('Discount') ?></label>
                                            <select class="form-control" onchange="changeDiscountFormat(this.value)"
                                                    id="discountFormat">
                                                <?php echo $this->common->disclist() ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
								
								
                                  

                               
                                    <div class="col-sm-6">
                                        <label for="toAddInfo"
                                               class="caption"><?php echo $this->lang->line('Order Note') ?> </label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea></div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div id="saman-row">
                        <table class="table-responsive ">
                            <thead>

                            <tr class="item_header bg-gradient-directional-amber">
                                <th width="20%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="5%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
                                <th width="10%" class="text-center">Unit</th>
                                <th width="8%" class="text-center">Fill <?php echo $this->lang->line('Quantity') ?></th>
                                <th width="8%" class="text-center"> Fill <?php echo $this->lang->line('Rate') ?></th>
								<th width="25%" class="text-center">
                                    Description
                                </th>
                                <th width="8%" class="text-center">
                                    <?php echo $this->lang->line('Amount') ?>
                                    (<?php echo $this->config->item('currency'); ?>)
                                </th>
								
                                <th width="5%" class="text-center"><?php echo $this->lang->line('Action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
							
							   <?php 
        $i = 1;
		$subtotal=0;
		//print_r($quote_items);
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
		
		<td><input type="text" class="form-control req" name="product_qty[]" id="product_qty-<?=$i?>"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off" value="<?= number_format($prqty, 2)?>"></td>
										   
										   
                                <td><input type="text" class="form-control req prc" name="product_price[]" id="price-<?=$i?>"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off"></td>
                            
                               
                          
										    <td><textarea id="dpid-<?=$i?>" class="form-control" name="product_description[]"
                                                          placeholder="<?php echo $this->lang->line('Enter Product description'); ?>"
                                                          autocomplete="off"></textarea><br></td>
														  
                                <td><span class="currenty"><?= currency($this->aauth->get_user()->loc); ?></span>
                                    <strong><span class='ttlText' id="result-<?=$i?>">0</span></strong></td>
									
														  
                                /* <td class="text-center">

                                </td> */
                                <input type="hidden" name="taxa[]" id="taxa-<?=$i?>" value="0">
                                <input type="hidden" name="disca[]" id="disca-<?=$i?>" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-<?=$i?>" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-<?=$i?>" value="<?=$item['pid']?>">
                                <input type="hidden" name="unit[]" id="unit-<?=$i?>" value="<?= htmlspecialchars($item['original_unit']) ?>">
                                <input type="hidden" name="varid[]" id="varid-<?=$i?>" value="<?= htmlspecialchars($item['varid']) ?>">
                                <input type="hidden" name="margintype[]" id="margintype-<?=$i?>" value="">
                                <input type="hidden" name="margin[]" id="margin-<?=$i?>" value="">
                                <input type="hidden" name="prodisc[]" id="prodisc-<?=$i?>" value="">
                                <input type="hidden" name="producttype[]" id="producttype-<?=$i?>" value="">
                                <input type="hidden" name="hsn[]" id="hsn-<?=$i?>" value="">
                            </tr>
                           
   <?php
$i++;

   } ?>
                            <tr class="last-item-row">
                                <td class="add-row">
                                    <button type="button" class="btn btn-success" aria-label="Left Align"
                                            id="addproduct">
                                        <i class="fa fa-plus-square"></i> <?php echo $this->lang->line('Add Row') ?>
                                    </button>
                                </td>
                                <td colspan="7"></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" align="right"><input type="hidden" value="0" id="subttlform"
                                                                     name="subtotal"><strong><?php echo $this->lang->line('Total Tax') ?></strong>
                                </td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="taxr" class="lightMode">0</span></td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" align="right">
                                    <strong><?php echo $this->lang->line('Total Discount') ?></strong></td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="discs" class="lightMode">0</span></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" align="right">
                                    <strong><?php echo $this->lang->line('Shipping') ?></strong></td>
                                <td align="left" colspan="2"><input type="text" class="form-control shipVal"
                                                                    onkeypress="return isNumber(event)"
                                                                    placeholder="Value"
                                                                    name="shipping" autocomplete="off"
                                                                    onkeyup="billUpyog();">
                                    ( <?php echo $this->lang->line('Tax') ?> <?= $this->config->item('currency'); ?>
                                    <span id="ship_final">0</span> )
                                </td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                               
                                <td colspan="6" align="right"><strong><?php echo $this->lang->line('Grand Total') ?>
                                        (<span
                                                class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>)</strong>
                                </td>
                                <td align="left" colspan="2"><input type="text" name="total" class="form-control"
                                                                    id="invoiceyoghtml" readonly="">

                                </td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"><?php echo $this->lang->line('Payment Terms') ?> <select name="pterms"
                                                                                                         class="selectpicker form-control"><?php foreach ($terms as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>

                                    </select></td>
                                <td colspan="2">
                                    <div>
                                        <label><?php echo $this->lang->line('Update Stock') ?></label>
                                        <fieldset class="right-radio">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="update_stock"
                                                       id="customRadioRight1" value="yes" checked="">
                                                <label class="custom-control-label"
                                                       for="customRadioRight1"><?php echo $this->lang->line('Yes') ?></label>
                                            </div>
                                        </fieldset>
                                        <fieldset class="right-radio">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="update_stock"
                                                       id="customRadioRight2" value="no">
                                                <label class="custom-control-label"
                                                       for="customRadioRight2"><?php echo $this->lang->line('No') ?></label>
                                            </div>
                                        </fieldset>

                                    </div>
                                </td>
                                <td align="right" colspan="4"><input type="submit" class="btn btn-success sub-btn"
                                                                     value="<?php echo $this->lang->line('Generate Order') ?>"
                                                                     id="submit-data" data-loading-text="Creating...">

                                </td>
                            </tr>


                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" value="indent/store_puquote" id="action-url">
                    <input type="hidden" value="puchase_search" id="billtype">
                    <input type="hidden" value="0" name="counter" id="ganak">
                    <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">
                    <input type="hidden" value="<?= $taxdetails['handle']; ?>" name="taxformat" id="tax_format">

                    <input type="hidden" value="<?= $taxdetails['format']; ?>" name="tax_handle" id="tax_status">
                    <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">


                    <input type="hidden" value="<?= $this->common->disc_status()['disc_format']; ?>"
                           name="discountFormat" id="discount_format">
                    <input type="hidden" value="<?= amountFormat_general($this->common->disc_status()['ship_rate']); ?>"
                           name="shipRate"
                           id="ship_rate">
                    <input type="hidden" value="<?= $this->common->disc_status()['ship_tax']; ?>" name="ship_taxtype"
                           id="ship_taxtype">
                    <input type="hidden" value="0" name="ship_tax" id="ship_tax">

                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addCustomer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="product_action" class="form-horizontal">
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-directional-success white">

                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('Add Supplier') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">


                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="name"><?php echo $this->lang->line('Name') ?></label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Name"
                                   class="form-control margin-bottom" id="mcustomer_name" name="name" required>
                        </div>
                    </div>

                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="phone"><?php echo $this->lang->line('Phone') ?></label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Phone"
                                   class="form-control margin-bottom" name="phone" id="mcustomer_phone">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label" for="email">Email</label>

                        <div class="col-sm-10">
                            <input type="email" placeholder="Email"
                                   class="form-control margin-bottom crequired" name="email" id="mcustomer_email">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="address"><?php echo $this->lang->line('Address') ?></label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Address"
                                   class="form-control margin-bottom " name="address" id="mcustomer_address1">
                        </div>
                    </div>
                    <div class="form-group row">


                        <div class="col-sm-4">
                            <input type="text" placeholder="City"
                                   class="form-control margin-bottom" name="city" id="mcustomer_city">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Region"
                                   class="form-control margin-bottom" name="region">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Country"
                                   class="form-control margin-bottom" name="country" id="mcustomer_country">
                        </div>

                    </div>

                    <div class="form-group row">


                        <div class="col-sm-6">
                            <input type="text" placeholder="PostBox"
                                   class="form-control margin-bottom" name="postbox">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" placeholder="TAX ID"
                                   class="form-control margin-bottom" name="taxid" id="tax_id">
                        </div>
                    </div>


                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                    <input type="submit" id="msupplier_add" class="btn btn-primary submitBtn"
                           value="<?php echo $this->lang->line('ADD') ?>"/>
                </div>
            </form>
        </div>
    </div>
</div>
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
    $(document).on('input', 'input[name="product_qty[]"], input[name="product_price[]"]', function () {
        calculateAllRows();
    });

    function calculateAllRows() {
        let grandTotal = 0;

        $('tbody tr').each(function () {
            const $row = $(this);
            const qty = parseFloat($row.find('input[name="product_qty[]"]').val()) || 0;
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
        url: baseurl + 'search_products/mysupplier',
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
$(document).on('click', '.select-supplier', function () {
    let supplierId = $(this).data('id');
    let supplierName = $(this).data('name');
    let supplierPhone = $(this).data('phone');
    let supplierAddress = $(this).data('address');

    // Avoid duplicates
    if ($("#supplier-" + supplierId).length === 0) {
        let html = `
            <div class="col-sm-4 selected-supplier" id="supplier-${supplierId}">
                <input type="hidden" name="supplier_ids[]" value="${supplierId}">
                <input type="hidden" name="supplier_phone[]" value="${supplierPhone}">
                <input type="hidden" name="supplier_name[]" value="${supplierName}">
                <strong>${supplierName}</strong><br>
                ${supplierPhone}<br>
                ${supplierAddress}<br>
                <button type="button" class="btn btn-danger btn-sm remove-supplier" data-id="${supplierId}">Remove</button>
                <hr>
            </div>
        `;
        $("#customer").append(html);
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