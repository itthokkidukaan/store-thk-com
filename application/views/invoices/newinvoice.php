<?php $is_seller_invoice = function_exists('is_seller_user') && is_seller_user(); ?>
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
                        <div class="col-sm-6 cmp-pnl">
                            <div id="customerpanel" class="inner-cmp-pnl">
                                <div class="form-group row">
                                    <div class="fcol-sm-12">
                                        <h3 class="title">
                                            <?php echo $this->lang->line('Bill To') ?> <a href='#'
                                                                                          class="btn btn-primary btn-sm round"
                                                                                          data-toggle="modal"
                                                                                          data-target="#addCustomer">
                                                <?php echo $this->lang->line('Add Client') ?>
                                            </a>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Client'); ?></label>
                                        <input type="text" class="form-control round" name="cst" id="customer-box"
                                        <input type="text" class="form-control round" name="cst" id="customer-box"
                                               placeholder="Enter Customer Name or Mobile Number to search"
                                               autocomplete="off"/>
                                        <div id="customer-box-result"></div>
                                    </div>
                                </div>
                                <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Client Details'); ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                                        <input type="hidden" name="customerphone" id="customerphone" value="0">
                                        <div id="customer_name"></div>
                                    </div>
                                    <div class="clientinfo">
                                        <div id="customer_address1"></div>
                                    </div>

                                    <div class="clientinfo">

                                        <div id="customer_phone"></div>
                                    </div>
                                    <hr>
                                    <div id="customer_pass"></div><?php echo $this->lang->line('Warehouse') ?> <select
                                            id="s_warehouses"
                                            class="form-control round">
                                        <?php if (!$is_seller_invoice) {
                                            echo $this->common->default_warehouse();
                                        }
                                        echo '<option value="0">' . $this->lang->line('All') ?></option><?php foreach ($warehouse as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>

                                    </select>
                                </div>


                            </div>
                        </div>
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">


                                <div class="form-group row">

                                    <div class="col-sm-12"><h3
                                                class="title"><?php echo $this->lang->line('Invoice Properties') ?></h3>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Invoice Number') ?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round" placeholder="Invoice #"
                                                   name="invocieno"
                                                   value="<?php echo $lastinvoice + 1 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4"><label for="invocieno"
                                                                 class="caption"><?php echo $this->lang->line('Reference') ?> / PO No.</label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round" placeholder="Reference #"
                                                   name="refer">
                                        </div>
                                    </div> 

									<div class="col-sm-4"><label for="invocieno"
                                                                 class="caption">Chalan No.</label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round" placeholder="Chalan #"
                                                   name="chalan">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
									
									
										<div class="col-sm-4"><label for="invocieno"
                                                                 class="caption">Vendor Code.</label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round" value="20022323"  name="vendocode" readonly>
                                        </div>
                                    </div>
									
                                    <div class="col-sm-4"><label for="invociedate"
                                                                 class="caption"><?php echo $this->lang->line('Invoice Date'); ?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar4"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round required"
                                                   placeholder="Billing Date" name="invoicedate"
                                                 value="<?php echo date("d-m-Y");?>"
                                                   autocomplete="false" data-toggle="datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-sm-4"><label for="invocieduedate"
                                                                 class="caption"><?php echo $this->lang->line('Invoice Due Date') ?></label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control round required"
                                                   name="invocieduedate"
                                                   placeholder="Due Date" autocomplete="false"    data-toggle="datepicker">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="taxformat"
                                               class="caption"><?php echo $this->lang->line('Tax') ?></label>
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
                                            <select class="form-control round"
                                                    onchange="changeDiscountFormat(this.value)"
                                                    id="discountFormat">

                                                <?php echo $this->common->disclist() ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="toAddInfo"
                                               class="caption"><?php echo $this->lang->line('Invoice Note') ?></label>
                                        <textarea class="form-control round" name="notes" rows="2"></textarea></div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div id="saman-row">
                        <table class="table-responsive tfr my_stripe">

                            <thead>
                            <tr class="item_header bg-gradient-directional-blue white">
                                <th width="30%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="8%" class="text-center">Article Number</th>
                                <th width="8%" class="text-center">UOM</th>
                                <th width="8%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Rate') ?></th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Tax(%)') ?></th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Tax') ?></th>
                                <th width="7%" class="text-center"><?php echo $this->lang->line('Discount') ?></th>
                                <th width="10%" class="text-center">
                                    <?php echo $this->lang->line('Amount') ?>
                                    (<?= currency($this->aauth->get_user()->loc); ?>)
                                </th>
                                <th width="5%" class="text-center"><?php echo $this->lang->line('Action') ?></th>
                            </tr>

                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" class="form-control" name="product_name[]"
                                           placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                                           id='productname-0'>
                                </td>
								<td><input type="text" class="form-control req amnt" name="product_article[]" id="article-0"
                                           onkeypress="return isNumber(event)" 
                                           autocomplete="off" ></td>
								<td>
								<select class="form-control prounits" name="product_unit[]" id="prounit-0"><option value="">Select</option></select></td>
                                <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off" value="1"><input type="hidden" id="alert-0" value=""
                                                                               name="alert[]"></td>
								
										   
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
                                <td><span class="currenty"><?= currency($this->aauth->get_user()->loc); ?></span>
                                    <strong><span class='ttlText' id="result-0">0</span></strong></td>
                                <td class="text-center">

                                </td>
                                <input type="hidden" name="taxa[]" id="taxa-0" value="0">
                                <input type="hidden" name="disca[]" id="disca-0" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                <input type="hidden" name="unit[]" id="unit-0" value="">
                                <input type="hidden" name="hsn[]" id="hsn-0" value="">
                                <input type="hidden" name="serial[]" id="serial-0" value="">
                                <input type="hidden" id="dpid-0" class="form-control" name="product_description[]" value="">
								<input type="hidden" name="prodpurprice[]" id="prodpurprice-0" value="">
                            </tr>
                       

                            <tr class="last-item-row sub_c">
                                <td class="add-row">
                                    <button type="button" class="btn btn-success" aria-label="Left Align"
                                            id="addproductbutton">
                                        <i class="fa fa-plus-square"></i> <?php echo $this->lang->line('Add Row') ?>
                                    </button>
                                </td>
                                <td colspan="7"></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" class="reverse_align"><input type="hidden" value="0" id="subttlform"
                                                                     name="subtotal"><strong><?php echo $this->lang->line('Total Tax') ?></strong>
                                </td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?= $this->config->item('currency'); ?></span>
                                    <span id="taxr" class="lightMode">0</span></td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" class="reverse_align">
                                    <strong><?php echo $this->lang->line('Total Discount') ?></strong></td>
                                <td align="left" colspan="2"><span
                                            class="currenty lightMode"><?php echo $this->config->item('currency');
                                        if (isset($_GET['project'])) {
                                            echo '<input type="hidden" value="' . intval($_GET['project']) . '" name="prjid">';
                                        } ?></span>
                                    <span id="discs" class="lightMode">0</span></td>
                            </tr>

                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" class="reverse_align">
                                    <strong><?php echo $this->lang->line('Shipping') ?></strong></td>
                                <td align="left" colspan="2"><input type="text" class="form-control shipVal"
                                                                    onkeypress="return isNumber(event)"
                                                                    placeholder="Value"
                                                                    name="shipping" autocomplete="off"
                                                                    onkeyup="billUpyog()">
                                    ( <?php echo $this->lang->line('Tax') ?> <?= $this->config->item('currency'); ?>
                                    <span id="ship_final">0</span> )
                                </td>
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="6" class="reverse_align">
                                    <strong> <?php echo $this->lang->line('Extra') . ' ' . $this->lang->line('Discount') ?></strong>
                                </td>
                                <td align="left" colspan="2"><input type="text"
                                                                    class="form-control form-control-sm discVal"
                                                                    onkeypress="return isNumber(event)"
                                                                    placeholder="Value"
                                                                    name="disc_val" autocomplete="off" value="0"
                                                                    onkeyup="billUpyog()">
                                    <input type="hidden"
                                           name="after_disc" id="after_disc" value="0">
                                    ( <?= $this->config->item('currency'); ?>
                                    <span id="disc_final">0</span> )
                                </td>
                            </tr>


                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"><?php if (isset($employee)){
                                       echo $this->lang->line('Employee')
                                ?><br>
                                    <select name="employee"
                                            class=" mt-1 col form-control form-control-sm">

                                        <?php foreach ($employee as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['name'] . ' (' . $row['name'] . ')</option>';
                                        } ?>

                                    </select><?php } ?><br><?php if ($exchange['active'] == 1){
                                    echo $this->lang->line('Payment Currency client') . ' <small>' . $this->lang->line('based on live market') ?></small>
                                    <select name="mcurrency"
                                            class="selectpicker form-control">
                                        <option value="0">Default</option>
                                        <?php foreach ($currency as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['symbol'] . ' (' . $row['code'] . ')</option>';
                                        } ?>

                                    </select><?php } ?></td>
                                <td colspan="4" class="reverse_align"><strong><?php echo $this->lang->line('Grand Total') ?>
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
                                <td class="reverse_align" colspan="6"><input type="submit"
                                                                     class="btn btn-success sub-btn btn-lg"
                                                                     value="<?php echo $this->lang->line('Generate Invoice') ?> "
                                                                     id="submit-mydata" data-loading-text="Creating...">

                                </td>
                            </tr>


                            </tbody>
                        </table>

                        <?php
                        if(is_array($custom_fields)){
                          echo'<div class="card">';
                                    foreach ($custom_fields as $row) {
                                        if ($row['f_type'] == 'text') { ?>
                                            <div class="row mt-1">

                                                <label class="col-sm-8"
                                                       for="docid"><?= $row['name'] ?></label>

                                                <div class="col-sm-6">
                                                    <input type="text" placeholder="<?= $row['placeholder'] ?>"
                                                           class="form-control margin-bottom b_input <?= $row['other'] ?>"
                                                           name="custom[<?= $row['id'] ?>]">
                                                </div>
                                            </div>


                                        <?php }
                                    }
                                    echo'</div>';
                        }
                                    ?>
                    </div>
                    <input type="hidden" value="new_i" id="inv_page">
                    <input type="hidden" value="invoices/action" id="action-url">
                    <input type="hidden" value="search" id="billtype">
                    <input type="hidden" value="0" name="counter" id="ganak">
                    <input type="hidden" value="<?= currency($this->aauth->get_user()->loc); ?>" name="currency">
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
                    <input type="hidden" value="0" id="custom_discount">

                </form>
	<script>
