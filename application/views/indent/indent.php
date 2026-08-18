<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Indent </h4>
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
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>

                <div class="message"></div>
            </div>
            <div class="card-body">
       <div class="filter-container">
        <form method="GET" action="">
            <label>From Date: </label>
            <input type="date" name="from_date" value="<?= $from_date ?>">
            <label>To Date: </label>
            <input type="date" name="to_date" value="<?= $to_date ?>">
            <button type="submit">Filter</button>
        </form>
    </div>
    <!-- Quotes Table -->
     <table class="table table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
				<th> Indent</th>
                <th>Date</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
  <?php $sr = 1; foreach($quotes as $quote): ?>
        <tr>
            <td><?= $sr++ ?> </td><td> <?= $quote->tid_start ?> - <?= $quote->tid_end ?> - <?= date('d-m-Y', strtotime($quote->invoicedate)) ?></td>
            <td><?= date('d-m-Y', strtotime($quote->invoicedate)) ?></td>
            <td><?= number_format($quote->total, 2) ?></td>
            <td><a href="<?= base_url('indent/view/'.$quote->invoicedate.'/'.$quote->tid_start.'_'.$quote->tid_end.'/'.$quote->totalinvoice) ?>" class="view-btn">View</a></td>
        </tr>
    <?php endforeach; ?>
</tbody>
    </table>

    <!-- Pagination -->
 <div class="d-flex justify-content-between align-items-center flex-wrap">
    <div class="dataTables_info">
        <?php if ($total_records > 0) : ?>
            Showing <?= $start + 1 ?> to <?= min($start + $limit, $total_records) ?> of <?= $total_records ?> entries
        <?php else : ?>
            No records found
        <?php endif; ?>
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if (!empty($pagination)) : ?>
                <?= $pagination ?>
            <?php endif; ?>
        </ul>
    </nav>
</div>

            </div>
        </div>


    </div>
</div>
<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Delete') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php echo 'delete this chalan' ?></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="quote/delete_i">
                <button type="button" data-dismiss="modal" class="btn btn-primary"
                        id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                <button type="button" data-dismiss="modal"
                        class="btn"><?php echo $this->lang->line('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>
 <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        a.view-btn {
            text-decoration: none;
            color: white;
            background: #28a745;
            padding: 5px 10px;
            border-radius: 5px;
        }
      .pagination {
        display: flex;
        list-style: none;
        padding: 0;
    }
    .pagination a, .pagination strong {
        padding: 8px 15px;
        margin: 3px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: white;
        font-weight: bold;
    }
    .pagination a:hover {
        background-color: #007bff;
        color: white;
    }
    .pagination strong {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }
		
		
    </style>