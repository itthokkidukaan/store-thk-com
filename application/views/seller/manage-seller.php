<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manage Seller</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Seller</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-header border-0">
                            <div class="card-tools row ">
                                <a href="#" id="open-add-seller" class="btn btn-block  btn-outline-primary btn-sm" data-toggle="modal" data-target="#addSellerModal">Add Seller </a>
                                <a href="#" id="create-slug" class="btn btn-block  btn-outline-primary btn-sm">Create Seller Slug </a>
                            </div>

                        </div>
                        <div class="card-innr">
                            <div class="row col-md-6">
                                <div class="row col-md-4 pull-right">
                                    <a href="#" class="btn btn-success update-seller-commission" title="If you found seller commission not crediting using cron job you can update seller commission from here!">Update Seller Commission</a>
                                </div>
                            </div>
                            <div class="gaps-1-5x"></div>
                            <table class='table-striped' id='seller_table' data-toggle="table" data-url="<?= base_url('sellers/view_sellers') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="sd.id" data-sort-order="DESC" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="false">Name</th>
                                        <th data-field="email" data-sortable="false">Email</th>
                                        <th data-field="mobile" data-sortable="true">Mobile No</th>
                                        <th data-field="address" data-sortable="true" data-visible="false">Address</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="rating" data-sortable="true">Rating</th>
                                        <th data-field="store_name" data-sortable="true">Store Name</th>
                                        <th data-field="store_url" data-sortable="true" data-visible="false">Store URL</th>
                                        <th data-field="store_description" data-sortable="true" data-visible="false">Store Description</th>
                                        <th data-field="account_number" data-sortable="true" data-visible="false">Account Number</th>
                                        <th data-field="account_name" data-sortable="true" data-visible="false">Account Name</th>
                                        <th data-field="bank_code" data-sortable="true" data-visible="false">Bank Code</th>
                                        <th data-field="bank_name" data-sortable="true" data-visible="false">Bank Name</th>
                                        <th data-field="latitude" data-sortable="true" data-visible="false">Latitude</th>
                                        <th data-field="longitude" data-sortable="true" data-visible="false">Longitude</th>
                                        <th data-field="tax_name" data-sortable="true" data-visible="false">Tax Name</th>
                                        <th data-field="tax_number" data-sortable="true" data-visible="false">Tax Number</th>
                                        <th data-field="pan_number" data-sortable="true" data-visible="false">Pan Number</th>
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="category_ids" data-sortable="true" data-visible="false">Category Ids</th>
                                        <th data-field="logo" data-sortable="true">Logo</th>
                                        <th data-field="national_identity_card" data-sortable="true" data-visible="false">National Identity Card</th>
                                        <th data-field="address_proof" data-sortable="true" data-visible="false">Address Proof</th>
                                        <th data-field="permissions" data-sortable="true" data-visible="false">Permissions</th>
                                        <th data-field="date" data-sortable="true" data-visible="false">Date</th>
                                        <th data-field="operate">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="addSellerModal" tabindex="-1" role="dialog" aria-labelledby="addSellerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSellerModalLabel">Add Seller</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-seller-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="add-seller-alert" class="alert alert-danger" style="display:none;"></div>

                    <h6 class="bold-text">Account Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="2" selected>Not Approved</option>
                                <option value="1">Approved</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                    </div>

                    <h6 class="bold-text mt-3">Store Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Store Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="store_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Store URL</label>
                            <input type="text" class="form-control" name="store_url">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Store Description</label>
                            <textarea class="form-control" name="store_description" rows="2"></textarea>
                        </div>
                    </div>

                    <h6 class="bold-text mt-3">Tax &amp; Bank Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tax Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tax_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tax Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tax_number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Pan Number</label>
                            <input type="text" class="form-control" name="pan_number">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Global Commission (%)</label>
                            <input type="number" step="any" min="0" class="form-control" name="global_commission" value="0">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Code / IFSC</label>
                            <input type="text" class="form-control" name="bank_code">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Account Name</label>
                            <input type="text" class="form-control" name="account_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="account_number">
                        </div>
                    </div>

                    <h6 class="bold-text mt-3">Category-wise Commission <small class="text-muted">(optional, overrides global commission for selected categories)</small></h6>
                    <div id="seller-category-commission-list" class="row" style="max-height:220px; overflow-y:auto;">
                        <div class="col-md-12 text-muted">Loading categories...</div>
                    </div>

                    <h6 class="bold-text mt-3">Documents</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Store Logo</label>
                            <input type="file" class="form-control-file" name="store_logo" accept="image/*">
                        </div>
                        <div class="form-group col-md-4">
                            <label>National Identity Card</label>
                            <input type="file" class="form-control-file" name="national_identity_card" accept="image/*">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Address Proof</label>
                            <input type="file" class="form-control-file" name="address_proof" accept="image/*">
                        </div>
                    </div>

                    <input type="hidden" name="latitude" value="0">
                    <input type="hidden" name="longitude" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="add_seller_submit_btn">Add Seller</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var categoriesLoaded = false;

    function loadSellerCategories() {
        if (categoriesLoaded) {
            return;
        }
        $.ajax({
            type: 'POST',
            url: base_url + 'sellers/get_seller_commission_data',
            dataType: 'json',
            success: function (result) {
                var $list = $('#seller-category-commission-list');
                if (result.csrfName) { csrfName = result.csrfName; }
                if (result.csrfHash) { csrfHash = result.csrfHash; }
                if (result.error === false && result.data && result.data.length) {
                    var html = '';
                    $.each(result.data, function (i, cat) {
                        html += '<div class="col-md-6 form-group mb-1">' +
                            '<div class="input-group input-group-sm">' +
                            '<div class="input-group-prepend">' +
                            '<div class="input-group-text">' +
                            '<input type="checkbox" class="seller-cat-checkbox" data-id="' + cat.id + '">' +
                            '</div>' +
                            '</div>' +
                            '<input type="text" class="form-control" value="' + cat.name + '" disabled>' +
                            '<input type="number" step="any" min="0" class="form-control seller-cat-commission" placeholder="%" style="max-width:80px;" disabled>' +
                            '</div>' +
                            '</div>';
                    });
                    $list.html(html);
                    categoriesLoaded = true;
                } else {
                    $list.html('<div class="col-md-12 text-muted">No categories available.</div>');
                }
            },
            error: function () {
                $('#seller-category-commission-list').html('<div class="col-md-12 text-danger">Unable to load categories.</div>');
            }
        });
    }

    $(document).on('show.bs.modal', '#addSellerModal', function () {
        loadSellerCategories();
    });

    $(document).on('change', '.seller-cat-checkbox', function () {
        $(this).closest('.input-group').find('.seller-cat-commission').prop('disabled', !this.checked);
    });

    $(document).on('submit', '#add-seller-form', function (e) {
        e.preventDefault();

        var $alert = $('#add-seller-alert').hide().html('');
        var password = $('input[name="password"]', this).val();
        var confirmPassword = $('input[name="confirm_password"]', this).val();
        if (password !== confirmPassword) {
            $alert.html('Password and Confirm Password do not match.').show();
            return;
        }

        var categoryIds = [];
        var commissions = [];
        $('.seller-cat-checkbox:checked').each(function () {
            categoryIds.push($(this).data('id'));
            commissions.push($(this).closest('.input-group').find('.seller-cat-commission').val() || 0);
        });

        var formData = new FormData(this);
        if (categoryIds.length > 0) {
            formData.append('commission_data', JSON.stringify({ category_id: categoryIds, commission: commissions }));
        }
        // PHP's empty("0") is true, so a literal "0" global_commission is treated as "missing" by
        // the server and wrongly demands commission_data. Drop the field in that case so isset() fails instead.
        var globalCommissionVal = parseFloat($('input[name="global_commission"]', this).val());
        if (!globalCommissionVal) {
            formData.delete('global_commission');
        }
        formData.append(csrfName, csrfHash);

        var $btn = $('#add_seller_submit_btn');
        var originalText = $btn.text();

        $.ajax({
            type: 'POST',
            url: base_url + 'sellers/add_seller',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function () {
                $btn.prop('disabled', true).text('Please Wait...');
            },
            success: function (result) {
                $btn.prop('disabled', false).text(originalText);
                if (result.csrfName) { csrfName = result.csrfName; }
                if (result.csrfHash) { csrfHash = result.csrfHash; }
                if (result.error === false) {
                    if (typeof iziToast !== 'undefined') {
                        iziToast.success({ message: result.message });
                    }
                    $('#add-seller-form')[0].reset();
                    $('#addSellerModal').modal('hide');
                    $('#seller_table').bootstrapTable('refresh');
                } else {
                    $alert.html(result.message).show();
                }
            },
            error: function () {
                $btn.prop('disabled', false).text(originalText);
                $alert.html('Something went wrong while adding the seller. Please try again.').show();
            }
        });
    });
})();
</script>