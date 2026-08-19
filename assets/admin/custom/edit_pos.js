"use strict";

/* POS - Point of Sale system starts */
if (document.readyState == 'loading') {
    document.addEventListener('DOMContentLoaded', ready);
} else {
    ready();
}

function ready() {
    display_cart();
}

function purchaseClicked() {
    var cartItems = document.getElementsByClassName('cart-items')[0];
    while (cartItems.hasChildNodes()) {
        cartItems.removeChild(cartItems.firstChild);
    }
    update_cart_total();
}
$(document).on("click", ".Remove-cart-item", function (e) {
    e.preventDefault();
    var variant_id = $(this).data("variant_id");
    $(this).parent().parent().remove();
    var cart = localStorage.getItem("cart");
    cart = (localStorage.getItem("cart") !== null) ? JSON.parse(cart) : null;
    if (cart) {
        var new_cart = cart.filter(function (item) { return item.variant_id != variant_id });
        localStorage.setItem("cart", JSON.stringify(new_cart));
        display_cart();
    }
});

$(document).on("click", ".cart-quantity-input", function (e) {
    var operation = $(this).data("operation");
    var variant_id = $(this).siblings().val();
    var input = (operation == "plus") ? $(this).siblings()[1] : $(this).siblings()[2];
    var qty = parseInt(input.value, 10);
    input.value = (operation == "minus") ? qty - 1 : qty + 1;
    update_quantity(input, variant_id);
});

function update_quantity(input, variant_id) {
    if (isNaN(input.value) || input.value <= 0) {
        input.value = 1;
    }
    var cart = localStorage.getItem("cart");
    cart = (localStorage.getItem("cart") !== null) ? JSON.parse(cart) : null;
    if (cart) {
        var i = cart.map(i => i.variant_id).indexOf(variant_id);
        cart[i].quantity = input.value;
        localStorage.setItem("cart", JSON.stringify(cart));
        display_cart();
    }
}

function SafeParseFloat(val) {
    if (isNaN(val)) {
        if ((val = val.match(/([0-9\.,]+\d)/g))) {
            val = val[0].replace(/[^\d\.]+/g, '');
        }
    }
    return parseFloat(val);
}

