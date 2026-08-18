<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="<?= LTR ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <?php /* if (@$title) {
        echo "<title>$title</title >";
    } else {
        echo "<title>Geo POS</title >";
    } */
    ?>
	
	<title>Supplier Qotation Form</title >
    <link rel="apple-touch-icon" href="<?= assets_url() ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= assets_url() ?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i"
          rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= LTR ?>/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/extensions/unslider.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/weather-icons/climacons.min.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/fonts/meteocons/style.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/charts/morris.css">
	
	<link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/dropzone.css">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/star-rating.min.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/custom/custom.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/tables/datatable/datatables.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= LTR ?>/app.css">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/<?= LTR ?>/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css"
          href="<?= assets_url() ?>app-assets/<?= LTR ?>/core/colors/palette-gradient.css">
    <link rel="stylesheet" href="<?php echo assets_url('assets/custom/datepicker.min.css') . APPVER ?>">
    <link rel="stylesheet" href="<?php echo assets_url('assets/custom/summernote-bs4.css') . APPVER; ?>">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/select2-bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/iziToast.min.css">

          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/css/style.css<?= APPVER ?>">
    <?php if(LTR=='rtl') echo '<link rel="stylesheet" type="text/css" href="'.assets_url().'assets/css/style-rtl.css'.APPVER.'">'; ?>
    <!-- END Custom CSS-->
    <script src="<?= assets_url() ?>app-assets/vendors/js/vendors.min.js"></script>
    <script type="text/javascript" src="<?= assets_url() ?>app-assets/vendors/js/ui/jquery.sticky.js"></script>
    <script type="text/javascript"
            src="<?= assets_url() ?>app-assets/vendors/js/charts/jquery.sparkline.min.js"></script>
    <script src="<?php echo assets_url(); ?>assets/portjs/raphael.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url(); ?>assets/portjs/morris.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url('assets/myjs/datepicker.min.js') . APPVER; ?>"></script>
    <script src="<?php echo assets_url('assets/myjs/summernote-bs4.min.js') . APPVER; ?>"></script>
    <script src="<?php echo assets_url('assets/myjs/select2.min.js') . APPVER; ?>"></script>
    <script type="text/javascript" src="<?= assets_url('assets/admin/js/star-rating.js') ?>"></script>
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/bootstrap-table.min.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/style.min.css') ?>">
    <link rel="stylesheet" href="<?= assets_url('assets/admin/css/jquery.fancybox.min.css') ?>" />
    <script type="text/javascript">var baseurl = '<?php echo base_url() ?>';
        var crsf_token = '<?=$this->security->get_csrf_token_name()?>';
        var crsf_hash = '<?=$this->security->get_csrf_hash(); ?>';
    </script>
    <script src="<?php echo assets_url(); ?>assets/portjs/accounting.min.js" type="text/javascript"></script>
    <?php accounting() ?>
</head>
<div class="container mt-4">
 <div class="header ">
 <div class="row">
        <div class="col-md-4">
		 <img src="<?= base_url('userfiles/company/17033090061032535047.jpeg') ?>" alt="Logo">
            <h1 class="header-title">Thok ki Dukaan</h1>
          
        </div>
        <div class="col-md-8">
             <p class="header-tagline"><strong>Deals in:</strong> All kind of Exotic Fruits and Indian Fresh Fruits & Vegetables</p>
            <p class="header-address">C 29 Niranjan pur mandi, Dehradun, Uttarakhand, India - 248002</p>
        </div>
    </div>
    </div>

    <h3 class="section-title">Submit Quote</h3>
    <h5 class="greeting">Dear <?= $supplier_name ?>,</h5>
	
	
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Invoice #</label>
            <input type="text" class="form-control" value="<?= $quotation->invoice_no ?>" readonly>
        </div>
        <div class="col-md-4">
            <label>Reference</label>
            <input type="text" class="form-control" value="<?= $quotation->reference_no ?>" readonly>
        </div>
        <div class="col-md-2">
            <label>Order Date</label>
            <input type="text" class="form-control" value="<?= $quotation->order_date ?>" readonly>
        </div>
        <div class="col-md-2">
            <label>Due Date</label>
            <input type="text" class="form-control" value="<?= $quotation->due_date ?>" readonly>
        </div>
    </div>

   <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Fill Quantity</th>
                <th>Fill Rate</th>
                <th>Description</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item->item_name ?></td>
                <td><?= $item->quantity ?></td>
                <td><?= $item->unit ?></td>
                <td>
                    <input type="number" step="0.01" class="form-control fill-qty"
                        data-id="<?= $item->id ?>"
                        value="<?= $item->fill_quantity ?? $item->quantity ?>">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control fill-rate"
                        data-id="<?= $item->id ?>"
                        value="<?= $item->fill_rate ?>">
                </td>
                <td>
                    <textarea class="form-control desc" data-id="<?= $item->id ?>"><?= $item->description ?></textarea>
                </td>
                <td class="amount" data-id="<?= $item->id ?>">
                    <?= number_format((float)($item->fill_quantity * $item->fill_rate), 2) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Amount (₹)</th>
                <th id="total-amount">0.00</th>
            </tr>
        </tfoot>
    </table>

    <button id="submitQuote" class="btn btn-success">Generate Order</button>
