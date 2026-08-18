<style>
    .customer-form-ui .customer-form-card {
        border: 0;
        box-shadow: none;
        background: transparent;
        margin-bottom: 0;
    }

    .customer-form-ui .customer-form-card > .card-content > .card-body {
        padding: 0;
    }

    .customer-form-ui .nav.nav-tabs {
        gap: 0.5rem;
        border-bottom: 1px solid #e3ebf3;
    }

    .customer-form-ui .nav.nav-tabs .nav-link {
        border: 1px solid transparent;
        border-radius: 10px 10px 0 0;
        color: #52606d;
        font-weight: 600;
        padding: 0.85rem 1.25rem;
    }

    .customer-form-ui .nav.nav-tabs .nav-link.active,
    .customer-form-ui .nav.nav-tabs .nav-link.show {
        color: #102a43;
        background: #f8fbff;
        border-color: #e3ebf3 #e3ebf3 #fff;
    }

    .customer-form-ui .tab-content {
        border: 1px solid #e3ebf3;
        border-top: 0;
        border-radius: 0 0 12px 12px;
        background: #fff;
        padding: 1.5rem !important;
    }

    .customer-form-ui .tab-pane .form-group.row {
        align-items: center;
        margin-bottom: 1.1rem;
    }

    .customer-form-ui .col-form-label {
        color: #243b53;
        font-weight: 600;
    }

    .customer-form-ui .form-control {
        min-height: 44px;
        border: 1px solid #d7e2ef;
        border-radius: 10px;
        box-shadow: none;
    }

    .customer-form-ui .form-control:focus {
        border-color: #4c9ffe;
        box-shadow: 0 0 0 0.15rem rgba(76, 159, 254, 0.15);
    }

    .customer-form-ui .customer-copy-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.4rem;
    }

    .customer-form-ui .customer-copy-toggle input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: #2f80ed;
    }

    .customer-form-ui .customer-copy-toggle label {
        margin: 0;
        color: #243b53;
        font-size: 1rem;
        font-weight: 600;
    }

    .customer-form-ui .shipping-note {
        margin: 0 0 1.25rem 1.75rem;
        color: #5f6c7b;
        font-size: 0.95rem;
    }

    .customer-form-ui #mybutton {
        display: flex;
        justify-content: flex-end;
        padding-top: 1rem;
    }

    .customer-form-ui #mybutton .btn {
        min-width: 180px;
        border-radius: 10px;
    }

    @media (max-width: 767.98px) {
        .customer-form-ui .nav.nav-tabs .nav-link {
            padding: 0.75rem 1rem;
        }

        .customer-form-ui .shipping-note {
            margin-left: 0;
        }
    }
</style>

