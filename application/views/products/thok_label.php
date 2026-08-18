<div class="content-body">
    <div class="card">
        <div class="card-header"><h5>Standard Label</h5></div>
        <div class="card-body">
            <?php
            echo form_open('products/print_labels', ['id' => 'label_form']);
            ?>
            <div class="form-group row">
                <label class="col-sm-2">Warehouse</label>
                <div class="col-sm-4">
                    <select name="from_warehouse" class="form-control">
                        <option value="">Select Warehouse</option>
                        <?php foreach ($warehouse as $row): ?>
                            <option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="product_variant_rows">
                <div class="form-row product-variant-row mb-2">
                    <div class="col-md-3">
                        <label>Product</label>
                        <select class="form-control product-select" name="product_id[]">
                            <option value="">Select Product</option>
                            <?php foreach ($all_products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Print Name</label>
                        <select class="form-control print-name-select" name="print_name[]">
                            <option value="">Select Print Name</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Article No</label>
                        <input type="text" class="form-control article-no" name="article_no[]" readonly>
                    </div>
                    <div class="col-md-3 variant-list">
                        <label>Variant Name</label>
                        <div class="variant-checkboxes"></div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-success add-row mr-1">+</button>
                        <button type="button" class="btn btn-danger remove-row">-</button>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Print Label</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    const all_variants = <?= json_encode($all_variant_data); ?>;

    function updatePrintNames(productId, row) {
        const printSelect = row.find('.print-name-select');
        const variantDiv = row.find('.variant-checkboxes');
        const articleInput = row.find('.article-no');

        printSelect.html('<option value="">Select Print Name</option>');
        variantDiv.html('');
        articleInput.val('');

        if (all_variants[productId]) {
            Object.keys(all_variants[productId]).forEach(printName => {
                printSelect.append(`<option value="${printName}">${printName}</option>`);
            });
        }
    }

    function updateVariants(productId, printName, row) {
        const variants = all_variants[productId]?.[printName] || [];
        const variantDiv = row.find('.variant-checkboxes');
        const articleInput = row.find('.article-no');

        variantDiv.html('');
        articleInput.val('');

        if (variants.length > 0) {
            articleInput.val(variants[0]['article_no']);
            variants.forEach((v, i) => {
                const label = v.variant_values.join(', ');
                variantDiv.append(`
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="variants[${productId}][${printName}][]" value="${label}" checked>
                        <label class="form-check-label">${label}</label>
                    </div>
                `);
            });
        }
    }

    $(document).on('change', '.product-select', function () {
        const row = $(this).closest('.product-variant-row');
        const productId = $(this).val();
        updatePrintNames(productId, row);
    });

    $(document).on('change', '.print-name-select', function () {
        const row = $(this).closest('.product-variant-row');
        const productId = row.find('.product-select').val();
        const printName = $(this).val();
        updateVariants(productId, printName, row);
    });

    $(document).on('click', '.add-row', function () {
        const clone = $('.product-variant-row').first().clone();
        clone.find('select, input').val('');
        clone.find('.variant-checkboxes').html('');
        $('#product_variant_rows').append(clone);
    });

    $(document).on('click', '.remove-row', function () {
        if ($('.product-variant-row').length > 1) {
            $(this).closest('.product-variant-row').remove();
        }
    });
</script>
