## POS make payment modal rounded totals instead of using exact cart amount - 2026-06-24
**Cause:** The `pos_invoices/create` payment modal in `application/views/pos/old_newinvoice.php` applied round-off rules from POS config before calculating balance/change, and the cart total hidden field was being stored as a formatted display value. This caused the Make Payment popup to use round figures instead of the exact cart total.
**Solution:** Removed round-off handling from the payment modal calculation, set the received amount from the raw cart total, and updated `assets/admin/custom/edit_pos.js` to keep a precise numeric total in `#invoiceyoghtml` while still rendering a 2-decimal formatted display.
**Prevention:** Payment popups should always calculate from raw decimal totals, not display-formatted strings or printer round-off settings intended for invoice presentation.
**Resources:** [JavaScript Number.toFixed()](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/toFixed), [accounting.js formatNumber](http://openexchangerates.github.io/accounting.js/)

## Forgot password used email reset instead of WhatsApp OTP - 2026-06-24
**Cause:** The forgot-password page in `application/views/user/forgot.php` only collected email and `User::send_reset()` generated an email reset link. The project already had WhatsApp OTP/message delivery using DialText, but forgot-password was not using that existing path.
**Solution:** Updated `application/controllers/User.php` so forgot-password now accepts the registered mobile number, generates a 6-digit OTP, stores it in `users.verification_code`, sends it through the existing DialText WhatsApp API pattern, and allows password reset through `application/views/user/reset.php` using the WhatsApp OTP. The legacy email reset path remains supported in `reset_change()` for existing link-based resets.
**Prevention:** When a project already standardizes OTP delivery on WhatsApp/mobile, keep password recovery on the same verified channel instead of maintaining a separate email-only reset flow.
**Resources:** [CodeIgniter Input Class](https://codeigniter.com/userguide3/libraries/input.html), [PHP random_int](https://www.php.net/manual/en/function.random-int.php)

## Password reset succeeded but login failed - 2026-06-20
**Cause:** The forgot-password flow used `$this->input->post('n_password', true)`, which XSS-cleaned the new password before hashing, while normal login checked the raw password input. Passwords containing special characters could therefore be saved differently from what users entered at login.
**Solution:** Read the reset password value without XSS-cleaning in `application/controllers/User.php` and return a login redirect after successful reset. Updated `application/views/user/reset.php` to redirect to `user` on success.
**Prevention:** Do not apply XSS-cleaning to password fields before hashing or verifying them. Keep password handling consistent across reset, change-password, and login flows.
**Resources:** [CodeIgniter Input Class](https://codeigniter.com/userguide3/libraries/input.html), [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)

## POS create page select box and action layout broken - 2026-06-20
**Cause:** The live POS page used `application/views/pos/old_newinvoice.php`, which still had an outdated toolbar layout, an empty seller dropdown, duplicate button IDs, mismatched cart labels, and weak Select2/product grid presentation. The POS user lookup also returned all users instead of seller-assigned customers.
**Solution:** Reworked the old POS page with scoped UI styles, a responsive control bar, corrected labels/buttons, aligned cart headings with the real cart layout, and improved product card rendering in `assets/admin/custom/edit_pos.js`. Updated `application/models/Point_of_sale_model.php` so seller users only fetch their assigned customers in POS search.
**Prevention:** Confirm which POS view is actually rendered before editing UI, keep toolbar controls scoped to the active page, avoid duplicate DOM IDs, and reuse seller-assignment filtering consistently in POS search endpoints.
**Resources:** [Select2 Bootstrap 4 theme](https://select2.org/appearance), [Bootstrap Form Controls](https://getbootstrap.com/docs/4.6/components/forms/), [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## POS seller check triggered ambiguous `id` in Ion Auth query - 2026-06-20
**Cause:** `application/models/Point_of_sale_model.php` began building a `users` query with `select('id, username, mobile, email')` before calling `is_seller_user()`. Ion Auth shares CodeIgniter's query builder state, so the later `get_users_groups()` join query inherited the earlier unqualified `id` select and failed with `Column 'id' in field list is ambiguous`.
**Solution:** Resolve `is_seller_user()` first, then start the POS `users` query. This keeps the Ion Auth group lookup isolated from the POS model query builder state.
**Prevention:** Any helper that reaches Ion Auth or other DB-backed libraries must run before starting a query builder chain in the current model, or after calling a fresh query with reset state.
**Resources:** [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html), [Ion Auth](https://github.com/benedmunds/CodeIgniter-Ion-Auth)

## Seller POS products hidden because product status was filtered - 2026-06-20
**Cause:** `application/controllers/Pos_invoices.php` used `fetch_product()` with its default active-product filter. That helper restricts POS results to `p.status = 1`, `pv.status = 1`, and `sd.status = 1`. Seller-linked products in this project can still be valid for internal POS while having `products.status = 2`, so the seller product grid appeared empty even though the product existed.
**Solution:** For seller POS requests, pass `show_only_active_products = 0` before calling `fetch_product()` so the POS page shows the seller’s related internal products instead of only storefront-active ones.
**Prevention:** Do not reuse storefront-only active product filters for authenticated back-office POS screens unless the business rule explicitly requires only public products.
**Resources:** [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## Seller POS category dropdown did not match seller products - 2026-06-20
**Cause:** The POS create page loaded seller categories from `seller_data.category_ids`, but the seller's actual products were stored under different `products.category_id` values. This made the category dropdown show `No Categories Exist` or unrelated categories even when seller products existed.
**Solution:** Added `get_seller_product_categories()` in `application/models/Category_model.php` and updated `application/controllers/Pos_invoices.php` to use real categories derived from the seller's products for the POS page. Also corrected the POS product card renderer in `assets/admin/custom/edit_pos.js` so cards render with valid markup, proper image layout, and standard sizing.
**Prevention:** For seller-facing POS/invoice filters, derive categories from actual product ownership instead of relying on optional seller profile category mappings.
**Resources:** [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## POS create page kept showing old broken product card UI - 2026-06-20
**Cause:** `application/views/fixed/footer-pos.php` loaded `assets/admin/custom/pos.js` for every `create` route, including `pos_invoices/create`. That page therefore kept using the old product renderer instead of the updated POS script. The same footer also injected global `.img-fluid`, `.shop-item-image`, and spacing overrides that distorted the product image/card layout.
**Solution:** Updated `footer-pos.php` so `pos_invoices/create` loads `assets/admin/custom/edit_pos.js`, and removed the global footer CSS overrides that were shrinking and breaking the product cards.
**Prevention:** Route shared footers should choose scripts by controller and action, not only by action name, and should avoid page-wide CSS overrides for component classes like `.img-fluid`.
**Resources:** [CodeIgniter URI Class](https://codeigniter.com/userguide3/libraries/uri.html)

## POS product search needed Select2 category-aware selection - 2026-06-20
**Cause:** The POS toolbar still used a plain text search field, which did not match the UX of the customer selector and could not provide a reliable category-aware product selection workflow. It also left the product cards looking undersized when only one product matched.
**Solution:** Replaced the POS product search control with a Select2 dropdown in `application/views/pos/old_newinvoice.php`, added `get_product_options()` plus `product_id` filtering in `application/controllers/Pos_invoices.php`, and updated `assets/admin/custom/edit_pos.js` so selected products filter the grid by category and render in wider, cleaner cards.
**Prevention:** Use a dedicated Select2 data source for searchable POS selectors instead of overloading plain text inputs when the UI needs search-plus-select behavior.
**Resources:** [Select2 AJAX](https://select2.org/data-sources/ajax), [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## POS product search must depend on selected category - 2026-06-20
**Cause:** The product search Select2 was still technically available without a category guard, so the intended workflow of "pick category first, then search related products" was not enforced. The product card styling also used an oversized minimum height that made single-product results look too tall.
**Solution:** Updated `application/controllers/Pos_invoices.php` so product search options return empty unless a valid category is selected, and updated `assets/admin/custom/edit_pos.js` to disable the product Select2 until category selection. Reduced card/image sizing in `application/views/pos/old_newinvoice.php` to remove the overly tall product tile layout.
**Prevention:** For dependent POS filters, disable downstream selectors until the parent filter is chosen, and keep card height content-driven instead of relying on large fixed minimum heights.
**Resources:** [Select2 AJAX](https://select2.org/data-sources/ajax), [Bootstrap Cards](https://getbootstrap.com/docs/4.6/components/card/)

## POS cash payment modal stayed on the same popup - 2026-06-20
**Cause:** The POS checkout AJAX in `assets/admin/custom/edit_pos.js` posted only `data`, `payment_method`, and `user_id` to `Point_of_sale::place_order()`, but the backend cash flow also reads `p_amount` and `p_account`. The success branch also redirected to `pos_invoices/view` using an order id from the ecommerce POS controller, which is not the right landing route for this page.
**Solution:** Updated the POS checkout request to send `p_amount` and `p_account`, synchronized the hidden `user_id`, closed the payment modal on success, and redirected to `pos_invoices` after checkout.
**Prevention:** When a page submits by AJAX instead of form serialization, include every backend-required payment field explicitly and redirect to a route that matches the controller generating the response.
**Resources:** [jQuery.ajax()](https://api.jquery.com/jQuery.ajax/), [CodeIgniter Input Class](https://codeigniter.com/userguide3/libraries/input.html)

## POS create page opened payment modal with incomplete state - 2026-06-20
**Cause:** The `Place Order` button in `application/views/pos/old_newinvoice.php` always toggled the payment modal immediately, even if no customer was selected or the cart total was still empty. That made the create page flow feel broken before checkout.
**Solution:** Updated the create page button flow to validate selected customer and cart items first, prefill `p_amount` from the live cart total, run `update_pay_pos()`, and only then open the payment modal.
**Prevention:** Gate modal-based checkout steps behind the same minimum validations as the final submit, and initialize modal form fields from the current create-page state before opening them.
**Resources:** [Bootstrap Modal](https://getbootstrap.com/docs/4.6/components/modal/), [SweetAlert2](https://sweetalert2.github.io/)

## POS create/edit payment modal showed duplicate amount and checkout hit 500 - 2026-06-20
**Cause:** The payment popup displayed the order total and the editable paid amount as if both were the same "Amount", which was confusing in create and edit flows. In checkout, `application/controllers/Point_of_sale.php` assumed `p_amount` and `p_account` were always present and valid; when they were missing or empty, the `place_order` payment path could break and return a 500.
**Solution:** Renamed the editable field to `Received Amount`, kept the top figure as `Order Total` in both `application/views/pos/old_newinvoice.php` and `application/views/pos/edit.php`, and hardened `Point_of_sale::place_order()` to use safe defaults, resolve a fallback related account, and return JSON errors instead of crashing.
**Prevention:** Distinguish read-only totals from editable payment fields in POS modals, and never assume optional posted payment/account fields exist in controller payment flows.
**Resources:** [CodeIgniter Controllers](https://codeigniter.com/userguide3/general/controllers.html), [Bootstrap Forms](https://getbootstrap.com/docs/4.6/components/forms/)

## Quote create/edit showed non-related warehouses for seller - 2026-06-20
**Cause:** The quote pages used `Quote_model::warehouses()` with only location filtering, so seller users could still see generic warehouse options unrelated to their products. The quote add/edit views also rendered the global default warehouse option for sellers.
**Solution:** Updated `application/models/Quote_model.php` so seller users get only warehouses joined through their own `products.seller_id`, and updated `application/views/quotes/newquote.php` plus `application/views/quotes/edit.php` to skip the global default warehouse option for sellers.
**Prevention:** Quote, invoice, and POS seller flows should all source warehouses from product ownership instead of only location-level visibility.
**Resources:** [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## Quote search client needed Select2 search on add/edit - 2026-06-20
**Cause:** The quote create/edit pages still used the old `#customer-box` text autocomplete that rendered an HTML result list, which did not provide the searchable select UX expected on these forms.
**Solution:** Added `Search_products::csearch_select2()` to return JSON customer results with seller filtering, updated `application/views/quotes/newquote.php` and `application/views/quotes/edit.php` to use Select2 for `Search Client`, and adjusted `assets/myjs/control.js` so newly added customers also sync back into a Select2 customer field.
**Prevention:** Use JSON-backed Select2 for searchable customer selectors instead of custom HTML result lists when the page already expects select-style client selection.
**Resources:** [Select2 AJAX](https://select2.org/data-sources/ajax), [CodeIgniter Query Builder](https://codeigniter.com/userguide3/database/query_builder.html)

## Quote create page needed a default selected related client - 2026-06-20
**Cause:** After moving `Search Client` to Select2, the quote create form still opened with an empty client field, so seller users had to search even when they already had related customers.
**Solution:** Added a seller-scoped default customer lookup in `application/controllers/Quote.php` and preloaded that customer as the selected option in `application/views/quotes/newquote.php`, while keeping Select2 search available for changing the client.
**Prevention:** When converting required relational fields to Select2, preload a valid default option if the business flow expects one to be immediately available.
**Resources:** [Select2 Programmatic Control](https://select2.org/programmatic-control/add-select-clear-items)

## Quote create client selector should list clients without auto-selecting one - 2026-06-20
**Cause:** The quote create page was auto-selecting one related client by default, while the Select2 endpoint only returned results after typing. That conflicted with the desired flow of showing the available related clients in the dropdown and letting the user choose one manually.
**Solution:** Removed the default selected client from `application/views/quotes/newquote.php`, removed the unused preload helper from `application/controllers/Quote.php`, and updated `Search_products::csearch_select2()` to return the related client list even when no search term is entered.
**Prevention:** For required searchable selectors, avoid auto-selecting records unless explicitly needed, and let the empty-state dropdown load the valid scoped options directly.
**Resources:** [Select2 AJAX](https://select2.org/data-sources/ajax)
