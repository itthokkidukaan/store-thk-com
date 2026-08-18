<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title> Recieve Challan #<?php echo $invoice['tid'] ?></title>
    <style>
        


        table {
            width: 100%;
            line-height: 16pt;
            text-align: left;
            border-collapse: collapse;
        }

        .plist tr td {
            line-height: 12pt;
        }

        .subtotal {
            page-break-inside: avoid;
        }

        .subtotal tr td {
            line-height: 10pt;
            padding: 6pt;
        }

        .subtotal tr td {
            border: 1px solid #ddd;
        }

        .sign {
            text-align: right;
            font-size: 10pt;
            margin-right: 110pt;
        }

        .sign1 {
            text-align: right;
            font-size: 10pt;
            margin-right: 90pt;
        }

        .sign2 {
            text-align: right;
            font-size: 10pt;
            margin-right: 115pt;
        }

        .sign3 {
            text-align: right;
            font-size: 10pt;
            margin-right: 115pt;
        }

        .terms {
            font-size: 9pt;
            line-height: 16pt;
            margin-right: 20pt;
        }

        .invoice-box table td {
            padding: 10pt 4pt 8pt 4pt;
            vertical-align: top;
        }

        .invoice-box table.top_sum td {
            padding: 0;
            font-size: 12pt;
        }

        .party tr td:nth-child(3) {
            text-align: center;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20pt;
        }

        table tr.top table td.title {
            font-size: 45pt;
            line-height: 45pt;
            color: #555;
        }

        table tr.information table td {
            padding-bottom: 20pt;
        }

        table tr.heading td {
            background: #144875;
            color: #FFF;
            padding: 6pt;
        }

        table tr.details td {
            padding-bottom: 20pt;
        }

        .invoice-box table tr.item td {
            border: 1px solid #ddd;
        }

        table tr.b_class td {
            border-bottom: 1px solid #ddd;
        }

        table tr.b_class.last td {
            border-bottom: none;
        }

        table tr.total td:nth-child(4) {
            border-top: 2px solid #fff;
            font-weight: bold;
        }

        .myco {
            width: 100pt;
        }
 .mycoss {
            width: 400pt;
        }

        .myco2 {
            width: 200pt;
        }

        .myw {
            width: 300pt;
            font-size: 14pt;
            line-height: 14pt;
			background-color: #B9CFF2;
			text-align:center;
        }

        .mfill {
            background-color: #B9CFF2;
        }

        .descr {
            font-size: 10pt;
            color: #515151;
        }

        .tax {
            font-size: 10px;
            color: #515151;
        }

        .t_center {
            text-align: right;
        }

        .party {
            border: #ccc 1px solid;

        }

        .top_logo {
            max-height: 180px;
            max-width: 250px;
        <?php if(LTR=='rtl') echo 'margin-left: 200px;' ?>
        }
		
		
	
      
        .invoice-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            color: red;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .header img {
            width: 50mm !important;
            float: left;
            margin-top: -10px;
        }
        .header .contact-info {
            font-size: 12px;
            margin-top: 5px;
        }
        .info-table, .customer-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table th, .info-table td {
            padding: 5px;
            border: 1px solid #000;
            font-size: 14px;
        }
        .info-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .customer-details td {
            border: none;
            font-size: 14px;
            padding: 4px 8px;
        }
        .customer-details td:first-child {
            width: 70%;
        }

    </style>
</head>
<body dir="<?= LTR ?>">


