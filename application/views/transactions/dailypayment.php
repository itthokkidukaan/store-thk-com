<div class="card-body">
    <div class="row mb-2">
        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" id="from_date" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" id="to_date" class="form-control form-control-sm">
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary btn-sm" id="filter_btn">Filter</button>
            <button class="btn btn-secondary btn-sm" id="reset_btn">Reset</button>
        </div>
    </div>

    <table id="trans_table" class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th>Date</th>
            <th>Account</th>
            <th>Amount</th>
            <th>Notes</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">Total:</th>
            <th id="total_amount">₹ 0.00</th>
            <th colspan="2"></th>
        </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var trans_table;

    function load_table(from = '', to = '') {
        trans_table = $('#trans_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            ajax: {
                url: "<?php echo site_url('transactions/dailypaytranslist?type=income') ?>",
                type: "POST",
                data: {
                    from_date: from,
                    to_date: to,
                    '<?= $this->security->get_csrf_token_name() ?>': crsf_hash
                },
                dataSrc: function (json) {
                    // Set total in footer
                    $('#total_amount').html('₹ ' + parseFloat(json.total_amount).toFixed(2));
                    return json.data;
                }
            },
            columnDefs: [
                { targets: [0], orderable: true }
            ],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ]
        });
    }

    $(document).ready(function () {
        load_table(); // load default

        $('#filter_btn').click(function () {
            let from = $('#from_date').val();
            let to = $('#to_date').val();
            if (from !== '' && to !== '') {
                load_table(from, to);
            } else {
                alert('Please select both From and To dates');
            }
        });

        $('#reset_btn').click(function () {
            $('#from_date').val('');
            $('#to_date').val('');
            load_table(); // reset to default
        });
    });
</script>




<div id="delete_model" class="modal fade">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

                            aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"><?php echo $this->lang->line('Delete') ?></h4>

            </div>

            <div class="modal-body">

                <p><?php echo $this->lang->line('delete this transaction') ?></p>

            </div>

            <div class="modal-footer">

                <input type="hidden" id="object-id" value="">

                <input type="hidden" id="action-url" value="transactions/delete_i">

                <button type="button" data-dismiss="modal" class="btn btn-primary"

                        id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>

                <button type="button" data-dismiss="modal"

                        class="btn"><?php echo $this->lang->line('Cancel') ?></button>

            </div>

        </div>

    </div>

</div>