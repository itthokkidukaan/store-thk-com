<div class="row">
    <div class="col-md-12">
        <form method="get">
            <input type="hidden" name="id" value="<?php echo $this->input->get('id'); ?>">
            <div class="form-group row">
                <label class="col-sm-1 col-form-label">From</label>
                <div class="col-sm-3">
                    <input type="date" name="from" class="form-control" value="<?= $from ?>">
                </div>
                <label class="col-sm-1 col-form-label">To</label>
                <div class="col-sm-3">
                    <input type="date" name="to" class="form-control" value="<?= $to ?>">
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?= base_url('supplier/ledger_view?id='.$this->input->get('id')) ?>" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<table id="ts_table" class="table table-striped table-bordered zero-configuration" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="10%">Date</th>
            <th width="20%" >Type</th>
            <th width="10%">Debit</th>
            <th width="10%">Credit</th>
            <th width="10%" >Balance</th>
            <th width="10%" >Account</th>
            <th width="10%">Method</th>
            <th width="20%">Note</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $balance = 0;
        $total_debit = 0;
        $total_credit = 0;

        foreach ($ledger as $row) {
            $balance += $row['credit'] - $row['debit'];
            $total_debit += $row['debit'];
            $total_credit += $row['credit'];

            $invoice = '';
            if ($row['type'] === 'Purchase') {
                $inv = $row['invoice'];
                $invoice = ' of invoice <a href="' . base_url() . 'purchase/view?id=' . $inv . '">' . $row['reference'] . '</a>';
            }

            // ✅ Date format change here
            $formatted_date = date('d-m-Y', strtotime($row['date']));

            $debit = ($row['debit'] == 0) ? '' : "₹ " . number_format($row['debit'], 2);
            $credit = ($row['credit'] == 0) ? '' : "₹ " . number_format($row['credit'], 2);
            $formatted_balance = "₹ " . number_format($balance, 2);

            echo "<tr>
                <td>{$formatted_date}</td>
                <td>{$row['type']} $invoice <br> {$row['note']}</td>
                <td>$credit</td>
                <td>$debit</td>
                <td>$formatted_balance</td>
                <td>{$row['account']}</td>
                <td>{$row['method']}</td>
                <td>{$row['customnote']}</td>
            </tr>";
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align:right">Total</th>
            <th>₹ <?= number_format($total_credit, 2) ?></th>
            <th>₹ <?= number_format($total_debit, 2) ?></th>
           
            <th colspan="4"></th>
        </tr>

		<tr>
            <th colspan="2" style="text-align:right">Balance</th>
            <th /> </th>
            
            <th style="color:#d00c0c;">₹ <?= number_format($balance, 2) ?></th>
            <th colspan="4"></th>
        </tr>
       
    </tfoot>
</table>
