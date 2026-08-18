<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Statement</title>
    <style>
        body { color: #2B2000; }
        table { width: 100%; line-height: 16pt; text-align: right; border-collapse: collapse; }
        .mfill { background-color: #eee; }
        .descr { font-size: 10pt; color: #515151; }
        .invoice-box {
            width: 210mm; height: 297mm; margin: auto; padding: 4mm;
            border: 0; font-size: 16pt; line-height: 24pt; color: #000;
        }
        .invoice-box table { width: 100%; line-height: 17pt; text-align: left; }
        .plist tr td { line-height: 12pt; }
        .subtotal tr td { line-height: 10pt; }
        .sign, .sign1, .sign2, .sign3 { text-align: right; font-size: 10pt; }
        .sign { margin-right: 110pt; }
        .sign1 { margin-right: 90pt; }
        .sign2, .sign3 { margin-right: 115pt; }
        .terms { font-size: 9pt; line-height: 16pt; }
        .invoice-box table td { padding: 10pt 4pt 5pt 4pt; vertical-align: top; }
        .invoice-box table tr.top table td { padding-bottom: 20pt; }
        .invoice-box table tr.top table td.title { font-size: 45pt; line-height: 45pt; color: #555; }
        .invoice-box table tr.heading td { background: #515151; color: #FFF; padding: 6pt; }
        .invoice-box table tr.item td { border-bottom: 1px solid #fff; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(4) {
            border-top: 2px solid #fff; font-weight: bold;
        }
        .myco { width: 500pt; }
        .myco2 { width: 290pt; }
        .myw { width: 180pt; font-size: 14pt; line-height: 30pt; }
		
		/* Table head style - Blue background, white text */
.plist thead tr,
.plist tr.heading {
    background-color: #007BFF !important;
    color: #fff !important;
}

/* Borders for all table cells */
.plist td, 
.plist th {
    border: 1px solid #ccc;
}

/* Alternate row colors */
.plist tr:nth-child(even) {
    background-color: #f9f9f9;
}
.plist tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Keep text alignment consistent */
.plist td, 
.plist th {
    padding: 6px;
    vertical-align: middle;
}

/* Ensure PDF prints colors */
@media print {
    .plist tr.heading {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .plist tr:nth-child(even) {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .plist tr:nth-child(odd) {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
/* Table container style */
.plist {
    border-collapse: collapse;
    width: 100%;
}

/* Header row style */
.plist tr.heading {
    background-color: #003366 !important; /* Dark blue */
    color: #ffffff !important;
}

/* Header cells */
.plist tr.heading td {
    font-weight: bold;
    padding: 8px;
    border: 1px solid #ccc;
}

/* Table cells border and padding */
.plist td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: left;
}

/* Alternate row colors */
.plist tr:nth-child(even) {
    background-color: #e6f0ff; /* Light blue */
}
.plist tr:nth-child(odd) {
    background-color: #ffffff; /* White */
}

/* Ensure colors are preserved in PDF print */
@media print {
    .plist tr.heading,
    .plist tr:nth-child(even),
    .plist tr:nth-child(odd) {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

    </style>
</head>

<body dir="<?= 'LTR' ?>">

<div class="invoice-box">
    <table>
        <tr>
            <td class="myco">
                <img src="<?= base_url('userfiles/company/' . $this->config->item('logo')) ?>" style="max-width:260px;">
            </td>
            <td></td>
            <td class="myw">
                <?= $this->lang->line('Products') . ' ' . $this->lang->line('Report'); ?>
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
        <tr class="heading">
            <td><?= $this->lang->line('Our Info') ?>:</td>
            <td>
                <div class="summary">
                    Report Type:
                    <?php
                    if ($r_type == 1) echo 'Sales';
                    elseif ($r_type == 2) echo 'Purchase';
                    elseif ($r_type == 3) echo 'Stock Transfer';

                    echo ' (';
                    if ($reportwise == 1) echo 'Date Wise';
                    elseif ($reportwise == 2) echo 'Product Wise';
                    elseif ($reportwise == 3) echo 'Customer Wise';
                    elseif ($reportwise == 4) echo 'Supplier Wise';
                    echo ')';
                    ?>
                </div>
            </td>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>
                <h3><?= $this->config->item('ctitle'); ?></h3>
                <?= $this->config->item('address') . '<br>' .
                    $this->config->item('city') . ',' . $this->config->item('country') . '<br>Phone: ' .
                    $this->config->item('phone') . '<br> Email: ' .
                    $this->config->item('email'); ?>
            </td>
            <td>
                <?= $this->lang->line('Warehouse') . ' : ' . $product['title']; ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                if ($reportwise == 3) {
                    echo 'Customer: ' . $party_name;
                } elseif ($reportwise == 4) {
                    echo 'Supplier: ' . $party_name;
                }
                ?>
            </td>
            <td>
                Date Range: <?= dateformat($this->input->post('s_date')) . ' To ' . dateformat($this->input->post('e_date')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <hr>

    <table class="plist" cellpadding="0" cellspacing="0">
        <?php
		$unit ="KG";
        if ($r_type < 3) {
            if ($reportwise == 1) {
                echo '<tr><td><strong>Date</strong></td><td><strong>Product Name</strong></td><td><strong>Qty</strong></td><td><strong>Total</strong></td></tr>';
                foreach ($report as $row) {
					$unit = $row['unit'];
					
                    echo '<tr>
                        <td>' . dateformat($row['date']) . '</td>
                        <td>' . $row['product_name'] . '</td>
                        <td>' . amountFormat_general($row['qty']) . ' ' . preg_replace('/[^a-zA-Z]/', '', $unit) . '</td>
                        <td>' . amountExchange($row['sub_total'], 0, $this->aauth->get_user()->loc) . '</td>
                    </tr>';
                }
            } elseif ($reportwise >= 2 && $reportwise <= 4) {
				
                $labels = ['2' => 'Product Name', '3' => 'Customer Name', '4' => 'Supplier Name'];
                echo '<tr><td><strong>Sr No</strong></td><td><strong>' . $labels[$reportwise] . '</strong></td><td><strong>Qty</strong></td><td><strong>Total</strong></td></tr>';
                $i = 1;
                foreach ($report as $row) {
					$unit = $row['unit'];
                    echo '<tr>
                        <td>' . $i++ . '.</td>
                        <td>' . $row['product_name'] . '</td>
                        <td>' . amountFormat_general($row['qty']) . ' ' . preg_replace('/[^a-zA-Z]/', '', $unit) . '</td>
                        <td>' . amountExchange($row['sub_total'], 0, $this->aauth->get_user()->loc) . '</td>
                    </tr>';
                }
            }
        } elseif ($r_type == 3) {
            echo '<tr><td><strong>Date</strong></td><td><strong>Product Name</strong></td><td><strong>Qty</strong></td><td><strong>Total</strong></td></tr>';
            $fill = false; $price = 0; $in = 1; $balance = 0;
            foreach ($report as $row) {

                $balance += $row['qty'];
                $flag = $fill ? ' mfill' : '';
                $price += $row['sub_total'];
                echo '
                    <tr class="item' . $flag . '"><td colspan="3">' . $in . '. ' . $row['product_name'] . '</td></tr>
                    <tr class="item' . $flag . '">
                        <td>' . $row['invoicedate'] . '</td>
                        <td>' . amountFormat_general($row['qty']) . '</td>
                        <td>' . $row['note'] . '</td>
                        <td>' . $balance . '</td>
                    </tr>';
                $fill = !$fill;
                $in++;
            }
        } elseif ($r_type == 4) {
		// Function: normalize unit
function normalize_unit($unit) {
    $unit = strtolower(trim($unit));
    if (in_array($unit, ['kg', 'kgs'])) {
        return 'KG';
    } elseif (in_array($unit, ['pc', 'pcs', 'piece', 'pieces'])) {
        return 'PC';
    } elseif (in_array($unit, ['ltr', 'litre', 'liter', 'ltrs'])) {
        return 'LTR';
    }
    return strtoupper($unit); // default
}

if ($reportwise == 1) { // Date Wise
    echo '<tr>
        <td><strong>Sr No</strong></td>
        <td><strong>Date</strong></td>
        <td><strong>Product Name</strong></td>
        <td><strong>Unit</strong></td>
        <td><strong>Opening Stock</strong></td>
        <td><strong>Stock In</strong></td>
        <td><strong>Stock Out</strong></td>
        <td><strong>Waste</strong></td>
        <td><strong>Closing Stock</strong></td>
    </tr>';

    $sr = 1;
    $totals = [
        'stock_in' => [],
        'stock_out' => [],
        'waste' => []
    ];

    foreach ($report as $row) {
        $unit = normalize_unit($row['unit'] ?? '');

        // Blank if 0
        $opening_stock = floatval($row['opening_stock']) != 0.00 ? number_format($row['opening_stock'], 2) : '';
        $stock_in      = floatval($row['stock_in']) != 0.00 ? number_format($row['stock_in'], 2) : '';
        $stock_out     = floatval($row['stock_out']) != 0.00 ? number_format($row['stock_out'], 2) : '';
        $waste         = floatval($row['waste']) != 0.00 ? number_format($row['waste'], 2) : '';
        $closing_stock = floatval($row['closing_stock']) != 0.00 ? number_format($row['closing_stock'], 2) : '';

        // Totals unit wise
        if ($stock_in !== '')  $totals['stock_in'][$unit]  = ($totals['stock_in'][$unit] ?? 0) + floatval($row['stock_in']);
        if ($stock_out !== '') $totals['stock_out'][$unit] = ($totals['stock_out'][$unit] ?? 0) + floatval($row['stock_out']);
        if ($waste !== '')     $totals['waste'][$unit]     = ($totals['waste'][$unit] ?? 0) + floatval($row['waste']);

        echo '<tr>
            <td>' . $sr++ . '.</td>
            <td>' . date('d-m-Y', strtotime($row['date'])) . '</td>
            <td>' . $row['product_name'] . '</td>
            <td>' . $unit . '</td>
            <td>' . $opening_stock . '</td>
            <td>' . $stock_in . '</td>
            <td>' . $stock_out . '</td>
            <td>' . $waste . '</td>
            <td>' . $closing_stock . '</td>
        </tr>';
    }

    // Total Row
    echo '<tr style="font-weight:bold; background:#f0f0f0;">
        <td colspan="5" align="right">Total</td>
        <td>';
        foreach ($totals['stock_in'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td>';
        foreach ($totals['stock_out'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td>';
        foreach ($totals['waste'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td></td></tr>';

} else { // Product Wise
    echo '<tr>
        <td><strong>Sr No</strong></td>
        <td><strong>Product Name</strong></td>
        <td><strong>Unit</strong></td>
        <td><strong>Opening Stock</strong></td>
        <td><strong>Stock In</strong></td>
        <td><strong>Stock Out</strong></td>
        <td><strong>Waste</strong></td>
        <td><strong>Closing Stock</strong></td>
    </tr>';

    $i = 1;
    $totals = [
        'stock_in' => [],
        'stock_out' => [],
        'waste' => []
    ];

    foreach ($report as $row) {
        if (
            floatval($row['opening_stock']) == 0.00 &&
            floatval($row['stock_in']) == 0.00 &&
            floatval($row['stock_out']) == 0.00 &&
            floatval($row['waste']) == 0.00 &&
            floatval($row['closing_stock']) == 0.00
        ) {
            continue;
        }

        $unit = normalize_unit($row['unit'] ?? '');

        // Blank if 0
        $opening_stock = floatval($row['opening_stock']) != 0.00 ? $row['opening_stock'] : '';
        $stock_in      = floatval($row['stock_in']) != 0.00 ? $row['stock_in'] : '';
        $stock_out     = floatval($row['stock_out']) != 0.00 ? $row['stock_out'] : '';
        $waste         = floatval($row['waste']) != 0.00 ? $row['waste'] : '';
        $closing_stock = floatval($row['closing_stock']) != 0.00 ? $row['closing_stock'] : '';

        // Totals unit wise
        if ($stock_in !== '')  $totals['stock_in'][$unit]  = ($totals['stock_in'][$unit] ?? 0) + floatval($row['stock_in']);
        if ($stock_out !== '') $totals['stock_out'][$unit] = ($totals['stock_out'][$unit] ?? 0) + floatval($row['stock_out']);
        if ($waste !== '')     $totals['waste'][$unit]     = ($totals['waste'][$unit] ?? 0) + floatval($row['waste']);

        echo '<tr>
            <td>' . $i++ . '.</td>
            <td>' . $row['product_name'] . '</td>
            <td>' . $unit . '</td>
            <td>' . $opening_stock . '</td>
            <td>' . $stock_in . '</td>
            <td>' . $stock_out . '</td>
            <td>' . $waste . '</td>
            <td>' . $closing_stock . '</td>
        </tr>';
    }

    // Total Row
    echo '<tr style="font-weight:bold; background:#f0f0f0;">
        <td colspan="4" align="right">Total</td>
        <td>';
        foreach ($totals['stock_in'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td>';
        foreach ($totals['stock_out'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td>';
        foreach ($totals['waste'] as $unit => $val) {
            echo number_format($val, 2) . ' ' . $unit . '<br>';
        }
    echo '</td><td></td></tr>';
}

		}



        ?>
    </table>

    <table class="subtotal">
        <tbody>
        <tr>
            <td class="myco2" rowspan="3"><br><br><br></td>
            <td><strong><?= $this->lang->line('Summary') ?>:</strong></td>
            <td></td>
        </tr>
        <tr>
            <td><?= $this->lang->line('Total') . ' ' . $this->lang->line('Products') ?>:</td>
            <td>
                <?php
                $unit_summary_string = '';
                foreach ($unit_summary as $unit => $qty) {
                    $unit_summary_string .= amountFormat_general($qty) . ' ' . $unit . ', ';
                }
                echo rtrim($unit_summary_string, ', ');
                ?>
            </td>
        </tr>
        <tr>
            <td><?= $this->lang->line('Total') ?>:</td>
            <td><?= amountExchange($total_amount, 0, $this->aauth->get_user()->loc); ?></td>
        </tr>
        </tbody>
    </table>

    <br>

    <div class="sign">Authorized person</div>
    <div class="sign1"></div>
    <div class="sign2"></div>
    <div class="sign3"></div>

    <br>

    <div class="terms">
        <hr>
    </div>
</div>

</body>
</html>