// Add CSS for red border
const style = document.createElement('style');
style.innerHTML = `
    .price-error {
        background-color: #ffd6d6 !important;
        border: 2px solid red !important;
    }
`;
document.head.appendChild(style);

// Validate one price field
function validateSinglePrice(index) {
    const priceInput = document.getElementById(`price-${index}`);
    const purPriceInput = document.getElementById(`prodpurprice-${index}`);

    if (!priceInput || !purPriceInput) return;

    const price = parseFloat(priceInput.value) || 0;
    const purPrice = parseFloat(purPriceInput.value) || 0;

    if (price < purPrice) {
        priceInput.classList.add("price-error");
    } else {
        priceInput.classList.remove("price-error");
    }
}

// Bind change events on all price fields
function bindPriceChangeEvents() {
    document.querySelectorAll('input[id^="price-"]').forEach(function (priceInput) {
        const index = priceInput.id.substring("price-".length);
        if (!priceInput.dataset.bound) {
            priceInput.addEventListener("input", function () {
                validateSinglePrice(index);
            });
            priceInput.dataset.bound = "true"; // Prevent duplicate binding
        }
    });
}

// Validate all prices before submit
function validateAllPricesBeforeSubmit() {
    let valid = true;

    document.querySelectorAll('input[id^="price-"]').forEach(function (priceInput) {
        const index = priceInput.id.substring("price-".length);
        const purPriceInput = document.getElementById(`prodpurprice-${index}`);
        if (!purPriceInput) return;

        const price = parseFloat(priceInput.value) || 0;
        const purPrice = parseFloat(purPriceInput.value) || 0;

        if (price < purPrice) {
            priceInput.classList.add("price-error");
            valid = false;
        } else {
            priceInput.classList.remove("price-error");
        }
    });

    return valid;
}

