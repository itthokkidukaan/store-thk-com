<div class="content-body">

    <div class="card">

        <div class="card-header">

            <h3>Transecton Statement</h3>

            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

            <div class="heading-elements">

                <ul class="list-inline mb-0">

                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>

                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>

                    <li><a data-action="close"><i class="ft-x"></i></a></li>

                </ul>

            </div>

        </div>

        <div class="card-content">


    <h4><?php echo $title; ?></h4>
    <p><b>From:</b> <?php echo $sdate; ?> &nbsp;&nbsp; <b>To:</b> <?php echo $edate; ?></p>

    <div class="mb-3">
        <a href="<?php echo base_url("transactions/export_pdf/{$account_id}/{$sdate}/{$edate}"); ?>" 
           class="btn btn-danger btn-sm"><i class="fa fa-file-pdf"></i> Download PDF</a>

        <a href="<?php echo base_url("transactions/export_excel/{$account_id}/{$sdate}/{$edate}"); ?>" 
           class="btn btn-success btn-sm"><i class="fa fa-file-excel"></i> Download Excel</a>
    </div>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Account</th>
                <th>Payer</th>
                <th>Method</th>
                <th>Note</th>
                <th class="text-end">Debit (Expense)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            if(!empty($records)){
                foreach($records as $i => $row){
                    echo "<tr>
                        <td>".($i+1)."</td>
                        <td>{$row['date']}</td>
                        <td>{$row['account']}</td>
                        <td>{$row['payer']}</td>
                        <td>{$row['method']}</td>
                        <td>{$row['note']}</td>
                        <td class='text-end'>".number_format($row['debit'],2)."</td>
                    </tr>";
                    $total += $row['debit'];
                }
            } else {
                echo '<tr><td colspan="7" class="text-center text-danger">No Expense Records Found!</td></tr>';
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <th colspan="6" class="text-end">Total Expense</th>
                <th class="text-end"><?php echo number_format($total,2); ?></th>
            </tr>
        </tfoot>
    </table>
</div>

</div>
</div>
