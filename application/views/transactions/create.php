<div class="content-body">
    <!-- Modal Popup for Initial Transaction -->
    <div class="modal fade" id="initialPopup" tabindex="-1" role="dialog" aria-labelledby="popupTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="initial-form">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Today Received Amount</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Received From</label>
                            
							
							      <select name="pay_acc" class="form-control">
                                <?php 
							$recivername= "";								
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
                                } ?>
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
    </div>

    <!-- Main Transaction Form -->
    <div class="card">
        <div class="card-header">
            <h4><?php echo $this->lang->line('Add New Transaction') ?> 
                <a href="<?php echo base_url('transactions/categories') ?>" class="btn btn-primary btn-sm rounded"><?php echo $this->lang->line('Transaction Categories') ?></a>
                <a href="<?php echo base_url('accounts/add') ?>" class="btn btn-blue btn-sm rounded">Add New Account</a>
                <a href="<?php echo base_url('transactions/view') ?>" class="btn btn-success btn-sm rounded"><i class="fa fa-eye"></i> View Transaction</a>
                <a href="<?php echo base_url('statements/view') ?>" class="btn btn-dark btn-sm rounded"><i class="fa fa-file-alt"></i> View Statement</a>
            </h4>
        </div>
        <hr>
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div class="card-body">
                <form method="post" id="data_form">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label">From Account / Sender</label>
                            <select name="pay_acc" class="form-control">
                                <?php foreach ($accounts as $row) { 
                                    if ($row['account_type'] == 'Basic') {
                                        if($this->aauth->get_user()->roleid == 1){
                                            echo "<option value='{$row['id']}'>{$row['acn']} - {$row['holder']} - ( ₹ {$row['lastbal']})</option>";
                                        } else {
                                            if($row['employeeID'] == $this->aauth->get_user()->id){
                                                echo "<option value='{$row['id']}' selected>{$row['acn']} - {$row['holder']} - ( ₹ {$row['lastbal']}) </option>";
                                            }
                                        }
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div id="transaction-fields">
                        <div class="transaction-group form-group row bg-blue bg-lighten-4 pb-1">
                            <input type="hidden" name="pay_catss[]" value="Expenses">
                           <!-- <div class="col-sm-5">
                                <label class="col-form-label">C/o / Receiver <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <select name="receiver_type[]" class="form-control receiver-type">
                                        <option value="employee">Employee</option>
                                        <option value="other">Other</option>
                                        <option value="Basic">Basic</option>
                                    </select>
                                    <input type="hidden" name="payer_id[]" class="customer_id" value="0">
                                    <input type="text" class="form-control receiver-autocomplete required" name="payer_name[]" id="receiver-autocomplete-0" placeholder="Start typing to search...">
                                    <div class="sbox-result"></div>
                                </div>
                            </div>-->
							
	<div class="col-sm-4">
  <label class="col-form-label">C/o / Receiver <span style="color: red;">*</span></label>
  <div class="row">
    <div class="col-sm-4 pr-1">
      <select name="receiver_type[]" class="form-control receiver-type">
        <option value="">All</option>
        <option value="employee">Employee</option>
        <option value="other">Other</option>
        <option value="Basic">Basic</option>
      </select>
    </div>
    <div class="col-sm-8 pl-1">
      <input type="hidden" name="payer_id[]" class="customer_id" value="0">
      <select class="form-control receiver-dropdown required" name="payer_name[]" id="receiver-dropdown-0" style="width:100%;">
        <option value="">Select or search receiver...</option>
      </select>
    </div>
  </div>
</div>



                            <div class="col-sm-2">
                                <label class="col-form-label">Category</label>
                                <select name="pay_cat[]" class="form-control" >
                                     <option value="">-- Select Category --</option>
                    <?php
                    foreach ($cat as $row) {
                        echo '<option value="' . $row["id"] . '" parentcat="' . $row["parent_cat"] . '">' . $row["name"] . '</option>';
                    }
                    ?>
                                </select>
                            </div>

							<div class="col-sm-2">
                                <label class="col-form-label">Type</label>
                                <select name="pay_type[]" class="form-control" readonly>
                                    <option value="Expense" selected>Expense / Debit</option>
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <label class="col-form-label">Amount</label>
                                <input type="text" placeholder="Amount" class="form-control margin-bottom required" name="amount[]" onkeypress="return isNumber(event)">
                            </div>
                            <div class="col-sm-3">
                                <label class="col-form-label">Notes</label>
                                <input type="text" placeholder="Notes" class="form-control margin-bottom" name="notes[]">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-primary" id="add-more">Add More</button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <input type="submit" id="submit-data" class="btn btn-success btn-lg margin-bottom" value="Add transaction" data-loading-text="Adding...">
                            <input type="hidden" value="transactions/save_trans" id="action-url">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
  const baseurl = "<?= base_url(); ?>";

  const rolId = "<?=$this->aauth->get_user()->roleid?>";
  if (rolId != 1) $("#initialPopup").modal("show");

  $("#initial-form").submit(function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    $.post(baseurl + $("#action-url").val(), formData, function () {
      $("#notify .message").html("Initial transaction saved successfully.");
      $("#notify").show();
      $("#initialPopup").modal("hide");
      $("#data_form").fadeIn();
    }).fail(() => alert("Something went wrong."));
  });

  // Initialize first dropdown
  setupReceiverDropdown("#receiver-dropdown-0");

  // Add More button
  $("#add-more").click(function () {
    const transactionFields = $("#transaction-fields");
    const newGroup = transactionFields.find(".transaction-group").first().clone();

    newGroup.find("input, select").val("");
    newGroup.find(".customer_id").val("0");

    const uniqueID = "receiver-dropdown-" + Date.now();
    newGroup.find(".receiver-dropdown").attr("id", uniqueID);

    transactionFields.append(newGroup);
    setupReceiverDropdown("#" + uniqueID);
  });

  // Initialize Select2 dropdown with default all data
  function setupReceiverDropdown(selector) {
    const $dropdown = $(selector);

    $dropdown.select2({
      placeholder: "Select or search receiver...",
      ajax: {
        url: baseurl + "search_products/myparty_search_transection",
        dataType: "json",
        delay: 300,
        data: function (params) {
          const group = $(this).closest(".transaction-group");
          const type = group.find(".receiver-type").val();
          return {
            keyword: params.term || "",
            ty: type || "all" // default load all
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data, function (item) {
              return {
                id: item.id,
                text: item.name + (item.phone ? " (" + item.phone + ")" : "")
              };
            })
          };
        },
        cache: true
      }
    }).on('select2:select', function (e) {
      const data = e.params.data;
      const group = $(this).closest(".transaction-group");
      group.find(".customer_id").val(data.id);
    });

    // Load default data on open (show all)
    $dropdown.on('select2:open', function () {
      const group = $(this).closest(".transaction-group");
      const type = group.find(".receiver-type").val();
      $.ajax({
        url: baseurl + "search_products/myparty_search_transection",
        data: { keyword: '', ty: type || 'all' },
        dataType: 'json',
        success: function (data) {
          const newOptions = data.map(item => new Option(item.name + (item.phone ? " (" + item.phone + ")" : ""), item.id, false, false));
          $dropdown.empty().append(newOptions);
        }
      });
    });

    // Refetch when receiver type changes
    $dropdown.closest(".transaction-group").find(".receiver-type").on("change", function () {
      $dropdown.val(null).trigger("change");
    });
  }
});
</script>


<!-- Styles -->
<style>

.receiver-type {
  min-width: 100%;
}

.select2-container {
  width: 100% !important;
}
  .sbox-result {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
  }
  .sbox-result li {
    padding: 8px 10px;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }
  .sbox-result li:hover {
    background-color: #f1f1f1;
  }
  .transaction-group {
    margin-bottom: 20px;
  }
  .card-header .btn {
    margin-left: 5px;
    font-size: 14px;
    padding: 8px 12px;
  }
</style>
