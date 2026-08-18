<div class="content-body">
       <div class="card">

        <div class="card-header">

            <h5><?php echo $this->lang->line('Transactions') ?> <a

                        href="<?php echo base_url('transactions/add') ?>"

                        class="btn btn-primary btn-sm rounded">

                    <?php echo $this->lang->line('Add new') ?>

                </a></h5>

            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

            <div class="heading-elements">

                <ul class="list-inline mb-0">

                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>

                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>

                    <li><a data-action="close"><i class="ft-x"></i></a></li>

                </ul>

            </div>

        </div>
<?php
print_r($accounts);

 ?>
 
      <div class="card-body">

            <div id="notify" class="alert alert-success" style="display:none;">

                <a href="#" class="close" data-dismiss="alert">&times;</a>



                <div class="message"></div>

            </div>





            <hr>
    <div class="row mb-3">
        <div class="col">
            <select id="location" class="form-control">
                <option value="">Select Location</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc->loc ?>">THOK KI DUKAAN</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select id="accountType" class="form-control">
                <option value="">Select Account type</option>
              <option>Customer</option>
              <option>Supplier</option>
              <option>Employee</option>
            </select>
        </div> 

		<div class="col">
            <select id="account" class="form-control">
                <option value="">Select Account</option>
                <?php
					

				foreach ($accounts as $acc): ?>
                    <option value="<?= $acc->payerid ?>"><?= $acc->payer ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col"><input type="date" id="from_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        <div class="col"><input type="date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        <div class="col"><button id="filter" class="btn btn-primary">Filter</button></div>
    </div>

    <table id="trans_table" class="table table-bordered">
        <thead>
        <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Particulars</th>
            <th>Voucher Type</th>
            <th>Voucher No</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Closing</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Particulars</th>
            <th>Voucher Type</th>
            <th>Voucher No</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Closing</th>
        </tr>
        </tfoot>
    </table>
</div>

    </div>

</div>
<script>
    let table;
    $(document).ready(function () {
        $('#filter').click(function () {
            if ($('#account').val() === '') {
                alert('Please select an account first.');
                return;
            }

            if (table) {
                table.destroy();
            }

            table = $('#trans_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?= site_url('transactions/fetch_transactions') ?>",
                    type: "POST",
                    data: {
                        account: $('#account').val(),
                        location: $('#location').val(),
                        from_date: $('#from_date').val(),
                        to_date: $('#to_date').val(),
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    }
                },
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    footer: true,
                    exportOptions: { columns: ':visible' }
                }]
            });
        });
    });
</script>