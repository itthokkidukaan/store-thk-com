<div class="content-body">

<!--
 <div class="modal fade" id="initialPopup" tabindex="-1" role="dialog" aria-labelledby="popupTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="initial-form">
                <div class="modal-content">
                   <div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Today Received Amount</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Received From</label>
                            
							
							      <select name="pay_acc" class="form-control">
                                <?php 
							/* $recivername= "";								
							$reciverId= "";								
								foreach ($accounts as $row) { 
								if($row['employeeID'] == $this->aauth->get_user()->id){
									
									$recivername= $row['holder'];
									$reciverId= $row['id'];
								}
                                    if ($row['account_type'] == 'Basic') {
                                        if($row['id'] == 5 ){
                                            echo "<option value='{$row['id']}'>{$row['acn']} - {$row['holder']}</option>";
                                        } 
                                    }
                                }  */
								
								?>
                            </select>
                        </div>
                        <input type="hidden" name="pay_cat[]" value="Expenses">
                        <input type="hidden" name="pay_type[]" value="Expense">
                        <input type="hidden" name="payer_names[]" value="<?=$recivername?>">
                        <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Received Amount</label>
                            <input type="text" name="amount[]" class="form-control" placeholder="Enter amount" required onkeypress="return isNumber(event)">
                        </div>
						
						<div class="form-group col-sm-6">
                                <label for="cardNumber">Payment Method</label>
                                <select class="form-control" name="paymethod" id="paymethod">
                                    <option value="">Select Payment Method</option>
                                    <option value="Due">Due</option>
                                    <option value="Cash" selected>Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Card Swipe">Card Swipe</option>
                                    <option value="Bank">Bank</option>

                                </select></div>
								</div>
                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="notes[]" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
				<?php
$payer_id = '';
$user_id = $this->aauth->get_user()->id;

foreach ($accounts as $acc) {
    if ($acc['employeeID'] == $user_id) {
        $payer_id = $acc['id'];
        break;
    }
}
?>

<input type="hidden" name="payer_id[]" value="<?php echo  $payer_id; ?>">
            </form>
        </div>
    </div> -->
	
	
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
                                        <input type="text" class="form-control" name="cst" id="supplier-box"
                                               value="<?= isset($supplier_details['name']) ? $supplier_details['name'] . ' (' . $supplier_details['phone'] . ')' : '' ?>"
                                               autocomplete="off"/>

                                        <div id="supplier-box-result"></div>
                                    </div>

                                </div>
                                <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Supplier Details') ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="<?= isset($supplier_details['id']) ? $supplier_details['id'] : 0 ?>">