function add_to_cart(e, triggerEl) {
    e.preventDefault();
    e.stopPropagation();
    var button = triggerEl || e.currentTarget || e.target;
    if (button && button.nodeType !== 1) {
        button = button.parentElement;
    }
    var shopItem = button ? $(button).closest('.shop-item')[0] : null;
    if (!shopItem) {
        return;
    }

    var variant_dropdown = shopItem.querySelector('.product-variants');
    if (!variant_dropdown || variant_dropdown.selectedIndex < 0) {
        return;
    }

    var selectedOption = variant_dropdown.options[variant_dropdown.selectedIndex];
    var display_price = parseFloat(variant_dropdown.value || 0);
    var product_id = $.trim($(shopItem).find('.shop-item-id').text());
    var stock = accounting.unformat(selectedOption.dataset.stock || 0, accounting.settings.number.decimal);
    var variant_id = selectedOption.dataset.variant_id || '';
    var sellerid = selectedOption.dataset.sellerid || '';
    var variant_values = selectedOption.dataset.variant_values || '';
    var special_price = selectedOption.dataset.special_price || 0;
    var price = selectedOption.dataset.price || display_price;
    var base_price = parseFloat(selectedOption.dataset.base_price || display_price);
    var tax_amount = parseFloat(selectedOption.dataset.tax_amount || 0);
    var title = $.trim($(shopItem).find('.shop-item-title').text());
    var image = $(shopItem).find('.item-image').attr('src') || '';

    if (!variant_id) {
        return;
    }

    var cart = localStorage.getItem("cart");
    cart = (cart !== null) ? JSON.parse(cart) : [];
    var existingIndex = cart.findIndex(function (item) {
        return String(item.variant_id) === String(variant_id);
    });

    if (existingIndex > -1) {
        var updatedQty = parseInt(cart[existingIndex].quantity, 10) + 1;
        if (stock > 0 && updatedQty > stock) {
            $('#stock_alert').modal('toggle');
            return;
        }
        cart[existingIndex].quantity = updatedQty;
    } else {
        cart.push({
            product_id: product_id,
            variant_id: variant_id,
            sellerid: sellerid,
            variant_values: variant_values,
            special_price: special_price,
            price: price,
            base_price: base_price,
            tax_amount: tax_amount,
            display_price: display_price,
            title: title,
            image: image,
            stock: stock,
            quantity: 1
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    display_cart();
}
function display_cart() {
    var cart = localStorage.getItem("cart");
    cart = (localStorage.getItem("cart") !== null) ? JSON.parse(cart) : null;
    var currency = $(".cart-total-price").attr('data-currency');
    var cartRowContents = "";
    if (cart !== null && cart.length > 0) {
        cart.forEach((item) => {
            cartRowContents += `
            <div class="container">
                <div class="row">
                    <div class="col">
                    <div class="cart-image">
                        <img class="mr-4 img-fluid" src="${item.image}">
                    </div>
                        <p class="cart-item-title ">${item.title}</p>
                    </div>
                    <div class="col">
                        <div style="display:flex;flex-direction:column;line-height:1.3;">
                            <span class="cart-price">Rs. ${ parseFloat(item.display_price).toLocaleString()}</span>
                            <!-- Special price / tax breakdown disabled per request -- uncomment to re-enable
                            <small class="text-muted">Special Rs. ${ parseFloat(item.base_price || item.display_price).toLocaleString() } + Tax Rs. ${ parseFloat(item.tax_amount || 0).toLocaleString() }</small>
                            -->
                        </div>
                    </div>
                    <div class="col">
                    <div class="input-group-prepend">
                        <input type="hidden" class="product-variant" name="variant_ids[]" type="number" value=${item.variant_id}>
                        <button type="button" class="cart-quantity-input btn btn-xs btn-secondary" data-operation="plus">+</button>
                            <input class="cart-quantity-input form-control text-center p-0" name="quantity[]" value="${item.quantity}">
                        <button type="button" class="cart-quantity-input btn btn-xs btn-secondary" data-operation="minus">-</button>
                        </div>
                    </div>
                    <div class="col">
                        <button class="btn btn-xs btn-danger Remove-cart-item"  data-variant_id=${item.variant_id}><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            </div>`
        })
    } else {
        cartRowContents = `
        <div class="container">
            <div class="row">
                <div class="col mt-4 d-flex justify-content-center text-primary h5">No items in cart</div>
            </div>
        </div>`;
    }
    $(".cart-items").html(cartRowContents);
    update_cart_total();
}
function get_cart_total() {
	
    var cart = localStorage.getItem("cart");
    var cart = (cart !== null && cart !== undefined) ? JSON.parse(cart) : null;
    var cart_total = 0;
    if (cart !== null && cart !== undefined) {
        cart_total = cart.reduce((cart_total, item) =>
            cart_total + (parseFloat(item.display_price) * parseFloat(item.quantity))
            , 0);
    }
    var currency = $('#cart-total-price').attr('data-currency');
    var total = { "currency": currency, "cart_total": cart_total, "cart_total_formated": accounting.formatNumber(cart_total, 2, ",", ".") }
    return total; 
}

function update_cart_total() {
    var total = get_cart_total();
    $('#cart-total-price').html(total.cart_total_formated);
    $('#b_total').html(total.cart_total_formated);
    $('#invoiceyoghtml').val(total.cart_total.toFixed(2));
    return;
}

function render_products_empty_state(message) {
    $("#get_products").html('<div class="col-12"><div class="pos-empty-state">' + message + '</div></div>');
}

// get products
function get_products(category_id = '', limit = 2, offset = 0, search_parameter = '', product_id = '') {
    $.ajax({
        type: 'GET',
        url: `${base_url}pos_invoices/get_products?category_id=${category_id}&limit=${limit}&offset=${offset}&search=${encodeURIComponent(search_parameter)}&product_id=${product_id}`,
        dataType: 'json',
        beforeSend: function () {
            render_products_empty_state('Please wait, loading products...');
        },
        success: function (data) {
            if (data.error == false) {
                $("#total_products").val(data.products.total);
                $('#get_products').empty();
                display_products(data.products);
                var total = $("#total_products").val();
                var current_page = $("#current_page").val();
                var limit = $("#limit").val();
                paginate(total, current_page, limit);
            } else {
                $("#total_products").val(0);
                render_products_empty_state(data.message || 'No products found');
                $(".pagination-container").empty();
            }

        }
    });
}

// display products
function display_products(products) {
    var display_products = '';
    var i;
    var products_list = products.product;
    if (!products_list || !products_list.length) {
        render_products_empty_state('No products found');
        return;
    }
    for (i = 0; i < products_list.length; i++) {
        var total_price = document.getElementById('cart-total-price');
        var currency = "";
        if ($('#cart-total-price').length) {
            currency = total_price.getAttribute('data-currency');
        }
        var product = products_list[i];
        var variants = product['variants'] || [];
        var categoryName = product['category_name'] || 'Product';
        var imageUrl = product['image_md'] || product['image'] || '';
        var selectedIndex = 0;
        var variantsHtml = '';
        var initialBreakdown = '';
        for (var j = 0; j < variants.length; j++) {
            var variant_values = (variants[j]['variant_values']) ? variants[j]['variant_values'] + ' - ' : "";
            var variant_price = variants[j]['special_price'] > 0 ? variants[j]['special_price'] : variants[j]['price'];
            var isSelected = (j === selectedIndex) ? "selected" : "";
            var base_price = parseFloat(variants[j]['base_price'] || variant_price);
            var tax_amount = parseFloat(variants[j]['tax_amount'] || 0);
            var priceLabel = currency + " " + parseFloat(variant_price).toLocaleString();
            var breakdownLabel = 'Special ' + currency + base_price.toLocaleString() + ' + Tax ' + currency + tax_amount.toLocaleString();
            if (j === selectedIndex) {
                initialBreakdown = breakdownLabel;
            }
            variantsHtml += '<option ' + isSelected + ' data-sku="' + (variants[j]['sku'] || '') + '" data-variant_values="' + (variants[j]['variant_values'] || '') + '" data-stock="' + variants[j]['stock'] + '" data-sellerid="' + product['seller_id'] + '" data-price="' + variants[j]['price'] + '" data-special_price="' + variants[j]['special_price'] + '" data-base_price="' + base_price + '" data-tax_amount="' + tax_amount + '" data-variant_id="' + variants[j]['id'] + '" value="' + variant_price + '" class="shop-item-price">' + variant_values + priceLabel + '</option>';
        }

        if (!variantsHtml) {
            variantsHtml = '<option value="">No variant available</option>';
        }

        display_products += `
            <div class="col-xl-4 col-md-6 mb-3 product-card-col">
                <div class="shop-item">
                    <span class="d-none shop-item-id"><b>${product.id}</b></span>
                    <span class="shop-item-title">
                        <a href="${base_url}admin/product/view-product?edit_id=${product.id}" target="_BLANK">${product.name}</a>
                    </span>
                    <span class="shop-item-category">${categoryName}</span>
                    <div class="shop-item-image">
                        <img class="item-image img-fluid" src="${imageUrl}" alt="${product.name}" onerror="this.src='${base_url}assets/admin/images/doc-file.png'">
                    </div>
                    <select class="form-control product-variants variant_value">
                        ${variantsHtml}
                    </select>
                    <!-- Special price / tax breakdown disabled per request -- uncomment to re-enable
                    <small class="text-muted variant-price-breakdown d-block mb-1">${initialBreakdown}</small>
                    -->
                    <div class="shop-item-details justify-content-center">
                        <button class="btn btn-sm btn-info shop-item-button" type="button" ${variants.length ? '' : 'disabled'}>Add To Cart</button>
                    </div>
                </div>
            </div>`;
    }
    $('#get_products').append(display_products);
}

$(document).ready(function () {
    get_products('', $('#limit').val() || 15, 0);
    $(".pagination-container").empty();
});

function update_product_search_state() {
    var category_id = $('#product_categories').val();
    if (category_id) {
        $('#search_products').data('select2').$container.find('.select2-selection__placeholder').text('Search product in selected category');
    } else {
        $('#search_products').data('select2').$container.find('.select2-selection__placeholder').text('Search product by name or SKU');
    }
}

$(document).on("click", ".shop-item-button", function (e) {
    add_to_cart(e, this);
});

// Special price / tax breakdown disabled per request -- uncomment to re-enable
// $(document).on("change", ".product-variants.variant_value", function () {
//     var selectedOption = this.options[this.selectedIndex];
//     var breakdownEl = $(this).closest('.shop-item').find('.variant-price-breakdown');
//     if (!selectedOption || !breakdownEl.length) {
//         return;
//     }
//     var currency = $('#cart-total-price').attr('data-currency') || '';
//     var base_price = parseFloat(selectedOption.dataset.base_price || 0);
//     var tax_amount = parseFloat(selectedOption.dataset.tax_amount || 0);
//     breakdownEl.text('Special ' + currency + base_price.toLocaleString() + ' + Tax ' + currency + tax_amount.toLocaleString());
// });

$("#search_products").select2({
    ajax: {
        url: base_url + 'pos_invoices/get_product_options',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term,
                category_id: $('#product_categories').val()
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    theme: 'bootstrap4',
    placeholder: 'Search product by name or SKU',
    allowClear: true,
    width: '100%',
    language: {
        searching: function () {
            return 'Loading products...';
        },
        noResults: function () {
            return 'No products found';
        }
    }
});
update_product_search_state();

// keeps the just-picked product from being wiped out when we auto-sync the category select
var syncing_category_from_product = false;

// category wise product change
$('#product_categories').on("change", function () {
    var category_id = $('#product_categories').val();
    $('#current_page').val("0");
    if (!syncing_category_from_product) {
        // clear the search box's own UI only -- the actual product fetch below
        // already covers both the "category picked" and "category cleared" cases.
        $("#search_products").val(null).trigger('change.select2');
    }
    syncing_category_from_product = false;
    update_product_search_state();
    get_products(category_id, $('#limit').val() || 15, 0);
});

$(document).ready(function () {
    $("#product_categories").on("change", function () {
        $("#get_products").empty();
    });
});

// auto-select the product's category when it's picked directly via search
$('#search_products').on('select2:select', function (e) {
    var product_category_id = e.params && e.params.data ? e.params.data.category_id : '';
    var current_category_id = $('#product_categories').val();
    if (product_category_id && String(product_category_id) !== String(current_category_id)) {
        syncing_category_from_product = true;
        $('#product_categories').val(product_category_id).trigger('change');
    }
});

$('#search_products').on('change', function () {
    var category_id = $('#product_categories').val();
    var limit = $('#limit').val();
    var product_id = $(this).val() || '';
    $('#current_page').val("0");
    get_products(category_id, limit, 0, '', product_id);
});

// transaction id input 
$(document).ready(function () {
    $('.transaction_id').hide();
    $('.payment_method_name').hide();
});

/* payment method selected event  */
$(".payment_method").on('click', function () {
    var payment_method = $(this).val();
    var exclude_txn_id = ["COD"];
    var include_payment_method_name = ["other"];

    if (exclude_txn_id.includes(payment_method)) {
        $(".transaction_id").hide();
    } else {
        $(".transaction_id").show();
    }

    if (include_payment_method_name.includes(payment_method)) {
        $('.payment_method_name').show();
    } else {
        $('.payment_method_name').hide();
    }
});

// select 2 js select user
$(".select_user").select2({
    ajax: {
        url: base_url + 'pos_invoices/get_users',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    theme: 'bootstrap4',
    placeholder: 'Search customer by name, mobile, or email',
    allowClear: true,
    width: '100%',
    templateResult: function (user) {
        if (user.loading) return user.text;
        return user.text;
    },
    templateSelection: function (user) {
        if (user.name) {
            return user.name + (user.number ? ' - ' + user.number : '');
        }
        return user.text;
    }
});

$('#select_user_id').on('select2:open', function () {
    var searchField = document.querySelector('.select2-container--open .select2-search__field');
    if (searchField) {
        searchField.focus();
    }
});
// clear selected values in select2

$("#clear_user_search").on('click', function () {
    pos_user_id = 0;
    $("#select_user_id").val(null).trigger('change');
});

// Register in pos

$(document).on('submit', '#register_form', function (e) {
    e.preventDefault();
    var name = $('#name').val();
    var mobile = $('#mobile').val();
    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function () {
            $('#save-register-result-btn').html('Please Wait..');
            $('#save-register-result-btn').attr('disabled', true);
        },
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            if (result.error == false) {
                iziToast.success({
                    message: result.message,
                });
                var customerData = result.data[0];
                if ($("#select_user_id").is('select')) {
                    var customerLabel = $.trim(customerData.username + ' - ' + customerData.mobile);
                    if ($("#select_user_id option[value='" + customerData.id + "']").length === 0) {
                        var customerOption = new Option(customerLabel, customerData.id, true, true);
                        $("#select_user_id").append(customerOption);
                    }
                    $("#select_user_id").val(String(customerData.id)).trigger('change.select2');
                }
                $('#register').modal('hide');
                $('#register_form')[0].reset();
            } else {
                iziToast.error({
                    message: result.message,
                });
            }
            $('#save-register-result-btn').html('Register').attr('disabled', false);
        }
    });
});

var pos_user_id = 0;
$('#select_user_id').on('change', function () {
    pos_user_id = ($('#select_user_id').val());
    $('#pos_user_id').val(pos_user_id || '');
});

$('#pos_form').on('submit', function (e) {
    e.preventDefault();
    if (confirm('Are you sure? want to check out.')) {
        var cart = localStorage.getItem("cart");
        if (cart == null || !cart) {
            var message = "Please add items to cart";
            show_message("Oops!", message, "error");
            return;
        }
        var payment_method = $('#p_method').val();

        if (!payment_method) {
            var message = "Please choose a payment method";
            show_message("Oops!", message, "error");
            return;
        }
        if (!pos_user_id || parseInt(pos_user_id, 10) <= 0) {
            var customerMessage = "Please select the customer!";
            show_message("Oops!", customerMessage, "error");
            return;
        }
        var payment_amount = $('#p_amount').val();
        var payment_account = $('#p_account').val();
        var txn_id = $('#transaction_id').val();
       /*  if (!txn_id && payment_method != 'COD') {
          
            var message = "Please enter  transaction id";
            show_message("Oops!", message, "error");
            return;
        } */
        var payment_method_name = $('#p_method').val();
        if (!payment_method_name) {
            payment_method_name = '';
        }
        const request_body = {
            [csrfName]: csrfHash,
            data: cart,
            payment_method: payment_method,
            user_id: pos_user_id,
            p_amount: payment_amount,
            p_account: payment_account,
            txn_id: txn_id,
            payment_method_name: payment_method_name
        }
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: request_body,
            dataType: 'json',
            beforeSend: function () {
                $('#pos_basic_pay').prop('disabled', true).text('Please wait...');
            },
            success: function (result) {
                csrfName = result['csrfName'];
                csrfHash = result['csrfHash'];
                if (result.error == true) {
                    iziToast.error({
                        message: '<span>' + result.message + '</span> ',
                    });
                } else {
                    iziToast.success({
                        message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                    });
                    $('#basicPay').modal('hide');
                    delete_cart_items();
                    setTimeout(function () { window.location.href = base_url + 'pos_invoices/view?id=' + result.data.order_id; }, 300);
                }
            },
            error: function () {
                iziToast.error({
                    message: 'Unable to complete payment. Please try again.',
                });
            },
            complete: function () {
                $('#pos_basic_pay').prop('disabled', false).html('<i class="fa fa-arrow-circle-o-right"></i> Pay now');
            }
        });
    }
});

