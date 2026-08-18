<div class="content-body">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="card-body">
            <h6><?php echo $this->lang->line('Account Statement') ?></h6>

            <hr>
            <p><?php echo $this->lang->line('Account') ?> : <?php echo $filter[5] ?></p><form method="post" action="<?=base_url()?>reports/viewstatement">
   <div class="row">          <div class="col-md-2"> <input type="text" name="sdate" id="sdate" class="date30 form-control form-control-sm" autocomplete="off" value="<?=$this->input->post('sdate')?>" /> </div>          <div class="col-md-2"> <input type="text" name="edate" id="end_date" class="form-control form-control-sm" data-toggle="datepicker" autocomplete="off" value="<?=$this->input->post('edate')?>" /> </div>		   <input type="hidden" name="trans_type" value="<?=$this->input->post('trans_type')?>" >		   <input type="hidden" name="pay_acc" value="<?=$this->input->post('pay_acc')?>" >          <div class="col-md-2"> <input type="submit" name="search" id="search" value="Search" class="btn btn-info btn-sm" /> </div>                 </div>
</form>
            <table class="table table-striped table-bordered zero-configuration">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('Date') ?></th>
                    <th><?php echo $this->lang->line('Description') ?></th>

                    <th><?php echo $this->lang->line('Debit') ?></th>
                    <th><?php echo $this->lang->line('Credit') ?></th>

                    <th><?php echo $this->lang->line('Balance') ?></th>


                </tr>
                </thead>
                <tbody id="entries">
                </tbody>

                <tfoot>
                <tr>
                    <th><?php echo $this->lang->line('Date') ?></th>
                    <th><?php echo $this->lang->line('Description') ?></th>

                    <th><?php echo $this->lang->line('Debit') ?></th>
                    <th><?php echo $this->lang->line('Credit') ?></th>

                    <th><?php echo $this->lang->line('Balance') ?></th>


                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">    $(document).ready(function () {        $('#entries').html('<td class="text-lg-center" colspan="5">Data loading...</td>');        $.ajax({            url: baseurl + 'reports/statements',            type: 'POST',            data: {                "ac": "5",                "sd": "2025-07-04 00:00:00",                "ed": "2025-08-03 00:00:00",                "ty": "All",                "ci_csrf_token": "crsf_hash"            },            dataType: 'html',            success: function (data) {                $('#entries').html(data);                let totalDebit = 0;                let totalCredit = 0;                $('#entries tr').each(function () {                    let debitText = $(this).find('td').eq(2).text().replace(/[^0-9.-]+/g, '');                    let creditText = $(this).find('td').eq(3).text().replace(/[^0-9.-]+/g, '');                    let debit = parseFloat(debitText) || 0;                    let credit = parseFloat(creditText) || 0;                    totalDebit += debit;                    totalCredit += credit;                });                var totalRow = '<tr>' +                    '<th colspan="2" class="text-right">Total</th>' +                    '<th>₹ ' + totalDebit.toFixed(2) + '</th>' +                    '<th>₹ ' + totalCredit.toFixed(2) + '</th>' +                    '<th></th>' +                    '</tr>';                $('table tfoot').html(totalRow);            },            error: function () {                $('#entries').html('<td colspan="5">Error loading data.</td>');            }        });    });</script>
