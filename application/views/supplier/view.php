<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo $details['name'] ?></h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img alt="image" id="dpic" class="img-fluid rounded-circle mb-2" src="<?php echo base_url('userfiles/customers/') . $details['picture'] ?>">
                                <div class="mt-2">
                                    <a href="#sendMail" data-toggle="modal" class="btn btn-primary btn-sm"><i class="icon-envelope"></i> <?php echo $this->lang->line('Send Message') ?></a>
                                    <a href="<?php echo base_url('supplier/bulkpayment?id=' . $details['id']) ?>" class="btn btn-secondary btn-sm"><i class="fa fa-money"></i> <?php echo $this->lang->line('Bulk Payment') ?></a>
                                    <a href="<?php echo base_url('supplier/edit?id=' . $details['id']) ?>" class="btn btn-warning btn-sm"><i class="icon-pencil"></i> <?php echo $this->lang->line('Edit Profile') ?></a>
                                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#allowProductModal"><i class="fa fa-plus"></i> Allow Product</a>
                                </div>
                                <hr>
                                <h5><?php echo $this->lang->line('Balance Summary') ?></h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $this->lang->line('Income') ?>
                                        <span class="badge badge-primary"> <?php echo amountExchange($money['credit'], 0, $this->aauth->get_user()->loc) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $this->lang->line('Expenses') ?>
                                        <span class="badge badge-danger"> <?php echo amountExchange($money['debit'], 0, $this->aauth->get_user()->loc) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h4><?php echo $this->lang->line('Supplier Details') ?></h4>
                                <hr>
                                <?php
                                $fields = [
                                    'Name' => $details['name'],
                                    'Company' => $details['company'],
                                    'Address' => $details['address'],
                                    'City' => $details['city'],
                                    'Region' => $details['region'],
                                    'Country' => $details['country'],
                                    'Postal' => $details['postbox'],
                                    'Email' => $details['email'],
                                    'Phone' => $details['phone']
                                ];
                                foreach ($fields as $label => $value): ?>
                                    <div class="row mb-2">
                                        <div class="col-md-3 font-weight-bold"><?php echo $this->lang->line($label) ?: $label ?></div>
                                        <div class="col-md-9"><?php echo $value ?></div>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>

                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url('supplier/invoices?id=' . $details['id']) ?>" class="btn btn-outline-primary btn-block"><i class="icon-file-text2"></i> <?php echo $this->lang->line('View Purchase Orders') ?></a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url('supplier/transactions?id=' . $details['id']) ?>" class="btn btn-outline-success btn-block"><i class="icon-money3"></i> <?php echo $this->lang->line('View Transactions') ?></a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url('supplier/ledger_view?id=' . $details['id']) ?>" class="btn btn-outline-info btn-block">Account Statement</a>
                                    </div>
                                </div>

                                <h5 class="mt-4">Allowed Products</h5>
                                <ul id="allowed-products-list" class="list-group">
                                    <?php
                                    $this->db->select('p.id, p.name');
                                    $this->db->from('supplier_allowed_products sap');
                                    $this->db->join('products p', 'p.id = sap.product_id');
                                    $this->db->where('sap.supplier_id', $details['id']);
                                    $products = $this->db->get()->result();
                                    foreach ($products as $product): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo $product->name; ?>
                                            <button class="btn btn-sm btn-danger remove-product" data-product-id="<?php echo $product->id; ?>">Remove</button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allow Product Modal -->
<div id="allowProductModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Allow Products to Supplier</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Filter by Category</label>
                    <select class="form-control" id="product-category-filter">
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" id="select-all-products"> Select All</label>
                </div>
                <div id="product-list" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-products">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let supplier_id = <?php echo $details['id']; ?>;

    function loadCategories() {
        $.post("<?= base_url('supplier/get_product_categories') ?>", function(data) {
            let categories = JSON.parse(data);
            for (let cat of categories) {
                $('#product-category-filter').append(`<option value="${cat.id}">${cat.name}</option>`);
            }
        });
    }

    function loadProducts(category_id = 'all') {
        $.post("<?= base_url('supplier/get_products_by_category') ?>", {
            category_id: category_id,
            supplier_id: supplier_id
        }, function(data) {
            let res = JSON.parse(data);
            let html = '';
            for (let p of res.products) {
                let checked = res.allowed.includes(p.id.toString()) ? 'checked' : '';
                html += `<div class="col-md-4"><label><input type="checkbox" class="product-checkbox" value="${p.id}" ${checked}> ${p.name}</label></div>`;
            }
            $('#product-list').html(html);
        });
    }

    $('#product-category-filter').on('change', function () {
        loadProducts(this.value);
    });

    $('#select-all-products').on('change', function () {
        $('.product-checkbox').prop('checked', this.checked);
    });

    $('#save-products').on('click', function () {
        let selected = $('.product-checkbox:checked').map(function () {
            return this.value;
        }).get();

        $.post("<?= base_url('supplier/assign_products') ?>", {
            supplier_id: supplier_id,
            product_ids: selected
        }, function (res) {
            $('#allowProductModal').modal('hide');
            location.reload();
        });
    });

    $(document).on('click', '.remove-product', function () {
        let pid = $(this).data('product-id');
        $.post("<?= base_url('supplier/remove_supplier_product') ?>", {
            supplier_id: supplier_id,
            product_id: pid
        }, function () {
            location.reload();
        });
    });

    $('#allowProductModal').on('shown.bs.modal', function () {
        $('#product-category-filter').html('<option value="all">All</option>');
        loadCategories();
        loadProducts();
    });
</script>
