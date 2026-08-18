<?php 
$modal_id = isset($modal_id) ? $modal_id : 'addCustomer';
?>
<div class="modal fade" id="<?= $modal_id ?>" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <script>
                var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            </script>
            <form method="post" id="product_action" class="form-horizontal">
                <div class="modal-header bg-gradient-directional-purple white">
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('Add Customer') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                </div>

                <div class="modal-body">
                    <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5><?php echo $this->lang->line('Billing Address') ?></h5>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="name"><?php echo $this->lang->line('Name') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Name" class="form-control margin-bottom" id="mcustomer_name" name="name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="phone"><?php echo $this->lang->line('Phone') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Phone" class="form-control margin-bottom" name="mobile" id="mcustomer_phone" required>
                                </div>
                            </div>  

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="whatsapp_number">Whatsapp Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Whatsapp Number" class="form-control margin-bottom" name="whatsappmobile" id="whatsapp_number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="email"><?php echo $this->lang->line('Email') ?></label>
                                <div class="col-sm-10">
                                    <input type="email" placeholder="Email" class="form-control margin-bottom" name="email" id="mcustomer_email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="address"><?php echo $this->lang->line('Address') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Address" class="form-control margin-bottom " name="address" id="mcustomer_address1">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" placeholder="City" class="form-control margin-bottom" name="city" id="mcustomer_city">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Region" id="region" class="form-control margin-bottom" name="region">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Country" class="form-control margin-bottom" name="country" id="mcustomer_country">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="PostBox" id="postbox" class="form-control margin-bottom" name="postbox">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Company" class="form-control margin-bottom" name="company">
                                </div>

                                <div class="col-sm-6">
                                    <input type="text" placeholder="TAX ID" class="form-control margin-bottom" name="taxid" id="mcustomer_city">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label col-form-label-sm" for="customergroup"><?php echo $this->lang->line('Group') ?></label>
                                <div class="col-sm-10">
                                    <select name="customergroup" class="form-control form-control-sm">
                                        <?php
                                        if (isset($customergrouplist) && is_array($customergrouplist)) {
                                            foreach ($customergrouplist as $row) {
                                                $cid = $row['id'];
                                                $title = $row['title'];
                                                echo "<option value='$cid'>$title</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <h5><?php echo $this->lang->line('Shipping Address') ?></h5>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label style="font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="customer1s" id="copy_address" style="width: 18px; height: 18px; cursor: pointer;">
                                        <?php echo $this->lang->line('Same As Billing') ?>
                                    </label>
                                    <small class="form-text text-muted" style="margin-top: 5px; margin-left: 26px;">
                                        <?php echo $this->lang->line("leave Shipping Address") ?>
                                    </small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="name_s"><?php echo $this->lang->line('Name') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Name" class="form-control margin-bottom" id="mcustomer_name_s" name="name_s">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="phone_s"><?php echo $this->lang->line('Phone') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Phone" class="form-control margin-bottom" name="phone_s" id="mcustomer_phone_s">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="email_s"><?php echo $this->lang->line('Email') ?></label>
                                <div class="col-sm-10">
                                    <input type="email" placeholder="Email" class="form-control margin-bottom" name="email_s" id="mcustomer_email_s">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="address_s"><?php echo $this->lang->line('Address') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Address" class="form-control margin-bottom " name="address_s" id="mcustomer_address1_s">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" placeholder="City" class="form-control margin-bottom" name="city_s" id="mcustomer_city_s">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Region" id="region_s" class="form-control margin-bottom" name="region_s">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Country" class="form-control margin-bottom" name="country_s" id="mcustomer_country_s">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="PostBox" id="postbox_s" class="form-control margin-bottom" name="postbox_s">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (isset($custom_fields_c) && is_array($custom_fields_c)) {
                        foreach ($custom_fields_c as $row) {
                            if ($row['f_type'] == 'text') { ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="docid"><?= $row['name'] ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="<?= $row['placeholder'] ?>" class="form-control margin-bottom b_input" name="custom[<?= $row['id'] ?>]">
                                    </div>
                                </div>
                            <?php }
                        }
                    }
                    ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                    <input type="submit" id="mclient_add" class="btn btn-primary submitBtn" value="ADD"/>
                </div>
            </form> 
        </div>
    </div>
</div>
