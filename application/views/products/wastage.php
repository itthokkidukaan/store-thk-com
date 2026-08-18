<div class="card">
    <div class="card-header">
        <h5>Wastage Report</h5>
    </div>
    <div class="card-body">
        <table id="wastage_table" class="table table-striped table-bordered" width="100%">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Product Name</th>
                    <th>Wastage Qty</th>
                    <th>Sell Value</th>
                    <th>Purchase Value</th>
                    <th>Wastage Date</th>
                    <th>Wastage Reason</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#wastage_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('productcategory/wastage_list'); ?>",
            "type": "POST",
            "data": {
                '<?=$this->security->get_csrf_token_name()?>': crsf_hash
            }
        },
        "columns": [
            { "data": "sr_no" },
            { "data": "product_name" },
            { "data": "qty" },
            { "data": "sell_value" },
            { "data": "purchase_value" },
            { "data": "wastage_date" },
            { "data": "wastage_reson" }
        ]
    });
});
</script>
