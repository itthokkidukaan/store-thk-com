<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><?= isset($product_details[0]['id']) ? 'Update' : 'Add' ?> Product</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home2</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <!-- form start -->
                        <form class="form-horizontal" action="<?= base_url((isset($product_details[0]['id']) && !empty($product_details[0]['id'])) ? 'products/save_product_update' : 'products/add_product'); ?>" method="POST" enctype="multipart/form-data" id="save-product">
                            <?php if (isset($product_details[0]['id'])) {
                            ?>
                                <input type="hidden" name="edit_product_id" value="<?= (isset($product_details[0]['id'])) ? $product_details[0]['id'] : "" ?>">
                                <input type="hidden" name="seller_id" value="<?= (isset($product_details[0]['seller_id'])) ? $product_details[0]['seller_id'] : "" ?>">
                                <input type="hidden" id="subcategory_id_js" value="<?= (isset($product_details[0]['subcategory_id'])) ? $product_details[0]['subcategory_id'] : "" ?>">
                            <?php } ?>
                            <input type="hidden" name="pro_input_image" id="pro_input_image" value="<?= (isset($product_details[0]['image']) && !empty($product_details[0]['image'])) ? $product_details[0]['image'] : '' ?>">
                            <div class="card-body">
                                <style>
                                    #save-product label,
                                    #save-product .col-form-label,
                                    #save-product label.control-label {
                                        font-size: 16px !important;
                                        font-weight: 600 !important;
                                    }

                                    #save-product {
                                        width: 100%;
                                        box-sizing: border-box;
                                        overflow-x: hidden;
                                    }

                                    #save-product *,
                                    #save-product *::before,
                                    #save-product *::after {
                                        box-sizing: border-box;
                                    }

                                    #save-product>.card-body {
                                        width: 100%;
                                        max-width: 1400px;
                                        margin-left: auto;
                                        margin-right: auto;
                                        overflow-x: hidden;
                                    }

                                    #save-product>.card-body>.form-group,
                                    #save-product>.card-body>.form-group.row {
                                        width: 100%;
                                        max-width: 100%;
                                        min-width: 0;
                                        overflow: hidden;
                                        background: #f8fafc;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 12px;
                                        padding: 14px;
                                        margin: 0 0 16px 0;
                                    }

                                    #save-product>.card-body>.form-group.row {
                                        margin-left: 0;
                                        margin-right: 0;
                                    }

                                    #save-product .ds-form-grid {
                                        width: 100%;
                                        min-width: 0;
                                        display: grid;
                                        grid-template-columns: repeat(3, minmax(0, 1fr));
                                        gap: 14px;
                                        align-items: start;
                                    }

                                    @media (max-width: 991.98px) {
                                        #save-product .ds-form-grid {
                                            grid-template-columns: repeat(2, minmax(0, 1fr));
                                        }
                                    }

                                    @media (max-width: 575.98px) {
                                        #save-product .ds-form-grid {
                                            grid-template-columns: 1fr;
                                        }
                                    }

                                    #save-product .ds-field {
                                        min-width: 0;
                                    }

                                    #save-product .ds-field>.col-form-label,
                                    #save-product .ds-field>label {
                                        display: block;
                                        margin: 0 0 6px 0;
                                        padding: 0;
                                        font-weight: 600;
                                        line-height: 1.2;
                                    }

                                    #save-product .form-control {
                                        width: 100%;
                                        min-width: 0;
                                        height: 40px;
                                        padding: .5rem .75rem;
                                    }

                                    #save-product textarea.form-control {
                                        height: auto;
                                        min-height: 40px;
                                    }

                                    #save-product select.form-control {
                                        padding-right: 2.25rem;
                                    }

                                    #save-product .select2-container {
                                        width: 100% !important;
                                        min-width: 0;
                                    }

                                    #save-product .select2-selection {
                                        min-width: 0;
                                    }

                                    #save-product .select2-container--bootstrap4 .select2-selection--single,
                                    #save-product .select2-container--default .select2-selection--single {
                                        height: 40px;
                                        min-height: 40px;
                                    }

                                    #save-product .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
                                    #save-product .select2-container--default .select2-selection--single .select2-selection__rendered {
                                        line-height: 38px;
                                    }

                                    #save-product .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow,
                                    #save-product .select2-container--default .select2-selection--single .select2-selection__arrow {
                                        height: 38px;
                                    }

                                    #save-product .select2-container--bootstrap4 .select2-selection--multiple,
                                    #save-product .select2-container--default .select2-selection--multiple {
                                        min-height: 40px;
                                    }

                                    #product-row-container .product-row-header {
                                        background: #f1f5f9;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 10px;
                                        padding: 10px 12px;
                                        margin: 0 0 10px 0;
                                        font-weight: 600;
                                    }

                                    #product-row-container .product-row-header .col-form-label {
                                        margin: 0;
                                        padding: 0;
                                        font-size: .9rem;
                                    }

                                    #product-row-container {
                                        max-width: 100%;
                                        overflow-x: auto;
                                    }

                                    #product-row-container .product-row,
                                    #product-row-container .product-row-header,
                                    #product-row-container .variant-details-row {
                                        margin-left: 0;
                                        margin-right: 0;
                                    }

                                    #product-row-container .product-row {
                                        align-items: center;
                                    }

                                    #product-row-container .ds-action-col {
                                        padding-left: 18px;
                                    }

                                    #product-row-container .ds-action-col .btn-group {
                                        margin-left: 0;
                                    }

                                    #product-row-container .product-row .btn-group .btn {
                                        padding: .375rem .55rem;
                                    }

                                    .ds-switch-row {
                                        display: flex;
                                        flex-wrap: wrap;
                                        gap: 18px 26px;
                                        align-items: center;
                                    }

                                    .ds-switch-row .ds-switch-item {
                                        display: flex;
                                        flex-direction: column;
                                        align-items: flex-start;
                                        min-width: 140px;
                                    }

                                    .ds-switch-row .ds-switch-item .col-form-label {
                                        margin: 0 0 6px 0;
                                        padding: 0;
                                    }

                                    .ds-flash {
                                        animation: dsFlash 1.2s ease-in-out;
                                    }

                                    @keyframes dsFlash {
                                        0% {
                                            background-color: #fff3cd;
                                        }

                                        100% {
                                            background-color: transparent;
                                        }
                                    }

                                    .ds-next-required {
                                        border: 2px solid #0d6efd !important;
                                        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12) !important;
                                    }

                                    a.ds-next-required.btn {
                                        outline: 2px solid #0d6efd !important;
                                        outline-offset: 2px;
                                    }

                                    .select2-container--default .select2-selection--single.ds-next-required,
                                    .select2-container--default .select2-selection--multiple.ds-next-required {
                                        border: 2px solid #0d6efd !important;
                                        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12) !important;
                                    }

                                    .tagify {
                                        min-height: 40px;
                                        border-radius: .25rem;
                                    }

                                    label[for="tags"] small {
                                        display: block;
                                        margin-top: 2px;
                                    }

                                    .ds-media-box .image-upload-section {
                                        min-height: 96px;
                                        border: 1px dashed #cbd5e1;
                                        border-radius: 12px;
                                        background: #fff;
                                        padding: 10px;
                                        margin-top: 10px;
                                    }

                                    .ds-media-box .image-upload-section.container-fluid.row {
                                        margin-left: 0;
                                        margin-right: 0;
                                    }

                                    .ds-media-box .image-upload-section .shadow {
                                        margin: 8px !important;
                                        padding: 10px !important;
                                    }

                                    .ds-media-box .image-upload-div img {
                                        max-height: 130px;
                                        width: 100%;
                                        object-fit: contain;
                                    }

                                    .ds-media-box .uploadFile {
                                        border-radius: 10px;
                                    }

                                    .ds-media-section {
                                        padding: 14px;
                                    }

                                    #save-product .ds-media-section>.row {
                                        margin: 0;
                                        display: grid;
                                        grid-template-columns: repeat(2, minmax(0, 1fr));
                                        gap: 14px;
                                    }

                                    #save-product .ds-media-section>.row>[class*="col-"] {
                                        padding: 0 !important;
                                        margin: 0 !important;
                                        max-width: 100%;
                                    }

                                    @media (max-width: 575.98px) {
                                        #save-product .ds-media-section>.row {
                                            grid-template-columns: 1fr;
                                        }
                                    }

                                    .card-body.pad {
                                        background: #f8fafc;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 12px;
                                        margin: 0 0 16px 0;
                                    }

                                    .ds-section-block {
                                        background: #f8fafc;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 12px;
                                        padding: 14px;
                                        margin: 0 0 16px 0;
                                    }

                                    .ds-section-header {
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                        gap: 12px;
                                        flex-wrap: wrap;
                                        margin-bottom: 10px;
                                    }

                                    .ds-section-header .col-form-label {
                                        margin: 0;
                                    }
                                    .ds-active-variant-row {
                                            background-color: #e6f7ff; /* Light blue background for highlighting */
                                        }
                                </style>
                                <?php
                                $current_product_type = isset($product_details[0]['type']) && !empty($product_details[0]['type']) ? $product_details[0]['type'] : 'variable_product';
                                $current_stock_type = isset($product_details[0]['stock_type']) ? (int)$product_details[0]['stock_type'] : 0;
                                $display_stock = '';

                                if (isset($product_details[0])) {
                                    if ($current_product_type === 'simple_product') {
                                        $base_stock = isset($product_details[0]['stock']) ? (float)$product_details[0]['stock'] : 0;
                                        $display_stock = ($base_stock != 0) ? number_format($base_stock, 2, '.', '') : '';
                                    } elseif (!empty($product_variants) && is_array($product_variants)) {
                                        if ($current_stock_type === 1) {
                                            $variant_stock = isset($product_variants[0]['stock']) ? (float)$product_variants[0]['stock'] : 0;
                                            $display_stock = ($variant_stock != 0) ? number_format($variant_stock, 2, '.', '') : '';
                                        } elseif ($current_stock_type === 2) {
                                            $variant_stock_total = 0;
                                            foreach ($product_variants as $variant_row) {
                                                if (isset($variant_row['stock']) && $variant_row['stock'] !== '' && $variant_row['stock'] !== null) {
                                                    $variant_stock_total += (float)$variant_row['stock'];
                                                }
                                            }
                                            $display_stock = ($variant_stock_total != 0) ? number_format($variant_stock_total, 2, '.', '') : '';
                                        } else {
                                            $fallback_stock = isset($product_details[0]['stock']) ? (float)$product_details[0]['stock'] : 0;
                                            $display_stock = ($fallback_stock != 0) ? number_format($fallback_stock, 2, '.', '') : '';
                                        }
                                    } else {
                                        $fallback_stock = isset($product_details[0]['stock']) ? (float)$product_details[0]['stock'] : 0;
                                        $display_stock = ($fallback_stock != 0) ? number_format($fallback_stock, 2, '.', '') : '';
                                    }
                                }
                                ?>
                                <input type="hidden" name="product_type" value="<?= $current_product_type ?>">
                                <input type="hidden" name="variant_stock_status" value="<?= ($current_product_type == 'variable_product') ? '0' : '' ?>">
                                <input type="hidden" name="variant_stock_level_type" value="<?= ($current_product_type == 'variable_product') ? 'variable_level' : '' ?>">
                                <input type="hidden" name="simple_product_stock_status" value="<?= ($current_product_type == 'simple_product') ? '1' : '' ?>">

                                <div class="form-group">
                                    <div class="ds-form-grid">
                                        <div class="ds-field">
                                            <label for="pro_input_text" class="col-form-label">Product Name <span class='text-danger text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="pro_input_text" placeholder="Enter product name" name="pro_input_name" value="<?= (isset($product_details[0]['name'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $product_details[0]['name'])) : "" ?>">
                                        </div>
                                        <div class="ds-field">
                                            <?php $selected_category_id = isset($product_details[0]['category_id']) ? (int)$product_details[0]['category_id'] : 0; ?>
                                            <label for="category_id" class="col-form-label">Category <span class='text-danger text-sm'>*</span></label>
                                            <select class="form-control" name="category_id" id="category_id">
                                                <option value="">Select category</option>
                                                <?= isset($categories) ? get_categories_option_html($categories, $selected_category_id ? [$selected_category_id] : []) : '' ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="ds-form-grid">
                                        <div class="ds-field">
                                            <label for="total_stock" class="col-form-label">Opening Stock</label>
                                            <input type="number" class="form-control" id="total_stock" name="product_total_stock" value="<?= $display_stock ?>" placeholder="Opening stock">
                                        </div>
                                        <div class="ds-field">
                                            <label for="total_allowed_quantity" class="col-form-label">Total Allowed Quantity (Minimum Stock)</label>
                                            <input type="number" class="form-control" id="total_allowed_quantity" name="total_allowed_quantity" value="<?= (isset($product_details[0]['total_allowed_quantity'])) ? $product_details[0]['total_allowed_quantity'] : ''; ?>" placeholder="Total allowed quantity">
                                        </div>
                                        <div class="ds-field">
                                            <label for="total_required_quantity" class="col-form-label">Required Stock</label>
                                            <?php
                                            $display_required = '';
                                            if (isset($product_details[0]['total_allowed_quantity']) && $display_stock !== '') {
                                                $display_required = max(0, floatval($product_details[0]['total_allowed_quantity']) - floatval($display_stock));
                                            }
                                            ?>
                                            <input type="number" class="form-control" id="total_required_quantity" name="total_required_quantity" value="<?= $display_required ?>" placeholder="Required stock" readonly style="background-color: #e9ecef;">
                                        </div>
                                        <div class="ds-field">
                                            <label for="purchaseprice" class="col-form-label">Purchase Price</label>
                                            <input type="text" class="form-control" name="purchaseprice" id="purchaseprice" value="<?= (isset($product_details[0]['purchase_price'])) ? $product_details[0]['purchase_price'] : ''; ?>" placeholder="Purchase price">
                                        </div>
                                        <div class="ds-field">
                                            <label for="purchase_price_per" class="col-form-label">Purchase Unit</label>
                                            <select class="form-control select_single" id="purchase_price_per" name="purchase_price_per" placeholder="Purchase unit">
                                                <?php
                                                foreach ($attributes_refind as $key => $value) {
                                                ?>
                                                    <optgroup label="<?= $key ?>"><?= $key ?>
                                                        <?php foreach ($value as $key => $value) {  ?>
                                                            <option name='<?= $key ?>' value='<?= $key ?>' data-values='<?= json_encode($value, 1) ?>'><?= $key ?></option>
                                                        <?php } ?>
                                                    </optgroup>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="ds-section-block">
                                    <div class="ds-section-header">
                                        <label for="pro_input_text" class="col-form-label">Variants <span class='text-danger text-sm'>*</span></label>
                                    </div>
                                    <div id="variants_required_error" class="invalid-feedback d-block" style="display:none;"></div>
                                    <div id="product-row-container">

                                        <input type="hidden" name="products_id" value="<?= (isset($product_details[0]['id'])) ? $product_details[0]['id'] : "" ?>">
                                        <div class="row product-row-header">
                                            <div class="col-md-3"> <label for="pro_input_text" class="col-form-label">Product Name</label> </div>
                                            <div class="col-md-2"> <label for="pro_input_text" class="col-form-label">Article No</label> </div>
                                            <div class="col-md-2"> <label for="pro_input_text" class="col-form-label">HP Number</label></div>
                                            <div class="col-md-2"> <label for="pro_input_text" class="col-form-label">Attribute</label></div>
                                            <div class="col-md-2"> <label for="pro_input_text" class="col-form-label">Value</label></div>
                                            <div class="col-md-1"> <label for="pro_input_text" class="col-form-label">Action</label> </div>
                                        </div>
                                        <?php
                                        $grouped_vars = [];
                                        $var_id_map = []; // Map variation text to ID
                                        if (!empty($variables)) {
                                            foreach ($variables as $av) {
                                                $grouped_vars[$av['name']][] = $av['variation'];
                                                $var_id_map[$av['variation']] = $av['id'];
                                            }
                                        }

                                        if (!empty($barcode_data)) {
                                            foreach ($barcode_data as $row) {
                                                $current_attr = '';
                                                if (!empty($variables)) {
                                                    foreach ($variables as $av) {
                                                        if ($av['variation'] == $row['UOM']) {
                                                            $current_attr = $av['name'];
                                                            break;
                                                        }
                                                    }
                                                }
                                        ?>
                                                <div class="row product-row mb-2">
                                                    <input type="hidden" name="row_id[]" value="<?= $row['id']; ?>">
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="printname[]" value="<?= $row['print_name']; ?>" placeholder="Print Name">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" name="article[]" value="<?= $row['article_no']; ?>" placeholder="Article No">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" name="hpnumber[]" placeholder="HP Number" value="<?= $row['hpnumber']; ?>">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-control attr-select" name="attr[]">
                                                            <option value="">Select Attribute</option>
                                                            <?php foreach (array_keys($grouped_vars) as $attr_name) { ?>
                                                                <option value="<?= $attr_name ?>" <?= ($current_attr == $attr_name) ? 'selected' : '' ?>><?= $attr_name ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-control uom-select w-100" name="uom[]" data-placeholder="Select Value">
                                                            <option value="">Select Value</option>
                                                            <?php
                                                            if ($current_attr && isset($grouped_vars[$current_attr])) {
                                                                foreach ($grouped_vars[$current_attr] as $val) {
                                                                    $selected = ($row['UOM'] == $val) ? 'selected' : '';
                                                                    echo "<option value='{$val}' data-id='{$var_id_map[$val]}' {$selected}>{$val}</option>";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-1 ds-action-col">
                                                         <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                    <div class="text-right mt-2" style="margin-top: 15px; margin-bottom: 10px; text-align: right;">
                                        <button type="button" class="btn btn-success btn-sm" id="add-row-btn">
                                            <i class="fa fa-plus"></i> Add Row
                                        </button>
                                    </div>
                                </div>





                                <div class="form-group">
                                    <div class="ds-form-grid">
                                        <div class="ds-field">
                                            <label for="seller_id" class="col-form-label">Seller <span class='text-danger text-sm'>*</span></label>
                                            <select class='form-control' name='seller_id' id="seller_id">
                                                <option value="">Select seller</option>
                                                <?php foreach ($sellers as $seller) { ?>
                                                    <option value="<?= $seller['seller_id'] ?>" <?= (isset($product_details[0]['seller_id']) && $product_details[0]['seller_id'] == $seller['seller_id']) ? 'selected' : "" ?>><?= $seller['seller_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="ds-field">
                                            <label for="short_description" class="col-form-label">Short Description <span class='text-danger text-sm'>*</span></label>
                                            <input type="text" class="form-control" id="short_description" placeholder="Short description" name="short_description" value="<?= isset($product_details[0]['short_description']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $product_details[0]['short_description'])) : ""; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ds-tight-section">
                                    <div class="ds-form-grid">
                                        <div class="ds-field">
                                            <label for="pro_input_tax" class="col-form-label">Tax</label>
                                             <select class="form-control" name="pro_input_tax">
                                                 <?php if (empty($taxes)) { ?>
                                                     <option value="0" selected> No Taxes Are Added </option>
                                                 <?php } ?>
                                                 <?php foreach ($taxes as $row) {
                                                     if (isset($product_details[0]['tax']) && $product_details[0]['tax'] == $row['id']) {
                                                         $selected = 'selected';
                                                     } else {
                                                         $selected = '';
                                                     }
                                                     $pct = (float)$row['percentage'];
                                                     $percentage_str1 = $pct . '%';
                                                     $percentage_str2 = $row['percentage'] . '%';
                                                     if (strpos($row['title'], $percentage_str1) === false && strpos($row['title'], $percentage_str2) === false) {
                                                         $tax_display = $row['title'] . ' (' . $pct . '%)';
                                                     } else {
                                                         $tax_display = $row['title'];
                                                     }
                                                 ?>
                                                     <option value="<?= $row['id'] ?>" <?= $selected ?>><?= $tax_display ?></option>
                                                 <?php
                                                 } ?>
                                             </select>
                                        </div>
                                        <div class="ds-field">
                                            <label for="indicator" class="col-form-label">Indicator</label>
                                            <select class='form-control' name='indicator'>
                                                <option value='0' <?= (isset($product_details[0]['indicator']) &&  $product_details[0]['indicator'] == '0') ? 'selected' : ''; ?>>None</option>
                                                <option value='1' <?= (isset($product_details[0]['indicator']) &&  $product_details[0]['indicator'] == '1') ? 'selected' : ''; ?>>Veg</option>
                                                <option value='2' <?= (isset($product_details[0]['indicator']) &&  $product_details[0]['indicator'] == '2') ? 'selected' : ''; ?>>Non-Veg</option>
                                            </select>
                                        </div>
                                        <div class="ds-field">
                                            <label for="made_in" class="col-form-label">Made In</label>
                                            <select class="form-control country_list" id="country_list" name="made_in">
                                                <?php if (isset($product_details[0]['made_in']) && ($product_details[0]['made_in']) != '') {
                                                ?>
                                                    <option value="<?= $product_details[0]['made_in'] ?>" <?= (isset($product_details[0]['made_in']) &&  $product_details[0]['made_in'] == $countries[0]['name']) ? 'selected' : ''; ?>><?= $product_details[0]['made_in'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="ds-field">
                                            <label for="minimum_order_quantity" class="col-form-label">Minimum Order Quantity</label>
                                            <input type="number" class="form-control" name="minimum_order_quantity" min="1" value="<?= (isset($product_details[0]['minimum_order_quantity'])) ? $product_details[0]['minimum_order_quantity'] : 1; ?>" placeholder='Minimum Order Quantity'>
                                        </div>
                                        <div class="ds-field">
                                            <label for="quantity_step_size" class="col-form-label">Quantity Step Size</label>
                                            <input type="number" class="form-control" name="quantity_step_size" min="1" value="<?= (isset($product_details[0]['quantity_step_size'])) ? $product_details[0]['quantity_step_size'] : 1; ?>" placeholder='Quantity Step Size'>
                                        </div>
                                        <div class="ds-field">
                                            <label for="warranty_period" class="col-form-label">Warranty Period</label>
                                            <input type="text" class="form-control" name="warranty_period" value="<?= (isset($product_details[0]['warranty_period'])) ? $product_details[0]['warranty_period'] : "" ?>" placeholder='Warranty Period if any'>
                                        </div>
                                        <div class="ds-field">
                                            <label for="guarantee_period" class="col-form-label">Guarantee Period</label>
                                            <input type="text" class="form-control" name="guarantee_period" value="<?= (isset($product_details[0]['guarantee_period'])) ? $product_details[0]['guarantee_period'] : "" ?>" placeholder='Guarantee Period if any'>
                                        </div>
                                        <div class="ds-field">
                                            <label for="zipcode" class="col-form-label">Deliverable Type</label>
                                            <select class='form-control' name='deliverable_type' id="deliverable_type">
                                                <option value=<?= NONE ?> <?= (isset($product_details[0]['deliverable_type']) &&  $product_details[0]['deliverable_type'] == NONE) ? 'selected' : ''; ?>>None</option>
                                                <?php if (!isset($product_details)) { ?>
                                                    <option value=<?= ALL ?> selected>All</option>
                                                <?php } else { ?>
                                                    <option value=<?= ALL ?> <?= (isset($product_details[0]['deliverable_type']) &&  $product_details[0]['deliverable_type'] == ALL) ? 'selected' : ''; ?>>All</option>
                                                <?php } ?>
                                                <option value=<?= INCLUDED ?> <?= (isset($product_details[0]['deliverable_type']) &&  $product_details[0]['deliverable_type'] == INCLUDED) ? 'selected' : ''; ?>>Included</option>
                                                <option value=<?= EXCLUDED ?> <?= (isset($product_details[0]['deliverable_type']) &&  $product_details[0]['deliverable_type'] == EXCLUDED) ? 'selected' : ''; ?>>Excluded</option>
                                            </select>
                                        </div>
                                        <?php
                                        $zipcodes = (isset($product_details[0]['deliverable_zipcodes']) &&  $product_details[0]['deliverable_zipcodes'] != NULL) ? explode(",", $product_details[0]['deliverable_zipcodes']) : "";
                                        ?>
                                        <div class="ds-field">
                                            <label for="zipcodes" class="col-form-label">Deliverable Zipcodes</label>
                                            <select name="deliverable_zipcodes[]" class="form-control search_zipcode" multiple onload="multiselect()" id="deliverable_zipcodes" <?= (isset($product_details[0]['deliverable_type']) &&  ($product_details[0]['deliverable_type'] == INCLUDED || $product_details[0]['deliverable_type'] == EXCLUDED))  ? "" : "disabled" ?>>
                                                <?php if (isset($product_details[0]['deliverable_type']) &&  ($product_details[0]['deliverable_type'] == INCLUDED || $product_details[0]['deliverable_type'] == EXCLUDED)) {
                                                    $zipcodes_name =  fetch_details('zipcodes', "",  'zipcode,id', "", "", "", "", "id", $zipcodes);
                                                    foreach ($zipcodes_name as $row) {
                                                ?>
                                                        <option value=<?= $row['id'] ?> <?= (in_array($row['id'], $zipcodes)) ? 'selected' : ''; ?>> <?= $row['zipcode'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="ds-switch-row">
                                        <div class="ds-switch-item">
                                            <label for="is_prices_inclusive_tax" class="col-form-label">Prices Include Tax</label>
                                            <input type="checkbox" id="is_prices_inclusive_tax" name="is_prices_inclusive_tax" <?= (isset($product_details[0]['is_prices_inclusive_tax']) && $product_details[0]['is_prices_inclusive_tax'] == '1') ? 'checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Yes" data-off-text="No">
                                        </div>
                                        <div class="ds-switch-item">
                                            <label for="cod_allowed" class="col-form-label">Cash on Delivery</label>
                                            <input type="checkbox" id="cod_allowed" name="cod_allowed" <?= (isset($product_details[0]['cod_allowed']) && $product_details[0]['cod_allowed'] == '1') ? 'Checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                        </div>
                                        <div class="ds-switch-item">
                                            <label for="is_returnable" class="col-form-label">Returnable</label>
                                            <input type="checkbox" id="is_returnable" name="is_returnable" <?= (isset($product_details[0]['is_returnable']) && $product_details[0]['is_returnable'] == '1') ? 'Checked' : '' ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                        </div>
                                        <div class="ds-switch-item">
                                            <label for="is_cancelable" class="col-form-label">Cancelable</label>
                                            <input type="checkbox" name="is_cancelable" id="is_cancelable" class="switch" <?= (isset($product_details[0]['is_cancelable']) && $product_details[0]['is_cancelable'] == '1') ? 'Checked' : ''; ?> data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                        </div>
                                        <div class="ds-switch-item flex-grow-1 <?= (isset($product_details[0]['is_cancelable']) && $product_details[0]['is_cancelable'] == 1) ? '' : 'collapse' ?>" id='cancelable_till'>
                                            <label for="cancelable_till" class="col-form-label">Till which status ? <span class='text-danger text-sm'>*</span></label>
                                            <select class='form-control' name="cancelable_till">
                                                <option value='received' <?= (isset($product_details[0]['cancelable_till']) && $product_details[0]['cancelable_till'] == 'received') ? 'selected' : '' ?>>Received</option>
                                                <option value='processed' <?= (isset($product_details[0]['cancelable_till']) && $product_details[0]['cancelable_till'] == 'processed') ? 'selected' : '' ?>>Processed</option>
                                                <option value='shipped' <?= (isset($product_details[0]['cancelable_till']) && $product_details[0]['cancelable_till'] == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="ds-section-block ds-media-section">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-group ds-media-box mb-0">
                                                <label for="image">Main Image <span class='text-danger text-sm'>*</span></label>
                                                <div class="col-12 p-0">
                                                    <div class="mb-2">
                                                        <a class="uploadFile img btn btn-primary text-white btn-sm" data-input='pro_input_image' data-isremovable='0' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a>
                                                    </div>
                                                    <?php
                                                    if (isset($product_details[0]['id']) && !empty($product_details[0]['id'])) {
                                                    ?>
                                                        <label class="text-danger mt-2">*Only Choose When Update is necessary</label>
                                                        <div class="container-fluid row image-upload-section ">
                                                            <div class="col-6 col-lg-4 shadow bg-white rounded text-center grow image">
                                                                <div class='image-upload-div'>
                                                                    <img class="img-fluid mb-2" src="<?= get_image_url($product_details[0]['image']) ?>" alt="Image Not Found">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    } else { ?>
                                                        <div class="container-fluid row image-upload-section">
                                                            <div class="col-6 col-lg-4 shadow bg-white rounded text-center grow image d-none"></div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ds-media-box mb-0">
                                                <label for="other_images">Other Images </label>
                                                <div class="col-12 p-0">
                                                    <div class="mb-2">
                                                        <a class="uploadFile img btn btn-primary text-white btn-sm" data-input='other_images[]' data-isremovable='1' data-is-multiple-uploads-allowed='1' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a>
                                                    </div>
                                                    <?php
                                                    if (isset($product_details[0]['id']) && !empty($product_details[0]['id'])) {
                                                    ?>
                                                        <div class="container-fluid row image-upload-section">
                                                            <?php
                                                            $other_images = json_decode($product_details[0]['other_images']);
                                                            if (!empty($other_images)) {
                                                                foreach ($other_images as $row) {
                                                            ?>
                                                                    <div class="col-6 col-lg-4 shadow bg-white rounded text-center grow">
                                                                        <div class='image-upload-div'>
                                                                            <img src="<?= get_image_url($row) ?>" alt="Image Not Found" class="img-fluid mb-2">
                                                                        </div>
                                                                        <a href="javascript:void(0)" class="delete-img m-2 d-inline-block" data-id="<?= $product_details[0]['id'] ?>" data-field="other_images" data-img="<?= $row ?>" data-table="products" data-path="<?= $row ?>" data-isjson="true">
                                                                            <span class="btn bg-gradient-danger btn-xs"><i class="far fa-trash-alt "></i> Delete</span>
                                                                        </a>
                                                                        <input type="hidden" name="other_images[]" value='<?= $row ?>'>
                                                                    </div>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php
                                                    } else { ?>
                                                        <div class="container-fluid row image-upload-section"></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3 mb-0">
                                        <div class="ds-form-grid">
                                            <div class="ds-field">
                                                <label for="video_type" class="col-form-label">Video Type</label>
                                                <select class='form-control' name='video_type' id='video_type'>
                                                    <option value='' <?= (isset($product_details[0]['video_type']) && ($product_details[0]['video_type'] == '' || $product_details[0]['video_type'] == NULL)) ? 'selected' : ''; ?>>None</option>
                                                    <option value='self_hosted' <?= (isset($product_details[0]['video_type']) &&  $product_details[0]['video_type'] == 'self_hosted') ? 'selected' : ''; ?>>Self Hosted</option>
                                                    <option value='youtube' <?= (isset($product_details[0]['video_type']) &&  $product_details[0]['video_type'] == 'youtube') ? 'selected' : ''; ?>>Youtube</option>
                                                    <option value='vimeo' <?= (isset($product_details[0]['video_type']) &&  $product_details[0]['video_type'] == 'vimeo') ? 'selected' : ''; ?>>Vimeo</option>
                                                </select>
                                            </div>
                                            <div class="ds-field <?= (isset($product_details[0]['video_type']) && ($product_details[0]['video_type'] == 'youtube' ||  $product_details[0]['video_type'] == 'vimeo')) ? '' : 'd-none'; ?>" id="video_link_container">
                                                <label for="video" class="col-form-label">Video Link <span class='text-danger text-sm'>*</span></label>
                                                <input type="text" class='form-control' name='video' id='video' value="<?= (isset($product_details[0]['video_type']) && ($product_details[0]['video_type'] == 'youtube' || $product_details[0]['video_type'] == 'vimeo')) ? $product_details[0]['video'] : ''; ?>" placeholder="Paste Youtube / Vimeo Video link or URL here">
                                            </div>
                                            <div class="ds-field <?= (isset($product_details[0]['video_type']) && ($product_details[0]['video_type'] == 'self_hosted')) ? '' : 'd-none'; ?>" id="video_media_container">
                                                <label for="image">Video <span class='text-danger text-sm'>*</span></label>
                                                <div class="mb-2"><a class="uploadFile img btn btn-primary text-white btn-sm" data-input='pro_input_video' data-isremovable='1' data-media_type='video' data-is-multiple-uploads-allowed='0' data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class='fa fa-upload'></i> Upload</a></div>
                                                <?php if (isset($product_details[0]['id']) && !empty($product_details[0]['id']) && isset($product_details[0]['video_type']) &&  $product_details[0]['video_type'] == 'self_hosted') { ?>
                                                    <label class="text-danger mt-3">*Only Choose When Update is necessary</label>
                                                    <div class="container-fluid row image-upload-section ">
                                                        <div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                            <div class='image-upload-div'><img class="img-fluid mb-2" src="<?= base_url('assets/admin/images/video-file.png') ?>" alt="Product Video" title="Product Video"></div>
                                                            <input type="hidden" name="pro_input_video" value='<?= $product_details[0]['video'] ?>'>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $grouped_vars_json = json_encode($grouped_vars);
                                $var_id_map_json = json_encode($var_id_map);
                                $attr_options = "<option value=''>Select Attribute</option>";
                                foreach (array_keys($grouped_vars) as $attr_name) {
                                    $attr_options .= "<option value='{$attr_name}'>{$attr_name}</option>";
                                }
                                ?>

                                <!-- JavaScript to add/remove/save rows -->
                                <script>
                                    const groupedVars = <?= $grouped_vars_json ?? '{}' ?>;
                                    const varIdMap = <?= $var_id_map_json ?? '{}' ?>;
                                    const existingProductVariants = <?= json_encode($product_variants ?? []) ?>;

                                    function generateSKU(productName, variantIds = "") {
                                        const slugify = (text) => {
                                            if (!text) return "PRODNEW";
                                            return text.toString().toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "") || "PRODNEW";
                                        };
                                        const prefix = slugify(productName);
                                        let variantStr = "";
                                        if (variantIds) {
                                            if (Array.isArray(variantIds)) {
                                                variantStr = variantIds.join("-");
                                            } else {
                                                variantStr = variantIds.toString().trim().replace(/[\s,]+/g, "-").replace(/-+/g, "-");
                                            }
                                        }
                                        const random = Math.random().toString(36).substring(2, 8).toUpperCase();
                                        let sku = prefix;
                                        if (variantStr) {
                                            sku += "-" + variantStr;
                                        }
                                        sku += "-" + random;
                                        return sku;
                                    }

                                    function newPrice(element) {
                                        const $row = $(element).closest('.product-variant-selectbox');
                                        const purchasePrice = parseFloat($row.find('input[name="purchase_price[]"]').val()) || 0;
                                        const packingPrice = parseFloat($row.find('input[name="packing_price[]"]').val()) || 0;
                                        const marginType = $row.find('select[name="margin_type[]"]').val();
                                        const marginPercent = parseFloat($row.find('input[name="margin_percent[]"]').val()) || 0;
                                        const discPercent = parseFloat($row.find('input[name="disc_percent[]"]').val()) || 0;

                                        let calculatedPrice = purchasePrice + packingPrice;

                                        if (marginType === 'Percentage') {
                                            calculatedPrice += calculatedPrice * (marginPercent / 100);
                                        } else { // Fixed
                                            calculatedPrice += marginPercent;
                                        }

                                        $row.find('input[name="variant_price[]"]').val((calculatedPrice || 0).toFixed(2));

                                        let specialPrice = calculatedPrice - (calculatedPrice * (discPercent / 100));
                                        $row.find('input[name="variant_special_price[]"]').val((specialPrice || 0).toFixed(2));
                                    }

                                    $(document).ready(function() {
                                        // Initialize Select2 for Category
                                        $('#category_id').select2({
                                            theme: 'bootstrap4',
                                            width: '100%',
                                            placeholder: 'Select category'
                                        });

                                        // Initialize Select2 for Attribute select boxes
                                        $(document).on('focus', '.attr-select', function() {
                                            if (!$(this).data('select2')) {
                                                $(this).select2({
                                                    theme: 'bootstrap4',
                                                    width: '100%',
                                                    placeholder: 'Select Attribute'
                                                });
                                            }
                                        });

                                        // 🔄 Handle Attribute change to populate Value dropdown
                                        $(document).on('change', '.attr-select', function() {
                                            const attrName = $(this).val();
                                            const uomSelect = $(this).closest('.product-row').find('.uom-select');
                                            uomSelect.empty().append('<option value="">Select Value</option>');

                                            if (attrName && groupedVars[attrName]) {
                                                groupedVars[attrName].forEach(function(val) {
                                                    const vid = varIdMap[val] || '';
                                                    uomSelect.append(`<option value="${val}" data-id="${vid}">${val}</option>`);
                                                });
                                            }
                                            if (typeof dsInitUomSelect === 'function') {
                                                dsInitUomSelect(uomSelect);
                                            }
                                        });

                                         // Update Required Stock on change of Opening Stock or Minimum Stock
                                         $('#total_stock, #total_allowed_quantity').on('input change', function() {
                                             let openingStock = parseFloat($('#total_stock').val()) || 0;
                                             let minimumStock = parseFloat($('#total_allowed_quantity').val()) || 0;

                                             const requiredStock = Math.max(0, minimumStock - openingStock);
                                            $('#total_required_quantity').val(requiredStock.toFixed(2)); // Round required stock to 2 decimal places
                                         });

                                         // Round Minimum Stock only when the user finishes editing (on blur or change)
                                         $('#total_allowed_quantity').on('blur change', function() {
                                             let val = $(this).val();
                                             if (val !== '') {
                                                 let minimumStock = parseFloat(val);
                                                 if (!isNaN(minimumStock)) {
                                                     $(this).val(parseFloat(minimumStock.toFixed(2)));
                                                 }
                                             }
                                         });

                                        // Initial calculation on page load
                                        $('#total_stock').trigger('change');

                                        // Function to restrict input to specified decimal places in real-time
                                        function restrictDecimalPlaces(event, decimals = 2) {
                                            const input = $(event.target);
                                            let value = input.val();
                                            const keyCode = event.keyCode;

                                            // Allow navigation keys, backspace, delete, tab
                                            if (keyCode >= 35 && keyCode <= 40 || keyCode === 8 || keyCode === 46 || keyCode === 9) {
                                                return;
                                            }

                                            // Allow only numbers and one decimal point
                                            if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105) || keyCode === 110 || keyCode === 190)) {
                                                event.preventDefault();
                                                return;
                                            }

                                            // Handle decimal point
                                            if (keyCode === 110 || keyCode === 190) {
                                                if (value.indexOf('.') !== -1) {
                                                    event.preventDefault(); // Already has a decimal point
                                                }
                                                return;
                                            }

                                            // Get current cursor position
                                            const selectionStart = input[0].selectionStart;
                                            const selectionEnd = input[0].selectionEnd;

                                            // Simulate new value after key press
                                            const newValue = value.substring(0, selectionStart) + String.fromCharCode(keyCode) + value.substring(selectionEnd);

                                            const parts = newValue.split('.');
                                            if (parts.length > 1 && parts[1].length > decimals) {
                                                event.preventDefault();
                                            }
                                        }

                                        // Apply to relevant input fields for 2 decimal places
                                        $(document).on('keydown',
                                            '#purchaseprice, #packing_price, #user_percent, #disc_percent, #price, #disc_price, input[name="purchase_price[]"], input[name="packing_price[]"], input[name="margin_percent[]"], input[name="disc_percent[]"], input[name="variant_price[]"], input[name="variant_special_price[]"]',
                                            function(event) {
                                                restrictDecimalPlaces.call(this, event, 2);
                                            }
                                        );

                                        // Apply to stock related fields for 0 decimal places
                                        $(document).on('keydown',
                                            'input[name="variant_total_stock[]"]',
                                            function(event) {
                                                restrictDecimalPlaces.call(this, event, 0);
                                            }
                                        );

                                        // Apply to stock related fields for 2 decimal places
                                        $(document).on('keydown',
                                            '#total_stock, #total_allowed_quantity',
                                            function(event) {
                                                restrictDecimalPlaces.call(this, event, 2);
                                            }
                                        );

                                        let nextVariantCounter = 1;

                                        function dsFormatPrice(val) {
                                            if (val === undefined || val === null || val === '') return '';
                                            const num = parseFloat(val);
                                            return isNaN(num) ? '' : num.toFixed(2);
                                        }

                                        function getOrAssignVariantCounter(row) {
                                            const existing = row.attr('data-variant-counter');
                                            if (existing) return parseInt(existing, 10);
                                            const counter = nextVariantCounter++;
                                            row.attr('data-variant-counter', counter);
                                            return counter;
                                        }

                                        function getRowDetailsContainer(row) {
                                            let details = row.nextUntil('.product-row', '.variant-details-row');
                                            if (details.length === 0) {
                                                details = $(`<div class="row variant-details-row mb-3"><div class="col-md-12"></div></div>`);
                                                details.insertAfter(row);
                                            }
                                            return details.find('> .col-md-12');
                                        }

                                        function syncRowToVariant(row) {
                                            if (row.find('.uom-select').length === 0) return;
                                            const uomSelect = row.find('.uom-select');
                                            let uomValue = uomSelect.val();
                                            if (Array.isArray(uomValue)) {
                                                uomValue = uomValue[0] || '';
                                            }
                                            const uomId = uomSelect.find(':selected').data('id') || '';
                                            const existingKey = row.attr('data-variant-key') || '';
                                            const nextKey = (uomId || uomValue || '').toString();

                                            if (!uomValue) {
                                                row.removeAttr('data-variant-key');
                                                row.nextUntil('.product-row', '.variant-details-row').remove();
                                                return;
                                            }

                                            const variantCounter = getOrAssignVariantCounter(row);
                                            const attr_name = 'pro_attr_' + variantCounter;
                                            const tempEvent = variantCounter === 1 ? 'onfocusout="newCalc();"' : '';

                                            const detailsCol = getRowDetailsContainer(row);

                                            const cached = {
                                                purchase_price: detailsCol.find('input[name="purchase_price[]"]').val(),
                                                packing_price: detailsCol.find('input[name="packing_price[]"]').val(),
                                                margin_type: detailsCol.find('select[name="margin_type[]"]').val(),
                                                margin_percent: detailsCol.find('input[name="margin_percent[]"]').val(),
                                                disc_percent: detailsCol.find('input[name="disc_percent[]"]').val(),
                                                variant_price: detailsCol.find('input[name="variant_price[]"]').val(),
                                                variant_special_price: detailsCol.find('input[name="variant_special_price[]"]').val(),
                                                variant_sku: detailsCol.find('input[name="variant_sku[]"]').val(),
                                                variant_total_stock: detailsCol.find('input[name="variant_total_stock[]"]').val(),
                                                variant_level_stock_status: detailsCol.find('select[name="variant_level_stock_status[]"]').val()
                                            };
                                            const prefill = row.data('ds-prefill') || {};

                                            const forceNewVariant = row.data('ds-force-new') === true;
                                            const normalizedUomId = (uomId || '').toString().trim();
                                            const normalizedUomValue = (uomValue || '').toString().trim();
                                            let existingData = {};

                                            if (!forceNewVariant) {
                                                existingData = existingProductVariants.find(function(v) {
                                                     const attrIds = (v.attribute_value_ids || '').toString().split(',').map(function(item) {
                                                         return item.trim();
                                                     });
                                                     const variantIds = (v.variant_ids || '').toString().split(',').map(function(item) {
                                                         return item.trim();
                                                     });
                                                     const variantValues = (v.variant_values || '').toString().split(',').map(function(item) {
                                                         return item.trim().toLowerCase();
                                                     });
 
                                                     return (normalizedUomId !== '' && (attrIds.indexOf(normalizedUomId) !== -1 || variantIds.indexOf(normalizedUomId) !== -1)) ||
                                                         (normalizedUomValue !== '' && variantValues.indexOf(normalizedUomValue.toLowerCase()) !== -1);
                                                 }) || {};
                                             }

                                            const productName = $('#pro_input_text').val() || 'Product';
                                            const generatedSku = cached.variant_sku || existingData.sku || generateSKU(productName, uomValue);

                                            const defaultStock = $('#total_stock').val() || '0';
                                            const hasCustomStock = (cached.variant_total_stock !== undefined || prefill.variant_total_stock !== undefined || existingData.total_stock !== undefined || existingData.stock !== undefined);
                                            const manualAttr = hasCustomStock ? 'data-manual="true"' : '';

                                            const html = `
                                                    <div class="p-2 border rounded bg-gray-light product-variant-selectbox" data-row-index="${variantCounter}">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control variant-label" id="quantity_${variantCounter}" value="${uomValue}" readonly>
                                                            </div>
                                                            <div class="col-md-10 text-right">
                                                                <a data-toggle="collapse" class="btn btn-tool text-primary" data-target="#${attr_name}" aria-expanded="true">
                                                                    <i class="fas fa-angle-down fa-2x"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="variants_ids[]" value="${uomId}">
                                                        <input type="hidden" name="edit_variant_id[]" value="${existingData.id || ''}">
                                                        <div id="${attr_name}" class="collapse show">
                                                            <div class="form-group row">
                                                                <div class="col">
                                                                    <label class="control-label">Purchase Price:</label>
                                                                    <input type="number" step="any" id="purchase_price_${variantCounter}" name="purchase_price[]" value="${cached.purchase_price ?? prefill.purchase_price ?? ((existingData.purchase_price && parseFloat(existingData.purchase_price) !== 0) ? existingData.purchase_price : (prefill.purchasePrice || ''))}" class="form-control" ${tempEvent} onkeyup="newPrice(this);">
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Packing Price:</label>
                                                                    <input type="number" step="any" id="packing_price_${variantCounter}" name="packing_price[]" value="${cached.packing_price ?? prefill.packing_price ?? (existingData.packing_price || '0')}" class="form-control" onkeyup="newPrice(this);">
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Margin Type:</label>
                                                                    <select class="form-control" onchange="newPrice(this)" name="margin_type[]" id="margin_type_${variantCounter}">
                                                                        <option ${(cached.margin_type ?? prefill.margin_type ?? existingData.margin_type) === 'Fixed' ? 'selected' : ''}>Fixed</option>
                                                                        <option ${(cached.margin_type ?? prefill.margin_type ?? existingData.margin_type) === 'Percentage' ? 'selected' : ''}>Percentage</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Margin:</label>
                                                                    <input type="number" step="any" id="user_percent_${variantCounter}" name="margin_percent[]" value="${cached.margin_percent ?? prefill.margin_percent ?? (existingData.margin_percent || '0')}" class="form-control" onkeyup="newPrice(this);">
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Discount %:</label>
                                                                    <input type="number" step="any" id="disc_percent_${variantCounter}" name="disc_percent[]" value="${cached.disc_percent ?? prefill.disc_percent ?? (existingData.disc_percent || '0')}" class="form-control" onkeyup="newPrice(this);">
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Price :</label>
                                                                    <input type="number" step="any" id="price_${variantCounter}" value="${dsFormatPrice(cached.variant_price ?? prefill.variant_price ?? (existingData.product_price || existingData.price || ''))}" readonly style="background-color: #e9ecef;" name="variant_price[]" class="form-control price" min="1">
                                                                </div>
                                                                <div class="col">
                                                                    <label class="control-label">Special Price :</label>
                                                                    <input type="number" step="any" id="disc_price_${variantCounter}" value="${dsFormatPrice(cached.variant_special_price ?? prefill.variant_special_price ?? (existingData.special_price || ''))}" readonly style="background-color: #e9ecef;" name="variant_special_price[]" class="form-control discounted_price" min="0">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <label class="control-label">Sku :</label>
                                                                    <input type="text" name="variant_sku[]" value="${generatedSku}" class="form-control">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="control-label">Total Stock :</label>
                                                                    <input type="number" step="any" min="0" name="variant_total_stock[]" value="${cached.variant_total_stock ?? prefill.variant_total_stock ?? (existingData.total_stock || existingData.stock || defaultStock || totalStock)}" class="form-control" ${manualAttr}>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="control-label">Stock Status :</label>
                                                                    <select name="variant_level_stock_status[]" class="form-control">
                                                                        <option value="1" ${(cached.variant_level_stock_status ?? prefill.variant_level_stock_status ?? existingData.level_stock_status ?? existingData.availability) == 1 ? 'selected' : ''}>In Stock</option>
                                                                        <option value="0" ${(cached.variant_level_stock_status ?? prefill.variant_level_stock_status ?? existingData.level_stock_status ?? existingData.availability) == 0 ? 'selected' : ''}>Out Of Stock</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="pt-2">
                                                                <label class="control-label">Images :</label>
                                                                <div class="col-md-3 p-0">
                                                                    <a class="uploadFile img btn btn-primary text-white btn-sm" data-input="variant_images[${variantCounter}][]" data-isremovable="1" data-is-multiple-uploads-allowed="1" data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class="fa fa-upload"></i> Upload</a>
                                                                </div>
                                                                <div class="container-fluid row image-upload-section"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;

                                            if (existingKey !== nextKey) {
                                                detailsCol.empty();
                                                detailsCol.html(html);
                                                row.attr('data-variant-key', nextKey);
                                                newPrice(detailsCol.find('input[name="purchase_price[]"]')[0]); // Trigger price calculation after rendering
                                            }
                                        }

                                            $('.product-row').each(function() {
                                            getOrAssignVariantCounter($(this));
                                            syncRowToVariant($(this));
                                        });

                                        function dsInitUomSelect(el) {
                                            const $el = $(el);
                                            if (!$el.length || !$.fn.select2) return;
                                            if ($el.hasClass('select2-hidden-accessible')) {
                                                $el.select2('destroy');
                                            }
                                            $el.select2({
                                                theme: 'bootstrap4',
                                                width: '100%',
                                                    placeholder: $el.data('placeholder') || 'Select Value'
                                            });
                                        }

                                        dsInitUomSelect($('.uom-select'));

                                        $(document).on('change', '.uom-select', function() {
                                            const row = $(this).closest('.product-row');
                                                syncRowToVariant(row);
                                        });

                                        // ➕ Add new row
                                        $(document).on('click', '#add-row-btn', function() {
                                            const productName = $('#pro_input_text').val();
                                            const purchasePrice = $('#purchaseprice').val();
                                            const purchaseUnit = $('#purchase_price_per option:selected').val();
                                            const totalStock = $('#total_stock').val();
                                            const packingPrice = $('#packing_price').val() || '0';
                                            const marginType = $('#margin_type').val() || 'Fixed';
                                            const margin = $('#user_percent').val() || '0';
                                            const discount = $('#disc_percent').val() || '0';

                                            const newRowHtml = dsBuildProductRowHtml(productName, purchasePrice, purchaseUnit, totalStock, packingPrice, marginType, margin, discount);
                                            const $newRow = $(newRowHtml);
                                            $('#product-row-container').append($newRow);
                                            dsInitUomSelect($newRow.find('.uom-select'));
                                            getOrAssignVariantCounter($newRow);
                                            $newRow.data('ds-prefill', {
                                                purchasePrice: purchasePrice,
                                                variant_total_stock: totalStock,
                                                packing_price: packingPrice,
                                                margin_type: marginType,
                                                margin_percent: margin,
                                                disc_percent: discount
                                            });
                                            syncRowToVariant($newRow);
                                            $('#product-row-container .product-row').removeClass('ds-active-variant-row');
                                            $newRow.addClass('ds-active-variant-row');
                                            dsScrollFlash($newRow);
                                        });

                                        function dsBuildProductRowHtml(productName = '', purchasePrice = '', purchaseUnit = '', totalStock = '', packingPrice = '0', marginType = 'Fixed', margin = '0', discount = '0') {
                                            let attrOptionsHtml = `<option value="">Select Attribute</option>`;
                                            for (const attrName in groupedVars) {
                                                attrOptionsHtml += `<option value="${attrName}" ${attrName === purchaseUnit ? 'selected' : ''}>${attrName}</option>`;
                                            }

                                            let uomOptionsHtml = `<option value="">Select Value</option>`;
                                            if (purchaseUnit && groupedVars[purchaseUnit]) {
                                                groupedVars[purchaseUnit].forEach(function(val) {
                                                    const vid = varIdMap[val] || '';
                                                    uomOptionsHtml += `<option value="${val}" data-id="${vid}" ${val === purchaseUnit ? 'selected' : ''}>${val}</option>`;
                                                });
                                            }

                                            return `
<div class="row product-row mb-2">
    <input type="hidden" name="row_id[]" value="0">
    <div class="col-md-3">
        <input type="text" class="form-control" name="printname[]" placeholder="Print Name" value="${productName}">
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" name="article[]" placeholder="Article No">
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" name="hpnumber[]" placeholder="HP Number">
    </div>
    <div class="col-md-2">
        <select class="form-control attr-select" name="attr[]">
            ${attrOptionsHtml}
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-control uom-select w-100" name="uom[]" data-placeholder="Select Value">
            ${uomOptionsHtml}
        </select>
    </div>
    <div class="col-md-1 ds-action-col">
        <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fa fa-trash"></i></button>
    </div>
</div>`;
                                        }

                                        // 🗑️ Remove row
                                        $(document).on('click', '.remove-row-btn', function() {
                                            const row = $(this).closest('.product-row');
                                            row.nextUntil('.product-row', '.variant-details-row').remove();
                                            row.remove();
                                        });

                                        function dsScrollFlash($row) {
                                            if (!$row || !$row.length) return;
                                            $row[0].scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                            $row.addClass('ds-flash');
                                            setTimeout(function() {
                                                $row.removeClass('ds-flash');
                                            }, 1500);
                                        }

                                        // 🖨️ Duplicate row
                                        $(document).on('click', '.duplicate-row-btn', function(e) {
                                            e.preventDefault();
                                            const row = $(this).closest('.product-row');
                                            const $insertAfter = row.nextUntil('.product-row', '.variant-details-row').length ? row.nextUntil('.product-row', '.variant-details-row') : row;
                                            const $newRow = $(dsBuildProductRowHtml());

                                            const $sourceAttr = row.find('.attr-select');
                                            const $sourceUom = row.find('.uom-select');
                                            const printname = row.find('input[name="printname[]"]').val();
                                            const article = row.find('input[name="article[]"]').val();
                                            const hpnumber = row.find('input[name="hpnumber[]"]').val();

                                            $newRow.find('input[name="printname[]"]').val(printname);
                                            $newRow.find('input[name="article[]"]').val(article);
                                            $newRow.find('input[name="hpnumber[]"]').val(hpnumber);
                                            $newRow.find('.attr-select').html($sourceAttr.html()).val($sourceAttr.val());
                                            $newRow.find('.uom-select').html($sourceUom.html()).val($sourceUom.val());

                                            const detailsCol = row.nextUntil('.product-row', '.variant-details-row').find('> .col-md-12');
                                            if (detailsCol.length) {
                                                const prefillObj = {
                                                    purchase_price: detailsCol.find('input[name="purchase_price[]"]').val(),
                                                    packing_price: detailsCol.find('input[name="packing_price[]"]').val(),
                                                    margin_type: detailsCol.find('select[name="margin_type[]"]').val(),
                                                    margin_percent: detailsCol.find('input[name="margin_percent[]"]').val(),
                                                    disc_percent: detailsCol.find('input[name="disc_percent[]"]').val(),
                                                    variant_price: detailsCol.find('input[name="variant_price[]"]').val(),
                                                    variant_special_price: detailsCol.find('input[name="variant_special_price[]"]').val(),
                                                    variant_total_stock: detailsCol.find('input[name="variant_total_stock[]"]').val(),
                                                    variant_level_stock_status: detailsCol.find('select[name="variant_level_stock_status[]"]').val()
                                                };
                                                if (detailsCol.find('input[name="variant_total_stock[]"]').attr('data-manual') === 'true') {
                                                    prefillObj.variant_total_stock_manual = true;
                                                }
                                                $newRow.data('ds-prefill', prefillObj);
                                            }

                                            $newRow.data('ds-force-new', true);
                                            $newRow.insertAfter($insertAfter);
                                            getOrAssignVariantCounter($newRow);
                                            dsInitUomSelect($newRow.find('.uom-select'));
                                            syncRowToVariant($newRow);
                                            $('#product-row-container .product-row').removeClass('ds-active-variant-row');
                                            $newRow.addClass('ds-active-variant-row');
                                            dsScrollFlash($newRow);
                                        });
                                    });
                                </script>
                    <div id="attributes_values_json_data" class="d-none">
                        <select class="select_single" data-placeholder=" Type to search and select attributes">
                            <option value=""></option>
                            <?php



                            foreach ($attributes_refind as $key => $value) {
                            ?>
                                <optgroup label="<?= $key ?>"><?= $key ?>
                                    <?php foreach ($value as $key => $value) {  ?>
                                        <option name='<?= $key ?>' value='<?= $key ?>' data-values='<?= json_encode($value, 1) ?>'><?= $key ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php
                            }
                            ?>
                        </select>
                    </div>


                    <div class="col-12 mb-3 d-none">
                        <h3 class="card-title">Additional Info</h3>

                        <?php
                        if (isset($product_details)) {
                            $HideStatus = (isset($product_details[0]['id']) && $product_details[0]['stock_type'] == NULL) ? 'collapse' : '';
                        ?>
                            <div class="col-12 row additional-info existing-additional-settings">
                                <div class="row mt-4 col-md-12 ">
                                    <nav class="w-100">
                                        <div class="nav nav-tabs" id="product-tab" role="tablist">
                                            <a class="nav-item nav-link active" id="tab-for-general-price" data-toggle="tab" href="#general-settings" role="tab" aria-controls="general-price" aria-selected="true">General</a>
                                            <a class="nav-item nav-link d-none edit-product-attributes" id="tab-for-attributes" data-toggle="tab" href="#product-attributes" role="tab" aria-controls="product-attributes" aria-selected="false">Attributes</a>
                                            <a class="nav-item nav-link <?= ($product_details[0]['type'] == 'simple_product') ? 'disabled d-none' : 'edit-variants'; ?>" id="tab-for-variations" data-toggle="tab" href="#product-variants" role="tab" aria-controls="product-variants" aria-selected="false">Variations</a>
                                        </div>
                                    </nav>
                                </div>

                                <div class="tab-content p-3 col-md-12" id="nav-tabContent">
                                    <div class="tab-pane fade active show" id="general-settings" role="tabpanel" aria-labelledby="general-settings-tab">
                                        <div class="form-group">
                                            <label for="type" class="col-md-12">Type Of Product :</label>
                                            <div class="col-md-12">
                                                <?php @$variant_stock_level = !empty($product_details[0]['stock_type']) && $product_details[0]['stock_type'] == '1' ? 'product_level' : 'variant_level' ?>
                                                <input type="hidden" name="product_type__ignore" value="<?= isset($product_details[0]['type']) ? $product_details[0]['type'] : '' ?>">
                                                <input type="hidden" name="simple_product_stock_status__ignore" <?= isset($product_details[0]['stock_type']) && !empty($product_details[0]['stock_type']) && $product_details[0]['type'] == 'simple_product' ? 'value="' . $product_details[0]['stock_type'] . '"'  : '' ?>>
                                                <input type="hidden" name="variant_stock_level_type__ignore" <?= isset($product_details[0]['stock_type']) && !empty($product_details[0]['stock_type']) && $product_details[0]['type'] == 'variable_product' ? 'value="' . $variant_stock_level . '"'  : '' ?>>
                                                <input type="hidden" name="variant_stock_status__ignore" <?= isset($product_details[0]['stock_type']) && !empty($product_details[0]['stock_type']) && $product_details[0]['type'] == 'variable_product' ? 'value="0"'  : '' ?>>
                                                <select name="type" id="product-type" class="form-control" data-placeholder=" Type to search and select type" <?= isset($product_details[0]['id']) ? 'disabled' : '' ?>>
                                                    <option value=" ">Select Type</option>
                                                    <!-- <option value="simple_product" <?= ($product_details[0]['type'] == "simple_product") ? 'selected' : '' ?>>Simple Product</option> -->
                                                    <option value="variable_product" <?= ($product_details[0]['type'] == "variable_product") ? 'selected' : '' ?>>Variable Product</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id='product-general-settings'>
                                            <?php
                                            if ($product_details[0]['type'] == "simple_product") {
                                            ?>
                                                <div id="general_price_section">
                                                    <!--yaha  -->
                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Purchase Priceddd:</label>
                                                        <div class="col-md-12">
                                                            <input type="number" id="purchase_price" value="<?= $product_variants[0]['purchase_price'] ?>" name="purchase_price" class="form-control" onkeyup="newQty();">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Margin Type:</label>
                                                        <div class="col-md-12">


                                                            <select class="form-control" onchange="newQty();" name="margin_type" id="margin_type">
                                                                <option>Fixed</option>
                                                                <option>Percentage</option>

                                                            </select>

                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Margin:</label>
                                                        <div class="col-md-12">
                                                            <input type="number" name="margin_percent" value="<?= $product_variants[0]['margin_percent'] ?>" id="user_percent" class="form-control" onkeyup="newQty();">

                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Discount Percentage:</label>
                                                        <div class="col-md-12">
                                                            <input type="number" name="disc_percent" value="<?= $product_variants[0]['disc_percent'] ?>" id="disc_percent" class="form-control" onkeyup="newQty();">

                                                        </div>
                                                    </div>
                                                    <!--yaha  -->
                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Price:</label>
                                                        <div class="col-md-12">
                                                            <input type="number" name="simple_price" id="price" class="form-control stock-simple-mustfill-field price" value="<?= isset($product_variants[0]['price']) ? round((float)$product_variants[0]['price'], 2) : '' ?>" min='1' step="0.01" readonly style="background-color: #e9ecef;">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="type" class="col-md-2">Special Price:</label>
                                                        <div class="col-md-12">
                                                            <input type="number" name="simple_special_price" id="disc_price" class="form-control  discounted_price" value="<?= isset($product_variants[0]['special_price']) ? round((float)$product_variants[0]['special_price'], 2) : '' ?>" min='0' step="0.01" readonly style="background-color: #e9ecef;">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col">
                                                            <input type="checkbox" name="simple_stock_management_status" class="align-middle simple_stock_management_status" <?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] != NULL) ? 'checked' : '' ?>> <span class="align-middle">Enable Stock Management</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group simple-product-level-stock-management <?= $HideStatus ?>">
                                                    <div class="col col-xs-12">
                                                        <label class="control-label">SKU :</label>
                                                        <input type="text" name="product_sku" class="col form-control simple-pro-sku" value="<?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] != NULL) ? $product_details[0]['sku'] : '' ?>">
                                                    </div>
                                                    <div class="col col-xs-12">
                                                        <label class="control-label">Total Stock :</label>
                                                        <input type="number" min="1" name="product_total_stock" class="col form-control stock-simple-mustfill-field" <?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] != NULL) ? ' value="' . $product_details[0]['stock'] . '" ' : '' ?>>
                                                    </div>
                                                    <div class="col col-xs-12">
                                                        <label class="control-label">Stock Status :</label>
                                                        <select type="text" class="col form-control stock-simple-mustfill-field" id="simple_product_stock_status">
                                                            <option value="1" <?= (isset($product_details[0]['stock_type']) &&
                                                                                    $product_details[0]['stock_type'] != NULL && $product_details[0]['availability'] == "1") ? 'selected' : '' ?>>In Stock</option>
                                                            <option value="0" <?= (isset($product_details[0]['stock_type']) &&
                                                                                    $product_details[0]['stock_type'] != NULL && $product_details[0]['availability'] == "0") ? 'selected' : '' ?>>Out Of Stock</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group simple-product-save">
                                                    <div class="col">
                                                        <a href="javascript:void(0);" class="btn btn-primary save-settings">Save Settings</a>
                                                        <a href="javascript:void(0);" class="btn btn-warning reset-settings">Reset Settings</a>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div id="variant_stock_level">
                                                    <div class="form-group">
                                                        <div class="col">
                                                            <input type="checkbox" name="variant_stock_management_status" class="align-middle variant_stock_status" <?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] != NULL) ? 'checked' : '' ?>> <span class="align-middle"> Enable Stock Management</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group <?= (intval($product_details[0]['stock_type']) > 0) ? '' : 'collapse' ?>" id='stock_level'>
                                                        <label for="type" class="col-md-2">Choose Stock Management Type:</label>
                                                        <div class="col-md-12">
                                                            <select id="stock_level_type" class="form-control variant-stock-level-type" data-placeholder=" Type to search and select type">
                                                                <option value=" ">Select Stock Type</option>
                                                                <!-- <option value="product_level" <?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] == '1') ? 'Selected' : '' ?>> Product Level ( Stock Will Be Managed Generally )</option> -->
                                                                <option value="variable_level" <?= (isset($product_details[0]['id']) && $product_details[0]['stock_type'] == '2') ? 'Selected' : '' ?>>Variable Level ( Stock Will Be Managed Variant Wise )</option>
                                                            </select>
                                                            <div class="form-group variant-product-level-stock-management <?= (intval($product_details[0]['stock_type']) == 1) ? '' : 'collapse' ?>">
                                                                <div class="col col-xs-12">
                                                                    <label class="control-label">SKU :</label>
                                                                    <input type="text" name="sku_variant_type" class="col form-control" value="<?= (intval($product_details[0]['stock_type']) == 1 && isset($product_variants[0]['id']) && !empty($product_variants[0]['sku'])) ? $product_variants[0]['sku'] : '' ?>">
                                                                </div>
                                                                <div class="col col-xs-12">
                                                                    <label class="control-label">Total Stock :</label>
                                                                    <input type="number" min="1" name="total_stock_variant_type" class="col form-control variant-stock-mustfill-field" value="<?= (intval($product_details[0]['stock_type']) == 1 && isset($product_variants[0]['id']) && !empty($product_variants[0]['stock'])) ? $product_variants[0]['stock'] : '' ?>">
                                                                </div>
                                                                <div class="col col-xs-12">
                                                                    <label class="control-label">Stock Status :</label>
                                                                    <select type="text" id="stock_status_variant_type" name="variant_status" class="col form-control variant-stock-mustfill-field">
                                                                        <option value="1" <?= (intval($product_details[0]['stock_type']) == 1 && isset($product_variants[0]['id']) && $product_variants[0]['availability'] == '1') ? 'Selected' : '' ?>>In Stock</option>
                                                                        <option value="0" <?= (intval($product_details[0]['stock_type']) == 1 && isset($product_variants[0]['id']) && $product_variants[0]['availability'] == '0') ? 'Selected' : '' ?>>Out Of Stock</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col">
                                                            <a href="javascript:void(0);" class="btn btn-primary save-variant-general-settings">Save Settings</a>
                                                            <a href="javascript:void(0);" class="btn btn-warning reset-settings">Reset Settings</a>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="product-attributes" role="tabpanel" aria-labelledby="product-attributes-tab">
                                        <div class="info col-12 p-3 d-none" id="note">
                                            <div class=" col-12 d-flex align-center">
                                                <strong>Note : </strong>
                                                <input type="checkbox" checked="" class="ml-3 my-auto custom-checkbox" disabled>
                                                <span class="ml-3">check if the attribute is to be used for variation </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <a href="javascript:void(0);" id="add_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm">Add Attributes</a>
                                            <a href="javascript:void(0);" id="save_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm d-none">Save Attributes</a>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div id="attributes_process">
                                            <div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-attributes-added">
                                                <div class="col-md-12 text-center">No Product Attribures Are Added ! </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade" id="product-variants" role="tabpanel" aria-labelledby="product-variants-tab">
                                        <div class="col-md-12">
                                            <a href="javascript:void(0);" id="reset_variants" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm collapse">Reset Variants</a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-variants-added">
                                            <div class="col-md-12 text-center"> No Product Variations Are Added ! </div>
                                        </div>
                                        <div id="variants_process_additional" class="ui-sortable">
                                        </div>
                                    </div>
                                <?php
                            } else {

                                ?>
                                    <div class="col-12 row additional-info existing-additional-settings">
                                        <div class="row mt-4 col-md-12 ">
                                            <nav class="w-100">
                                                <div class="nav nav-tabs" id="product-tab" role="tablist"> <a class="nav-item nav-link active" id="tab-for-general-price" data-toggle="tab" href="#general-settings" role="tab" aria-controls="general-price" aria-selected="true">General</a> <a class="nav-item nav-link d-none disabled product-attributes" id="tab-for-attributes" data-toggle="tab" href="#product-attributes" role="tab" aria-controls="product-attributes" aria-selected="false">Attributes</a> <a class="nav-item nav-link disabled product-variants d-none" id="tab-for-variations" data-toggle="tab" href="#product-variants" role="tab" aria-controls="product-variants" aria-selected="false">Variations</a>
                                                </div>
                                            </nav>
                                            <div class="tab-content p-3 col-md-12" id="nav-tabContent">
                                                <div class="tab-pane fade active show" id="general-settings" role="tabpanel" aria-labelledby="general-settings-tab">
                                                    <div class="form-group">
                                                        <label for="type" class="col-md-12">Type Of Product :</label>
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="product_type__ignore">
                                                            <input type="hidden" name="simple_product_stock_status__ignore">
                                                            <input type="hidden" name="variant_stock_level_type__ignore">
                                                            <input type="hidden" name="variant_stock_status__ignore">
                                                            <select name="type" id="product-type" class="form-control product-type" data-placeholder=" Type to search and select type">
                                                                <option value=" ">Select Type</option>
                                                                <!-- <option value="simple_product">Simple Product</option> -->
                                                                <option value="variable_product">Variable Product</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div id="product-general-settings">
                                                        <div id="general_price_section" class="collapse">

                                                            <!--yaha  -->
                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Purchase Price:</label>
                                                                <div class="col-md-12">
                                                                    <input type="number" id="purchase_price" name="purchase_price" class="form-control" onkeyup="newQty();">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Margin Type:</label>
                                                                <div class="col-md-12">


                                                                    <select class="form-control" onchange="newQty();" name="margin_type" id="margin_type">
                                                                        <option>Fixed</option>
                                                                        <option>Percentage</option>

                                                                    </select>

                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Margin:</label>
                                                                <div class="col-md-12">
                                                                    <input type="number" name="margin_percent" id="user_percent" class="form-control" onkeyup="newQty();">

                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Discount %:</label>
                                                                <div class="col-md-12">
                                                                    <input type="number" name="disc_percent" value="0" id="disc_percent" class="form-control" onkeyup="newQty();">

                                                                </div>
                                                            </div>
                                                            <!--yaha  -->
                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Price:</label>
                                                                <div class="col-md-12">
                                                                    <input type="number" id="price" name="simple_price" class="form-control stock-simple-mustfill-field price" min='1' step="0.01" readonly style="background-color: #e9ecef;">

                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="type" class="col-md-2">Special Price:</label>
                                                                <div class="col-md-12">
                                                                    <input type="number" id="disc_price" name="simple_special_price" class="form-control discounted_price" min='0' step="0.01" readonly style="background-color: #e9ecef;">
                                                                    <!-- <input type="number" id="disc_price" name="simple_special_price" class="form-control discounted_price" min='0' step="0.01" disabled> -->

                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col">
                                                                    <input type="checkbox" name="simple_stock_management_status" class="align-middle simple_stock_management_status"> <span class="align-middle">Enable Stock Management</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group simple-product-level-stock-management collapse">
                                                            <div class="col col-xs-12">
                                                                <label class="control-label">SKU :</label>
                                                                <input type="text" name="product_sku" class="col form-control simple-pro-sku">
                                                            </div>
                                                            <div class="col col-xs-12">
                                                                <label class="control-label">Total Stock :</label>
                                                                <input type="number" min="1" name="product_total_stock" class="col form-control stock-simple-mustfill-field">
                                                            </div>
                                                            <div class="col col-xs-12">
                                                                <label class="control-label">Stock Status :</label>
                                                                <select type="text" class="col form-control stock-simple-mustfill-field" id="simple_product_stock_status">
                                                                    <option value="1">In Stock</option>
                                                                    <option value="0">Out Of Stock</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group collapse simple-product-save">
                                                            <div class="col"> <a href="javascript:void(0);" class="btn btn-primary save-settings">Save Settings</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="variant_stock_level" class="collapse">
                                                        <div class="form-group">
                                                            <div class="col">
                                                                <input type="checkbox" name="variant_stock_management_status" class="align-middle variant_stock_status"> <span class="align-middle"> Enable Stock Management</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group collapse" id="stock_level">
                                                            <label for="type" class="col-md-2">Choose Stock Management Type:</label>
                                                            <div class="col-md-12">
                                                                <select id="stock_level_type" class="form-control variant-stock-level-type" data-placeholder=" Type to search and select type">
                                                                    <option value=" ">Select Stock Type</option>
                                                                    <!-- <option value="product_level">Product Level ( Stock Will Be Managed Generally )</option> -->
                                                                    <option value="variable_level">Variable Level ( Stock Will Be Managed Variant Wise )</option>
                                                                </select>
                                                                <div class="form-group row variant-product-level-stock-management collapse">
                                                                    <div class="col col-xs-12">
                                                                        <label class="control-label">SKU :</label>
                                                                        <input type="text" name="sku_variant_type" class="col form-control">
                                                                    </div>
                                                                    <div class="col col-xs-12">
                                                                        <label class="control-label">Total Stock :</label>
                                                                        <input type="number" min="1" name="total_stock_variant_type" class="col form-control variant-stock-mustfill-field">
                                                                    </div>
                                                                    <div class="col col-xs-12">
                                                                        <label class="control-label">Stock Status :</label>
                                                                        <select type="text" id="stock_status_variant_type" name="variant_status" class="col form-control variant-stock-mustfill-field">
                                                                            <option value="1">In Stock</option>
                                                                            <option value="0">Out Of Stock</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col"> <a href="javascript:void(0);" class="btn btn-primary save-variant-general-settings">Save Settings</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="product-attributes" role="tabpanel" aria-labelledby="product-attributes-tab">
                                                    <div class="info col-12 p-3 d-none" id="note">
                                                        <div class=" col-12 d-flex align-center"> <strong>Note : </strong>
                                                            <input type="checkbox" checked="checked" class="ml-3 my-auto custom-checkbox" disabled> <span class="ml-3">check if the attribute is to be used for variation </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12"> <a href="javascript:void(0);" id="add_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm">Add Attributes</a> <a href="javascript:void(0);" id="save_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm d-none">Save Attributes</a>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div id="attributes_process">
                                                        <div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-attributes-added">
                                                            <div class="col-md-12 text-center">No Product Attribures Are Added !</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="product-variants" role="tabpanel" aria-labelledby="product-variants-tab">
                                                    <div class="clearfix"></div>
                                                    <div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-variants-added">
                                                        <div class="col-md-12 text-center">No Product Variations Are Added !</div>
                                                    </div>
                                                    <div id="variants_process_additional" class="ui-sortable"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                                ?>
                                </div>
                            </div>
                    </div>
                    <div class="card-body pad">
                        <label for="pro_input_description">Description</label>
                        <div class="mb-3">

                            <textarea name="pro_input_description" class="textarea addr_editor form-control w-100" rows="10" placeholder="Enter product description"><?= (isset($product_details[0]['id'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $product_details[0]['description'])) : ''; ?></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="form-group" id="error_box">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button type="submit" class="btn btn-success" id="submit_btn"><?= (isset($product_details[0]['id'])) ? 'Update Product' : 'Add Product' ?></button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <!--/.card-->
</div>
<!--/.col-md-12-->
</div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>


<script>
    $(document).ready(function() {


        $('.add_variant_button').on('click', function() {
            // Assuming you insert a new variant element here...
            setTimeout(function() {
                upatevarprice();
            }, 300); // adjust timing based on how your DOM updates
        });


        $('#tab-for-variations').on('click', function() {
            // Short delay ensures DOM updates (if variants were dynamically added)
            setTimeout(function() {
                upatevarprice();
            }, 200); // You can reduce or increase this depending on how your DOM updates
        });

        upatevarprice();



        $('#purchaseprice').on('input change', function() {
            upatevarprice(true);
        });
    });
</script>

<script>
    $(document).on('click', '#tab-for-variations', function() {

        upatevarprice();

    });

    // Function to sync variant stocks with opening stock
    function syncVariantStocksWithOpeningStock() {
        // Get opening stock value using ID selector
        var openingStock = $('#total_stock').val();
        var productId = $('input[name="edit_product_id"]').val() || $('input[name="products_id"]').val();

        if (openingStock === '' || openingStock === null || openingStock === undefined) {
            // Only try to get from localStorage if we have a productId (edit mode)
            if (productId) {
                var finalStock = localStorage.getItem('final_stock_' + productId);
                if (finalStock && finalStock !== 'null' && finalStock !== '') {
                    openingStock = parseFloat(finalStock);
                    if (!isNaN(openingStock) && openingStock >= 0) {
                        $('#total_stock').val(openingStock);
                    } else {
                        return;
                    }
                } else {
                    return;
                }
            } else {
                return;
            }
        }

        var stockValue = parseFloat(openingStock);
        if (isNaN(stockValue) || stockValue < 0) {
            return;
        }

        // Sync Product-level variant stock
        var $prodLevelStock = $('input[name="total_stock_variant_type"]');
        if ($prodLevelStock.length) {
            $prodLevelStock.val(stockValue);
        }

        // Sync Simple Product stock
        var $simpleProdStock = $('.simple-product-level-stock-management input[name="product_total_stock"]');
        if ($simpleProdStock.length) {
            $simpleProdStock.val(stockValue);
        }

        // Sync all variant-level stocks (only if they are empty)
        $('input[name="variant_total_stock[]"]').each(function() {
            var $variantStock = $(this);
            var existingVariantId = $variantStock.closest('.product-variant-selectbox').find('input[name="edit_variant_id[]"]').val();
            if (existingVariantId) {
                return;
            }
            if ($variantStock.val() === '' || $variantStock.val() === null) {
                $variantStock.val(stockValue);
            }
        });

        console.log('Stocks synced with opening stock: ' + stockValue);
    }

    // Function to populate opening stock from localStorage (only in edit mode)
    function populateOpeningStockFromLocalStorage() {
        // Only run in edit mode (not when creating new product)
        var productId = $('input[name="edit_product_id"]').val() || $('input[name="products_id"]').val();
        if (!productId) {
            return; // Don't run for new products
        }

        var finalStock = localStorage.getItem('final_stock_' + productId);
        if (finalStock && finalStock !== 'null' && finalStock !== '') {
            var stockValue = parseFloat(finalStock);
            if (!isNaN(stockValue) && stockValue > 0) {
                // Populate Opening Stock
                $('#total_stock').val(stockValue);
                // Then sync variant stocks
                syncVariantStocksWithOpeningStock();
                console.log('Opening stock populated with ledger stock: ' + stockValue);
                // Clear the localStorage so it doesn't persistently overwrite DB value on future page loads
                localStorage.removeItem('final_stock_' + productId);
            }
        }
    }

    // Populate stock fields from localStorage (final stock from warehouse modal)
    $(document).ready(function() {
        // Only populate in edit mode
        populateOpeningStockFromLocalStorage();

        // Also populate after a delay to handle dynamically loaded variants
        setTimeout(function() {
            populateOpeningStockFromLocalStorage();
            syncVariantStocksWithOpeningStock();
        }, 1000);

        // Sync variant stocks when variants tab is clicked
        $(document).on('click', '#tab-for-variations', function() {
            setTimeout(function() {
                syncVariantStocksWithOpeningStock();
            }, 500);
        });

        // Sync variant stocks when opening stock changes manually
        $(document).on('input change', '#total_stock', function() {
            syncVariantStocksWithOpeningStock();
        });

        // Mark variant stocks as manually modified if user interacts with them
        $(document).on('input change', 'input[name="variant_total_stock[]"], input[name="total_stock_variant_type"], .simple-product-level-stock-management input[name="product_total_stock"]', function() {
            $(this).attr('data-manual', 'true');
        });

        // Use MutationObserver to watch for dynamically added variant stock fields
        var observer = new MutationObserver(function(mutations) {
            var hasVariantStockFields = $('input[name="variant_total_stock[]"]').length > 0;
            if (hasVariantStockFields) {
                syncVariantStocksWithOpeningStock();
            }
        });

        // Observe the variants container (product-row-container holds both rows and details)
        var variantsContainer = document.getElementById('product-row-container');
        if (variantsContainer) {
            observer.observe(variantsContainer, {
                childList: true,
                subtree: true
            });
        }
    });
</script>
