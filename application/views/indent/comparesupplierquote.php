<style>
    .rate-green { color: green; font-weight: bold; }
    .rate-red { color: red; font-weight: bold; }
    .rate-orange { color: orange; font-weight: bold; }
    .indent-btn { margin-top: 20px; }
    .quote-action-bar {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
</style>

<article class="content">
    <div class="card card-body shadow-sm">
        <h5 class="mb-3">Supplier Quotation Comparison</h5>
        <p class="text-muted mb-3">Select one supplier rate for each product, then click Update Selected Price to send it to the warehouse Updated Price column.</p>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="thead-light align-middle">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Product</th>
                        <th rowspan="2">Require Qty</th>
                        <?php foreach ($quotations as $quote): ?>
                            <th colspan="2" class="text-center">
                                <div><strong><?= $quote->supplier_name ?></strong></div>
                                <div class="text-muted" style="font-size: 0.8rem;">(<?= $quote->phone ?>)</div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($quotations as $quote): ?>
                            <th>Qty</th>
                            <th>Rate</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($items_grouped as $group_key => $suppliers):
					
                        echo "<tr>";
                        echo "<td>{$i}</td>";
                        $first = reset($suppliers);
                        $display_name = (isset($first->display_name) && $first->display_name !== '') ? $first->display_name : (isset($first->item_name) ? $first->item_name : $group_key);
                        echo "<td>{$display_name}</td>";

                        $require_qty = reset($suppliers)->quantity;
                        echo "<td>{$require_qty}</td>";

                        $rates = array_map(function ($s) { return $s->fill_rate; }, $suppliers);
                        $min = min($rates);
                        $max = max($rates);

                        $has_checked = false;
                        foreach ($suppliers as $sup) {
                            if ($sup->adm_status == 1) {
                                $has_checked = true;
                                break;
                            }
                        }
                        $min_checked = false;
					
                        foreach ($quotations as $quote):
                            $supplier_id = $quote->supplier_id;
                            $suppliername = $quote->supplier_name;
                            $phone = $quote->phone;
                            if (isset($suppliers[$supplier_id])):
                                $s = $suppliers[$supplier_id];
                                $colorClass = 'text-dark';
                                if ($s->fill_rate == $min) $colorClass = 'rate-green';
                                elseif ($s->fill_rate == $max) $colorClass = 'rate-red';
                                elseif (count($rates) > 2) $colorClass = 'rate-orange';
                                
                                $checked = '';
                                if ($has_checked) {
                                    if ($s->adm_status == 1) {
                                        $checked = 'checked';
                                    }
                                } else {
                                    if ($s->fill_rate == $min && !$min_checked) {
                                        $checked = 'checked';
                                        $min_checked = true;
                                    }
                                }

                                echo "<td>{$s->fill_quantity}</td>";
                                echo "<td>
                                        <input type='checkbox' 
                                               class='rate-checkbox' 
                                               name='selected_items[" . md5($group_key) . "]' 
                                               data-item-id='{$s->itemid}' 
                                               data-product-name='" . htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') . "'
                                               data-suppliername='{$suppliername}' 
                                               data-phone='{$phone}' 
                                               data-supplier-id='{$supplier_id}' 
                                               data-order-id='{$s->quotation_id}'
                                               data-rate='{$s->fill_rate}' {$checked}>
                                        <span class='{$colorClass}'>{$s->fill_rate}</span>
                                      </td>";
                            else:
                                echo "<td colspan='2'>N/A</td>";
                            endif;
                        endforeach;

                        echo "</tr>";
                        $i++;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>

        <div class="quote-action-bar">
            <button type="button" class="btn btn-warning indent-btn" id="updatePriceBtn">Update Price</button>
            <button type="button" class="btn btn-primary indent-btn" data-toggle="modal" data-target="#confirmModal">Submit Indent</button>
        </div>
    </div>
</article>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Indent</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to send the indent?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button id="confirmSendBtn" class="btn btn-success">Yes, Send</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Toast -->
<div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
  <div id="successToast" class="toast bg-success text-white" data-delay="3000" role="alert">
    <div class="toast-body">
      Your indent sent successfully!
    </div>
  </div>
</div>

<!-- jQuery Script -->
<script>
    $(document).ready(function () {
        // ✅ Ensure only one checkbox per product
        $('.rate-checkbox').on('change', function () {
            const name = $(this).attr('name');
            if ($(this).is(':checked')) {
                $(`input[name='${name}']`).not(this).prop('checked', false);
            }
        });

        // ✅ Submit selected items
        $('#confirmSendBtn').on('click', function () {
            const selectedItems = [];

            $('.rate-checkbox:checked').each(function () {
                selectedItems.push({
                    item_id: $(this).data('item-id'),
                    suppliername: $(this).data('suppliername'),
                    phone: $(this).data('phone'),
                    supplier_id: $(this).data('supplier-id'),
                    reference_no: '<?=$reference_no?>',
                    order_id: $(this).data('order-id')
                });
            });

            if (selectedItems.length === 0) {
                alert("Please select at least one product!");
                return;
            }

            $.ajax({
                url: "<?= site_url('purchase/update_adm_status') ?>",
                type: "POST",
                data: { items: selectedItems },
                success: function (res) {
                    $('#confirmModal').modal('hide');
                    $('#successToast').toast('show');
                },
                error: function () {
                    alert("Error updating status. Try again.");
                }
            });
        });
        function sendPriceUpdate(selectedItems, successMessage) {
            $.ajax({
                url: "<?= site_url('purchase/update_product_price') ?>",
                type: "POST",
                dataType: "json",
                data: { items: selectedItems },
                success: function (res) {
                    if (res && res.status === 'success') {
                        alert(successMessage || "Prices updated successfully!");
                        location.reload();
                    } else {
                        alert((res && res.message) ? res.message : "Error updating prices. Try again.");
                    }
                },
                error: function (xhr) {
                    let message = "Error updating prices. Try again.";
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }

        // ✅ Update selected prices in bulk
        $('#updatePriceBtn').on('click', function () {
            const selectedItems = [];

            $('.rate-checkbox:checked').each(function () {
                selectedItems.push({
                    item_id: $(this).data('item-id'),
                    rate: $(this).data('rate')
                });
            });

            if (selectedItems.length === 0) {
                alert("Please select at least one product price to update!");
                return;
            }

            if (confirm("Are you sure you want to update the prices for selected products?")) {
                sendPriceUpdate(selectedItems, "Selected product prices updated successfully!");
            }
        });

    });
</script>