// Initial bind when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    bindPriceChangeEvents();
});

// Submit handler
document.getElementById("submit-mydata").addEventListener("click", function (e) {
    e.preventDefault();

    bindPriceChangeEvents(); // Ensure any newly added rows are bound
    const isValid = validateAllPricesBeforeSubmit();

    if (!isValid) {
        alert("❌ Error: Product price cannot be less than purchase price.");
        return false;
    }

    // Custom submit logic
    this.style.display = 'none';
    var o_data = $("#data_form").serialize();
    var action_url = $('#action-url').val();
    addObject(o_data, action_url);
    setTimeout(() => {
        this.style.display = 'inline-block';
    }, 1000);
});
</script>


<style>
.price-error {
  background-color: #ffd6d6 !important;
  border: 2px solid red !important;
}
</style>

            </div>

        </div>
    </div>
</div>






<div class="modal fade" id="variantModal" role="dialog">



       <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">
		 <div class="modal-header">

                <h4 class="modal-title">Select Variant</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
		  <div class="modal-body">
  <table id="variantOptions" class="table table-striped table-bordered table-hover text-center">
    <thead>
      <tr>
        <th>Print Name</th>
        <th>Article No</th>
        <th>UOM</th>
        <th>HP Number</th>
        <th>Select</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

<style>
#variantModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

#variantModal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    padding: 15px 20px;
}

#variantModal .modal-header .close {
    color: white;
    opacity: 0.8;
    background: transparent;
    border: none;
    font-size: 28px;
    line-height: 20px;
}

#variantModal .modal-header .close:hover {
    opacity: 1;
}

#variantOptions {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
}

#variantOptions th {
    background-color: #f3f4f6;
    color: #4b5563;
    font-weight: 600;
    padding: 12px 10px;
    border-bottom: 2px solid #e5e7eb;
}

#variantOptions td {
    padding: 12px 10px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

#variantOptions tbody tr:hover {
    background-color: #f9fafb;
    transition: background-color 0.2s ease;
}

.selectVariantBtn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: white !important;
    border: none !important;
    padding: 6px 16px !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2) !important;
    transition: all 0.2s ease !important;
}

.selectVariantBtn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3) !important;
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
}

.selectVariantBtn:active {
    transform: translateY(0) !important;
}
</style>
		
		</div>
		</div>
		</div>
		</div><?php $this->load->view('customers/common_add_customer_modal', ['modal_id' => 'addCustomer', 'customergrouplist' => $customergrouplist, 'custom_fields_c' => $custom_fields_c]); ?>
