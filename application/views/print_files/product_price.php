<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title> Today Price</title>
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


    <table class="plist" cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td style="width: 1rem;">
                #
            </td>
            <td>
                Category
            </td> 

			<td>
                Product
            </td>

            <td>
               Today Price
            </td>

	
           
        
        </tr>
        <?php
        $fill = true;
        $sub_t = 0;
        $sub_t_col = 3;
        $n = 1;
	$total_qty = []; 

        foreach ($products as $row) {
            $cols = 4;

            $variants = $this->Products_model->get_all_active_variants($row['id']);

            if (empty($variants)) {
                echo '<tr class="item' . $flag . '">  <td>' . $n . '</td>
                                <td>' . $row['catname']  . '</td>
                                <td>' . $row['name']  . '</td>
                                <td>-</td> ';
                $fill = !$fill;
                $n++;
                continue;
            }

            foreach ($variants as $variant) {
                // Full variant label, e.g. "500 gm" / "1 Kg"
                $variant_name = $this->Products_model->get_variant_name($variant['attribute_value_ids']);

                // Same fallback used on the product edit page: use special_price when set, else price
                $price = (!empty($variant['special_price']) && floatval($variant['special_price']) > 0)
                    ? $variant['special_price']
                    : $variant['price'];

                echo '<tr class="item' . $flag . '">  <td>' . $n . '</td>
                                <td>' . $row['catname']  . '</td>
                                <td>' . $row['name']  . '</td>
                                <td>' . number_format((float)$price, 2) . ' Rs For ' . $variant_name . '</td> ';

                $fill = !$fill;
                $n++;
            }
        }

     
		


        ?>


    </table>

   
</div>
</div>
</body>
</html>