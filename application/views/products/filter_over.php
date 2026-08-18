<?php
$sr_no = 1;
$unit = '';

$live_close_stock = 0.00;

$total_sell_qty = 0;
$total_purchage_qty = 0;
$total_sell_amount = 0;
$total_purchage_amount = 0;
$total_value = 0;

$total_wastage_qty = 0;
$total_wastage_amount = 0;

$latest_sell_rate = 0;
$latest_purchage_rate = 0;

// Color list (50 pastel colors)
$colorList = [
    '#e3f2fd', '#f1f8e9', '#fff3e0', '#e8f5e9', '#ede7f6',
    '#e0f7fa', '#f9fbe7', '#fce4ec', '#f3e5f5', '#fbe9e7',
    '#e1f5fe', '#f0f4c3', '#f8bbd0', '#d7ccc8', '#ffe0b2',
    '#c8e6c9', '#f5f5f5', '#e0f2f1', '#f3f6f4', '#e6ee9c',
    '#dcedc8', '#f9fbe7', '#e6f7ff', '#f0fff0', '#fdf6e3',
    '#f1f1f1', '#eef7fa', '#f3f3e9', '#eaf2ff', '#ecf0f1',
    '#f2f9f1', '#f4f8fb', '#e4f9f5', '#f6f8e9', '#ebf5fb',
    '#f9f4ed', '#e8f0fe', '#f5f9fc', '#f0f8ff', '#e2f0d9',
    '#f4f4ea', '#eaf3fc', '#e5f6df', '#e1f3f8', '#f8f8ff',
    '#f0f4e8', '#effafc', '#e8f8f5', '#eef4ff', '#f2f2e9'
];
$orderColorMap = [];
$colorIndex = 0;

function getRowColor($orderId, &$orderColorMap, &$colorIndex, $colorList) {
    if (empty($orderId)) return '#ffffff';
    if (!isset($orderColorMap[$orderId])) {
        $orderColorMap[$orderId] = $colorList[$colorIndex % count($colorList)];
        $colorIndex++;
    }
    return $orderColorMap[$orderId];
}

// Wastage entries array collect karne ke liye
$wastageEntries = [];

// ensure unit defined if prev_unit not present
if (!isset($prev_unit) || $prev_unit == '') {
    $prev_unit = '';
}

// Show Previous Summary Row
echo "<tr style='background:#d9edf7; font-weight:bold'>";
echo "<td></td><td  class='text-left'>" . (isset($prev_last_date) ? dateformat($prev_last_date) : '') . "</td>";
echo "<td colspan='2' class='text-right'>Previous Total:</td>";
echo "<td>" . formatVal($prev_sell_qty) . " $prev_unit</td>";
echo "<td>" . formatVal($prev_purchage_qty) . " $prev_unit</td>";
echo "<td colspan='3'></td>";
echo "<td>" . formatVal($prev_sell_amount) . "</td>";
echo "<td>" . formatVal($prev_purchage_amount) . "</td>";
echo "<td>Closing Stock: " . formatVal($prev_close_stock) . " $prev_unit</td>";
echo "<td></td>";
echo "</tr>";