<div id="customer_name"><?= isset($supplier_details['name']) ? $supplier_details['name'] : '' ?></div>
                                    </div>
									
								


                                    <div class="clientinfo">

                                       	<div id="customer_address1"><?= isset($supplier_details['address']) ? $supplier_details['address'] : '' ?> <?= isset($supplier_details['city']) ? ', ' . $supplier_details['city'] : '' ?></div>
                                    </div>

                                    <div class="clientinfo">

                                       <div type="text" id="customer_phone"><?= isset($supplier_details['phone']) ? $supplier_details['phone'] : '' ?></div>
                                    </div>

                                </div>


                            </div>
							
                        </div>
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">


                                <div class="form-group row">

                                    <div class="col-sm-12"><h3
                                                class="title">Purchase Challan </h3>
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
                                                   value="<?php echo $lastinvoice + 1 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Reference') ?> </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Reference #"
                                                   name="refer">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">

                                  <div class="col-sm-6">
    <label for="invociedate" class="caption"><?php echo $this->lang->line('Order Date') ?> </label>
    <div class="input-group">
        <div class="input-group-addon">
            <span class="icon-calendar4" aria-hidden="true"></span>
        </div>
      <input type="text" 
       class="form-control required" 
       placeholder="Billing Date" 
       name="invoicedate" 
       id="invoicedate" 
       data-toggle="mydatepicker" 
       autocomplete="off">
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
                        <table class="table-responsive tfr my_stripe">
                            <thead>

                            <tr class="item_header bg-gradient-directional-amber">
                                <th width="20%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="5%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
                                <th width="10%" class="text-center">Unit</th>
                                <th width="8%" class="text-center"><?php echo $this->lang->line('Rate') ?></th>
                                <th width="8%" class="text-center"><?php echo $this->lang->line('Tax') ?>(%)</th>
                                <th width="8%" class="text-center"><?php echo $this->lang->line('Tax') ?></th>
                                <th width="7%" class="text-center"><?php echo $this->lang->line('Discount') ?></th>
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
                          <!--  <tr>
                                <td><input type="text" class="form-control text-center" name="product_name[]"
                                           placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                                           id='productname-0'>
                                </td>
                                <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off" value="1"></td>
        <td><select class="form-control prounits" id="prounit-0" name="product_unit[]"><option value="">Select</option></select></td>
                                <td><input type="text" class="form-control req prc" name="product_price[]" id="price-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off"></td>
                                <td><input type="text" class="form-control vat " name="product_tax[]" id="vat-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off"></td>
                                <td class="text-center" id="texttaxa-0">0</td>
                                <td><input type="text" class="form-control discount" name="product_discount[]"
                                           onkeypress="return isNumber(event)" id="discount-0"
                                           onkeyup="rowTotal('0'), billUpyog()" autocomplete="off"></td>
										    <td><textarea id="dpid-0" class="form-control" name="product_description[]"
                                                          placeholder="<?php echo $this->lang->line('Enter Product description'); ?>"
                                                          autocomplete="off"></textarea><br></td>
                                <td><span class="currenty"><?php echo $this->config->item('currency'); ?></span>
                                    <strong><span class='ttlText' id="result-0">0</span></strong></td>
									
														  
                                <td class="text-center">

                                </td>
                                <input type="hidden" name="taxa[]" id="taxa-0" value="0">
                                <input type="hidden" name="disca[]" id="disca-0" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                <input type="hidden" name="unit[]" id="unit-0" value="">
                                <input type="hidden" name="varid[]" id="varid-0" value="">
                                <input type="hidden" name="margintype[]" id="margintype-0" value="">
                                <input type="hidden" name="margin[]" id="margin-0" value="">
                                <input type="hidden" name="prodisc[]" id="prodisc-0" value="">
                                <input type="hidden" name="producttype[]" id="producttype-0" value="">
                                <input type="hidden" name="hsn[]" id="hsn-0" value="">
                            </tr> -->
                           
						 <?php if (!empty($quotation_items)): ?>
    <?php foreach ($quotation_items as $k => $item): ?>
        <tr>
            <td><input type="text" class="form-control text-center" name="product_name[]"
                       placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                       id="productname-<?= $k ?>"
                       value="<?= $item['item_name'] ?>"></td>

            <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-<?= $k ?>"
                       onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $k ?>'), billUpyog()"
                       autocomplete="off" value="<?= $item['quantity'] ?>"></td>

            <td>
                <select class="form-control prounits" id="prounit-<?= $k ?>" name="product_unit[]">
                    <option value="<?= $item['unit'] ?>" selected><?= $item['unit'] ?></option>
                </select>
            </td>

            <td><input type="text" class="form-control req prc" name="product_price[]" id="price-<?= $k ?>"
                       onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $k ?>'), billUpyog()"
                       autocomplete="off" value="<?= $item['fill_rate'] ?>"></td>

            <td><input type="text" class="form-control vat" name="product_tax[]" id="vat-<?= $k ?>"
                       onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $k ?>'), billUpyog()"
                       autocomplete="off" value="0"></td>

            <td class="text-center" id="texttaxa-<?= $k ?>">0</td>

            <td><input type="text" class="form-control discount" name="product_discount[]"
                       onkeypress="return isNumber(event)" id="discount-<?= $k ?>"
                       onkeyup="rowTotal('<?= $k ?>'), billUpyog()" autocomplete="off" value="0"></td>

            <td><textarea id="dpid-<?= $k ?>" class="form-control" name="product_description[]"
                          placeholder="<?php echo $this->lang->line('Enter Product description'); ?>"
                          autocomplete="off"><?= $item['description'] ?></textarea><br></td>

            <td><span class="currenty"><?php echo $this->config->item('currency'); ?></span>
                <strong><span class='ttlText' id="result-<?= $k ?>"><?= number_format($item['amount'], 2) ?></span></strong>
            </td>

            <td class="text-center"></td>

            <!-- Hidden Inputs -->
            <input type="hidden" name="taxa[]" id="taxa-<?= $k ?>" value="0">
            <input type="hidden" name="disca[]" id="disca-<?= $k ?>" value="0">
            <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-<?= $k ?>" value="<?= $item['amount'] ?>">
            <input type="hidden" class="pdIn" name="pid[]" id="pid-<?= $k ?>" value="<?= $item['item_id'] ?>" >
            <input type="hidden" name="unit[]" id="unit-<?= $k ?>" value="<?= $item['unit'] ?>">
            <input type="hidden" name="varid[]" id="varid-<?= $k ?>" value="">
            <input type="hidden" name="margintype[]" id="margintype-<?= $k ?>" value="">
            <input type="hidden" name="margin[]" id="margin-<?= $k ?>" value="">
            <input type="hidden" name="prodisc[]" id="prodisc-<?= $k ?>" value="">
            <input type="hidden" name="producttype[]" id="producttype-<?= $k ?>" value="">
            <input type="hidden" name="hsn[]" id="hsn-<?= $k ?>" value="">
        </tr>
    <?php endforeach; ?>
