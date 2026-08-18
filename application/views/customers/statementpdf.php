<!doctype html>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Print Statement</title>

    <style>
        body {
            color: #2B2000;
            font-family: 'Helvetica';
        }

        .invoice-box {
            width: 210mm;
            height: 297mm;
            margin: auto;
            padding: 4mm;
            border: 0;
            font-size: 12pt;
            line-height: 14pt;
            color: #000;
        }

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

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
		
		  .top_logo {
            max-height: 180px;
            max-width: 250px;
        <?php if(LTR=='rtl') echo 'margin-left: 200px;' ?>
        }
    </style>
</head>

<body <?php if (LTR == 'rtl') echo 'dir="rtl"'; ?>>
<div class="invoice-box">
    <table>
  
		
		   <tr>
	
        <td class="myco">
            <img src="<?php $loc = location($invoice['loc']);  echo FCPATH . 'userfiles/company/' . $loc['logo'] ?>" class="top_logo" height="70px">
        </td>
      
		
		<td class="mycoss" colspan="2">
<h2>Thok ki Dukaan Online Store</h2>
<p> C 29 NIRANJAN PUR MANDI
Dehradun, Uttarakhand
India - 2481002</p>
        </td>
		<td class="myw">
		<h2><?php echo $this->lang->line('Account Statement'); ?></h2>
        </td>

    </tr>
	
    </table>
    <br>
    <table>
        <thead>
        <tr class="heading">
            <td> <?php echo $this->lang->line('Our Info') ?>:</td>

            <td><?php echo $this->lang->line('Customer') ?>:</td>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td><strong><?php $loc = location($invoice['loc']);
                    echo $loc['cname']; ?></strong><br>
                <?php echo
                    $loc['address'] . '<br>' . $loc['city'] . ', ' . $loc['region'] . '<br>' . $loc['country'] . ' -  ' . $loc['postbox'] . '<br>' . $this->lang->line('Phone') . ': ' . $loc['phone'] . '<br> ' . $this->lang->line('Email') . ': ' . $loc['email'];
                if ($loc['taxid']) echo '<br>' . $this->lang->line('TaxID') . ': ' . $loc['taxid'];
                ?>
            </td>

            <td>
                <?php echo $customer['username'] . '</strong><br>' . $customer['address'] . '<br>' . $customer['city'] . '<br>Phone: ' . $customer['mobile'] . '<br>Email: ' . $customer['email']; ?>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <table  cellpadding="0" cellspacing="0">

        <tr class="heading">
            <td><strong><?php echo $this->lang->line('Date') ?></strong></td>
            <td><strong><?php echo $this->lang->line('Description') ?></strong></td>

            <td><strong><?php echo $this->lang->line('Debit') ?></strong></td>
            <td><strong><?php echo $this->lang->line('Credit') ?></strong></td>

            <td><strong><?php echo $this->lang->line('Balance') ?></strong></td>


        </tr>

        <?php
        $fill = false;
        foreach ($list as $row) {
            if ($fill == true) {
                $flag = ' mfill';
            } else {
                $flag = '';
            }
            $balance += $row['total'] - $row['pamnt'];
			if($row['pamnt']==0){
				$credit = "";
				
			}else{
				
				$credit = amountExchange($row['pamnt'], 0, $this->aauth->get_user()->loc);
				
			}
            echo '<tr class="item' . $flag . '"><td>' . dateformat($row['date_added']) . '</td><td> Bill Generate againest invoice no #' . $row['id'] . '</td><td>' . amountExchange($row['total'], 0, $this->aauth->get_user()->loc) . '</td><td>' . $credit  . '</td><td>' . amountExchange($balance, 0, $this->aauth->get_user()->loc) . '</td></tr>';
            $fill = !$fill;
        }
        ?>
    </table>


    <table class="subtotal">
        <thead>
        <tbody>
        <tr>
            <td class="myco2" rowspan="2">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;

            </td>
            <td><strong><?php echo $this->lang->line('Summary') ?>:</strong></td>
            <td></td>


        </tr>
        <tr>


            <td ><?php echo $this->lang->line('Balance') ?>:</td>

            <td ><b style="color:red;"><?php echo  amountExchange($balance, 0, $this->aauth->get_user()->loc); ?></b></td>
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