// Ledger entries
foreach ($daily_stock as $stock) {
    if ($stock->ledger_type == 'Updated by Admin') {
        continue;
    }

    $unit = strtoupper(preg_replace('/[0-9]/', '', $stock->unit));
    $rowColor = getRowColor($stock->order_id, $orderColorMap, $colorIndex, $colorList);
    $rowStyle = ($stock->ledger_type == 'Invoice Deleted') ? "background: #000; color: #fff;" : "background: $rowColor;";

    // Update latest rates
    if ($stock->sell_rate > 0) {
        $latest_sell_rate = $stock->sell_rate;
    }
    if ($stock->purchage_rate > 0) {
        $latest_purchage_rate = $stock->purchage_rate;
    }

    // === Skip Wastage here (upar nahi dikhana) ===
    if ($stock->ledger_type == 'Wastage') {
        $total_wastage_qty += $stock->wastage;
        $total_wastage_amount += $stock->sell_amount;
        $wastageEntries[] = $stock;
        continue;
    }

    echo "<tr style='$rowStyle'>";
    echo "<td>" . $sr_no++ . "</td>";
    echo "<td>" . dateformat($stock->created_date) . "</td>";
    echo "<td>" . $stock->sellername . "</td>";
    echo "<td>" . $stock->customername . "</td>";

    // Sell
    if ($stock->sell_qty > 0) {
        echo "<td>" . formatVal($stock->sell_qty) . " $unit</td><td></td>";
        $total_sell_qty += $stock->sell_qty;
        $total_sell_amount += $stock->sell_amount;
        $live_close_stock -= $stock->sell_qty;
    }
    // Purchase or Restock
    else if ($stock->purchage_qty > 0 || $stock->ledger_type == 'Restock') {
        echo "<td></td><td>" . formatVal($stock->purchage_qty) . " $unit</td>";
        $total_purchage_qty += $stock->purchage_qty;
        $total_value += $stock->purchage_amount;
        $live_close_stock += $stock->purchage_qty;
    } else {
        echo "<td></td><td></td>";
    }

    echo "<td>" . formatVal($live_close_stock) . " $unit</td>";
    echo "<td>" . formatVal($stock->purchage_rate) . "</td>";
    echo "<td>" . formatVal($stock->sell_rate) . "</td>";
    echo "<td>" . formatVal($stock->purchage_amount) . "</td>";
    echo "<td>" . formatVal($stock->sell_amount) . "</td>";
    echo "<td></td>";
    $total_purchage_amount += $stock->purchage_amount;
    if ($stock->ledger_type == 'purchage') {
        // Fetch PO number (tid) from purchase table
        $po_number = '';
        if (!empty($stock->order_id)) {
            $CI =& get_instance();
            $CI->db->select('tid');
            $CI->db->from('geopos_purchase');
            $CI->db->where('id', $stock->order_id);
            $po_query = $CI->db->get();
            if ($po_query->num_rows() > 0) {
                $po_number = $po_query->row()->tid;
            }
        }
        
        $purchase_url = base_url() . 'purchase/view?id=' . $stock->order_id;
        echo '<td>';
        if (!empty($po_number)) {
            echo '<p class="pb-1"><a class="btn btn-pink btn-sm" href="' . $purchase_url . '" target="_blank">' . prefix(2) . $po_number . '</a></p>';
        }
        echo '<a class="btn btn-pink btn-sm" href="' . $purchase_url . '" target="_blank">PUR#' . $stock->order_id . '</a>';
        echo '</td>';
    } elseif ($stock->ledger_type == 'Sell') {
        echo '<td><a class="btn btn-pink btn-sm" href="' . base_url() . 'invoices/view?id=' . $stock->order_id . '" target="_blank">INV#' . $stock->order_id . '</a></td>';
    } else {
        echo "<td>" . $stock->ledger_type . "</td>";
    }

    echo "</tr>";
}

// Calculate Wastage and Final Balances
$wastage_sell_value = $total_wastage_qty * $latest_sell_rate;
$wastage_purchage_value = $total_wastage_qty * $latest_purchage_rate;
$total_balance_qty = $prev_close_stock + $live_close_stock;
$total_balance_value = $total_balance_qty * $latest_purchage_rate;

// Footer Summary
echo "<tr style='font-weight:bold; background:#f1f1f1'>";
echo "<td colspan='4' class='text-right'>Total:</td>";
echo "<td>" . formatVal($total_sell_qty) . " $unit</td>";
echo "<td>" . formatVal($total_purchage_qty) . " $unit</td>";
echo "<td colspan='3'></td>";
echo "<td>₹" . formatVal($total_purchage_amount) . "</td>";
echo "<td>₹" . formatVal($total_sell_amount) . "</td>";
echo "<td>PRF ₹" . formatVal($total_sell_amount - ($total_purchage_amount+$prev_purchage_amount)) . "</td>";
echo "<td></td>";
echo "</tr>";

// Previous Balance
echo "<tr style='font-weight:bold; background:#e0ffe0; color:#000;'>";
echo "<td colspan='4' class='text-right'>Previous Balance:</td>";
echo "<td colspan='2'>" . formatVal($prev_close_stock) . " $unit</td>";
echo "<td colspan='3'>Value ₹".formatVal($prev_purchage_amount)."</td>";
echo "<td colspan='2'></td>";
echo "<td colspan='2'></td>";
echo "</tr>";