</div>

<script>
/* $(document).ready(function() {
    function recalculateAmount(id) {
        let qty = parseFloat($(`.fill-qty[data-id='${id}']`).val()) || 0;
        let rate = parseFloat($(`.fill-rate[data-id='${id}']`).val()) || 0;
        let amount = qty * rate;
        $(`.amount[data-id='${id}']`).html('₹ ' + amount.toFixed(2));
    }

    $('.fill-qty, .fill-rate').on('input', function() {
        let id = $(this).data('id');
        recalculateAmount(id);
    });

    $('#submitQuote').click(function() {
        let items = [];
        $('.fill-qty').each(function() {
            let id = $(this).data('id');
            let fill_quantity = $(this).val();
            let fill_rate = $(`.fill-rate[data-id='${id}']`).val();
            let description = $(`.desc[data-id='${id}']`).val();
            items.push({ id, fill_quantity, fill_rate, description });
        });

        $.ajax({
            url: '<?= base_url('supplierqotation/update_items') ?>',
            method: 'POST',
            data: { items },
            success: function(res) {
                alert('Quote submitted successfully!');
            }
        });
    });
}); */


function recalculateAmount(id) {
    let qty = parseFloat($(`.fill-qty[data-id='${id}']`).val()) || 0;
    let rate = parseFloat($(`.fill-rate[data-id='${id}']`).val()) || 0;
    let amount = qty * rate;
    $(`.amount[data-id='${id}']`).html(amount.toFixed(2));
    recalculateTotal();
}

function recalculateTotal() {
    let total = 0;
    $('.amount').each(function() {
        let val = $(this).text().replace('₹', '').trim();
        total += parseFloat(val) || 0;
    });
    $('#total-amount').html(total.toFixed(2));
}

$(document).ready(function() {
    $('.fill-qty, .fill-rate').on('input', function() {
        let id = $(this).data('id');
        recalculateAmount(id);
    });

    // Recalculate totals on load
    $('.fill-qty').each(function() {
        let id = $(this).data('id');
        recalculateAmount(id);
    });

    $('#submitQuote').click(function() {
        let items = [];
        $('.fill-qty').each(function() {
            let id = $(this).data('id');
            let fill_quantity = $(this).val();
            let fill_rate = $(`.fill-rate[data-id='${id}']`).val();
            let description = $(`.desc[data-id='${id}']`).val();
            items.push({ id, fill_quantity, fill_rate, description });
        });

        $.ajax({
            url: '<?= base_url('supplierqotation/update_items') ?>',
            method: 'POST',
            data: { items },
            success: function(res) {
                alert('Quote submitted successfully!');
            }
        });
    });
});

</script>

<style>
.container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
.table input, .table textarea {
    width: 100%;
    padding: 5px;
    box-sizing: border-box;
}

        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .header {
            background: #fff;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #ffc107;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header img {
            max-height: 80px;
        }
        .header h2 {
            margin-top: 10px;
            color: #343a40;
        }
        .header p {
           
            color: #fff;
        }
        .invoice-info {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        table {
            background: #fff;
            margin-top: 20px;
        }
        .table th {
            background: #ffc107;
            color: #212529;
        }
        #total-amount {
            font-weight: bold;
            color: #000;
        }
       
		
		
		    body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .header {
  background: #0b377b;
  color: #fff;
  border-radius: 10px;
  margin-bottom: 30px;
}
       
        
        .header-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        .header-tagline, {
            margin-top: 20px;
            font-size: 16px;
        }
        h3.section-title {
            text-align: center;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        h5.greeting {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }
        .table th {
            background: #ffc107;
            color: #212529;
        }
        .table input, .table textarea {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        #total-amount {
            font-weight: bold;
            color: #000;
        }
        .btn-success {
            margin-top: 20px;
        }
    </style>