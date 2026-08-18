
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
                        <div class="col-sm-4"></div>
                        <div class="col-sm-3"></div>
                        <div class="col-sm-2"></div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="row">
                        <!-- LEFT PANEL -->
                        <div class="col-sm-6 cmp-pnl">
                            <div id="customerpanel" class="inner-cmp-pnl">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <h3 class="title"><?php echo $this->lang->line('Bill To'); ?></h3>
                                    </div>
                                </div>

                              

                                <!-- CUSTOMER SEARCH -->
                                <div class="form-group row">
                                    <div class="frmSearch col-sm-12">
                                        <label for="cst" class="caption">
                                            <?php echo $this->lang->line('Search Customer'); ?>
                                        </label>
                                        <input type="text" class="form-control" name="cst" id="customer-box"
                                            placeholder="Enter Customer Name or Mobile Number to search"
                                            autocomplete="off" />
                                        <div id="customer-box-result"></div>
                                    </div>
                                </div>
  <!-- 🔹 NEW: ORDER ID SEARCH -->
                             <div class="form-group row">
  <div class="frmSearch col-sm-12">
    <label for="order_search" class="caption">Enter Order ID</label>
    <input type="text" class="form-control" id="order_search"
           placeholder="Enter Order ID" disabled />
    <div id="order-box-result"></div> <!-- Suggestion list will appear here -->
  </div>
</div>

                                <!-- CUSTOMER DETAILS -->
                                <div id="customer">
                                    <div class="clientinfo">
                                        <?php echo $this->lang->line('Customer Details'); ?>
                                        <hr>
                                        <input type="hidden" name="customer_id" id="customer_id" value="0">
                                        <div id="customer_name"></div>
                                    </div>
                                    <div class="clientinfo">
                                        <div id="customer_address1"></div>
                                    </div>
                                    <div class="clientinfo">
                                        <div id="customer_phone"></div>
                                    </div>

                                    <hr>
                                    <?php echo $this->lang->line('Warehouse'); ?>
                                    <select id="warehouses" class="selectpicker form-control">
                                        <option value="0"><?php echo $this->lang->line('All'); ?></option>
                                        <?php foreach ($warehouse as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT PANEL -->
                        <div class="col-sm-6 cmp-pnl">
                            <div class="inner-cmp-pnl">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <h4 class="title">
                                            <?php echo $this->lang->line('Customer'); ?>
                                            <?php echo $this->lang->line('Stock Return'); ?>
                                        </h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="invocieno" class="caption"><?php echo $this->lang->line('Order'); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <span class="icon-file-text-o" aria-hidden="true"></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Invoice #"
                                                name="invocieno" value="<?php echo $lastinvoice + 1 ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="refer" class="caption"><?php echo $this->lang->line('Reference'); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-bookmark-o"
                                                    aria-hidden="true"></span></div>
                                            <input type="text" class="form-control" placeholder="Reference #" name="refer">
                                        </div>
                                    </div>
                                </div>

                                <!-- DATE FIELDS -->
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label for="invociedate" class="caption"><?php echo $this->lang->line('Order Date'); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required" name="invoicedate" placeholder="Billing Date" data-toggle="datepicker" autocomplete="false">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="invocieduedate" class="caption"><?php echo $this->lang->line('Order Due Date'); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="icon-calendar-o" aria-hidden="true"></span></div>
                                            <input type="text" class="form-control required" id="tsn_due" name="invocieduedate" placeholder="Due Date" data-toggle="datepicker" autocomplete="false">
                                        </div>
                                    </div>
                                </div>

                                <!-- ORDER NOTE -->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="return_note" class="caption">Return Note / Remarks</label>
                                        <textarea class="form-control" name="return_note" rows="3" placeholder="Write a note for this return..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ITEMS TABLE -->
                    <div id="saman-row">
                        <table class="table-responsive tfr my_stripe">
                            <thead>
                                <tr class="item_header bg-gradient-directional-pink white">
                                    <th width="30%" class="text-center"><?php echo $this->lang->line('Item Name'); ?></th>
                                    <th width="8%" class="text-center">Article Number</th>
                                    <th width="8%" class="text-center">UOM</th>
                                    <th width="8%" class="text-center"><?php echo $this->lang->line('Quantity'); ?></th>
                                    <th width="10%" class="text-center"><?php echo $this->lang->line('Rate'); ?></th>
                                    <th width="10%" class="text-center"><?php echo $this->lang->line('Tax(%)'); ?></th>
                                    <th width="10%" class="text-center"><?php echo $this->lang->line('Tax'); ?></th>
                                    <th width="7%" class="text-center"><?php echo $this->lang->line('Discount'); ?></th>
                                    <th width="10%" class="text-center"><?php echo $this->lang->line('Amount'); ?></th>
                                    <th width="5%" class="text-center"><?php echo $this->lang->line('Action'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="order_item_list">
                                <!-- dynamically filled -->
                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" value="stockreturn/action" id="action-url">
                    <input type="hidden" value="1" name="person_type">
                    <input type="hidden" value="puchase_search" id="billtype">
                    <input type="hidden" value="0" name="counter" id="ganak">
                    <input type="hidden" value="<?php echo $this->config->item('currency'); ?>" name="currency">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 JS LOGIC -->
<script>
$(document).ready(function(){

    // ✅ Watch for customer selection change
    // When common search sets customer_id, this triggers
    const observer = new MutationObserver(() => {
        let cust_id = $("#customer_id").val();
        if (cust_id && cust_id !== "0" && cust_id !== "") {
            $("#order_search").prop("disabled", false); // enable order box
        }
    });

    // Observe changes to #customer_id element
    observer.observe(document.getElementById('customer_id'), { attributes: true, attributeFilter: ['value'] });

    // 🔹 Order search logic
    $("#order_search").keyup(function(){
        let cust_id = $("#customer_id").val();
        if(!cust_id || cust_id === "0") return; // safety check
        $.ajax({
            type: "POST",
            url: baseurl + "stockreturn/search_orders",
            data: {keyword: $(this).val(), customer_id: cust_id, [crsf_token]: crsf_hash},
            success: function(data){
                $("#order-box-result").show().html(data);
            }
        });
    });

    // 🔹 Select order → load items
    $(document).on("click", ".order-select", function(){
        let order_id = $(this).data("id");
        $("#order_search").val(order_id);
        $("#order-box-result").hide();
        $.ajax({
            type: "POST",
            url: baseurl + "stockreturn/load_order_items",
            data: {order_id: order_id, [crsf_token]: crsf_hash},
            success: function(data){
                $("#order_item_list").html(data);
            }
        });
    });

    // 🔹 Remove unwanted row
    $(document).on("click", ".remove-row", function(){
        $(this).closest("tr").remove();
    });
});
</script>
<style>
#order-box-result {
    position: absolute;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    width: 96%;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 2px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    display: none;
}

#order-box-result .order-select {
   padding: 10px 15px;
  border-bottom: 1px solid #4d4444;
  cursor: pointer;
  transition: background 0.2s ease;
  font-size: 14px;
  color: #3e0e0e;
  background: #2dcee3;
}

#order-box-result .order-select:hover {
    background: #b51212;
}

#order-box-result .order-select:last-child {
    border-bottom: none;
}

#order-box-result::-webkit-scrollbar {
    width: 6px;
}
#order-box-result::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
</style>