// Total Balance (Qty + Value)
echo "<tr style='font-weight:bold; background:#284168; color:#fff;'>";
echo "<td colspan='4' class='text-right'>Total Balance:</td>";
echo "<td colspan='2'>" . formatVal($total_balance_qty) . " $unit </td>";
echo "<td colspan='3'>Stock Value: ₹" . formatVal($total_balance_value) . "</td>";
echo "<td colspan='2'></td>";
echo "<td colspan='2'></td>";
echo "</tr>";


if (!empty($wastageEntries)) {
    foreach ($wastageEntries as $w) {
        echo "<tr style='background:#ff4d4d; color:#fff; font-weight:bold;'>";
        echo "<td>*</td>";
        echo "<td>" . dateformat($w->created_date) . "</td>";
        echo "<td>" . $w->sellername . "</td>";
        echo "<td>" . $w->customername . "</td>";
        echo "<td colspan='2'>" . formatVal($w->wastage) . " $unit</td>";
       
        echo "<td>" . formatVal($latest_purchage_rate) . "</td>";
        echo "<td>" . formatVal($latest_sell_rate) . "</td>";
        echo "<td colspan='3'>Wastage Loss: ₹" . formatVal($w->wastage * $latest_purchage_rate) . "</td>";
        echo "<td colspan='2'></td>";
        echo "</tr>";
    }
}

// Total Wastage
echo "<tr style='font-weight:bold; background:#ffe0e0; color:#000;'>";
echo "<td colspan='4' class='text-right'>Total Wastage:</td>";
echo "<td colspan='2'>" . formatVal($total_wastage_qty) . " $unit</td>";
echo "<td colspan='3'>Wastage Value: ₹" . formatVal($wastage_purchage_value) . "</td>";

echo "<td ></td>";
echo "<td ></td>";
echo "<td colspan='2'></td>";
echo "</tr>";

// ----------------- NEW: Total Return Stock (accepted) -----------------
$total_return_qty = isset($total_return_qty) ? $total_return_qty : 0;
$total_return_value = isset($total_return_value) ? $total_return_value : 0;

echo "<tr style='font-weight:bold; background:#fff3a3; color:#000;'>"; // yellowish
echo "<td colspan='4' class='text-right'>Total Return Stock (Accepted):</td>";
echo "<td colspan='2'>" . formatVal($total_return_qty) . " $unit</td>";
echo "<td colspan='3'>Return Value: ₹" . formatVal($total_return_value) . "</td>";
echo "<td colspan='3'></td>";
echo "<td></td>";
echo "</tr>";

// Total Balance After Wastage (before returns)
echo "<tr style='font-weight:bold; background:green; color:#fff;'>";
echo "<td colspan='4' class='text-right'>Total Balance After Wastage:</td>";
echo "<td colspan='2'>" . formatVal($total_balance_qty - $total_wastage_qty) . " $unit</td>";
echo "<td colspan='3'></td>";
echo "<td ></td>";
echo "<td ></td>";
echo "<td ></td>";
echo "<td colspan='2'></td>";
echo "</tr>";

// Final Stock (after adding returns)
$final_qty = ($total_balance_qty - $total_wastage_qty) - $total_return_qty;
$final_value = $final_qty * $latest_purchage_rate; // or you can use ($total_balance_value - $wastage_purchage_value + $total_return_value)
$final_value_alt = ($total_balance_value - $wastage_purchage_value) + $total_return_value;

echo "<tr style='font-weight:bold; background:#003366; color:#fff;'>";
echo "<td colspan='4' class='text-right'>Final Stock (After Wastage & Returns):</td>";
echo "<td colspan='2'>" . formatVal($final_qty) . " $unit</td>";
echo "<td colspan='3'>Stock Value: ₹" . formatVal($final_value_alt) . "</td>";
echo "<td colspan='2'></td>";
echo "<td colspan='2'></td>";
echo "</tr>";


function formatVal($val) { return ($val == 0 || $val == null) ? '' : rtrim(rtrim(number_format((float)$val, 2, '.', ''), '0'), '.'); }
?>
