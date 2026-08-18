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
                                                                                          class="btn btn-primary btn-sm rounded"
                                                                                          data-toggle="modal"
                                                                                          data-target="#addCustomer">
                                                <?php echo $this->lang->line('Add Client') ?>
                                            </a>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12"><label for="cst"
                                                                            class="caption"><?php echo $this->lang->line('Search Client') ?></label>
                                        <input type="text" class="form-control" name="cst" id="customer-box"
                                               placeholder="Enter Customer Name or Mobile Number to search"
                                               autocomplete="off"/>

                                        <div id="customer-box-result"></div>
                                    </div>

                                </div>
                                <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Client Details') ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                                        <div id="customer_name"></div>
                                    </div>
                                    <div class="clientinfo">

                                        <div id="customer_address1"></div>
                                    </div>

                                    <div class="clientinfo">

                                        <div type="text" id="customer_phone"></div>
                                    </div>
                                    <hr>
                                    <div id="customer_pass"></div>
                                    <?php echo $this->lang->line('Warehouse') ?> <select id="s_warehouses"
                                                                                         class="selectpicker form-control">
                                        <?php echo $this->common->default_warehouse();
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
                                                class="title">Challan</h3>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption">Challan Number</label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Quote #"
                                                   name="invocieno"
                                                   value="<?php echo $lastinvoice + 1 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6"><label for="invocieno"
                                                                 class="caption"> Reference / Po No. </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Reference / Po No"
                                                   name="refer">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-sm-6"><label for="invociedate"
                                                                 class="caption"> Challan Date</label>

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
                                                                 class="caption">Vendor Code </label>

                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                 aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required"
                                                   name="invocieduedate"
                                                   placeholder="Due Date" value="20022323" autocomplete="false" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="taxformat"
                                               class="caption"> <?php echo $this->lang->line('Tax') ?></label>
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
                                    <div class="col-sm-12">
                                        <label for="toAddInfo"
                                               class="caption"> Chalan Note </label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea></div>
                                </div>

                            </div>
                        </div>

                    </div>

                  
