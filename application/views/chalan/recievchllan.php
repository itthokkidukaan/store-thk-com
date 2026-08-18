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



	
    <form method="post" action="<?= base_url('chalan/updatechallan/'.$invoice['iid']) ?>">

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

   <table class="plist" cellpadding="0" cellspacing="0">
    <tr class="heading">
        <td>#</td>
        <td>Product</td> 
        <td>Article</td>
        <td>UOM</td>
        <td><?php echo $this->lang->line('Qty') ?></td>
        <td>Dispatch Qty</td>
        <td>Dispatch %</td>
        <td>Received Qty</td>
        <td>Received %</td>
        <td>Quality Report</td>
    </tr>

    <?php
    $n = 1;
    foreach ($products as $row) {

        // qty value
        $qty = $row['qty'];

        // agar dispatch aur received qty empty hai to default qty set karo
        $dispatch_qty = ($row['dispatch_qty'] == '' || $row['dispatch_qty'] == 0) ? $qty : $row['dispatch_qty'];
        $received_qty = ($row['received_qty'] == '' || $row['received_qty'] == 0) ? $qty : $row['received_qty'];

        // percentage calculate
        $dispatch_per = ($qty > 0) ? round(($dispatch_qty / $qty) * 100, 2) : 0;
        $received_per = ($qty > 0) ? round(($received_qty / $qty) * 100, 2) : 0;

        echo '<tr>
                <td>'.$n.'</td>
                <td>'.$row['product'].'</td>
                <td>'.$row['article'].'</td>
                <td>'.$row['unit'].'</td>
                <td>
                   <input type="hidden" id="qty-'.$row['id'].'" value="'.$qty.'">
                   '.sprintf("%g",$qty).'
                </td>
                <td>
                    <input type="number" step="any" 
                           name="dispatchqty['.$row['id'].']" 
                           id="dispatchqty-'.$row['id'].'" 
                           onkeyup="calculatePercentage('.$row['id'].')" 
                           class="form-control" 
                           value="'.$dispatch_qty.'">
                </td>
                <td>
                    <input type="text" 
                           name="dispatchper['.$row['id'].']" 
                           id="dispatchper-'.$row['id'].'" 
                           readonly class="form-control" 
                           value="'.$dispatch_per.'%">
                </td>
                <td>
                    <input type="number" step="any" 
                           name="receivedqty['.$row['id'].']" 
                           id="receivedqty-'.$row['id'].'" 
                           onkeyup="calculatePercentage('.$row['id'].')" 
                           class="form-control" 
                           value="'.$received_qty.'">
                </td>
                <td>
                    <input type="text" 
                           name="receivedper['.$row['id'].']" 
                           id="receivedper-'.$row['id'].'" 
                           readonly class="form-control" 
                           value="'.$received_per.'%">
                </td>
                <td>
                    <input type="text" 
                           name="qualityreport['.$row['id'].']" 
                           class="form-control" 
                           value="'.$row['qualityreport'].'">
                </td>
             </tr>';
        $n++;
    }
    ?>

    <tr style="background:#f2f2f2;font-weight:bold;">
        <td colspan="6" style="text-align:right">Total Dispatch %</td>
        <td id="totalDispatch">0%</td>
        <td style="text-align:right">Total Received %</td>
        <td id="totalReceived">0%</td>
        <td></td>
    </tr>
</table>

    <br>
    <button type="submit" class="btn btn-large btn-success mb-1">Submit</button>
    </form>

     
    </div>

<script>
/* function calculatePercentage(id) {
    var qty = parseFloat(document.getElementById("qty-" + id).value) || 0;
    var dispatchQty = parseFloat(document.getElementById("dispatchqty-" + id).value) || 0;
    var receivedQty = parseFloat(document.getElementById("receivedqty-" + id).value) || 0;

    var dispatchPer = qty > 0 ? ((dispatchQty / qty) * 100).toFixed(2) : 0;
    var receivedPer = qty > 0 ? ((receivedQty / qty) * 100).toFixed(2) : 0;

    document.getElementById("dispatchper-" + id).value = dispatchPer + "%";
    document.getElementById("receivedper-" + id).value = receivedPer + "%";

    calculateTotal();
}

function calculateTotal() {
    var dispatchInputs = document.querySelectorAll("[id^='dispatchper-']");
    var receivedInputs = document.querySelectorAll("[id^='receivedper-']");

    var totalDispatch = 0, totalReceived = 0, count = 0;

    dispatchInputs.forEach(function(el) {
        totalDispatch += parseFloat(el.value) || 0;
        count++;
    });
    receivedInputs.forEach(function(el) {
        totalReceived += parseFloat(el.value) || 0;
    });

    if(count > 0) {
        document.getElementById("totalDispatch").innerText = (totalDispatch / count).toFixed(2) + "%";
        document.getElementById("totalReceived").innerText = (totalReceived / count).toFixed(2) + "%";
    }
}

// page load hone ke baad bhi total calculate karo
window.onload = calculateTotal; */
</script>

<script>
function calculatePercentage(id) {
    var qty = parseFloat(document.getElementById("qty-" + id).value) || 0;
    var dispatchQty = parseFloat(document.getElementById("dispatchqty-" + id).value) || 0;
    var receivedQty = parseFloat(document.getElementById("receivedqty-" + id).value) || 0;

    // dispatch % qty ke base par
    var dispatchPer = qty > 0 ? ((dispatchQty / qty) * 100).toFixed(2) : 0;

    // received % dispatch ke base par
    var receivedPer = dispatchQty > 0 ? ((receivedQty / dispatchQty) * 100).toFixed(2) : 0;

    document.getElementById("dispatchper-" + id).value = dispatchPer + "%";
    document.getElementById("receivedper-" + id).value = receivedPer + "%";

    calculateTotal();
}

function calculateTotal() {
    var rows = document.querySelectorAll("[id^='qty-']");
    var totalQty = 0, totalDispatchQty = 0, totalReceivedQty = 0;

    rows.forEach(function(el) {
        var id = el.id.split("-")[1];

        var qty = parseFloat(document.getElementById("qty-" + id).value) || 0;
        var dispatchQty = parseFloat(document.getElementById("dispatchqty-" + id).value) || 0;
        var receivedQty = parseFloat(document.getElementById("receivedqty-" + id).value) || 0;

        totalQty += qty;
        totalDispatchQty += dispatchQty;
        totalReceivedQty += receivedQty;
    });

    // total dispatch % = (dispatch total / qty total) * 100
    var totalDispatchPer = totalQty > 0 ? ((totalDispatchQty / totalQty) * 100).toFixed(2) : 0;

    // total received % = (received total / dispatch total) * 100
    var totalReceivedPer = totalDispatchQty > 0 ? ((totalReceivedQty / totalDispatchQty) * 100).toFixed(2) : 0;

    document.getElementById("totalDispatch").innerText = totalDispatchPer + "%";
    document.getElementById("totalReceived").innerText = totalReceivedPer + "%";
}

// page load hone ke baad bhi total calculate karo
window.onload = calculateTotal;
</script>


</body>
</html>