// Clear Cart

$(document).on("click", ".btn-clear_cart", function (e) {
    e.preventDefault();
    delete_cart_items();
});
function delete_cart_items() {
    localStorage.removeItem("cart");
    display_cart();
}
function show_message(prefix = "Great!", message, type = 'success') {
    Swal.fire(prefix, message, type);
}

function paginate(total, current_page, limit) {
    var number_of_pages = Math.ceil(total / limit);
    var i = 0;
    if (!number_of_pages || number_of_pages <= 1) {
        $(".pagination-container").empty();
        return;
    }
    var pagination = `<div class="row p-2">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            <ul class="pagination mb-0">`;
    pagination += `<li class="page-item"><a class="page-link" href="javascript:prev_page()" >Previous</a></li>`;
    var active = "";
    while (i < number_of_pages) {
        active = (current_page == i) ? "active" : "";
        pagination += `<li class="page-item ${active}"><a class="page-link" href="javascript:go_to_page(${limit},${i})" >${++i}</a></li>`;
    }
    pagination += `<li class="page-item"><a class="page-link" href="javascript:next_page()">Next</a></li>
                </ul>
            </div>
        </div>
    </div>`;
    $(".pagination-container").html(pagination);
}
function go_to_page(limit, page_number) {
    var total = $("#total_products").val();
    var category_id = $("#product_categories").val();
    var product_id = $("#search_products").val();
    var offset = page_number * limit;

    get_products(category_id, limit, offset, '', product_id);
    paginate(total, page_number, limit);

    $("#limit").val(limit);
    $("#offset").val(offset);
    $("#current_page").val(page_number);
}
function prev_page() {
    var current_page = $("#current_page").val();
    var total = $("#total_products").val();
    var limit = $("#limit").val();
    var prev_page = parseFloat(current_page) - 1;

    if (prev_page >= 0) {
        go_to_page(limit, prev_page);
    }
}
function next_page() {
    var current_page = $("#current_page").val();
    var total = $("#total_products").val();
    var limit = $("#limit").val();

    var number_of_pages = Math.ceil(total / limit);
    var next_page = parseFloat(current_page) + 1;

    if (next_page < number_of_pages) {
        go_to_page(limit, next_page);
    }
}

/* POS - Point of Sale system ends */

var rowTotal = function (numb) {
	
	
    //most res
    var result;
    var page = '';
    var totalValue = 0;
    var amountVal = accounting.unformat($("#amount-" + numb).val(), accounting.settings.number.decimal);
    var priceVal = accounting.unformat($("#price-" + numb).val(), accounting.settings.number.decimal);
    var discountVal = accounting.unformat($("#discount-" + numb).val(), accounting.settings.number.decimal);
    var vatVal = accounting.unformat($("#vat-" + numb).val(), accounting.settings.number.decimal);
    var taxo = 0;
    var disco = 0;
    var totalPrice = amountVal.toFixed(two_fixed) * priceVal;
    var tax_status = $("#taxformat option:selected").val();
    var disFormat = $("#discount_format").val();
	
    $("#result-" + numb).html(accounting.formatNumber(totalPrice));
    $("#taxa-" + numb).val(taxo);
    $("#texttaxa-" + numb).text(taxo);
    $("#disca-" + numb).val(disco);
    $("#total-" + numb).val(accounting.formatNumber(totalPrice));
    samanYog();
};
