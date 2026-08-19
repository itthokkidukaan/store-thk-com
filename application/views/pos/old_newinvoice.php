<style>
.app-content.content .content-wrapper {
    padding-top: 0.75rem;
}

.pos-old-page {
    padding-top: 0.25rem;
    padding-bottom: 1.5rem;
}

.pos-old-page .section-title .title {
    font-weight: 600;
}

.pos-toolbar {
    margin-top: 0.75rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}

.pos-toolbar-grid {
    display: grid;
    grid-template-columns: minmax(150px, 180px) minmax(220px, 1fr) minmax(220px, 1fr);
    gap: 0.875rem;
    align-items: end;
}

.pos-toolbar-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    background: #0f172a;
    color: #fff;
    font-weight: 600;
}

.pos-toolbar-field label,
.pos-customer-block label {
    display: block;
    margin-bottom: 0.4rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.pos-toolbar-field .form-control,
.pos-old-page .select2-container--bootstrap4 .select2-selection,
.pos-old-page #p_method,
.pos-old-page #p_account,
.pos-old-page #p_amount,
.pos-old-page #balance1,
.pos-old-page #change_p {
    min-height: 46px;
    border-radius: 10px;
}

.pos-old-page .select2-container {
    width: 100% !important;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single {
    display: flex;
    align-items: center;
    min-height: 46px;
    padding: 0 2.25rem 0 0.85rem;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    width: 100%;
    padding-left: 0;
    padding-right: 0;
    margin-top: 0;
    line-height: 1.35;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 44px;
    right: 8px;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
    border-color: #111827 transparent transparent transparent;
    border-width: 6px 5px 0 5px;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    margin-top: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 999px;
    background: #111827;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
}

.pos-old-page .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
    background: #000000;
    color: #ffffff;
}

.pos-old-page .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
.pos-old-page .select2-container--bootstrap4.select2-container--open .select2-selection--single {
    border-color: #60a5fa;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
}

.pos-old-page .select2-container--bootstrap4 .select2-selection__placeholder {
    color: #6b7280;
}

.pos-toolbar-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.625rem;
}

.pos-toolbar-actions .btn {
    border-radius: 10px;
}