<?php endif; ?>



                            <tr class="last-item-row">
                                <td class="add-row">
                                    <button type="button" class="btn btn-success" aria-label="Left Align"
                                            id="addproduct">
                                        <i class="fa fa-plus-square"></i> <?php echo $this->lang->line('Add Row') ?>
                                    </button>
                                </td>
                                <td colspan="9"></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="8" align="right"><input type="hidden" value="0" id="subttlform"
                                                                     name="subtotal"><strong><?php echo $this->lang->line('Total Tax') ?></strong>
                                </td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="taxr" class="lightMode">0</span></td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="8" align="right">
                                    <strong><?php echo $this->lang->line('Total Discount') ?></strong></td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency'); ?></span>
                                    <span id="discs" class="lightMode">0</span></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="8" align="right">
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
                                <td colspan="2"><?php if ($exchange['active'] == 1){
                                    echo $this->lang->line('Payment Currency client') . ' <small>' . $this->lang->line('based on live market') ?></small>
                                    <select name="mcurrency"
                                            class="selectpicker form-control">
                                        <option value="0">Default</option>
                                        <?php foreach ($currency as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['symbol'] . ' (' . $row['code'] . ')</option>';
                                        } ?>

                                    </select><?php } ?></td>
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

                    <input type="hidden" value="purchase/action" id="action-url">
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
    $(window).on('load', function () {
        setTimeout(function () {
            <?php foreach ($quotation_items as $k => $item): ?>
                rowTotal('<?= $k ?>');
            <?php endforeach; ?>
            billUpyog();
        }, 100); // wait 100ms to ensure DOM is fully ready
    });
</script>
<script>


$(document).ready(function () {
	
	
	 
  //  $("#data_form").hide(); // hide main form
  var rolId = "<?=$this->aauth->get_user()->roleid?>";
 // alert(rolId);
  if(rolId !=1){
    $("#initialPopup").modal("show"); // show popup
  }
    // Handle popup form submit
    $("#initial-form").submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url:  '<?=base_url()?>transactions/save_trans',
            type: "POST",
            data: formData,
            success: function () {
                $("#notify .message").html("Initial transaction saved successfully.");
                $("#notify").show();
                $("#initialPopup").modal("hide");
                $("#data_form").fadeIn();
            },
            error: function () {
                alert("Something went wrong.");
            }
        });
    });
});


    document.addEventListener('DOMContentLoaded', function () {
        var dateInput = document.getElementById('tsn_due');
        var today = new Date();
        today.setDate(today.getDate() + 7); // Add 7 days

        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        var formattedDate = dd + '-' + mm + '-' + yyyy;

        dateInput.value = formattedDate;
    });
</script>
