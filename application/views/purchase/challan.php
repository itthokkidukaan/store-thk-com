
<article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="card-body">
            <h5><?php echo $this->lang->line('Quotation') ?></h5>

            <hr>
            <div class="card card-block">
                <h4>Purchase Challan</h4>
                <hr>
             
            </div>
            <hr>
			
			<div class="row mb-3">
    <div class="col-md-3">
        <label>From Date</label>
        <input type="date" id="from_date" class="form-control" placeholder="From Date">
    </div>
    <div class="col-md-3">
        <label>To Date</label>
        <input type="date" id="to_date" class="form-control" placeholder="To Date">
    </div>
    <div class="col-md-3">
        <label>&nbsp;</label><br>
        <button type="button" id="filter" class="btn btn-primary">Filter</button>
        <button type="button" id="reset" class="btn btn-secondary">Reset</button>
    </div>
</div>
            <table id="invoices" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                      <th><?php echo $this->lang->line('No') ?></th>
                    <th><?php echo $this->lang->line('Order') ?> #</th>
					<th>Supplier Name</th>
                    <th><?php echo $this->lang->line('Date') ?></th>
                    <th>Total Item</th>            
					<th>Fill Item</th>
                    <th class="no-sort"><?php echo $this->lang->line('Status') ?></th>
                    <th class="no-sort">Action</th>
                   


                </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                <tr>
                    <th><?php echo $this->lang->line('No') ?></th>
                    <th><?php echo $this->lang->line('Order') ?> #</th>
					<th>Supplier Name</th>
                    <th><?php echo $this->lang->line('Date') ?></th>
                    <th>Total Item</th>            
					<th>Fill Item</th>
                    <th class="no-sort"><?php echo $this->lang->line('Status') ?></th>
                    <th class="no-sort">Action</th>

                </tr>
                </tfoot>
            </table>
        </div>
    </div>


</article>
<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('Delete') ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo $this->lang->line('delete this order') ?></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="purchase/delete_i">
                <button type="button" data-dismiss="modal" class="btn btn-primary"
                        id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                <button type="button" data-dismiss="modal"
                        class="btn"><?php echo $this->lang->line('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        var table = $('#invoices').DataTable({
            "processing": true,
            "serverSide": true,
            responsive: true,
            <?php datatable_lang();?>
            "order": [],
            "ajax": {
                "url": "<?php echo site_url('purchase/supplierchallanlist')?>",
                "type": "POST",
                "data": function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d['<?=$this->security->get_csrf_token_name()?>'] = crsf_hash;
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false,
                },
            ],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                }
            ],
        });

        $('#filter').on('click', function () {
            table.ajax.reload();
        });

        $('#reset').on('click', function () {
            $('#from_date').val('');
            $('#to_date').val('');
            table.ajax.reload();
        });

    });
</script>