.pos-page-note {
    margin-top: 0.75rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.pos-card {
    height: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
}

.pos-card-body {
    padding: 1.25rem;
}

.pos-customer-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.pos-customer-head-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.pos-customer-head .btn {
    white-space: nowrap;
}

.pos-customer-link {
    margin-top: 1rem;
    padding: 0.875rem 1rem;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
}

.pos-customer-link p {
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #374151;
}

.pos-cart-title {
    margin: 1.25rem 0 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.pos-cart-head {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(75px, 0.8fr) minmax(135px, 1.2fr) 50px;
    gap: 1rem;
    padding: 0.75rem 1rem;
    margin-bottom: 0.75rem;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
}

.pos-old-page .cart-items > .container {
    max-width: 100%;
    padding-left: 0;
    padding-right: 0;
}

.pos-old-page .cart-items .row {
    display: grid !important;
    grid-template-columns: minmax(0, 2fr) minmax(75px, 0.8fr) minmax(135px, 1.2fr) 50px !important;
    gap: 1rem;
    align-items: center;
    margin: 0 0 0.75rem;
    padding: 0.875rem 1rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.pos-old-page .cart-items .col {
    display: flex;
    align-items: center;
    min-width: 0;
}

.pos-old-page .Remove-cart-item {
    margin-left: 0.5rem !important;
}

.pos-old-page .cart-item-title {
    margin: 0;
    font-weight: 600;
    color: #111827;
    word-break: break-word;
}

.pos-old-page .cart-image img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 10px;
}

.pos-old-page .input-group-prepend {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: nowrap;
}

.pos-old-page .cart-quantity-input.form-control {
    width: 56px;
    min-width: 56px;
    height: 34px;
    border-radius: 8px;
}

.pos-total-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.pos-total-label {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
}

.pos-action-row {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.pos-action-row .btn {
    min-width: 160px;
    min-height: 42px;
    border-radius: 10px;
}

.pos-product-card {
    padding: 0;
}

.pos-product-panel {
    padding: 1rem;
}

.pos-product-grid {
    margin: 0;
}

.pos-old-page .product-card-col {
    display: flex;
}

.pos-old-page .shop-item {
    height: 100%;
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: 270px;
    margin: 0;
    padding: 0.6rem;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.pos-old-page .shop-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
}

.pos-old-page .shop-item-title {
    display: block;
    min-height: 38px;
    margin-bottom: 0.3rem;
    line-height: 1.3;
    font-size: 0.95rem;
    font-weight: 700;
}

.pos-old-page .shop-item-title a {
    color: #111827;
    text-decoration: none;
}

.pos-old-page .shop-item-title a:hover {
    color: #2563eb;
}

.pos-old-page .shop-item-category {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: fit-content;
    margin: 0 auto 0.5rem;
    padding: 0.2rem 0.5rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #2563eb;
    font-size: 0.7rem;
    font-weight: 600;
}

.pos-old-page .shop-item-image {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 0 0.6rem;
    min-height: 110px;
    padding: 0.4rem;
    border-radius: 10px;
    background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    overflow: hidden;
}

.pos-old-page .item-image {
    display: block;
    max-height: 90px;
    max-width: 100%;
    width: auto;
    object-fit: contain;
}

.pos-old-page .product-variants {
    margin-top: 0 !important;
    margin-bottom: 0.5rem;
    min-height: 36px;
    height: 36px;
    border-radius: 8px;
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
}

.pos-old-page .shop-item-details {
    margin-top: auto;
}

.pos-old-page .shop-item-details .btn {
    width: 100%;
    min-height: 36px;
    height: 36px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
}

.pos-empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #64748b;
    font-weight: 600;
}

@media (max-width: 991.98px) {
    .pos-toolbar-grid {
        grid-template-columns: 1fr;
    }

    .pos-cart-head {
        display: none;
    }
}
</style>

<div class="content-wrapper">
    <div class="pos-old-page">

    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12 border-bottom">

                    <div class="col-lg-9 col-md-8">

                        <div class="section-title">

                            <h4 class="title mb-2 mt-2">Point Of Sale</h4>

                        </div>

                    </div>

                </div>

            </div>

            <div class="pos-toolbar">
                <div class="pos-toolbar-grid">
                    <div class="pos-toolbar-badge">All Products</div>
                    <div class="pos-toolbar-field">
                        <label for="product_categories">Category</label>
                        <select class="form-control" id="product_categories">
                            <option value="" selected><?= (isset($categories) && empty($categories)) ? 'No Categories Exist' : 'Select Category' ?></option>
                            <?= get_categories_option_html($categories); ?>
                        </select>
                    </div>
                    <div class="pos-toolbar-field">
                        <label for="search_products">Search Product</label>
                        <select class="form-control" id="search_products"></select>
                    </div>
                </div>

                <input type="hidden" id="product_seller" value="<?= isset($pos_seller_id) ? (int)$pos_seller_id : 0; ?>">

                <div class="pos-toolbar-actions mt-3">
                    <a class="btn btn-blue btn-md t_tooltip" href="<?= base_url() ?>customers" target="_blank">CRM</a>
                    <a class="btn btn-amber btn-md t_tooltip" href="<?= base_url() ?>customers/todayappointment">Today Appointment (<?= $alltoday ?>)</a>
                    <a class="btn btn-blue btn-md t_tooltip" href="<?= base_url() ?>customers/upcomingappointment">Upcoming Appointment (<?= $allcoming ?>)</a>
                    <a class="btn btn-success btn-md t_tooltip" href="<?= base_url() ?>productcategory/viewwarehouse?id=1" target="_blank">Send Price List</a>
                    <a class="btn btn-blue btn-md t_tooltip" href="<?= base_url() ?>productcategory/viewwarehouse?id=1" target="_blank">Price Update</a>
                    <a class="btn btn-amber btn-md t_tooltip" href="javascript:void(0)"><i class="fa fa-xs fa-inr"></i> <?= $balance ?></a>
                    <a class="btn btn-blue btn-md t_tooltip" href="<?= base_url() ?>transactions/add" target="_blank"><i class="fa fa-xs fa-plus"></i> Expense</a>
                </div>

                <div class="pos-page-note">
                    <?= !empty($is_seller_user) ? 'Only this seller related products and customers are shown on this POS page.' : 'Use category and search to quickly find products on this POS page.'; ?>
                </div>
            </div>

            <div class="container-fluid mt-4">

                <div class="row">

                  

                    <div class="col-md-6">

                        <div class="card pos-card">

                            <section class="content-section pos-card-body">

                                <form id="pos_form" method="post" action='<?= base_url('point_of_sale/place_order') ?>'>

                                    <div class="pos-customer-head">
                                        <div class="pos-customer-head-title">Customer</div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear_user_search">Clear</button>
                                    </div>

                                    <!-- select user -->

                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                                    <input type="hidden" name="user_id" id="pos_user_id" value="">

                                    <input type="hidden" name="product_variant_id" value="">

                                    <input type="hidden" name="quantity" value="">

                                    <input type="hidden" name="total" value="">

                                    <div class="pos-customer-block">
                                        <label for="select_user_id">Search Customer</label>
                                        <select class="select_user form-control" id="select_user_id">
                                            <!-- user name display here  -->
                                        </select>
                                    </div>

                                    <div class="pos-customer-link">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-success btn-rounded" data-toggle="modal" data-target="#register">Add Customer</a>
                                    </div>

                                    <p class="pos-cart-title">Cart</p>

                                    <div class="pos-cart-head">
                                        <span>Product</span>
                                        <span>Price</span>
                                        <span>Quantity</span>
                                        <span>Remove</span>
                                    </div>

                                    <div class="cart-items">

                                    </div>

                                    <div class="pos-total-row">
                                        <p class="pos-total-label">Total</p>
                                        <?php $settings = get_settings('system_settings', true); ?>
                                        <p class="cart-total-price h6 m-1 px-2" id="cart-total-price" data-currency="<?= (isset($settings['currency']) && !empty($settings['currency'])) ?   $settings['currency'] : '';   ?>"></p>
                                        <input type="hidden" id="invoiceyoghtml" name="cartAmount">
                                    </div>
                             



                                    <div class="pos-action-row">
                                        <button class="btn btn-sm btn-clear_cart btn-danger" type="button" id="clear_cart_btn">Clear Cart</button>
                                        <button class="btn btn-sm btn-purchase btn-primary" type="button" id="open_payment_modal_btn">Place Order</button>
                                    </div>



<div class="modal fade" id="basicPay" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">
            
                <!-- Modal Header -->
                <div class="modal-header">

                    <h4 class="modal-title"><?php echo $this->lang->line('Make Payment') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p id="statusMsg"></p>

                    <div class="text-center mb-2">
                        <div class="text-muted text-uppercase small">Order Total</div>
                        <h1 id="b_total"></h1>
                    </div>
                    <div class="row">


                        <div class="col-6">
                            <div class="card-title">
                                <label for="cardNumber">Received Amount</label>
                                <div class="input-group">
                                    <input
                                            type="text"
                                            class="form-control  text-bold-600 blue-grey"
                                            name="p_amount"
                                            placeholder="Amount" onkeypress="return isNumber(event)"
                                            id="p_amount" onkeyup="update_pay_pos()" inputmode="numeric"
                                    />
                                    <span class="input-group-addon"><i
                                                class="icon icon-cash"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-title">
                                <label for="cardNumber"><?php echo $this->lang->line('Payment Method') ?></label>
                                <select class="form-control" name="p_method" id="p_method">
                                    <option value=''>Select Payment Method</option>
                                    <option value='Due'><?php echo $this->lang->line('Due') ?></option>
                                    <option value='Cash'><?php echo $this->lang->line('Cash') ?></option>
                                    <option value='UPI'>UPI</option>
                                    <option value='Card Swipe'><?php echo $this->lang->line('Card Swipe') ?></option>
                                    <option value='Bank'><?php echo $this->lang->line('Bank') ?></option>

                                </select></div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group  text-bold-600 red">
                                <label for="amount"><?php echo $this->lang->line('Balance Due') ?>
                                </label>
                                <input type="text" class="form-control red" name="amount" id="balance1"
                                       onkeypress="return isNumber(event)"
                                       value="0.00"
                                       required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group text-bold-600 text-g">
                                <label for="b_change"><?php echo $this->lang->line('Change') ?></label>
                                <input
                                        type="text"
                                        class="form-control green"
                                        name="b_change" id="change_p" value="0">
                            </div>
                        </div>
                    </div>
                    <?php if (PAC) { ?>
                        <div class="col">
                            <div class="form-group text-bold-600 text-g">
                                <label for="account_p"><?php echo $this->lang->line('Account') ?></label>

                                <select name="p_account" id="p_account" class="form-control">
                                    <?php foreach ($acc_list as $row) {
                                        echo '<option value="' . $row['id'] . '">' . $row['holder'] . ' / ' . $row['acn'] . '</option>';
                                    }
                                    ?>
                                </select></div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-success btn-lg btn-block mb-1"
                                    type="submit"
                                    id="pos_basic_pay" data-type="4"><i
                                        class="fa fa-arrow-circle-o-right"></i> <?php echo $this->lang->line('Paynow') ?>
                            </button>
                     
                        </div>
                    </div>

                    <div class="row" style="display:none;">
                        <div class="col-xs-12">
                            <p class="payment-errors"></p>
                        </div>
                    </div>


                    <!-- shipping -->


                </div>
                <!-- Modal Footer -->

          
        </div>
    </div>
</div>




                                </form>

                            </section>

                        </div>

                    </div>
					
					  <div class="col-md-6">

                        <div class="card pos-card pos-product-card">

                            <div class="pos-card-body pos-product-panel">

                            <input type="hidden" name="limit" id="limit" value="15" />

                            <input type="hidden" name="offset" id="offset" value="0" />

                            <input type="hidden" name="total" id="total_products" />

                            <input type="hidden" name="current_page" id="current_page" value="0" />

                            <div class="row d-flex p-0 align-content-start pos-product-grid" id="get_products">

                                <!-- product display in this container -->

                            </div>

                            <div class="pagination-container mt-3"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    </div>
</div>




<?php $this->load->view('customers/common_add_customer_modal', ['modal_id' => 'register', 'customergrouplist' => $customergrouplist, 'custom_fields_c' => $custom_fields_c]); ?>

<script type="text/javascript">
$( document ).ready(function() {
     $(".btn-purchase").click(function () {
        var cart = localStorage.getItem("cart");
        cart = (cart !== null) ? JSON.parse(cart) : [];
        var selectedCustomer = $("#select_user_id").val();
        var totalAmount = accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal) || 0;

        if (!selectedCustomer) {
            Swal.fire('Oops!', 'Please select the customer!', 'error');
            return;
        }

        if (!cart.length || totalAmount <= 0) {
            Swal.fire('Oops!', 'Please add items to cart', 'error');
            return;
        }

        $('#pos_user_id').val(selectedCustomer);
        $('#b_total').html(totalAmount.toFixed(2));
        $('#p_amount').val(totalAmount.toFixed(2));
        update_pay_pos();
        $('#basicPay').modal('show');
    });

});

 function update_pay_pos() {
        var am_pos = accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal) || 0;
        var ttl_pos = parseFloat($('#invoiceyoghtml').val()) || 0;
        var due = ttl_pos - am_pos;
        if (due >= 0) {
            $('#balance1').val(due.toFixed(2));
            $('#change_p').val('0.00');
        } else {
            due = Math.abs(due);
            $('#balance1').val('0.00');
            $('#change_p').val(due.toFixed(2));
        }
    }
	
	$(document).ready(function () {

    // Pre-select a sensible default account, but only when nothing has been
    // chosen yet -- never override an account the user has actually picked,
    // and never disable the other options (they must stay pickable).
    function setDefaultAccountIfEmpty() {
        var defaultAccount = "<?=$accnumber?>";
        if (!$("#p_account").val() && defaultAccount && $("#p_account option[value='" + defaultAccount + "']").length) {
            $("#p_account").val(defaultAccount);
        }
    }

  /*
    function calculateBalanceDue() {
        var amount = parseFloat($("#p_amount").val()) || 0;
        var total =  parseFloat($("#invoiceyoghtml").val()) || 0;
        var balanceDue = Math.max(total - amount, 0);
        $("#balance1").val(balanceDue.toFixed(2));
        return balanceDue;
    } */


    function checkBalanceDue() {
        setDefaultAccountIfEmpty();
    }


    $("#p_method").change(function () {
        checkBalanceDue();

        var total = parseFloat($('#invoiceyoghtml').val()) || 0;
        if ($(this).val() === "Due") {
            // Nothing received yet on a due sale -- balance due is the full order total.
            $("#p_amount").val('0.00');
        } else if ($(this).val() !== "") {
            // Any other payment method defaults to fully paid.
            $("#p_amount").val(total.toFixed(2));
        }
        update_pay_pos();
    });

   
    $("#p_amount").on('input', function () {
        checkBalanceDue();
    });

    
    checkBalanceDue();
});





</script>