<input type="hidden"  name="propos" >
                    <div id="saman-row">
                        <table class="table-responsive tfr my_stripe">
                            <thead>
                            <tr class="item_header bg-gradient-directional-purple white">
                                <th width="30%" class="text-center"><?php echo $this->lang->line('Item Name') ?></th>
                                <th width="20%" class="text-center">Article</th>
                                <th width="20%" class="text-center">HP Number</th>
                                <th width="10%" class="text-center">UOM</th>
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Quantity') ?></th>
                           
                                <th width="10%" class="text-center"><?php echo $this->lang->line('Action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                        
							
							 <tr>
                                <td><input type="text" class="form-control" name="product_name[]"
                                           placeholder="<?php echo $this->lang->line('Enter Product name') ?>"
                                           id='productname-0'>
                                </td>
								
								<td><input type="text" class="form-control" name="product_article[]"
                                           placeholder="Article Number"
                                           id='article-0'>
                                </td>	

								<td><input type="text" class="form-control" name="hpnumber[]"
                                           placeholder="HP Number"
                                           id='hpnumber-0'>
                                </td>
										 <td>
                                             <select class="form-control prounits" name="product_unit[]" id="prounit-0" style="display: none;">
                                                 <option value="">Select</option>
                                             </select>
                                             <input type="text" class="form-control" id="prounit-display-0" readonly placeholder="UOM">
                                         </td>
                                <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-0"
                                           onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()"
                                           autocomplete="off" value="1"></td>
										   
                                
                                <td class="text-center">

                                </td>
                                <input type="hidden" name="product_price[]" id="price-0" value="0">
                                <input type="hidden" name="amount[]" id="result-0" value="0">
                                <input type="hidden" name="product_discount[]" id="discount-0" value="0">
                                <input type="hidden" name="product_tax[]" id="vat-0" value="0">
                                <input type="hidden" name="taxa[]" id="taxa-0" value="0">
                                <input type="hidden" name="disca[]" id="disca-0" value="0">
                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                <input type="hidden" name="unit[]" id="unit-0" value="">
                                <input type="hidden" name="hsn[]" id="hsn-0" value="">
                            </tr>

<tr class="last-item-row sub_c">
   <td class="add-row">
        <!-- Add Row button -->
        <button type="button" class="btn btn-success" id="addchalanproduct">
            <i class="icon-plus-square"></i> Add Row
        </button>


        <a class="btn btn-blue btn-md t_tooltip" href="#" onclick="addprodfunc()"> Add Product </a>

    
      
   </td> 
   
 
     <!-- Upload Excel button -->
        <button type="button" class="btn btn-info mb-0" id="uploadExcelBtn">
            <i class="icon-upload"></i> Upload from Excel
        </button>

        <!-- Hidden file input -->
        <input type="file" id="excelFileInput" accept=".xlsx,.xls" style="display:none;">
		

   <td colspan="7"></td>
</tr>



<input type="hidden" class="form-control shipVal"
                                                                    onkeypress="return isNumber(event)"
                                                                    placeholder="Value"
                                                                    name="shipping" autocomplete="off"
                                                                    onkeyup="billUpyog()">
																	<input type="hidden" value="0" id="subttlform"
                                                                     name="subtotal">
								<input type="hidden" name="total" class="form-control"
                                                                    id="invoiceyoghtml" readonly="">		

<input type="hidden" name="total" class="form-control"
                                                                    id="invoiceyoghtml" readonly="">																	
					

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
                            
                            </tr>
                            <tr class="sub_c" style="display: table-row;">
                                <td colspan="2"><?php echo $this->lang->line('Payment Terms') ?> <select name="pterms"
                                                                                                         class="selectpicker form-control"><?php foreach ($terms as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>

                                    </select></td>
                                <td align="right" colspan="6"><input type="submit" class="btn btn-success sub-btn"
                                                                     value="Generate Chalan"
                                                                     id="submit-data" data-loading-text="Creating...">

                                </td>
                            </tr>


                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" value="chalan/action" id="action-url">
                    <input type="hidden" value="search" id="billtype">
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
		</div>
		
<?php $this->load->view('customers/common_add_customer_modal', ['modal_id' => 'addCustomer', 'customergrouplist' => $customergrouplist, 'custom_fields_c' => $custom_fields_c]); ?>

<!-- Excel JS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
function addprodfunc(){
	
	
	 window.open("<?=base_url()?>products/add", "_blank"); 
}


document.getElementById("uploadExcelBtn").addEventListener("click", function() { document.getElementById("excelFileInput").click(); });


let rowCounter = document.querySelectorAll("#saman-row tbody tr").length;

document.getElementById("excelFileInput").addEventListener("change", function(e) {
    let file = e.target.files[0];
    if (!file) {
        alert("No file selected!");
        return;
    }

    notFoundHP = []; // reset on each upload

    let reader = new FileReader();
    reader.onload = function(event) {
        let data = new Uint8Array(event.target.result);
        let workbook = XLSX.read(data, { type: "array" });

        let sheetName = workbook.SheetNames[0];
        let sheet = workbook.Sheets[sheetName];
        let rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        if (rows.length <= 1) {
            alert("Excel file is empty or missing data!");
            return;
        }

        // build a clean list of HP numbers to query (skip header & empty rows)
        const hpList = [];
        for (let i = 1; i < rows.length; i++) {
            const r = rows[i];
            if (!r || r.length === 0) continue;
            const hpnumber = (r[2] || "").toString().trim(); // Column C (index 2)
            const qty = r[4] || 1; // Column D (index 3)
            if (hpnumber !== "") hpList.push({ hpnumber, qty });
        }

        if (hpList.length === 0) {
            alert("No HP numbers found in the file.");
            return;
        }

        // process list and show not-found after all AJAX calls complete
        let processed = 0;
        hpList.forEach(item => {
            fetchProductFromDB(item.hpnumber, item.qty, function() {
                processed++;
                if (processed === hpList.length) {
                    showNotFoundList();
                }
            });
        });
    };

    reader.readAsArrayBuffer(file);
});

// AJAX (with callback)
function fetchProductFromDB(hpnumber, qty, callback) {
    $.ajax({
        url: "<?= base_url('chalan/get_product_by_article'); ?>",
        type: "POST",
        data: { article: hpnumber },
        dataType: "json",
        success: function(res) {
            if (res && res.status === "success") {
                addProductRow(res.product_id, res.print_name, res.article_no, res.uom, qty, hpnumber);
            } else {
                notFoundHP.push(hpnumber);
            }
            callback();
        },
        error: function() {
            // network/server error -> treat as not found (or handle separately if you prefer)
            notFoundHP.push(hpnumber);
            callback();
        }
    });
}

function showNotFoundList() {
    if (!notFoundHP || notFoundHP.length === 0) return;

    // remove duplicates and keep insertion order
    const unique = Array.from(new Set(notFoundHP));

    let listHtml = "<tr class='hp-notfound-row'><td colspan='6'><strong>HP number not found</strong><br><ol>";
    unique.forEach(num => {
        listHtml += `<li>${num}</li>`;
    });
    listHtml += "</ol></td></tr>";

    // Prefer to insert after .last-item-row.sub_c. Fallbacks included.
    let anchor = document.querySelector(".last-item-row.sub_c");
    if (!anchor) anchor = document.querySelector(".last-item-row");
    if (anchor) {
        anchor.insertAdjacentHTML("afterend", listHtml);
    } else {
        // final fallback: append to the first tbody (if exists)
        const tb = document.querySelector("table tbody") || document.querySelector("table");
        if (tb) tb.insertAdjacentHTML("beforeend", listHtml);
        else console.warn("Could not find insertion point for not-found list. Please ensure there is a table in DOM.");
    }
}

// addProductRow uses global rowCounter
function addProductRow(productid, productName, article, uom, qty, hpnumber) {
    let html = `
        <tr>
            <td><input type="text" class="form-control" name="product_name[]" 
                       value="${productName}" placeholder="Enter Product name" id="productname-${rowCounter}"></td>
            <td><input type="text" class="form-control" name="product_article[]" 
                       value="${article}" placeholder="Article Number" id="article-${rowCounter}"></td> 
            <td><input type="text" class="form-control" name="hpnumber[]" 
                       value="${hpnumber}" placeholder="HP Number" id="hpnumber-${rowCounter}"></td>
            <td>
                <select class="form-control prounits" name="product_unit[]" id="prounit-${rowCounter}" style="display: none;">
                    <option value="">Select</option>
                    <option value="${uom}" selected>${uom}</option>
                </select>
                <input type="text" class="form-control" id="prounit-display-${rowCounter}" value="${uom}" readonly placeholder="UOM">
            </td>
            <td><input type="text" class="form-control req amnt" name="product_qty[]" 
                       id="amount-${rowCounter}" onkeypress="return isNumber(event)" 
                       onkeyup="rowTotal('${rowCounter}'), billUpyog()" autocomplete="off" value="${qty}"></td>
            <td class="text-center"></td>

            <input type="hidden" name="product_price[]" id="price-${rowCounter}" value="0">
            <input type="hidden" name="amount[]" id="result-${rowCounter}" value="0">
            <input type="hidden" name="product_discount[]" id="discount-${rowCounter}" value="0">
            <input type="hidden" name="product_tax[]" id="vat-${rowCounter}" value="0">
            <input type="hidden" name="taxa[]" id="taxa-${rowCounter}" value="0">
            <input type="hidden" name="disca[]" id="disca-${rowCounter}" value="0">
            <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${rowCounter}" value="0">
            <input type="hidden" class="pdIn" name="pid[]" id="pid-${rowCounter}" value="${productid}">
            <input type="hidden" name="unit[]" id="unit-${rowCounter}" value="">
            <input type="hidden" name="hsn[]" id="hsn-${rowCounter}" value="">
        </tr>
    `;
    // try to insert before the .last-item-row if present
    const insertBefore = document.querySelector(".last-item-row");
    if (insertBefore) {
        insertBefore.insertAdjacentHTML("beforebegin", html);
    } else {
        // fallback: append to first tbody
        const tb = document.querySelector("table tbody") || document.querySelector("table");
        if (tb) tb.insertAdjacentHTML("beforeend", html);
        else console.warn("Cannot find place to insert product row. Ensure there's a table in DOM.");
    }

    rowCounter++;
}

</script>


<script type="text/javascript">
    $(function () {
        $('.summernote').summernote({
            height: 100,
            tooltip:false,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['fullscreen', ['fullscreen']],
                ['codeview', ['codeview']]
            ]
        });
    });

</script>