<div class="invoice-box">

    <table class="party">
        <thead>
        <tr class="heading">
            <td width="50%"><?= $general['person'] ?>:</td>
            <td>Challan Detail:</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <?php echo '<strong>' . $invoice['username'] . '</strong><br>';
                if ($invoice['company']) echo $invoice['company'] . '<br>';
                echo $invoice['address'] . '<br>' . $invoice['city'];
                if ($invoice['country']) echo '<br>' . $invoice['country'];
                if ($invoice['postbox']) echo ' - ' . $invoice['postbox'];
                if ($invoice['mobile']) echo '<br>' . $this->lang->line('Phone') . ': ' . $invoice['mobile'];
                if ($invoice['email']) echo '<br> ' . $this->lang->line('Email') . ': ' . $invoice['email'];
                if ($invoice['taxid']) echo '<br>' . $this->lang->line('TaxID') . ': ' . $invoice['taxid'];
                ?>
            </td>
            <td>
              <table class="top_sum">
                <tr><td>Challan No: <?=  'CN' . $invoice['tid'] ?></td></tr>
                <tr><td><?= 'Challan ' . $this->lang->line('Date') ?>: <?php echo dateformat($invoice['invoicedate']) ?></td></tr>
                <tr><td>Vendor Code: 20022323</td></tr> 
                <?php if ($invoice['refer']) { ?>
                    <tr><td>P.O. No. <?php echo $invoice['refer'] ?></td></tr>
                <?php } ?>
              </table>
            </td>
        </tr>
        </tbody>
    </table>

   <br>

<!-- ================= SUMMARY TABLE ================== -->
<h3 style="background:#144875;color:#fff;padding:8px;text-align:center;">
    Challan Summary
</h3>

<?php
$total_qty = 0;
$total_short = 0;
$total_received = 0;

foreach ($products as $row) {
    $qty = floatval($row['qty']);
    $received_qty = ($row['received_qty'] == '' || $row['received_qty'] == null) ? 0 : floatval($row['received_qty']);

    $short_qty = $qty - $received_qty;
    if ($short_qty < 0) $short_qty = 0;

    $total_qty += $qty;
    $total_short += $short_qty;
    $total_received += $received_qty;
}


$delivered_per = ($total_qty > 0) ? round(($total_received / $total_qty) * 100, 2) : 0;

?>

<table style="width:100%;border:1px solid #000;border-collapse:collapse;margin-bottom:15px;">
    <tr style="background:#f2f2f2;text-align:center;font-weight:bold;">
        <td style="border:1px solid #000;padding:6px;">Total Article</td>
        <td style="border:1px solid #000;padding:6px;">Total Short</td>
        <td style="border:1px solid #000;padding:6px;">Total Delivered</td>
    </tr>
    <tr style="text-align:center;font-size:14px;">
        <td style="border:1px solid #000;padding:6px;"><?php echo $total_qty; ?></td>
        <td style="border:1px solid #000;padding:6px;"><?php echo $total_short; ?></td>
        <td style="border:1px solid #000;padding:6px;"><?php echo $delivered_per; ?>%</td>
    </tr>
</table>


<!-- ================= SHORT PRODUCT LIST ================== -->
<h3 style="background:#144875;color:#fff;padding:8px;text-align:center;">
    Short Product List
</h3>

<table class="plist" cellpadding="0" cellspacing="0">
    <tr class="heading">
        <td>#</td>
        <td>Product</td>
        <td>Article</td>
        <td>UOM</td>
        <td>Qty</td>
        <td>Short Qty</td>
        <td>Short %</td>
    </tr>

    <?php
    $n = 1;
    foreach ($products as $row) {
        $qty = $row['qty'];
        $received_qty = ($row['received_qty'] == '' || $row['received_qty'] == 0) ? $qty : $row['received_qty'];
        $short_qty = $qty - $received_qty;
        if ($short_qty <= 0) continue; // only short products

        $short_per = ($qty > 0) ? round(($short_qty / $qty) * 100, 2) : 0;

        echo '<tr>
                <td>'.$n.'</td>
                <td>'.$row['product'].'</td>
                <td>'.$row['article'].'</td>
                <td>'.$row['unit'].'</td>
                <td>'.sprintf("%g",$qty).'</td>
                <td>'.$short_qty.'</td>
                <td>'.$short_per.'%</td>
              </tr>';
        $n++;
    }
    ?>
</table>





     
    </div>





</body>
</html>