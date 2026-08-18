<link rel="stylesheet" type="text/css"
      href="<?= assets_url() ?>app-assets/<?= LTR ?>/core/menu/menu-types/horizontal-menu.css">
	  <link rel="stylesheet" type="text/css"
      href="<?= assets_url() ?>assets/css/all.css">
</head>
<body class="horizontal-layout horizontal-menu 2-columns menu-expanded" data-open="click" data-menu="horizontal-menu"
      data-col="2-columns">
<span id="hdata"
      data-df="<?php echo $this->config->item('dformat2'); ?>"
      data-curr="<?php echo currency($this->aauth->get_user()->loc); ?>"></span>
<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-static-top navbar-dark bg-gradient-x-grey-blue navbar-border navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a
                            class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item"><a class="navbar-brand" href="<?= base_url() ?>dashboard/"><img
                                class="brand-logo" alt="logo"
                                src="<?php echo base_url(); ?>userfiles/theme/logo-header.png">
                    </a></li>
                <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse"
                                                  data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                                                              href="#"><i class="ft-menu"></i></a></li>


                    <li class="dropdown  nav-item"><a class="nav-link nav-link-label" href="#"
                                                      data-toggle="dropdown"><i
                                    class="ficon ft-map-pin success"></i></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-left" style="width: 250px;">
                            <?php
                            $current_role = $this->aauth->get_user() ? $this->aauth->get_user()->roleid : 0;
                            $is_switched = $this->session->userdata('admin_original_user_id') ? true : false;

                            if ($current_role == 1 || $is_switched) {
                                // Retrieve all active sellers/stores
                                $ci =& get_instance();
                                $ci->load->database();
                                $stores = $ci->db->select('u.id as user_id, sd.store_name')
                                    ->join('users_groups ug', 'ug.user_id = u.id')
                                    ->join('seller_data sd', 'sd.user_id = u.id')
                                    ->where('ug.group_id', 4)
                                    ->where('u.active', 1)
                                    ->get('users u')
                                    ->result_array();
                                
                                if ($is_switched) {
                                    $switched_seller_id = $this->session->userdata('admin_switched_seller_id');
                                    $active_seller_data = $ci->db->select('store_name')->where('user_id', $switched_seller_id)->get('seller_data')->row_array();
                                    $active_store_name = isset($active_seller_data['store_name']) ? $active_seller_data['store_name'] : 'Unknown Store';
                                    ?>
                                    <li class="dropdown-menu-header" style="background-color: #ffebd2; border-bottom: 1px solid #ffd2a0;">
                                        <h6 class="dropdown-header m-0 text-center text-warning" style="font-weight: bold; color: #cc6600 !important;">
                                            <i class="fa fa-eye"></i> Viewing: <?php echo htmlspecialchars($active_store_name); ?>
                                        </h6>
                                    </li>
                                    <li style="border-bottom: 1px solid #eaeaea;">
                                        <a href="<?php echo base_url('settings/switch_back_to_admin'); ?>" class="dropdown-item text-center text-danger font-weight-bold" style="padding: 10px 15px;">
                                            <i class="fa fa-undo"></i> Switch Back to Admin
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0"><span class="grey darken-2"><i class="fa fa-store"></i> Switch Store</span></h6>
                                </li>
                                <div style="max-height: 250px; overflow-y: auto;">
                                    <?php foreach ($stores as $row) { 
                                        $is_current = ($is_switched && $this->session->userdata('admin_switched_seller_id') == $row['user_id']);
                                        $style = $is_current ? 'background-color: #f3f9f3; font-weight: bold; color: #28a745;' : '';
                                        ?>
                                        <li>
                                            <a href="<?php echo base_url('settings/switch_to_seller?id=' . $row['user_id']); ?>" class="dropdown-item" style="<?php echo $style; ?>">
                                                <i class="fa <?php echo $is_current ? 'fa-check-circle' : 'fa-store'; ?>"></i> <?php echo htmlspecialchars($row['store_name']); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </div>
                                <?php
                            } else {
                                // Regular business location display for non-admin/non-switched users
                                ?>
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0"><span class="grey darken-2"><i class="ficon ft-map-pin success"></i><?php echo $this->lang->line('business_location') ?></span></h6>
                                </li>
                                <li class="dropdown-menu-footer">
                                    <span class="dropdown-item text-muted text-center blue"><?php 
                                        $loc = location($this->aauth->get_user()->loc);
                                        echo $loc['cname']; 
                                    ?></span>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <?php    if ($this->aauth->premission(12)) { ?> <li class="nav-item d-none d-md-block nav-link "><a href="<?= base_url() ?>pos_invoices/create"
                                                                        class="btn btn-info btn-md t_tooltip"
                                                                        title="Access POS"><i
                                    class="icon-handbag"></i><?php echo $this->lang->line('POS') ?> </a>
                    </li>  <?php    } ?>
                    <li class="nav-item nav-search"><a class="nav-link nav-link-search" href="#" aria-haspopup="true"
                                                       aria-expanded="false" id="search-input"><i
                                    class="ficon ft-search"></i></a>
                        <div class="search-input">
                            <input class="input" type="text"
                                   placeholder="<?php echo $this->lang->line('Search Customer') ?>"
                                   id="head-customerbox">
                        </div>
                        <div id="head-customerbox-result" class="dropdown-menu ml-5"
                             aria-labelledby="search-input"></div>
                    </li>
					<li class="nav-item d-none d-md-block nav-link "><a href="<?= base_url() ?>transactions/add"
                                                                        class="btn btn-success btn-md t_tooltip"
                                                                        title="Add Expenses"><i class="ft-external-link"></i>Add Expense </a>
                    </li>
					
                </ul>

                <ul class="nav navbar-nav float-right"><?php if ($this->aauth->get_user()->roleid == 1) { ?>
                        <li class="dropdown nav-item mega-dropdown"><a class="dropdown-toggle nav-link " href="#"
                                                                       data-toggle="dropdown"> <?php echo $this->lang->line('admin_settings') ?> </a>
                            <ul class="mega-dropdown-menu dropdown-menu row" style="display: flex !important;">
                                <li class="col-md-3">
                                    <div id="accordionWrap" role="tablist" aria-multiselectable="true">
                                        <div class="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading1" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap" href="#accordion1"
                                                   aria-controls="accordion1"><i
                                                            class="fa fa-leaf"></i> <?php echo $this->lang->line('business_settings')  ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion1" role="tabpanel"
                                                 aria-labelledby="heading1" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/company"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('company_settings') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>locations"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Business Locations') ?>
                                                            </a></li><li><select  class="dropdown-item" onchange="javascript:location.href = baseurl+'settings/switch_location?id='+this.value;"><?php
                        $user_loc = $this->aauth->get_user()->loc;
                        if ($user_loc) {
                            $loc = location($user_loc);
                            if (!empty($loc['id'])) {
                                echo ' <option value="' . $loc['id'] . '"> *' . $loc['cname'] . '*</option>';
                            }
                        }

                        $loc = locations();
                        foreach ($loc as $row) {
                            echo ' <option value="' . $row['id'] . '"> ' . $row['cname'] . '</option>';
                        }
                        echo ' <option value="0">Master/Default</option>';
                        ?></select></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>tools/setgoals"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Set Goals') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading2" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap" href="#accordion2"
                                                   aria-controls="accordion2"> <i
                                                            class="fa fa-calendar"></i><?php echo $this->lang->line('Localisation') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion2" role="tabpanel"
                                                 aria-labelledby="heading2" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/currency"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Currency') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/language"><i
                                                                        class="ft-chevron-right"></i>Languages</a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/dtformat"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Date & Time Format') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/theme"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Theme') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading3" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap" href="#accordion3"
                                                   aria-controls="accordion3"> <i
                                                            class="fa fa-lightbulb-o"></i><?php echo $this->lang->line('miscellaneous_settings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion3" role="tabpanel"
                                                 aria-labelledby="heading3" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>webupdate"><i
                                                                        class="ft-chevron-right"></i> Software
                                                                Update</a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/email"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Email Config') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>transactions/categories"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Transaction Categories') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/misc_automail"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('EmailAlert') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/about"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('About') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </li>
                                <li class="col-md-3">

                                    <div id="accordionWrap1" role="tablist" aria-multiselectable="true">
                                        <div class="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading4" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap1" href="#accordion4"
                                                   aria-controls="accordion4"><i
                                                            class="fa fa-fire"></i><?php echo $this->lang->line('AdvancedSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion4" role="tabpanel"
                                                 aria-labelledby="heading4" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>restapi"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('REST API') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>cronjob"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Automatic Corn Job') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/custom_fields"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('CustomFields') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/dual_entry"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('DualEntryAccounting') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/logdata"><i
                                                                        class="ft-chevron-right"></i> Application
                                                                Activity Log</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/debug"><i
                                                                        class="ft-chevron-right"></i> Debug Mode </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading2" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap1" href="#accordion5"
                                                   aria-controls="accordion5"> <i
                                                            class="fa fa-shopping-cart"></i><?php echo $this->lang->line('BillingSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion5" role="tabpanel"
                                                 aria-labelledby="heading5" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>              <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/billing_settings"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('billing_settings') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/discship"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('DiscountShipping') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/prefix"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Prefix') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/billing_terms"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Billing Terms') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/automail"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Auto Email SMS') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/warehouse"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('DefaultWarehouse') ?>
                                                            </a></li>

                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/pos_style"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('POSStyle') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading6" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap1" href="#accordion6"
                                                   aria-controls="accordion6"><i
                                                            class="fa fa-scissors"></i><?php echo $this->lang->line('TaxSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion6" role="tabpanel"
                                                 aria-labelledby="heading6" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/tax"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Tax') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/taxslabs"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('OtherTaxSettings') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </li>
                                <li class="col-md-3">

                                    <div id="accordionWrap2" role="tablist" aria-multiselectable="true">
                                        <div class="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading7" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap2" href="#accordion7"
                                                   aria-controls="accordion7"><i
                                                            class="fa fa-flask"></i><?php echo $this->lang->line('ProductsSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion7" role="tabpanel"
                                                 aria-labelledby="heading7" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>units"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Measurement Unit') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>units/variations"><i
                                                                        class="ft-chevron-right"></i> Attribute
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>units/variables"><i
                                                                        class="ft-chevron-right"></i> Attribute Value
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading8" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap2" href="#accordion8"
                                                   aria-controls="accordion8"> <i
                                                            class="fa fa-money"></i><?php echo $this->lang->line('Payment Settings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion8" role="tabpanel"
                                                 aria-labelledby="heading8" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways/settings"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Payment Settings') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Payment Gateways') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways/currencies"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Payment Currencies') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways/exchange"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Currency Exchange') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways/bank_accounts"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Bank Accounts') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading9" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap2" href="#accordion9"
                                                   aria-controls="accordion9"><i
                                                            class="fa fa-umbrella"></i><?php echo $this->lang->line('CRMHRMSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion9" role="tabpanel"
                                                 aria-labelledby="heading9" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>employee/auto_attendance"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('SelfAttendance')  ?>
                                                            </a></li>

                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/registration"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('CRMSettings') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>plugins/recaptcha"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Security') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/tickets"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Support Tickets') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </li>


                                <li class="col-md-3">

                                    <div id="accordionWrap3" role="tablist" aria-multiselectable="true">
                                        <div class="card border-0 box-shadow-0 collapse-icon accordion-icon-rotate">
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading10" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap3" href="#accordion10"
                                                   aria-controls="accordion10"><i
                                                            class="fa fa-magic"></i><?php echo $this->lang->line('PluginsSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion10" role="tabpanel"
                                                 aria-labelledby="heading10" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>plugins/recaptcha"><i
                                                                        class="ft-chevron-right"></i>reCaptcha Security</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>plugins/shortner"><i
                                                                        class="ft-chevron-right"></i> URL Shortener</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>plugins/twilio"><i
                                                                        class="ft-chevron-right"></i> SMS Configuration</a>
                                                        </li>

														<li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>plugins/whatsapp"><i
                                                                        class="ft-chevron-right"></i> Whatsapp API Configuration</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>paymentgateways/exchange"><i
                                                                        class="ft-chevron-right"></i>Currency Exchange
                                                                API</a></li>
                                                        <?php plugins_checker(); ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading11" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap3" href="#accordion11"
                                                   aria-controls="accordion11"> <i
                                                            class="fa fa-eye"></i><?php echo $this->lang->line('TemplatesSettings') ?>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion11" role="tabpanel"
                                                 aria-labelledby="heading8" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>templates/email"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Email') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>templates/whatsapp"><i
                                                                        class="ft-chevron-right"></i>Whatsapp Template</a></li> 

																	<!--	<li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>templates/sms"><i
                                                                        class="ft-chevron-right"></i> SMS</a></li> -->

																		<li><a class="dropdown-item"
                                                               href="#"><i
                                                                        class="ft-chevron-right"></i> SMS</a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/print_invoice"><i
                                                                        class="ft-chevron-right"></i> <?php echo $this->lang->line('Print Invoice') ?>
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>settings/theme"><i
                                                                        class="ft-chevron-right"></i><?php echo $this->lang->line('Theme') ?>
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="card-header p-0 pb-1 border-0 mt-1" id="heading12" role="tab">
                                                <a class=" text-uppercase black" data-toggle="collapse"
                                                   data-parent="#accordionWrap3" href="#accordion12"
                                                   aria-controls="accordion12"><i
                                                            class="fa fa-print"></i>POS Printers</a>
                                                </a></div>
                                            <div class="card-collapse collapse mb-1 " id="accordion12" role="tabpanel"
                                                 aria-labelledby="heading12" aria-expanded="true">
                                                <div class="card-content">
                                                    <ul>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>printer/add"><i
                                                                        class="ft-chevron-right"></i>Add Printer</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               href="<?php echo base_url(); ?>printer"><i
                                                                        class="ft-chevron-right"></i> List Printers</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </li>


                            </ul>
                        </li>       <?php } ?>
                    <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#"
                                                                           data-toggle="dropdown"><i
                                    class="ficon ft-bell"></i><span
                                    class="badge badge-pill badge-default badge-danger badge-default badge-up"
                                    id="taskcount">0</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <h6 class="dropdown-header m-0"><span
                                            class="grey darken-2"><?php echo $this->lang->line('Pending Tasks') ?></span><span
                                            class="notification-tag badge badge-default badge-danger float-right m-0"><?=$this->lang->line('New') ?></span>
                                </h6>
                            </li>
                            <li class="scrollable-container media-list" id="tasklist"></li>
                            <li class="dropdown-menu-footer"><a class="dropdown-item text-muted text-center"
                                                                href="<?php echo base_url('manager/todo') ?>"><?php echo $this->lang->line('Manage tasks') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#"
                                                                           data-toggle="dropdown"><i
                                    class="ficon ft-mail"></i><span
                                    class="badge badge-pill badge-default badge-info badge-default badge-up"><?php echo $this->aauth->count_unread_pms() ?></span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <h6 class="dropdown-header m-0"><span
                                            class="grey darken-2"><?php echo $this->lang->line('Messages') ?></span><span
                                            class="notification-tag badge badge-default badge-warning float-right m-0"><?php echo $this->aauth->count_unread_pms() ?><?php echo $this->lang->line('new') ?></span>
                                </h6>
                            </li>
                            <li class="scrollable-container media-list">
                                <?php $list_pm = $this->aauth->list_pms(6, 0, $this->aauth->get_user()->id, false);

                                foreach ($list_pm as $row) {

                                    echo '<a href="' . base_url('messages/view?id=' . $row->pid) . '">
                      <div class="media">
                        <div class="media-left"><span class="avatar avatar-sm  rounded-circle"><img src="' . base_url('userfiles/employee/' . $row->picture) . '" alt="avatar"><i></i></span></div>
                        <div class="media-body">
                          <h6 class="media-heading">' . $row->name . '</h6>
                          <p class="notification-text font-small-3 text-muted">' . $row->{'title'} . '</p><small>
                            <time class="media-meta text-muted" datetime="' . $row->{'date_sent'} . '">' . $row->{'date_sent'} . '</time></small>
                        </div>
                      </div></a>';
                                } ?>    </li>
                            <li class="dropdown-menu-footer"><a class="dropdown-item text-muted text-center"
                                                                href="<?php echo base_url('messages') ?>"><?php echo $this->lang->line('Read all messages') ?></a>
                            </li>
                        </ul>
                    </li>
                
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                                                                  href="#" data-toggle="dropdown"><span
                                    class="avatar avatar-online"><img
                                        src="<?php echo base_url('userfiles/employee/thumbnail/' . $this->aauth->get_user()->picture) ?>"
                                        alt="avatar"><i></i></span><span
                                    class="user-name">Hello, <?php
                                        $user = $this->aauth->get_user();
                                        $label = '';
                                        if (!empty($user->email)) {
                                            $label = $user->email;
                                        } elseif (!empty($user->username)) {
                                            $label = $user->username;
                                        } elseif (!empty($user->mobile)) {
                                            $label = $user->mobile;
                                        }
                                        echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
                                    ?></span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if($this->aauth->user_can('profile-view')):?>
                            <a class="dropdown-item"
                                href="<?php echo base_url(); ?>user/profile"><i
                                class="ft-user"></i> <?php echo $this->lang->line('Profile') ?>
                            </a>
                            <?php endif; 
                            if($this->aauth->user_can('attendance-view')):
                            ?>
                            <a href="<?php echo base_url(); ?>user/attendance"
                               class="dropdown-item"><i
                                class="fa fa-list-ol"></i><?php echo $this->lang->line('Attendance') ?>
                            </a>
                            <?php endif;
                            if($this->aauth->user_can('holiday-view')):
                            ?>
                            <a href="<?php echo base_url(); ?>user/holidays"
                               class="dropdown-item"><i
                                class="fa fa-hotel"></i><?php echo $this->lang->line('Holidays') ?>
                            </a>
                            <?php endif;?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo base_url('user/logout'); ?>"><i
                                        class="ft-power"></i> <?php echo $this->lang->line('Logout') ?></a>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</nav>

<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- Horizontal navigation-->
<div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-light navbar-without-dd-arrow navbar-shadow menu-border"
     role="navigation" data-menu="menu-wrapper">
    <!-- Horizontal menu content-->
    <div class="navbar-container main-menu-content" data-menu="menu-container">

        <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
            <?php
            $menu_user_id = $this->session->userdata('id') ?: $this->session->userdata('user_id');
            $menu_group_id = null;
            if (!empty($menu_user_id)) {
                $menu_row = $this->db->select('group_id')
                    ->where('user_id', (int)$menu_user_id)
                    ->get('users_groups')
                    ->row_array();
                $menu_group_id = $menu_row['group_id'] ?? null;
            }
            $show_dashboard = true;
            if ((int)$menu_group_id === 4) {
                $show_dashboard = $this->aauth->user_can('dashboard-view');
            }
            ?>
            <?php if ($show_dashboard): ?>
                <li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>dashboard/"><i
                                class="icon-speedometer"></i><span><?= $this->lang->line('Dashboard') ?></span></a>
                </li>
            <?php endif; ?>
            <?php
            if ($this->aauth->premission(1)) { 
                 if($this->aauth->user_can_any([
                    'pos-invoice-view','pos-invoice-create','sales-view','new-invoice','trash-invoice',
                    'quotes-view','quotes-create','chalan-view','chalan-create','credit-notes-view'
                    ])){
            ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-basket-loaded"></i><span><?php echo $this->lang->line('sales') ?></span></a>
                    <ul class="dropdown-menu">
                        <?php    if ($this->aauth->premission(12)) { 
                             if($this->aauth->user_can_any([
                                'pos-invoice-view','pos-invoice-create'
                            ])){
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-paper-plane"></i><?php echo $this->lang->line('pos sales') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('pos-invoice-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>pos_invoices/create"
                                    data-toggle="dropdown"><?php echo $this->lang->line('New Invoice'); ?></a>
                                </li>
                                <?php endif;
                                  if($this->aauth->user_can('pos-invoice-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>pos_invoices"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Manage Invoices'); ?></a>
                                </li> 
                                <?php endif; ?>
								

                            </ul>
                        </li>
                        <?php 
                            }
                        }
                         if($this->aauth->user_can_any(['sales-view','new-invoice','trash-invoice'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-basket"></i><?php echo $this->lang->line('sales') ?></a>
                            <ul class="dropdown-menu">
                                <?php if ($this->aauth->user_can('new-invoice')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>invoices/create"
                                    data-toggle="dropdown"><?php echo $this->lang->line('New Invoice'); ?></a>
                                </li>
                                <?php 
                                endif;
                                if ($this->aauth->user_can('sales-view')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>invoices"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Manage Invoices'); ?></a>
								</li>
								<?php 
                                endif;
                                if ($this->aauth->user_can('trash-invoice')):?>
								<li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>invoices/trashinvoices"
                                    data-toggle="dropdown">Trash invoices</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; 
                        if($this->aauth->user_can_any(['quotes-view','quotes-create'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-call-out"></i><?php echo $this->lang->line('Quotes') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('quotes-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>quote/create"
                                    data-toggle="dropdown"><?php echo $this->lang->line('New Quote'); ?></a>
                                </li>
                                <?php 
                                    endif;
                                    if($this->aauth->user_can('quotes-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>quote"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Manage Quotes'); ?></a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif;
                        if($this->aauth->user_can_any(['chalan-view','chalan-create'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="ft-radio"></i>Chalan</a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('chalan-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>chalan/create"
                                    data-toggle="dropdown">New Chalan</a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('chalan-view')):
                                ?>

                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>chalan"
                                    data-toggle="dropdown">Manage Chalan</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                         <?php endif; 
                        if($this->aauth->user_can('credit-notes-view')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>stockreturn/creditnotes"><i
                                        class="icon-screen-tablet"></i><?php echo $this->lang->line('Credit Notes'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php 
                    }
            }
           if ($this->aauth->premission(2)) { 
            //start stock
                if($this->aauth->user_can_any([
                    'product-view','product-create','category-view','warehouse-view','stock-transfer',
                    'indent-view','indent-create','order-view','order-create','supplier-record-view',
                    'customer-record-view','supplier-view','supplier-create','custom-label','standard-label'
                ])){ 
            ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="ft-layers"></i><span><?php echo $this->lang->line('Stock') ?></span></a>
                    <ul class="dropdown-menu">
                        <?php
                            if($this->aauth->user_can_any(['product-view','product-create'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="ft-list"></i> <?php echo $this->lang->line('Items Manager') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('product-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>products/add"
                                    data-toggle="dropdown"> <?php echo $this->lang->line('New Product'); ?></a>
                                </li>
                                <?php 
                                    endif;
                                    if($this->aauth->user_can('product-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>products"
                                    data-toggle="dropdown"><?= $this->lang->line('Manage Products'); ?></a>
                                </li>
                                <?php endif; ?>

                            </ul>
                        </li>
                        <?php
                            endif;
                            if($this->aauth->user_can('category-view')):
                        ?>
                        <li data-menu=""><a class="dropdown-item"
                            href="<?php echo base_url(); ?>productcategory"
                            data-toggle="dropdown"><i
                            class="ft-umbrella"></i><?php echo $this->lang->line('Product Categories'); ?>
                            </a>
                        </li>
                        <?php
                            endif;
                            if($this->aauth->user_can('warehouse-view')):
                        ?>
                        <li data-menu=""><a class="dropdown-item"
                            href="<?php echo base_url(); ?>productcategory/warehouse"
                            data-toggle="dropdown"><i
                            class="ft-sliders"></i><?php echo $this->lang->line('Warehouses'); ?></a>
                        </li>
						 <li data-menu=""><a class="dropdown-item"
                            href="<?php echo base_url(); ?>productcategory/wastage"
                            data-toggle="dropdown"><i
                            class="ft-sliders"></i>Wastage</a>
                        </li>
                        <?php 
                            endif;
                            if($this->aauth->user_can('stock-transfer')):
                        ?>
                        <li data-menu=""><a class="dropdown-item"
                            href="<?php echo base_url(); ?>products/stock_transfer"
                            data-toggle="dropdown"><i
                            class="ft-wind"></i><?php echo $this->lang->line('Stock Transfer'); ?></a>
                        </li>
                        <?php endif; ?>
						
						<?php if($this->aauth->user_can_any(['indent-view','indent-create'])): ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                    class="ft-radio"></i>Indent</a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('indent-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>indent/create"
                                    data-toggle="dropdown">New Indent</a>
                                </li>
                                <?php
                                endif;
                                 if($this->aauth->user_can('indent-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>indent"
                                    data-toggle="dropdown">Manage Indent</a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
						<?php endif; ?>
                        <?php if($this->aauth->user_can_any(['order-view','order-create'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-handbag"></i> Purchase Quotation</a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('order-create')):?>
                                <!--<li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>purchase/create"
                                    data-toggle="dropdown"> <?php echo $this->lang->line('New Order'); ?></a>
                                </li>-->
                                <?php
                                endif;
                                if($this->aauth->user_can('order-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>purchase/sellerquote"
                                    data-toggle="dropdown"><?= $this->lang->line('Manage Orders'); ?></a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-handbag"></i> <?php echo $this->lang->line('Purchase Order') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('order-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>purchase/create"
                                    data-toggle="dropdown"> <?php echo $this->lang->line('New Order'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('order-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>purchase"
                                    data-toggle="dropdown"><?= $this->lang->line('Manage Orders'); ?></a>
                                </li>
                                <?php endif; ?>
								
								 <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>purchase/challan"
                                    data-toggle="dropdown"> Purchase Challan</a>
                                </li> 
								
								<li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>purchase/received"
                                    data-toggle="dropdown"> Purchase Received</a>
                                </li>
                            </ul>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can_any(['supplier-record-view','customer-record-view'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-puzzle"></i> <?php echo $this->lang->line('Stock Return') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('supplier-record-view')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>stockreturn"
                                    data-toggle="dropdown"> <?php echo $this->lang->line('SuppliersRecords'); ?></a>
                                </li>
                                <?php 
                                endif;
                                if($this->aauth->user_can('customer-record-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>stockreturn/customer"
                                    data-toggle="dropdown"><?php echo $this->lang->line('CustomersRecords'); ?></a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php 
                        endif;
                        if($this->aauth->user_can_any(['supplier-view','supplier-create'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="ft-target"></i><?php echo $this->lang->line('Suppliers') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('supplier-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?= base_url(); ?>supplier/create"
                                    data-toggle="dropdown"><?php echo $this->lang->line('New Supplier'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('supplier-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>supplier"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Manage Suppliers'); ?></a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can_any(['custom-label','standard-label'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                            class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                class="fa fa-barcode"></i><?php echo $this->lang->line('ProductsLabel'); ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('custom-label')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>products/custom_label"
                                    data-toggle="dropdown"><?php echo $this->lang->line('custom_label'); ?></a>
                                </li>
                                <?php 
                                endif;
                                if($this->aauth->user_can('standard-label')):
                                ?>
                                  <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>products/standard_label"
                                    data-toggle="dropdown"><?php echo $this->lang->line('standard_label'); ?></a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php 
                }
            }
            if ($this->aauth->premission(3)) {
                if($this->aauth->user_can_any([
                'client-view','client-create','client-group-view','support-ticket-view',
                'unsolved-support-ticket-view'
            ])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-diamond"></i><span><?php echo $this->lang->line('CRM') ?></span></a>
                    <ul class="dropdown-menu">
                        <?php if($this->aauth->user_can_any(['client-view','client-create'])):?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="ft-users"></i><?php echo $this->lang->line('Clients') ?></a>
                            <ul class="dropdown-menu">
                                 <?php if($this->aauth->user_can('client-create')):?>
                            <li data-menu=""><a class="dropdown-item"
                                href="<?php echo base_url(); ?>customers/create"
                                data-toggle="dropdown"><?php echo $this->lang->line('New Client') ?></a>
                            </li>
                            <?php 
                            endif;
                            if($this->aauth->user_can('client-view')):
                            ?>
                            <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>customers"
                                data-toggle="dropdown"><?= $this->lang->line('Manage Clients'); ?></a>
                            </li>
                            <?php endif; ?>
                            </ul>
                        </li>
                        <?php
                    endif;
                    if($this->aauth->user_can('client-group-view')):
                    ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>clientgroup"><i
                                        class="icon-grid"></i><?php echo $this->lang->line('Client Groups'); ?></a>
                        </li>
                        <?php
                    endif;
                    if($this->aauth->user_can_any(['support-ticket-view','unsolved-support-ticket-view'])):
                    ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="fa fa-ticket"></i><?php echo $this->lang->line('Support Tickets') ?></a>
                            <ul class="dropdown-menu">
                               <?php if($this->aauth->user_can('unsolved-support-ticket-view')):?>
                            <li data-menu=""><a class="dropdown-item"
                                href="<?php echo base_url(); ?>tickets/?filter=unsolved"
                                data-toggle="dropdown"><?php echo $this->lang->line('UnSolved') ?></a>
                            </li>
                            <?php
                            endif;
                            if($this->aauth->user_can('support-ticket-view')):
                            ?>
                            <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>tickets"
                                data-toggle="dropdown"><?= $this->lang->line('Manage Tickets'); ?></a>
                            </li>
                            <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;?>

                    </ul>
                </li>
            <?php 
         endif;    
        }
            if ($this->aauth->premission(4)) {
                 if($this->aauth->user_can_any(['project-view','project-create','task-view'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-briefcase"></i><span><?= $this->lang->line('Project') ?></span></a>
                    <ul class="dropdown-menu">
                         <?php if($this->aauth->user_can_any(['project-view','project-create'])):?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-calendar"></i><?php echo $this->lang->line('Project Management') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('project-create')):?>
                            <li data-menu=""><a class="dropdown-item"
                                href="<?php echo base_url(); ?>projects/addproject"
                                data-toggle="dropdown"><?php echo $this->lang->line('New Project') ?></a>
                            </li>
                            <?php
                            endif;
                            if($this->aauth->user_can('project-view')):
                            ?>
                            <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>projects"
                                data-toggle="dropdown"><?= $this->lang->line('Manage Projects'); ?></a>
                            </li>
                            <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;?>
                    <?php if($this->aauth->user_can('task-view')):?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>tools/todo"><i
                                        class="icon-list"></i><?php echo $this->lang->line('To Do List'); ?></a>
                        </li>
                    <?php endif;?>
                    </ul>
                </li>
            <?php 
      endif;      
        }
            if (!$this->aauth->premission(4) && $this->aauth->premission(7)) {
                 if($this->aauth->user_can_any(['project-view','task-view'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-briefcase"></i><span><?php echo $this->lang->line('Project') ?></span></a>
                    <ul class="dropdown-menu">
                         <?php if($this->aauth->user_can('project-view')):?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>manager/projects"><i
                                        class="icon-calendar"></i><?php echo $this->lang->line('Manage Projects'); ?>
                            </a>
                        </li>
                        <?php 
                    endif;
                    if($this->aauth->user_can('task-view')):
                    ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>manager/todo"><i
                                        class="icon-list"></i><?php echo $this->lang->line('To Do List'); ?></a>
                        </li>
                    <?php endif; ?>
                    </ul>
                </li>
            <?php 
        endif;    
        }
            if ($this->aauth->premission(5)) {
                 if($this->aauth->user_can_any([
                'account-view','account-satement-view','balancesheet-view','transaction-view','transaction-create',
                'add-new-transfer','income-transaction-view','expenses-transaction-view','client-view'
                ])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-calculator"></i><span><?= $this->lang->line('Accounts') ?></span></a>
                    <ul class="dropdown-menu">
                        <?php if($this->aauth->user_can_any(['account-view','account-satement-view','balancesheet-view'])):?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-book-open"></i><?php echo $this->lang->line('Accounts') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('account-view')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>accounts"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Manage Accounts') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('balancesheet-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>accounts/balancesheet"
                                    data-toggle="dropdown"><?= $this->lang->line('BalanceSheet'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('account-satement-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/accountstatement"
                                    data-toggle="dropdown"><?= $this->lang->line('Account Statements'); ?></a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;
                        if($this->aauth->user_can_any(['transaction-view','transaction-create','add-new-transfer',
                            'income-transaction-view','expenses-transaction-view','client-view'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-wallet"></i><?php echo $this->lang->line('Transactions') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('transaction-view')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>transactions"
                                    data-toggle="dropdown"><?php echo $this->lang->line('View Transactions') ?></a>
                                </li>
                                <?php 
                                endif;
                                if($this->aauth->user_can('transaction-create')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>transactions/add"
                                    data-toggle="dropdown"><?= $this->lang->line('New Transaction'); ?></a>
                                </li>
                                <?php
                                endif;  
                                if($this->aauth->user_can('add-new-transfer')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>transactions/transfer"
                                    data-toggle="dropdown"><?= $this->lang->line('New Transfer'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('income-transaction-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>transactions/income"
                                    data-toggle="dropdown"><?= $this->lang->line('Income'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('expenses-transaction-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>transactions/expense"
                                    data-toggle="dropdown"><?= $this->lang->line('Expense'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('client-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>customers"
                                    data-toggle="dropdown"><?= $this->lang->line('Clients Transactions'); ?></a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
                         <?php
                        endif;
                         if($this->aauth->user_can('income-transaction-view')):
                        ?>
 <li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>transactions/income"
                                                    data-toggle="dropdown"><i class="fa fa-money"></i><?= $this->lang->line('Income'); ?></a>
                                </li>
                                 <?php
                        endif;
                        if($this->aauth->user_can('expenses-transaction-view')):
                        ?>
                                <li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>transactions/expense"
                                                    data-toggle="dropdown"><i class="ft-external-link"></i><?= $this->lang->line('Expense'); ?></a>
                                </li>       


								<li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>transactions/dailypayment"
                                                    data-toggle="dropdown"><i class="ft-external-link"></i>Daily Payment</a>
                                </li>
                                <?php endif;?>
                    </ul>
                </li>
                <?php
                    endif;
                    //end accounts
                   
                    //start promo codes
                    if($this->aauth->user_can_any(['promo-code-view','promo-code-create'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-energy"></i><span><?php echo $this->lang->line('Promo Codes') ?></span></a>
                    <ul class="dropdown-menu">
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-trophy"></i><?php echo $this->lang->line('Coupons') ?></a>
                            <ul class="dropdown-menu">
                               <?php if($this->aauth->user_can('promo-code-create')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>promo/create"
                                    data-toggle="dropdown"><?php echo $this->lang->line('New Promo') ?></a>
                                </li>
                                <?php 
                                    endif;
                                    if($this->aauth->user_can('promo-code-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>promo"
                                    data-toggle="dropdown"><?= $this->lang->line('Manage Promo'); ?></a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>


                    </ul>
                </li>

            <?php 
            endif;
        }
            if ($this->aauth->premission(10)) {
                if($this->aauth->user_can_any([
                'business-register-view','account-satement-view','customer-account-Satement-view',
                'supplier-account-statement-view','tax-statement-view','product-sales-report-view',
                'product-categories-report-view','trending-product-report-view','profit-report-view',
                'top-customer-report-view','income-vs-expenses-report-view','income-report-view','expences-report-view',
                'statistics-report-view','profit-report-view','calculate-income-report-view','calculate-expenses-report-view',
                'sales-report-view','product-report-view','sales-commission-report-view'
            ])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-pie-chart"></i><span><?php echo $this->lang->line('Data & Reports') ?></span></a>
                    <ul class="dropdown-menu">
                         <?php if($this->aauth->user_can('business-register-view')):?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>register"><i
                                        class="icon-eyeglasses"></i><?php echo $this->lang->line('Business Registers'); ?>
                            </a>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can_any([
                            'account-satement-view','customer-account-Satement-view',
                            'supplier-account-statement-view','tax-statement-view','product-sales-report-view'
                        ])):
                        ?>

                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-doc"></i><?php echo $this->lang->line('Statements') ?></a>
                            <ul class="dropdown-menu">

                                <?php if($this->aauth->user_can('account-satement-view')):?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/accountstatement"
                                    data-toggle="dropdown"><?= $this->lang->line('Account Statements'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('customer-account-Satement-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/customerstatement"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Customer_Account_Statements')  ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('supplier-account-statement-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/supplierstatement"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Supplier_Account_Statements') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('tax-statement-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/taxstatement"
                                    data-toggle="dropdown"><?php echo $this->lang->line('TAX_Statements'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('product-sales-report-view')):
                                ?>
                                 <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>pos_invoices/extended"
                                    data-toggle="dropdown"><?php echo $this->lang->line('ProductSales'); ?></a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
                    <?php
                        endif;
                        if($this->aauth->user_can_any([
                            'product-categories-report-view','trending-product-report-view','profit-report-view',
                            'top-customer-report-view','income-vs-expenses-report-view','income-report-view','expences-report-view'
                        ])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-bar-chart"></i><?php echo $this->lang->line('Graphical Reports') ?>
                            </a>
                            <ul class="dropdown-menu">
                            <?php if($this->aauth->user_can('product-categories-report-view')):?>
                                <li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>chart/product_cat"
                                                    data-toggle="dropdown"><?= $this->lang->line('Product Categories'); ?></a>
                                </li>
                                 <?php
                                endif;
                                if($this->aauth->user_can('trending-product-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>chart/trending_products"
                                                    data-toggle="dropdown"><?= $this->lang->line('Trending Products'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('profit-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>chart/profit"
                                                    data-toggle="dropdown"><?= $this->lang->line('Profit'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('top-customer-report-view')):
                                ?>

                                <li data-menu=""><a class="dropdown-item"
                                                    href="<?php echo base_url(); ?>chart/topcustomers"
                                                    data-toggle="dropdown"><?php echo $this->lang->line('Top_Customers') ?></a>
                                </li>
                                <?php
                                endif;  
                                if($this->aauth->user_can('income-vs-expenses-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>chart/incvsexp"
                                                    data-toggle="dropdown"><?php echo $this->lang->line('income_vs_expenses') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('income-report-view')):
                                ?>


                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>chart/income"
                                                    data-toggle="dropdown"><?= $this->lang->line('Income'); ?></a>
                                </li>
                                 <?php
                                endif;
                                if($this->aauth->user_can('expences-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>chart/expenses"
                                                    data-toggle="dropdown"><?= $this->lang->line('Expenses'); ?></a></li>
                                <?php endif;?>


                            </ul>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can_any(['statistics-report-view','profit-report-view',
                            'calculate-income-report-view','calculate-expenses-report-view','sales-report-view',
                            'product-report-view','sales-commission-report-view'])):
                        ?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="icon-bulb"></i><?php echo $this->lang->line('Summary_Report') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('statistics-report-view')):?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/statistics"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Statistics') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('profit-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/profitstatement"
                                    data-toggle="dropdown"><?= $this->lang->line('Profit'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('calculate-income-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/incomestatement"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Calculate Income'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('calculate-expenses-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/expensestatement"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Calculate Expenses') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('sales-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>reports/sales"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Sales') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('product-report-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/products"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Products') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('sales-commission-report-view')):
                                ?>
                                </li>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>reports/commission"
                                    data-toggle="dropdown"><?= $this->lang->line('Employee_Commission'); ?></a>
                                </li>
                                <?php
                                endif;  
                                ?>

                            </ul>
                        </li>
                        <?php endif;?>

                    </ul>
                </li>
            <?php 
         endif;    
        }
            if ($this->aauth->premission(6)) {
                if($this->aauth->user_can_any(['note-view','calendar-view','document-view'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="icon-note"></i><span><?php echo $this->lang->line('Miscellaneous') ?></span></a>
                    <ul class="dropdown-menu">
                        <?php if($this->aauth->user_can('note-view')):?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>tools/notes"><i
                                class="icon-note"></i><?php echo $this->lang->line('Notes'); ?></a>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can('calendar-view')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>events"><i
                                class="icon-calendar"></i><?php echo $this->lang->line('Calendar'); ?></a>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can('document-view')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>tools/documents"><i
                                class="icon-doc"></i><?php echo $this->lang->line('Documents'); ?></a>
                        </li>
                        <?php endif;?>


                    </ul>
                </li>
            <?php 
        endif;    
        }
            if ($this->aauth->premission(9)) {
                if($this->aauth->user_can_any(['employee-view','department-view','payroll-view',
                'attendance-view','holiday-view','role-view','permission-view','profile-view'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="ft-file-text"></i><span><?php echo $this->lang->line('HRM') ?></span></a>
                    <ul class="dropdown-menu">
                         <?php if($this->aauth->user_can_any([
                            'employee-view','attendance-view','holiday-view','role-view','permission-view'
                        ])):?>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a
                                    class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i
                                        class="ft-users"></i><?php echo $this->lang->line('Employees') ?></a>
                            <ul class="dropdown-menu">
                                <?php if($this->aauth->user_can('employee-view')):?>
                                <li data-menu=""><a class="dropdown-item" href="<?php echo base_url(); ?>employee"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Employees') ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('role-view')):
                                ?>
                                 <li data-menu=""><a class="dropdown-item" href="<?php echo base_url('roles'); ?>"
                                    data-toggle="dropdown"><?php echo $this->lang->line('Roles & Permission') ?></a>
                                 </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('permission-view')):
                                ?>
                                 <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url('permissions'); ?>"
                                    data-toggle="dropdown"><?= $this->lang->line('Permissions'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('employee-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>employee/salaries"
                                    data-toggle="dropdown"><?= $this->lang->line('Salaries'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('attendance-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>employee/attendances"
                                    data-toggle="dropdown"><?= $this->lang->line('Attendance'); ?></a>
                                </li>
                                <?php
                                endif;
                                if($this->aauth->user_can('holiday-view')):
                                ?>
                                <li data-menu=""><a class="dropdown-item"
                                    href="<?php echo base_url(); ?>employee/holidays"
                                    data-toggle="dropdown"><?= $this->lang->line('Holidays'); ?></a>
                                </li>
                                <?php
                                endif;
                                ?>
                            </ul>
                        </li>
                         <?php
                        endif;
                        if($this->aauth->user_can('department-view')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>employee/departments"><i
                            class="icon-folder"></i><?php echo $this->lang->line('Departments'); ?></a>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can('payroll-view')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>employee/payroll"><i
                            class="icon-notebook"></i><?php echo $this->lang->line('Payroll'); ?></a>
                        </li>
                        <?php
                        endif;
                        ?>

                    </ul>
                </li>
            <?php 
         endif;    
        }
            if ($this->aauth->get_user()->roleid == 6) {
                 if($this->aauth->user_can_any(['export-customer-and-supplier','export-transaction','export-product','export-account-statement',
                'export-tax','export-database','import-product','import-customer','export-product-account-statement'])):
                ?>
                <li class="dropdown mega-dropdown nav-item" data-menu="megamenu"><a class="dropdown-toggle nav-link"
                                                                                    href="#" data-toggle="dropdown"><i
                                class="ft-bar-chart-2"></i><span><?php echo $this->lang->line('Export_Import'); ?></span></a>
                    <ul class="mega-dropdown-menu dropdown-menu row">
                        <li class="col-md-4" data-mega-col="col-md-3">
                            <ul class="drilldown-menu">
                                <li class="menu-list">
                                    <ul class="mega-menu-sub">
                                        <?php if($this->aauth->user_can('export-customer-and-supplier')):?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>export/crm"><i
                                            class="fa fa-caret-right"></i><?php echo $this->lang->line('Export People Data'); ?>
                                            </a>
                                        </li>
                                        <?php 
                                        endif;
                                        if($this->aauth->user_can('export-transaction')):
                                        ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo base_url(); ?>export/transactions"><i
                                                class="fa fa-caret-right"></i><?php echo $this->lang->line('Export Transactions'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        if($this->aauth->user_can('export-product')):
                                        ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>export/products"><i
                                                class="fa fa-caret-right"></i><?php echo $this->lang->line('Export Products'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        ?>

                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="col-md-4" data-mega-col="col-md-3">
                            <ul class="drilldown-menu">
                                <li class="menu-list">
                                    <ul class="mega-menu-sub">
                                        <?php if($this->aauth->user_can('export-account-statement')):?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>export/account"><i
                                                        class="fa fa-caret-right"></i><?php echo $this->lang->line('Account Statements'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        if($this->aauth->user_can('export-tax')):
                                        ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo base_url(); ?>export/taxstatement"><i
                                                        class="fa fa-caret-right"></i><?php echo $this->lang->line('Tax_Export'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        if($this->aauth->user_can('export-database')):
                                        ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>export/dbexport"><i
                                                        class="fa fa-caret-right"></i><?php echo $this->lang->line('Database Backup'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="col-md-4" data-mega-col="col-md-3">
                            <ul class="drilldown-menu">
                                <li class="menu-list">
                                    <ul class="mega-menu-sub">
                                        <?php if($this->aauth->user_can('import-product')):?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>import/products"><i
                                                        class="fa fa-caret-right"></i></i><?php echo $this->lang->line('Import Products'); ?>
                                            </a></li>
                                        <?php
                                        endif; 
                                        if($this->aauth->user_can('import-customer')):
                                        ?>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>import/customers"><i
                                                        class="fa fa-caret-right"></i><?php echo $this->lang->line('Import Customers'); ?>
                                            </a></li>
                                        <?php
                                        endif;
                                        if($this->aauth->user_can('export-product-account-statement')):
                                        ?>
                                        <li><a  class="dropdown-item" href="<?php echo base_url(); ?>export/people_products"><i
                                                    class="fa fa-caret-right"></i> <?php echo $this->lang->line('ProductsAccount Statements'); ?>
                                        </a></li>
                                        <?php
                                        endif;
                                        ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>
            <?php 
         endif;    
        }
			if ($this->aauth->premission(9)) {
                if($this->aauth->user_can_any(['seller-view','wallet-transaction'])):
                ?>
                <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#"
                                                                      data-toggle="dropdown"><i
                                class="fa fa-store"></i><span>Seller</span></a>
                    <ul class="dropdown-menu">
                       
                        <?php if($this->aauth->user_can('seller-view')):?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>sellers"><i
                                        class="ft-users"></i>Manage Sellers</a>
                        </li>
                        <?php
                        endif;
                        if($this->aauth->user_can('wallet-transaction')):
                        ?>
                        <li data-menu="">
                            <a class="dropdown-item" href="<?php echo base_url(); ?>transaction/wallet-transactions"><i
                                        class="fa fa-wallet"></i>Wallet Transactions</a>
                        </li>
                        <?php
                        endif;
                        ?>

                    </ul>
                </li>
            <?php 
         endif;    
        }
            ?>

        </ul>
    </div>
    <!-- /horizontal menu content-->
</div>
<!-- Horizontal navigation-->
<div id="c_body"></div>
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
        <?php if ($this->session->userdata('admin_original_user_id')): 
            $ci =& get_instance();
            $ci->load->database();
            $switched_seller_id = $this->session->userdata('admin_switched_seller_id');
            $active_seller_data = $ci->db->select('store_name')->where('user_id', $switched_seller_id)->get('seller_data')->row_array();
            $active_store_name = isset($active_seller_data['store_name']) ? $active_seller_data['store_name'] : 'Unknown Store';
        ?>
        <div class="alert alert-primary alert-warning" style="background-color: #fff3cd; border-color: #ffeeba; color: #856404; margin-top: 15px;">
            <a href="#" class="close" data-dismiss="alert">×</a>
            <div class="message">
                <strong><i class="fa fa-eye"></i> Admin Switched View</strong>: You are currently viewing data for store <strong><?php echo htmlspecialchars($active_store_name); ?></strong>. 
                <a href="<?php echo base_url('settings/switch_back_to_admin'); ?>" class="btn btn-sm btn-danger ml-2" style="color: white !important; font-weight: bold; border-radius: 4px; padding: 2px 8px; text-decoration: none;">
                    <i class="fa fa-undo"></i> Switch Back to Admin
                </a>
            </div>
        </div>
        <?php endif; ?>