<div class="content-body">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title"><?php echo $this->lang->line('Manage Invoices') ?> <a href="<?php echo base_url('invoices/create') ?>" class="btn btn-primary btn-sm rounded"> <?php echo $this->lang->line('Add new') ?></a></h4> <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
      <div class="heading-elements">
        <ul class="list-inline mb-0">
          <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
          <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
          <li><a data-action="close"><i class="ft-x"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="card-content">
      <div id="notify" class="alert alert-success" style="display:none;"> <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2"><select name="employee"
                                            class=" mt-1 col form-control form-control-sm">
											<option>All</option>
                                        <?php foreach ($employee as $row) {
                                            echo '<option value="' . $row['id'] . '">' . $row['name'] . ' (' . $row['username'] . ')</option>';
                                        } ?>

                                    </select></div>
          <div class="col-md-2"> <input type="text" name="start_date" id="start_date" class="date30 form-control form-control-sm" autocomplete="off" /> </div>
          <div class="col-md-2"> <input type="text" name="end_date" id="end_date" class="form-control form-control-sm" data-toggle="datepicker" autocomplete="off" /> </div>
		   <div class="col-md-2"><select class="form-control form-control-sm" id="txttype" name="txttype">
					<option value="All">All</option>
					<option value="due" >Due</option>
					<option value="partial" >Partial</option>
					<option value="paid" >Paid</option>
					
					</select></div>
          <div class="col-md-2"> <input type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm" /> </div>
          <div class="col-md-2">
            <div id="totalSale"></div>
          </div>
        </div>
        <hr>
        <div class="table-responsive">
        <table id="invoices" class="table table-striped table-bordered zero-configuration ">
          <thead>
            <tr>
              <th><?php echo $this->lang->line('No') ?></th>
              <th> #</th>
              <th><?php echo $this->lang->line('Customer') ?></th>
              <th><?php echo $this->lang->line('Date') ?></th>
              <th>Sell Amount</th>
			  
             	<?php if($this->aauth->get_user()->roleid==1){
											echo '<th class="text-xs-left">Purchase Amount</th>';
											echo '<th class="text-xs-left">Profit</th>';
											echo '<th class="text-xs-left">Margin</th>';
											
										}?>
              <th>Created By</th>
              <th><?php echo $this->lang->line('Status') ?></th>
              <th class="no-sort"><?php echo $this->lang->line('Settings') ?></th>
            </tr>
          </thead>
          <tbody> </tbody>
          <tfoot>
            <tr>
              <th><?php echo $this->lang->line('No') ?></th>
              <th> #</th>
              <th><?php echo $this->lang->line('Customer') ?></th>
              <th><?php echo $this->lang->line('Date') ?></th>
              <th>
                <div id="totalAmount"></div>
              </th>
			  	<?php if($this->aauth->get_user()->roleid==1){
											echo '<th class="text-xs-left"><div id="totalPurchase"></div></th>  ';
											echo '<th class="text-xs-left"><div id="totalprofit"></div></th> ';
											echo '<th class="text-xs-left">Margin</th>';
											
										}?>
			   <th>Created By</th>
              <th><?php echo $this->lang->line('Status') ?></th>
              <th class="no-sort"><?php echo $this->lang->line('Settings') ?></th>
            </tr>
          </tfoot>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="delete_model" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('Delete Invoice') ?></h4> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <p><?php echo $this->lang->line('delete this invoice') ?> ?</p>
      </div>
      <div class="modal-footer"> <input type="hidden" id="object-id" value=""> <input type="hidden" id="action-url" value="invoices/delete_i"> <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button> <button type="button" data-dismiss="modal" class="btn"><?php echo $this->lang->line('Cancel') ?></button> </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    draw_data();

    function draw_data(start_date = '', end_date = '' , txttype='All') {
        var table = $('#invoices').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ],
            <?php datatable_lang();?>
            'order': [],
            'ajax': {
                'url': "<?php echo site_url('invoices/ajax_list')?>",
                'type': 'POST',
                'data': {
                    '<?=$this->security->get_csrf_token_name()?>': crsf_hash,
                    start_date: start_date,
                    end_date: end_date,
					txttype:  txttype
                },
                'dataSrc': function(json) {
                    if (json.totalsale !== undefined) {
                        $('#totalSale').html('<span style="font-size:20px; font-weight:bold; color:red"> Total Sales: ₹ ' + json.totalsale.toFixed(2) + '</span>');
                    }
                    if (Array.isArray(json.data)) {
                        return json.data;
                    } else {
                        console.error('Data array is missing or not an array.');
                        return [];
                    }
                }
            },
            'columnDefs': [{
                'targets': [0],
                'orderable': false,
            }],
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                footer: true,
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            }],
            drawCallback: function() {
                calculateTotalAmount();
            },
        });

        function calculateTotalAmount() {
            var total = 0;
            table.column(4, {search: 'applied'}).data().each(function(value) {
                total += parseFloat(value) || 0;
            }); 

			var totalpur = 0;
            table.column(5, {search: 'applied'}).data().each(function(value) {
                totalpur += parseFloat(value) || 0;
            });
			
			var totalprofit = 0;
            table.column(6, {search: 'applied'}).data().each(function(value) {
                totalprofit += parseFloat(value) || 0;
            });

            // ✅ Margin column (percentage) average calculation
            var totalMarginPercent = 0;
            var marginCount = 0;
            table.column(7, {search: 'applied'}).data().each(function(value) {
                // Remove % symbol and extra spaces
                var num = parseFloat(value.toString().replace('%', '').trim());
                if (!isNaN(num)) {
                    totalMarginPercent += num;
                    marginCount++;
                }
            });
            var avgMargin = ((total - totalpur) / totalpur) * 100;

            // ✅ Update footer values
            $('#totalAmount').html('Total Sell: ' + total.toFixed(2));
            $('#totalPurchase').html('Total Purchase: ' + totalpur.toFixed(2));
            $('#totalprofit').html('Total Profit: ' + totalprofit.toFixed(2));

            // ✅ Margin footer display
            $('tfoot th:contains("Margin")').html('Avg Margin: ' + avgMargin.toFixed(2) + '%');
        }
    };

    $('#search').click(function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
		var txttype = $("#txttype option:selected").val();
        if (start_date != '' && end_date != '') {
            $('#invoices').DataTable().destroy();
            draw_data(start_date, end_date, txttype);
        } else {
            alert("Date range is Required");
        }
    });
});
</script>