<div class="content-body customer-form-ui">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo $this->lang->line('Edit Customer Details') ?></h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <form method="post" id="data_form" class="form-horizontal">
                <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                <div class="card customer-form-card">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active show" id="base-tab1" data-toggle="tab"
                                       aria-controls="tab1" href="#tab1" role="tab"
                                       aria-selected="true"><?php echo $this->lang->line('Billing Address'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab2" data-toggle="tab" aria-controls="tab2"
                                       href="#tab2" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('Shipping Address'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab4" data-toggle="tab" aria-controls="tab4"
                                       href="#tab4" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('CustomFields'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab3" data-toggle="tab" aria-controls="tab3"
                                       href="#tab3" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('Other') . ' ' . $this->lang->line('Settings'); ?></a>
                                </li>
                            </ul>
                            <div class="tab-content p-3">
                                <div class="tab-pane active show" id="tab1" role="tabpanel" aria-labelledby="base-tab1">
                                    <div class="form-group row mt-1">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_name"><?php echo $this->lang->line('Name'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Name"
                                                   class="form-control margin-bottom b_input required" name="name"
                                                   value="<?php echo $customer['username']; ?>" id="mcustomer_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="company"><?php echo $this->lang->line('Company'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Company"
                                                   class="form-control margin-bottom b_input" name="company"
                                                   value="<?php echo $customer['company']; ?>" id="company">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_phone">Bussiness Owner</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="phone"
                                                   class="form-control margin-bottom required b_input" name="phone"
                                                   value="<?php echo $customer['mobile']; ?>" id="mcustomer_phone">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="whatsappmobile">Whatsapp Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Whatsapp Number"
                                                   class="form-control margin-bottom b_input" name="whatsappmobile"
                                                   value="<?php echo $customer['whatsapp_mobile']; ?>" id="whatsappmobile">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="managermobile">Manager Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Manager Number"
                                                   class="form-control margin-bottom b_input" name="managermobile"
                                                   value="<?php echo $customer['manager_mobile']; ?>" id="managermobile">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_email">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="email"
                                                   class="form-control margin-bottom required b_input" name="email"
                                                   value="<?php echo $customer['email']; ?>" id="mcustomer_email">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_address1"><?php echo $this->lang->line('Address'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="address"
                                                   class="form-control margin-bottom b_input" name="address"
                                                   value="<?php echo $customer['address']; ?>" id="mcustomer_address1">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_city"><?php echo $this->lang->line('City'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="city"
                                                   class="form-control margin-bottom b_input" name="city"
                                                   value="<?php echo $customer['city']; ?>" id="mcustomer_city">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="region"><?php echo $this->lang->line('Region'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Region"
                                                   class="form-control margin-bottom b_input" name="region"
                                                   value="<?php echo $customer['region']; ?>" id="region">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_country"><?php echo $this->lang->line('Country'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Country"
                                                   class="form-control margin-bottom b_input" name="country"
                                                   value="<?php echo $customer['country']; ?>" id="mcustomer_country">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="postbox"><?php echo $this->lang->line('PostBox'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="PostBox"
                                                   class="form-control margin-bottom b_input" name="postbox"
                                                   value="<?php echo $customer['postbox']; ?>" id="postbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="base-tab2">
                                    <div class="customer-copy-toggle">
                                        <input type="checkbox" name="customer1" id="copy_address">
                                        <label for="copy_address"><?php echo $this->lang->line('Same As Billing'); ?></label>
                                    </div>
                                    <p class="shipping-note"><?php echo $this->lang->line("leave Shipping Address"); ?></p>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_name_s"><?php echo $this->lang->line('Name'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Name"
                                                   class="form-control margin-bottom b_input" name="name_s"
                                                   value="<?php echo $customer['name_s']; ?>" id="mcustomer_name_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_phone_s"><?php echo $this->lang->line('Phone'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="phone"
                                                   class="form-control margin-bottom b_input" name="phone_s"
                                                   value="<?php echo $customer['phone_s']; ?>" id="mcustomer_phone_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_email_s">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="email"
                                                   class="form-control margin-bottom b_input" name="email_s"
                                                   value="<?php echo $customer['email_s']; ?>" id="mcustomer_email_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_address1_s"><?php echo $this->lang->line('Address'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="address_s"
                                                   class="form-control margin-bottom b_input" name="address_s"
                                                   value="<?php echo $customer['address_s']; ?>" id="mcustomer_address1_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_city_s"><?php echo $this->lang->line('City'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="city"
                                                   class="form-control margin-bottom b_input" name="city_s"
                                                   value="<?php echo $customer['city_s']; ?>" id="mcustomer_city_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="region_s"><?php echo $this->lang->line('Region'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Region"
                                                   class="form-control margin-bottom b_input" name="region_s"
                                                   value="<?php echo $customer['region_s']; ?>" id="region_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="mcustomer_country_s"><?php echo $this->lang->line('Country'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Country"
                                                   class="form-control margin-bottom b_input" name="country_s"
                                                   value="<?php echo $customer['country_s']; ?>" id="mcustomer_country_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="postbox_s"><?php echo $this->lang->line('PostBox'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="PostBox"
                                                   class="form-control margin-bottom b_input" name="postbox_s"
                                                   value="<?php echo $customer['postbox_s']; ?>" id="postbox_s">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab4" role="tabpanel" aria-labelledby="base-tab4">
                                    <?php foreach ($custom_fields as $row) {
                                        if ($row['f_type'] == 'text') { ?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label" for="custom_<?php echo $row['id']; ?>"><?= $row['name']; ?></label>
                                                <div class="col-sm-8">
                                                    <input type="text" placeholder="<?= $row['placeholder']; ?>"
                                                           class="form-control margin-bottom b_input <?= $row['other']; ?>"
                                                           name="custom[<?= $row['id']; ?>]"
                                                           value="<?= $row['data']; ?>"
                                                           id="custom_<?php echo $row['id']; ?>">
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="base-tab3">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="discount"><?php echo $this->lang->line('Discount'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Custom Discount"
                                                   class="form-control margin-bottom b_input" name="discount"
                                                   value="<?php echo $customer['discount_c']; ?>" id="discount">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="taxid"><?php echo $this->lang->line('TAX'); ?> ID</label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="TAX ID"
                                                   class="form-control margin-bottom b_input" name="taxid"
                                                   value="<?php echo $customer['taxid']; ?>" id="taxid">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="docid"><?php echo $this->lang->line('Document'); ?> ID</label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Document ID"
                                                   class="form-control margin-bottom b_input" name="docid"
                                                   value="<?php echo $customer['docid']; ?>" id="docid">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="c_field"><?php echo $this->lang->line('Extra'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Custom Field"
                                                   class="form-control margin-bottom b_input" name="c_field"
                                                   value="<?php echo $customer['custom1']; ?>" id="c_field">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="customergroup"><?php echo $this->lang->line('Customer group'); ?></label>
                                        <div class="col-sm-6">
                                            <select name="customergroup" class="form-control b_input" id="customergroup">
                                                <?php foreach ($customergrouplist as $row) {
                                                    $selected = ((string)$row['id'] === (string)$customer['gid']) ? 'selected' : '';
                                                    echo "<option value='{$row['id']}' $selected>{$row['title']}</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="paymentterm">Payment Term</label>
                                        <div class="col-sm-6">
                                            <select name="paymentterm" class="form-control b_input" id="paymentterm">
                                                <option value="7" <?php echo ((string)$customer['paymentterm'] === '7') ? 'selected' : ''; ?>>1 Week</option>
                                                <option value="10" <?php echo ((string)$customer['paymentterm'] === '10') ? 'selected' : ''; ?>>10 Days</option>
                                                <option value="15" <?php echo ((string)$customer['paymentterm'] === '15') ? 'selected' : ''; ?>>15 Days</option>
                                                <option value="30" <?php echo ((string)$customer['paymentterm'] === '30') ? 'selected' : ''; ?>>1 Month</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="repeatorder">Repeat Order</label>
                                        <div class="col-sm-6">
                                            <select name="repeatorder" class="form-control b_input" id="repeatorder">
                                                <option value="7" <?php echo ((string)$customer['repeatorder'] === '7') ? 'selected' : ''; ?>>1 Week</option>
                                                <option value="10" <?php echo ((string)$customer['repeatorder'] === '10') ? 'selected' : ''; ?>>10 Days</option>
                                                <option value="15" <?php echo ((string)$customer['repeatorder'] === '15') ? 'selected' : ''; ?>>15 Days</option>
                                                <option value="30" <?php echo ((string)$customer['repeatorder'] === '30') ? 'selected' : ''; ?>>1 Month</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="document_file">Upload Document</label>
                                        <div class="col-sm-6">
                                            <input type="file" class="form-control margin-bottom b_input" name="document_file" id="document_file">
                                            <?php if (!empty($customer['document_file'])) { ?>
                                                <small class="text-muted">Current: <?php echo $customer['document_file']; ?></small>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div id="mybutton">
                                    <input type="submit" id="submit-data"
                                           class="btn btn-lg btn-primary margin-bottom round float-xs-right mr-2"
                                           value="Update customer"
                                           data-loading-text="Updating...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" value="customers/editcustomer" id="action-url">
            </form>
        </div>
    </div>
</div>
