<!DOCTYPE html>
<html>
<head>
    <title>Product Barcode Labels - 50mm x 50mm (2-UP)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            box-sizing: border-box;
        }

        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10mm 6mm;
        }

        .label {
            width: 50mm;
            height: 50mm;
            border: 1px solid #ccc;
            box-sizing: border-box;
            padding: 5px;
            position: relative;
            page-break-inside: avoid;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .veg-icon {
            width: 12px;
            height: 12px;
            border: 1px solid black;
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .veg-icon::after {
            content: '';
            background: green;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: block;
        }

        .barcode {
            text-align: center;
            margin: 3px 0;
        }

        .barcode svg {
            width: 100%;
            height: 24mm;
        }

        .barcode-number {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .section {
            margin-bottom: 2px;
        }

        .address {
            font-size: 7.5px;
            line-height: 1.1;
        }

        .fssai {
            font-weight: bold;
            margin-top: 1px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="label-grid">
            <?php
            $products = [
                [
                    'product_name' => 'Broccoli',
                    'barcode' => '590000100',
                    'packer' => 'THOK KI DUKAAN',
                    'address' => 'Shop No.C29, Niranjanpur,<br>New Sabzi Mandi,<br>Dehradun, Uttarakhand<br>248001',
                    'fssai_no' => '22624030001319'
                ]
            ];

            $uom = $_REQUEST['UOM'] ?? '1kg';
            $counter = 0;
            foreach ($products as $lab) {
                for ($i = 0; $i < 20; $i++) {
                    $id = 'barcode_' . $counter;
                    ?>
                    <div class="label">
                        <div class="product-name"><?= htmlspecialchars($lab['product_name']) ?></div>
                        <div class="veg-icon"></div>

                        <div class="barcode">
                            <svg id="<?= $id ?>"></svg>
                        </div>

                        <div class="barcode-number"><?= htmlspecialchars($lab['barcode']) ?></div>

                        <div class="section">Net Wt: <?= htmlspecialchars($uom) ?></div>
                        <div class="section">Packed by: <?= htmlspecialchars($lab['packer']) ?></div>

                        <div class="address">
                            <?= $lab['address'] ?>
                        </div>

                        <div class="fssai">FSSAI: <?= htmlspecialchars($lab['fssai_no']) ?></div>
                    </div>
                    <?php
                    $counter++;
                    if ($counter >= 20) break;
                }
                if ($counter >= 20) break;
            }
            ?>
        </div>
    </div>

    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        <?php
        for ($j = 0; $j < $counter; $j++) {
            $barcode = $products[0]['barcode'];
            echo "JsBarcode('#barcode_$j', '$barcode', {format: 'CODE128', height: 50, displayValue: false});\n";
        }
        ?>
    </script>
</body>
</html>
