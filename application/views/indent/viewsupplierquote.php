<article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <div class="message"></div>
        </div>
        <div class="card-body">
            <h5>Supplier Quotation</h5>
            <hr>
            <div class="card card-block">
                <h4>Supplier Name: <?php echo $quotation->supplier_name; ?></h4>
                <h4>Quotation Date: <?php echo date('d-m-Y', strtotime($quotation->order_date)); ?></h4>
                <hr>
            </div>
            <hr>
            <table id="quotation_items" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Require Qty</th>
                        <th>Fill Qty</th>
                        <th>Fill Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($items as $item) {
                        echo "<tr>";
                        echo "<td>{$i}</td>";
                        echo "<td>{$item->item_name}</td>";
                        echo "<td>{$item->quantity}</td>";
                        echo "<td>{$item->fill_quantity}</td>";
                        echo "<td>{$item->fill_rate}</td>";
                        echo "</tr>";
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Product</th>
                        <th>Require Qty</th>
                        <th>Fill Qty</th>
                        <th>Fill Rate</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</article>
