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
            <h4 class="card-title"><?php echo $this->lang->line('Add New Customer') ?></h4>

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
                <div class="card customer-form-card">

                    <div class="card-content">
                        <div class="card-body">

                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active show" id="base-tab1" data-toggle="tab"
                                       aria-controls="tab1" href="#tab1" role="tab"
                                       aria-selected="true"><?php echo $this->lang->line('Billing Address') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab2" data-toggle="tab" aria-controls="tab2"
                                       href="#tab2" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('Shipping Address') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab4" data-toggle="tab" aria-controls="tab4"
                                       href="#tab4" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('CustomFields') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="base-tab3" data-toggle="tab" aria-controls="tab3"
                                       href="#tab3" role="tab"
                                       aria-selected="false"><?php echo $this->lang->line('Other') . ' ' . $this->lang->line('Settings') ?></a>
                                </li>

                            </ul>
                            <div class="tab-content p-3">
                                <div class="tab-pane active show" id="tab1" role="tabpanel" aria-labelledby="base-tab1">
                                    <div class="form-group row mt-1">

                                        <label class="col-sm-2 col-form-label"
                                               for="name"><?php echo $this->lang->line('Name') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Name"
                                                   class="form-control margin-bottom b_input required" name="name"
                                                   id="mcustomer_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="name"><?php echo $this->lang->line('Company') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Company"
                                                   class="form-control margin-bottom b_input" name="company">
                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="phone"><?php echo $this->lang->line('Phone') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="phone"
                                                   class="form-control margin-bottom required b_input" name="phone"
                                                   id="mcustomer_phone">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label" for="email">Email</label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="email"
                                                   class="form-control margin-bottom required b_input" name="email"
                                                   id="mcustomer_email">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="address"><?php echo $this->lang->line('Address') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="address"
                                                   class="form-control margin-bottom b_input" name="address"
                                                   id="mcustomer_address1">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="city"><?php echo $this->lang->line('City') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="city"
                                                   class="form-control margin-bottom b_input" name="city"
                                                   id="mcustomer_city">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="region"><?php echo $this->lang->line('Region') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Region"
                                                   class="form-control margin-bottom b_input" name="region"
                                                   id="region">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="country"><?php echo $this->lang->line('Country') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Country"
                                                   class="form-control margin-bottom b_input" name="country"
                                                   id="mcustomer_country">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="postbox"><?php echo $this->lang->line('PostBox') ?></label>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="PostBox"
                                                   class="form-control margin-bottom b_input" name="postbox"
                                                   id="postbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="base-tab2">
                                    <div class="customer-copy-toggle">
                                        <input type="checkbox" name="customer1" id="copy_address">
                                        <label for="copy_address"><?php echo $this->lang->line('Same As Billing') ?></label>
                                    </div>

                                    <p class="shipping-note"><?php echo $this->lang->line("leave Shipping Address") ?></p>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="name_s"><?php echo $this->lang->line('Name') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Name"
                                                   class="form-control margin-bottom b_input" name="name_s"
                                                   id="mcustomer_name_s">
                                        </div>
                                    </div>


                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="phone_s"><?php echo $this->lang->line('Phone') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="phone"
                                                   class="form-control margin-bottom b_input" name="phone_s"
                                                   id="mcustomer_phone_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label" for="email_s">Email</label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="email"
                                                   class="form-control margin-bottom b_input" name="email_s"
                                                   id="mcustomer_email_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="address"><?php echo $this->lang->line('Address') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="address_s"
                                                   class="form-control margin-bottom b_input" name="address_s"
                                                   id="mcustomer_address1_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="city_s"><?php echo $this->lang->line('City') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="city"
                                                   class="form-control margin-bottom b_input" name="city_s"
                                                   id="mcustomer_city_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="region_s"><?php echo $this->lang->line('Region') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Region"
                                                   class="form-control margin-bottom b_input" name="region_s"
                                                   id="region_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="country_s"><?php echo $this->lang->line('Country') ?></label>

                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Country"
                                                   class="form-control margin-bottom b_input" name="country_s"
                                                   id="mcustomer_country_s">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="postbox"><?php echo $this->lang->line('PostBox') ?></label>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="PostBox"
                                                   class="form-control margin-bottom b_input" name="postbox_s"
                                                   id="postbox_s">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="base-tab3">
                                    <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                       for="Discount"><?php echo $this->lang->line('Discount') ?> </label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Custom Discount"
                                                   class="form-control margin-bottom b_input" name="discount">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="taxid"><?php echo $this->lang->line('TAX') ?> ID</label>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="TAX ID"
                                                   class="form-control margin-bottom b_input" name="taxid">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="docid"><?php echo $this->lang->line('Document') ?> ID</label>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Document ID"
                                                   class="form-control margin-bottom b_input" name="docid">
                                        </div>
                                    </div>
                                    <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                       for="c_field"><?php echo $this->lang->line('Extra') ?> </label>
                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Custom Field"
                                                   class="form-control margin-bottom b_input" name="c_field">
                                        </div>
                                    </div>



                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="customergroup"><?php echo $this->lang->line('Customer group') ?></label>

                                        <div class="col-sm-6">
                                            <select name="customergroup" class="form-control b_input">
                                                <?php

                                                foreach ($customergrouplist as $row) {
                                                    $cid = $row['id'];
                                                    $title = $row['title'];
                                                    echo "<option value='$cid'>$title</option>";
                                                }
                                                ?>
                                            </select>


                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="currency">Language</label>

                                        <div class="col-sm-6">
                                            <select name="language" class="form-control b_input">

                                                <?php

                                                echo $langs;
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="currency"><?php echo $this->lang->line('customer_login') ?></label>

                                        <div class="col-sm-6">
                                            <select name="c_login" class="form-control b_input">

                                                <option value="1"><?php echo $this->lang->line('Yes') ?></option>
                                                <option value="0"><?php echo $this->lang->line('No') ?></option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label"
                                               for="password_c"><?php echo $this->lang->line('New Password') ?></label>

                                        <div class="col-sm-6">
                                            <input type="text" placeholder="Leave blank for auto generation"
                                                   class="form-control margin-bottom b_input" name="password_c"
                                                   id="password_c">
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane" id="tab4" role="tabpanel" aria-labelledby="base-tab4">

                                 <?php
                                    foreach ($custom_fields as $row) {
                                        if ($row['f_type'] == 'text') { ?>
                                            <div class="form-group row">

                                                <label class="col-sm-2 col-form-label"
                                                       for="docid"><?= $row['name'] ?></label>

                                                <div class="col-sm-8">
                                                    <input type="text" placeholder="<?= $row['placeholder'] ?>"
                                                           class="form-control margin-bottom b_input <?= $row['other'] ?>"
                                                           name="custom[<?= $row['id'] ?>]">
                                                </div>
                                            </div>


                                        <?php }
                                    }
                                    ?>

                                </div>
                                <div id="mybutton">
                                    <input type="submit" id="submit-data"
                                           class="btn btn-lg btn btn-primary margin-bottom round float-xs-right mr-2"
                                           value="<?php echo $this->lang->line('Add customer') ?>"
                                           data-loading-text="Adding...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <input type="hidden" value="customers/addcustomer" id="action-url">
            </form>
        </div>
    </div>
</div>

