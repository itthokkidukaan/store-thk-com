<?php
$is_seller_dashboard = function_exists('is_seller_user') && is_seller_user();
$has_recent_buyers = isset($recent_buy[0]['csd']);
$has_recent_invoices = !empty($recent);
$has_recent_payments = !empty($recent_payments);
$has_stock_rows = !empty($stock);
$has_income_chart = !empty($incomechart);
$has_expense_chart = !empty($expensechart);
$has_donut_values = false;
if (!empty($incomechart)) {
    foreach ($incomechart as $chart_row) {
        if (!empty($chart_row['total'])) {
            $has_donut_values = true;
            break;
        }
    }
}
if (!$has_donut_values && !empty($expensechart)) {
    foreach ($expensechart as $chart_row) {
        if (!empty($chart_row['total'])) {
            $has_donut_values = true;
            break;
        }
    }
}
?>
<script type="text/javascript">
    var dataVisits = [
        <?php $tt_inc = 0;foreach ($incomechart as $row) {
        $tt_inc += $row['total'];
        echo "{ x: '" . $row['date'] . "', y: " . intval(amountExchange_s($row['total'], 0, $this->aauth->get_user()->loc)) . "},";
    }
        ?>
    ];
    var dataVisits2 = [
        <?php $tt_exp = 0; foreach ($expensechart as $row) {
        $tt_exp += $row['total'];
        echo "{ x: '" . $row['date'] . "', y: " . intval(amountExchange_s($row['total'], 0, $this->aauth->get_user()->loc)) . "},";
    }
        ?>];

</script>
<?php if(ENVIRONMENT == 'development') { ?>
<div class="alert alert-primary alert-danger" style="">
    <a href="#" class="close" data-dismiss="alert">×</a>
    <div class="message"><strong>Alert</strong>: Application is running in Development/Debug mode! Set it production mode <a href="<?=base_url('settings/debug') ?>">here</a></div>
</div>
<?php } ?>
<div class="dashboard-shell <?php echo $is_seller_dashboard ? 'seller-dashboard' : 'admin-dashboard'; ?>">
<div class="row">
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card">
            <div class="card-content">
                <div class="media align-items-stretch">
                    <div class="p-2 text-center bg-primary bg-darken-2">
                        <i class="fa fa-file-text-o text-bold-200  font-large-2 white"></i>
                    </div>
                    <div class="p-1 bg-gradient-x-primary white media-body">
                        <h5><?php echo $this->lang->line('today_invoices') ?></h5>
                        <h5 class="text-bold-400 mb-0"><i class="ft-plus"></i> <?= $todayin ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card">
            <div class="card-content">
                <div class="media align-items-stretch">
                    <div class="p-2 text-center bg-danger bg-darken-2">
                        <i class="icon-notebook font-large-2 white"></i>
                    </div>
                    <div class="p-1 bg-gradient-x-danger white media-body">
                        <h5><?= $this->lang->line('this_month_invoices') ?></h5>
                        <h5 class="text-bold-400 mb-0"><i class="ft-arrow-up"></i><?= $monthin ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card">
            <div class="card-content">
                <div class="media align-items-stretch">
                    <div class="p-2 text-center bg-warning bg-darken-2">
                        <i class="icon-basket-loaded font-large-2 white"></i>
                    </div>
                    <div class="p-1 bg-gradient-x-warning white media-body">
                        <h5><?= $this->lang->line('today_sales') ?></h5>
                        <h5 class="text-bold-400 mb-0"><i
                                    class="ft-arrow-up"></i><?= amountExchange($todaysales, 0, $this->aauth->get_user()->loc) ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card">
            <div class="card-content">
                <div class="media align-items-stretch">
                    <div class="p-2 text-center bg-success bg-darken-2">
                        <i class="icon-wallet font-large-2 white"></i>
                    </div>
                    <div class="p-1 bg-gradient-x-success white media-body">
                        <h5><?php echo $this->lang->line('this_month_sales') ?></h5>
                        <h5 class="text-bold-400 mb-0"><i
                                    class="ft-arrow-up"></i> <?= amountExchange($monthsales, 0, $this->aauth->get_user()->loc) ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	  <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <a href="<?=base_url()?>dashboard/">
                               
                                   
									<div class="row">
									
									<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>	
									<div class=" col-md-6">
                                    <div class="media-body text-right">
                                       <a href="<?=base_url()?>invoices"> <h5 class="text-muted text-bold-500">Online Orders</h5>
                                        <h3 class="text-bold-600"><?=$order_online?></h3> </a>
                                    </div>
									
									
                                    </div>
									
			<?php } ?>
									<div class="col-md-6">
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Offline Orders</h5>
                                        <h3 class="text-bold-600"><?=$order_counter?></h3>
                                    </div>
                                    </div>
                                </div>
                              
								
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                <a href="<?=base_url()?>customers">
                                <div class="media d-flex">
                                    <div class="align-self-center text-primary">
                                        <i class="ion-ios-personadd-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">New Signups</h5>
                                        <h3 class="text-bold-600"><?=$user_counter?></h3>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                
                                <a href="<?=base_url()?>dashboard/">
                                <div class="media d-flex">
                                    <div class="align-self-center text-success">
                                        <i class="ion-ios-people-outline display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Delivery Boys</h5>
                                        <h3 class="text-bold-600"><?=$delivery_boy_counter?></h3>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                    <div class="card pull-up">
                        <div class="card-content">
                            <div class="card-body">
                                 <a href="<?=base_url()?>products">
                                <div class="media d-flex">
                                    <div class="align-self-center text-info">
                                        <i class="ion-ios-albums-outline display-4 display-4"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h5 class="text-muted text-bold-500">Products</h5>
                                        <h3 class="text-bold-600"><?=$product_counter?></h3>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
</div>
<?php if ($this->aauth->get_user()->roleid == 1) { ?>
<div class="row col-12 d-flex">

                    <div class="col-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>13</h3>
                                <p><button class="btn btn-outline-success text-white border-0" data-toggle="modal" data-target="#approved_sellers">Approved sellers</button></p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>0</h3>
                                <p><button class="btn btn-outline-secondary text-white border-0" data-toggle="modal" data-target="#not_approved_sellers">Not Approved Sellers</button></p>

                            </div>
                            <div class="icon">

                                <i class="fa fa-xs fa-pause-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>6</h3>
                                <p><button class="btn btn-outline-danger text-white border-0" data-toggle="modal" data-target="#deactive_sellers">Deactiveted sellers</button></p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-times-circle"></i>
                            </div>
                        </div>
                    </div> 
					
					<div class="col-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?=$balance?></h3>
                                <p><button class="btn btn-outline-danger text-white border-0" data-toggle="modal" data-target="#deactive_sellers">Account Balance</button></p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-xs fa-inr"></i>
                            </div>
                        </div>
                    </div>
                </div>
<?php } ?>
		
<div class="row match-height">
    <div class="col-xl-8 col-lg-12">
	 <div id="products-sales" class="height-300" style="display:none"></div>
	
	<!--	<div class="card">
            <div class="card-header">
                <h4 class="card-title">Today Report</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div id="products-sales" class="height-300" style="display:none"></div>
                </div>
                <div class="row">
                    <div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="primary"><?= amountExchange($todayinsell['credit'], 0, $this->aauth->get_user()->loc) ?></h3>
                                        <a href="<?=base_url()?>invoices">    <span>Today Sales</span> </a>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
			
                    <div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger"><?= amountExchange($todayexpens['debit'], 0, $this->aauth->get_user()->loc) ?></h3>
                                             <a href="<?=base_url()?>purchase">  <span>Today Purchase</span></a>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
											<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>	
					<div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger"><?= amountExchange($todayinsell['credit'] - $todayinexp['debit'], 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span>Today GP</span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
					<div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger"><?= amountExchange($todayinexp['debit'], 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span><?php echo $this->lang->line('today_expenses') ?></span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
			<?php 
			}
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>		
                    <div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="success"><?= amountExchange($todayprofit, 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span><?php echo $this->lang->line('today_profit') ?></span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
					
			
                    
					
					<?php } ?>
                </div>
				
				<div class="row">
				
					
				<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>	
					<div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger"><?= amountExchange($thismontpurc['credit'] - $thismontpurc['pamnt'], 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span>Total Due Purchase</span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
			<?php } ?>
					<div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger"><?= amountExchange(0, 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span>Total Balance Amount</span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
		
               
					
			
                    <div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="warning"><?= amountExchange($thismontsell['pamnt'], 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span>Today Received</span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 35%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
					
					
                    <div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="warning"><?= amountExchange($thismontsell['pamnt'], 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span>Total Received</span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 35%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
									<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>	
					<div class="row">
					<div class="col-xl-2 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="warning"><?= amountExchange($tt_inc - $tt_exp, 0, $this->aauth->get_user()->loc) ?></h3>
                                            <span><?php echo $this->lang->line('revenue') ?></span>
                                        </div>

                                    </div>
                                    <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 35%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
					
			<?php } ?>
            </div>
        </div> -->
		<div id="chart" style="display:none;"></div>
	<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			
$start_date = date('01-m-Y'); // current month ka first day
$end_date = date('Y-m-d');    // aaj ka date

		$i = 1;

                    $gross2 = 0;

                    foreach ($accounts as $row) {

                        if ($row['account_type'] == 'Expenses') {

                            $aid = $row['id'];

                            $acn = $row['acn'];

                            $holder = $row['holder'];



                            $balance = $row['lastbal'];

                           // $qty = $row['adate'];

                      

                            $i++;

                            $gross2 += $balance;

                        }

                    }

                    ?>


<div class="card position-relative">
  <div id="card-loader" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;
    background:rgba(255,255,255,0.8);z-index:999;display:flex;justify-content:center;align-items:center;">
    <div class="spinner-border"></div>
  </div>

  <div class="card-header">
    <div class="row align-items-center">
      <div class="col-md-2"><h4 class="card-title">TOTAL REPORT</h4></div>
      <div class="col-md-1"><button id="todayBtn" class="btn btn-success filter-btn">Today</button></div>
      <div class="col-md-1"><button id="weekBtn" class="btn btn-danger filter-btn">This Week</button></div>
      <div class="col-md-1"><button id="monthBtn" class="btn btn-warning filter-btn">This Month</button></div>
      <div class="col-md-2"> <input type="text" name="start_date" id="dashstart_date" class=" form-control form-control-sm" autocomplete="off" data-toggle="datepicker"  value="<?=$start_date ?>"/> </div>
          <div class="col-md-2"> <input type="text" name="end_date" id="dashend_date" class="form-control form-control-sm" data-toggle="datepicker" autocomplete="off"  value="<?=$end_date ?>" /> </div>
		 
      <div class="col-md-2"><button id="searchBtn" class="btn btn-info btn-sm">Search</button></div>
    </div>
  </div>

  <div class="card-content">
    <div id="sortableCards" class="row">

      <!-- Each card uses class for dynamic update -->
      <template id="report-card-template">
        <div class="col-xl-2 col-lg-4 col-12 card-item">
          <div class="card">
            <div class="card-body">
              <h3 class="value">₹ 0.00</h3>
              <span class="title"></span>
              <div class="progress"><div class="progress-bar"></div></div>
              <div class="unit-summary"></div>
            </div>
          </div>
        </div>
      </template>

    </div>
  </div>
</div>



		
					<?php } ?>
		
	
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><?php echo $this->lang->line('Recent Buyers') ?></h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                    </ul>
                </div>
            </div>
			<?php if (!$is_seller_dashboard) { ?>
			<div class="row">
			   <div class="col-xl-6 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger">0</h3>
                                             <a href="<?=base_url()?>customers">  <span>Total Buyers</span></a>
                                        </div>

                                    </div>
                                   <!-- <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div> 

					<div class="col-xl-6 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body text-left w-100">
                                            <h3 class="danger">0</h3>
                                             <a href="<?=base_url()?>customers">  <span>Drop Buyers</span></a>
                                        </div>

                                    </div>
                                  <!--  <div class="progress progress-sm mt-1 mb-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div> 
                    </div>
                    <?php } ?>
					
            <div class="card-content px-1">
                <div id="recent-buyers" class="media-list height-450  mt-1 position-relative">
                    <?php
                    if ($has_recent_buyers) {

                        foreach ($recent_buy as $item) {

                            echo '       <a href="' . base_url('customers/view?id=' . $item['csd']) . '" class="media border-0">
                        <div class="media-left pr-1">
                            <span class="avatar avatar-md avatar-online"><img class="media-object rounded-circle" src="' . base_url() . 'userfiles/customers/thumbnail/' . $item['picture'] . '">
                            <i></i>
                            </span>
                        </div>
                        <div class="media-body w-100">
                            <h6 class="list-group-item-heading">' . $item['name'] . ' <span class="font-medium-4 float-right pt-1">' . amountExchange($item['total'], 0, $this->aauth->get_user()->loc) . '</span></h6>
                            <p class="list-group-item-text mb-0"><span class="badge  st-' . $item['status'] . '">' . $this->lang->line(ucwords($item['status'])) . '</span></p>
                        </div>
                    </a>';

                        }
                    } elseif ($recent_buy == 'sql') {
                        echo ' <div class="media-body w-100">  <h5 class="list-group-item-heading bg-danger white">Critical SQL Strict Mode Error: </h5>Please Disable Strict SQL Mode for in database  settings.</div>';
                    } else {
                        echo '<div class="dashboard-empty-state"><div class="empty-state-title">No Recent Buyers</div><div class="empty-state-text">Buyer activity will appear here once customers place orders for this seller.</div></div>';
                    }

                    ?>


                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<div class="row match-height">
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header recent-invoices-header">
                <h4 class="card-title"><?php echo $this->lang->line('recent_invoices') ?></h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements recent-invoices-actions">
                    <a href="<?php echo base_url() ?>invoices/create"
                       class="btn btn-primary btn-sm rounded"><?php echo $this->lang->line('Add Sale') ?></a>
                    <a href="<?php echo base_url() ?>invoices"
                       class="btn btn-success btn-sm rounded"><?php echo $this->lang->line('Manage Invoices') ?></a>
                    <a href="<?php echo base_url() ?>pos_invoices"
                       class="btn btn-blue btn-sm rounded"><?php echo $this->lang->line('POS') ?></a>
                </div>
            </div>
            <div class="card-content">
                <div class="table-responsive">
                    <table id="recent-orders" class="table table-hover mb-1">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('Invoices') ?>#</th>
                            <th><?php echo $this->lang->line('Customer') ?></th>
                            <th><?php echo $this->lang->line('Status') ?></th>
                            <th><?php echo $this->lang->line('Due') ?></th>
                            <th><?php echo $this->lang->line('Amount') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($has_recent_invoices) {
                        foreach ($recent as $item) {
							$t ='';
                        if($item['orderdone_by']=='POS'){
							
							$t ='POS';
							$page='pos_invoices';
						}else{
							
							$t ='THKD';
							$page='invoices';
						}
                            echo '   <tr>
                                <td class="text-truncate"><a href="' . base_url() . $page . '/view?id=' . $item['id'] . '">' . $t . '#' . $item['id'] . '</a></td>
                             
                                <td class="text-truncate"> ' . $item['username'] . '</td>
                                <td class="text-truncate"><span class="badge  st-' . $item['status'] . ' st-' . $item['status'] . '">' . $this->lang->line(ucwords($item['status'])) . '</span></td><td class="text-truncate">' . dateformat($item['date_added']) . '</td>
                                <td class="text-truncate">' . amountExchange($item['total'], 0, $this->aauth->get_user()->loc) . '</td>
                            </tr>';
                        }
                        } else {
                            echo '<tr><td colspan="5"><div class="dashboard-empty-state compact"><div class="empty-state-title">No Recent Invoices</div><div class="empty-state-text">New seller invoices will appear here after sales are created.</div></div></td></tr>';
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12">
        <div class="card">

            <div class="card-header">
                <div class="header-block">
                    <h4 class="title">
                        <?php echo $this->lang->line('income_vs_expenses') ?>
                    </h4></div>
            </div>
            <div class="card-body">
                <div id="salesbreakdown" class="card mt-2"
                     data-exclude="xs,sm,lg">
                    <div class="dashboard-sales-breakdown-chart" id="dashboard-sales-breakdown-chart"></div>

                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card-group">
		
			<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>		
            <div class="card">
                <div class="card-content">

                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="primary"><?php $ipt = sprintf("%0.0f", ($tt_inc * 100) / $goals['income']); ?><?php echo ' ' . $ipt . '%' ?></h3><?= '<span class=" font-medium-1 display-block">' . date('F') . ' ' . $this->lang->line('income') . '</span>'; ?>
                                <span class="font-medium-1"><?= amountExchange($tt_inc, 0, $this->aauth->get_user()->loc) . '/' . amountExchange($goals['income'], 0, $this->aauth->get_user()->loc) ?></span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="fa fa-money primary font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $ipt ?>%"
                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>  
			<?php
			
			}
			?>
			
            <div class="card">
                <div class="card-content">

                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="red"><?php $ipt = sprintf("%0.0f", ($tt_exp * 100) / $goals['expense']); ?><?php echo ' ' . $ipt . '%' ?></h3><?= '<span class="font-medium-1 display-block">' . date('F') . ' ' . $this->lang->line('expenses') . '</span>'; ?>
                                <span class="font-medium-1"><?= amountExchange($tt_exp, 0, $this->aauth->get_user()->loc) . '/' . amountExchange($goals['expense'], 0, $this->aauth->get_user()->loc) ?></span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-external-link red font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $ipt ?>%"
                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">

                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="blue"><?php $ipt = sprintf("%0.0f", ($monthsales * 100) / $goals['sales']); ?><?php echo ' ' . $ipt . '%' ?></h3><?= '<span class="font-medium-1 display-block">' . date('F') . ' ' . $this->lang->line('sales') . '</span>'; ?>
                                <span class="font-medium-1"><?= amountExchange($monthsales, 0, $this->aauth->get_user()->loc) . '/' . amountExchange($goals['sales'], 0, $this->aauth->get_user()->loc) ?></span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-flag blue font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-blue" role="progressbar" style="width: <?= $ipt ?>%"
                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
			
				<?php 
			if($this->aauth->get_user()->roleid == 1 || $this->aauth->get_user()->roleid == 4){
			?>	
            <div class="card">
                <div class="card-content">

                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left w-100">
                                <h3 class="purple"><?php $ipt = sprintf("%0.0f", (($tt_inc - $tt_exp) * 100) / $goals['sales']); ?><?php echo ' ' . $ipt . '%' ?></h3><?= '<span class="font-medium-1 display-block">' . date('F') . ' ' . $this->lang->line('net_income') . '</span>'; ?>
                                <span class="font-medium-1"><?= amountExchange($tt_inc - $tt_exp, 0, $this->aauth->get_user()->loc) . '/' . amountExchange($goals['netincome'], 0, $this->aauth->get_user()->loc) ?></span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-inbox purple font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0">
                            <div class="progress-bar bg-purple" role="progressbar" style="width: <?= $ipt ?>%"
                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
			<?php
			
			}
			
			?>
			
        </div>
    </div>
</div>
<div class="row match-height">
    <div class="col-xl-7 col-lg-12">
        <div class="card" id="transactions">

            <div class="card-body">
                <h4><?php echo $this->lang->line('cashflow') ?></h4>
                <p><?php echo $this->lang->line('graphical_presentation') ?></p>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="base-tab1" data-toggle="tab" aria-controls="tab1"
                           href="#sales"
                           aria-expanded="true"><?php echo $this->lang->line('income') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="base-tab2" data-toggle="tab" aria-controls="tab2"
                           href="#transactions1"
                           aria-expanded="false"><?php echo $this->lang->line('expenses') ?></a>
                    </li>


                </ul>
                <div class="tab-content pt-1">
                    <div role="tabpanel" class="tab-pane active" id="sales" aria-expanded="true"
                         data-toggle="tab">
                        <div id="dashboard-income-chart"></div>

                    </div>
                    <div class="tab-pane" id="transactions1" data-toggle="tab" aria-expanded="false">
                        <div id="dashboard-expense-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (!$is_seller_dashboard) { ?>
    <div class="col-xl-5 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employee Balance <a
                            href="<?php echo base_url() ?>reports/accountstatement">Statement </a></h4>
            </div>

            <div class="card-content">
                <div id="daily-activity">
				
						 <div class="row">

          <div class="col-md-4"> <input type="text" name="start_date" id="start_date" class=" form-control form-control-sm" autocomplete="off" value="<?=$start_date ?>"/> </div>
          <div class="col-md-4"> <input type="text" name="end_date" id="end_date" class="form-control form-control-sm" data-toggle="datepicker" autocomplete="off"  value="<?=$end_date ?>" /> </div>
		 
          <div class="col-md-4"> <input type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm" /> </div>
         </div>
                    <table class="table table-striped table-bordered base-style table-responsive" >
                        <thead>
                        <tr>
                           

                            <th>Account</th>
                            <th>Transfer</th>
                            <th>Purchase</th>
                            <th>Expense</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
						
						   foreach ($accounts as $row) { 
                                    if ($row['account_type'] == 'Basic') {
                                      
                                          
											echo "<tr><td>{$row['acn']} - {$row['holder']}</td><td></td><td></td><td></td><td>₹ {$row['lastbal']}</td></tr>";
                                        
                                    }
                                }
								
                      /*   $t = 0;
                        foreach ($tasks as $row) {
                            $name = '<a class="check text-default" data-id="' . $row['id'] . '" data-stat="Due"> <i class="fa fa-check"></i> </a><a href="#" data-id="' . $row['id'] . '" class="view_task"></a>';
                            if ($row['status'] == 'Done') {
                                $name = '<a class="check text-success" data-id="' . $row['id'] . '" data-stat="Done"> <i class="fa fa-check"></i> </a><a href="#" data-id="' . $row['id'] . '" class="view_task"></a>';
                            }

                            echo ' <tr>
                                <td class="text-truncate">
                                   ' . $name . '
                                </td>
                            
                                <td class="text-truncate">' . $row['name'] . '</td>
                                <td class="text-truncate"><span id="st' . $t . '" class="badge badge-default task_' . $row['status'] . '">' . $row['status'] . '</span></td>
                            </tr>';


                            $t++;
                        } */
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<div class="row match-height">
    <div class="col-xl-7 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><?php echo $this->lang->line('recent') ?> <a
                            href="<?php echo base_url() ?>transactions"
                            class="btn btn-primary btn-sm rounded"><?php echo $this->lang->line('Transactions') ?></a>
                </h4>
                <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                        <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover mb-1">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('Date') ?>#</th>
                            <th><?php echo $this->lang->line('Account') ?></th>
                            <th><?php echo $this->lang->line('Debit') ?></th>
                            <th><?php echo $this->lang->line('Credit') ?></th>

                            <th><?php echo $this->lang->line('Method') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($has_recent_payments) {
                        foreach ($recent_payments as $item) {

                            echo '<tr>
                                <td class="text-truncate"><a href="' . base_url() . 'transactions/view?id=' . $item['id'] . '">' . dateformat($item['date']) . '</a></td>
                                <td class="text-truncate"> ' . $item['account'] . '</td>
                                <td class="text-truncate">' . amountExchange($item['debit'], 0, $this->aauth->get_user()->loc) . '</td>
                                <td class="text-truncate">' . amountExchange($item['credit'], 0, $this->aauth->get_user()->loc) . '</td>                    
                                <td class="text-truncate">' . $this->lang->line($item['method']) . '</td>
                            </tr>';

                        }
                        } else {
                            echo '<tr><td colspan="5"><div class="dashboard-empty-state compact"><div class="empty-state-title">No Recent Transactions</div><div class="empty-state-text">Transaction activity for this seller will show here once entries are created.</div></div></td></tr>';
                        } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-12">
        <div class="card">
            <div class="card-header ">
                <h4 class="card-title"><?php echo $this->lang->line('Stock Alert') ?></h4>

            </div>
            <div class="card-body">
             <!--   <ul class="list-group list-group-flush"> -->
			 
		  <table class='table-striped' id='products_table' 
    data-toggle="table" 
    data-click-to-select="true"  
    data-pagination="true" 
    data-page-list="[ 20, 50, 100, 200]" 
    data-search="true" 
    data-show-columns="true" 
    data-show-refresh="true" 
    data-trim-on-search="true" 
    data-sort-name="id" 
    data-sort-order="desc" 
    data-mobile-responsive="true" 
    data-show-export="true" 
    data-maintain-selected="true" 
	  data-page-size="20" 
    data-export-types='["txt","excel","print"]'>
			<thead>
			<tr>
			<th>Sr No</th>
			<th data-field="name" data-sortable="true">Product Name</th>
			<th >Category</th>
			<th>Total Stock</th>
			<th>Required Stock</th>
			</tr>
			</thead>
			<tbody>
                    <?php
				$n=1;
                    if ($has_stock_rows) {
                    foreach ($stock as $item) {
						
						
                     //   echo '<li class="list-group-item"><span class="badge badge-danger float-xs-right">' . + $item['total_allowed_quantity'] - $item['stock'] . ' ' . $item['unit'] . '</span> <a href="' . base_url() . 'products/edit?id=' . $item['id'] . '">' . $item['name'] . '  </a><small class="purple"> <i class="ft-map-pin"></i> ' . $item['title'] . '</small> </li>';
					 
					 ?>
					 <tr>
					 <td><?=$n?></td>
					 <td><?=$item['name']?></td>
					 <td><?=$item['catname']?></td>
					 <td><?=$item['stock']?></td>
					 <td><?=$item['total_allowed_quantity'] - $item['stock']?></td>
					 
					 </tr>
					 <?php
					 
					 $n++;
                    }
                    } else { ?>
                    <tr>
                        <td colspan="5">
                            <div class="dashboard-empty-state compact">
                                <div class="empty-state-title">No Stock Alerts</div>
                                <div class="empty-state-text">Products with low stock will be listed here when attention is needed.</div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>

               <!-- </ul> -->
			   </tbody>
</table>
            </div>
        </div>
    </div>
</div>
</div>




<script>
$(document).ready(function(){
  const isSellerDashboard = <?php echo $is_seller_dashboard ? 'true' : 'false'; ?>;

  function renderChartEmptyState(selector, title, message) {
    $(selector).html(
      '<div class="dashboard-empty-state chart-empty">' +
        '<div class="empty-state-title">' + title + '</div>' +
        '<div class="empty-state-text">' + message + '</div>' +
      '</div>'
    );
  }

  // === All 14 reports ===
  const reports = [
    { key:'total_stock', title:'Total Stock', color:'success', link:'productcategory/viewwarehouse?id=1', summaryKey:'total_stock_summary' },
    { key:'consumption_stock', title:'Consumption Stock', color:'info', link:'invoices', summaryKey:'consumption_stock_summary' },
    { key:'total_wastage', title:'Wastage', color:'danger', percentKey:'total_wastage_percent', link:'productcategory/wastage', summaryKey:'total_wastage_summary' },
    { key:'total_purchase', title:'Total Purchase', color:'primary', link:'purchase', summaryKey:'total_purchase_summary' },
    { key:'total_paid_purchase', title:'Paid Purchase', color:'success', link:'purchase' },
    { key:'total_due_purchase', title:'Due Purchase', color:'warning', link:'purchase' },
    { key:'total_return_stock', title:'Return Stock', color:'danger', percentKey:'total_return_percent', link:'stockreturn', summaryKey:'total_return_summary' },
    { key:'total_paid_old_purchase', title:'Paid Old Purchase', color:'success', link:'purchase' },
    { key:'total_expense', title:'Total Transection', color:'danger', link:'transactions/expense' },
    { key:'recevedamount', title:'Received Amount', color:'success', link:'received_amount_report' },
    { key:'total_balance', title:'Balance', color:'info', link:'balance_report' },
    { key:'total_sales', title:'Total Sales', color:'primary', link:'invoices', summaryKey:'total_sales_summary' },
    { key:'total_gp', title:'Gross Profit (GP)', color:'warning', percentCalc:true, link:'#' },
    { key:'total_np', title:'Net Profit (NP)', color:'warning', link:'#' }
  ];

  const container = $('#sortableCards');
  const template = $('#report-card-template').html();

  // === Create all cards ===
  reports.forEach(rep => {
    const card = $(template);
    card.attr('id', 'card-' + rep.key);
    card.find('.title').text(rep.title);
    card.find('.progress-bar').addClass('bg-' + rep.color);
    card.find('.card').attr('data-link', rep.link);
    container.append(card);
  });

  // === Click to open link ===
  $(document).on('click', '.card', function() {
    const link = $(this).data('link');
    if (link) window.location.href = "<?= base_url(); ?>" + link;
  });

  // === Filter buttons ===
  setActiveButton($('#todayBtn'));
  setDateRange('today');

  $('#todayBtn').click(()=>{setActiveButton($('#todayBtn'));setDateRange('today');});
  $('#weekBtn').click(()=>{setActiveButton($('#weekBtn'));setDateRange('week');});
  $('#monthBtn').click(()=>{setActiveButton($('#monthBtn'));setDateRange('month');});
  $('#searchBtn').click(()=>{loadReport($('#dashstart_date').val(),$('#dashend_date').val());});

  function setActiveButton(btn){
    $('.filter-btn').removeClass('active-filter');
    btn.addClass('active-filter');
  }
  
  function setDateRange(type) {
  let s = new Date();
  let e = new Date();

  function formatDate(d) {
    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();
    return `${day}-${month}-${year}`;
  }

  if (type === 'today') {
    // both start & end = today
    s = new Date();
    e = new Date();
  } else if (type === 'week') {
    s.setDate(e.getDate() - 6);
  } else if (type === 'month') {
    
    s = new Date(e.getFullYear(), e.getMonth(), 1);
  }

 
  let startDate = formatDate(s);
  let endDate = formatDate(e);


  $('#dashstart_date').val(startDate);
  $('#dashend_date').val(endDate);

  loadReport(startDate, endDate);
}


  // === Load Report Data ===
  function loadReport(start,end){
    $('#card-loader').fadeIn(200);

    $.ajax({
      url: '<?= base_url("dashboard/filter_report"); ?>',
      type:'POST',
      data:{start_date:start,end_date:end},
      dataType:'json',
      cache:false,
      success:function(res){
        $('#card-loader').fadeOut(200);

        // Convert ₹ 123,456.78 -> 123456.78
        function toNumber(value) {
          if (!value) return 0;
          return parseFloat(value.toString().replace(/[₹,\s]/g, '')) || 0;
        }

        const totalSales = toNumber(res.total_sales);
        const totalPurchase = toNumber(res.consumption_stock);

        reports.forEach(r => {
          const raw = res[r.key] || 0;
          const num = toNumber(raw);
          const formatted = '₹ ' + num.toLocaleString('en-IN',{minimumFractionDigits:2});
          $('#card-'+r.key+' .value').html(formatted);
          $('#card-'+r.key).toggleClass('is-empty-card', isSellerDashboard && num === 0);

          // progress bar (visual fill)
          const p = Math.min(100, (num % 100) + 10);
          $('#card-'+r.key+' .progress-bar').css('width', p+'%');

          // GP, Wastage, Return Percent
          if (r.percentCalc) {
            let gp = 0;
            if (totalPurchase > 0) gp = ((totalSales - totalPurchase) / totalPurchase) * 100;
            $('#card-'+r.key+' .title').html(gp.toFixed(2)+'% | '+r.title);
          } else if (r.percentKey) {
            const per = res[r.percentKey] || 0;
            $('#card-'+r.key+' .title').html(per.toFixed(2)+'% | '+r.title);
          } else {
            $('#card-'+r.key+' .title').html(r.title);
          }

          // Summary under cards (unit-wise info)
          if (r.summaryKey && res[r.summaryKey]) {
            $('#card-'+r.key+' .unit-summary').html(res[r.summaryKey]);
          } else if (isSellerDashboard && num === 0) {
            $('#card-'+r.key+' .unit-summary').html('No data for selected range');
          } else {
            $('#card-'+r.key+' .unit-summary').html('');
          }
        });
      },
      error:function(){
        $('#card-loader').fadeOut(200);
        alert('Error loading report data!');
      }
    });
  }

  // === Drag & Drop order ===
  container.sortable({
    placeholder:"ui-state-highlight",
    update:saveOrder
  });

  function saveOrder(){
    const order=[];
    $(".card-item").each(function(){order.push($(this).attr("id"));});
    localStorage.setItem("cardOrder",JSON.stringify(order));
  }

  function loadOrder(){
    const order=JSON.parse(localStorage.getItem("cardOrder")||"[]");
    if(order.length){$.each(order,function(i,id){$("#"+id).appendTo(container);});}
  }
  loadOrder();

});
</script>






<script type="text/javascript">
    $(window).on("load", function () {

    alert("Helloworld !");
        $('#recent-buyers').perfectScrollbar({
            wheelPropagation: true
        });
        /********************************************
         *               PRODUCTS SALES              *
         ********************************************/
        var sales_data = [
            <?php foreach ($countmonthlychart as $row) {
            echo "{ y: '" . $row['date'] . "', sales: " . intval(amountExchange_s($row['total'], 0, $this->aauth->get_user()->loc)) . ", invoices: " . intval($row['ttlid']) . "},";
        } ?>
        ];
        var months = ["<?=lang('Jan') ?>", "<?=lang('Feb') ?>", "<?=lang('Mar') ?>", "<?=lang('Apr') ?>", "<?=lang('May') ?>", "<?=lang('Jun') ?>", "<?=lang('Jul') ?>", "<?=lang('Aug') ?>", "<?=lang('Sep') ?>", "<?=lang('Oct') ?>", "<?=lang('Nov') ?>", "<?=lang('Dec') ?>"];
        Morris.Area({
            element: 'products-sales',
            data: sales_data,
            xkey: 'y',
            ykeys: ['sales', 'invoices'],
            labels: ['sales', 'invoices'],
            behaveLikeLine: true,
            xLabelFormat: function (x) { // <--- x.getMonth() returns valid index
                var day = x.getDate();
                var month = months[x.getMonth()];
                return day + ' ' + month;
            },
            resize: true,
            pointSize: 0,
            pointStrokeColors: ['#00B5B8', '#FA8E57', '#F25E75'],
            smooth: true,
            gridLineColor: '#E4E7ED',
            numLines: 6,
            gridtextSize: 14,
            lineWidth: 0,
            fillOpacity: 0.9,
            hideHover: 'auto',
            lineColors: ['#00B5B8', '#F25E75']
        });


    });
</script>
<script type="text/javascript">
    function hasMeaningfulSeries(points) {
        if (!Array.isArray(points) || !points.length) {
            return false;
        }

        return points.some(function (point) {
            return Number(point.y || 0) > 0;
        });
    }

    function renderDashboardChartEmpty(selector, title, message) {
        $(selector).html(
            '<div class="dashboard-empty-state chart-empty">' +
            '<div class="empty-state-title">' + title + '</div>' +
            '<div class="empty-state-text">' + message + '</div>' +
            '</div>'
        );
    }

    function drawIncomeChart(dataVisits) {
        $('#dashboard-income-chart').empty();
        if (!hasMeaningfulSeries(dataVisits)) {
            renderDashboardChartEmpty('#dashboard-income-chart', 'No Income Data', 'Income activity for this dashboard will appear here once sales are recorded.');
            return;
        }
        Morris.Area({
            element: 'dashboard-income-chart',
            data: dataVisits,
            xkey: 'x',
            ykeys: ['y'],
            ymin: 'auto 40',
            labels: ['<?php echo $this->lang->line('Amount') ?>'],
            xLabels: "day",
            hideHover: 'auto',
            yLabelFormat: function (y) {
                // Only integers
                if (y === parseInt(y, 10)) {
                    return y;
                } else {
                    return '';
                }
            },
            resize: true,
            lineColors: [
                '#00A5A8',
            ],
            pointFillColors: [
                '#00A5A8',
            ],
            fillOpacity: 0.4,
        });
    }

    function drawExpenseChart(dataVisits2) {

        $('#dashboard-expense-chart').empty();
        if (!hasMeaningfulSeries(dataVisits2)) {
            renderDashboardChartEmpty('#dashboard-expense-chart', 'No Expense Data', 'Expense activity for this dashboard will appear here once transactions are recorded.');
            return;
        }
        Morris.Area({
            element: 'dashboard-expense-chart',
            data: dataVisits2,
            xkey: 'x',
            ykeys: ['y'],
            ymin: 'auto 0',
            labels: ['<?php echo $this->lang->line('Amount') ?>'],
            xLabels: "day",
            hideHover: 'auto',
            yLabelFormat: function (y) {
                // Only integers
                if (y === parseInt(y, 10)) {
                    return y;
                } else {
                    return '';
                }
            },
            resize: true,
            lineColors: [
                '#ff6e40',
            ],
            pointFillColors: [
                '#34cea7',
            ]
        });
    }

    drawIncomeChart(dataVisits);
    drawExpenseChart(dataVisits2);
    $('#dashboard-sales-breakdown-chart').empty();
    <?php if ($has_donut_values) { ?>
    Morris.Donut({
        element: 'dashboard-sales-breakdown-chart',
        data: [{
            label: "<?php echo $this->lang->line('Income') ?>",
            value: <?= intval(amountExchange_s($tt_inc, 0, $this->aauth->get_user()->loc)); ?> },
            {
                label: "<?php echo $this->lang->line('Expenses') ?>",
                value: <?= intval(amountExchange_s($tt_exp, 0, $this->aauth->get_user()->loc)); ?> }
        ],
        resize: true,
        colors: ['#34cea7', '#ff6e40'],
        gridTextSize: 6,
        gridTextWeight: 400
    });
    <?php } else { ?>
    renderDashboardChartEmpty('#dashboard-sales-breakdown-chart', 'No Income vs Expenses Data', 'This comparison chart will appear once dashboard activity is available.');
    <?php } ?>
    $('a[data-toggle=tab').on('shown.bs.tab', function (e) {
        window.dispatchEvent(new Event('resize'));
    });
</script>



<style>
body { background:#f4f6f9;font-family:'Poppins',sans-serif; }
.dashboard-shell{padding-bottom:12px;}
h4.card-title{font-weight:600;}
.card{border-radius:16px;box-shadow:0 3px 10px rgba(0,0,0,0.08);background:linear-gradient(145deg,#fff,#f5f7fa);transition:.2s;}
.card:hover{transform:translateY(-4px);box-shadow:0 6px 16px rgba(0,0,0,0.15);}
.card-body{padding:16px;}
#sortableCards{display:flex;flex-wrap:wrap;gap:15px;justify-content:flex-start;}
.card-item{flex:1 0 19%;min-width:230px;cursor:move;}
.active-filter{border:2px solid #000!important;box-shadow:0 0 10px rgba(0,0,0,0.25);}
.progress{background:#e9ecef;border-radius:8px;height:6px;margin-top:6px;}
.progress-bar{height:6px;border-radius:8px;}
.unit-summary{margin-top:10px;font-size:13px;color:#555;border-top:1px dashed #ccc;padding-top:6px;}
.ui-state-highlight{border:2px dashed #007bff;background:#f0f8ff;min-height:120px;}
.spinner-border{border:0.25em solid #ccc;border-top-color:#007bff;border-radius:50%;width:3rem;height:3rem;animation:spin .8s linear infinite;}
@keyframes spin{100%{transform:rotate(360deg);}}
.success{color:#00b386;} .danger{color:#ff5c5c;} .warning{color:#ffa500;} .primary{color:#007bff;}
.dashboard-empty-state{display:flex;flex-direction:column;justify-content:center;align-items:flex-start;min-height:220px;padding:1.25rem;border:1px dashed #d7e2ef;border-radius:14px;background:#f8fbff;color:#52606d;}
.dashboard-empty-state.compact{min-height:120px;padding:1rem;margin:0.35rem 0;}
.dashboard-empty-state.chart-empty{min-height:260px;align-items:center;text-align:center;}
.empty-state-title{font-size:16px;font-weight:600;color:#243b53;margin-bottom:6px;}
.empty-state-text{font-size:13px;line-height:1.6;color:#6b7c93;}
.seller-dashboard .match-height{align-items:flex-start;}
.seller-dashboard .card{height:auto!important;}
.seller-dashboard .height-450{height:auto!important;}
.seller-dashboard #recent-buyers{height:auto!important;max-height:320px;min-height:0;padding:0.25rem 0.5rem 0.75rem;}
.seller-dashboard #salesbreakdown.card{margin-top:0!important;background:transparent;box-shadow:none;border:0;}
.seller-dashboard #dashboard-sales-breakdown-chart,
.seller-dashboard #dashboard-income-chart,
.seller-dashboard #dashboard-expense-chart{min-height:260px;}
.seller-dashboard .table-responsive{min-height:0;}
.seller-dashboard #recent-orders tbody td,
.seller-dashboard #daily-activity tbody td,
.seller-dashboard #products_table tbody td{vertical-align:middle;}
.seller-dashboard .heading-elements p{margin:0;}
.recent-invoices-header{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.recent-invoices-header .card-title{margin:0;}
.recent-invoices-header .heading-elements-toggle{display:none;}
.recent-invoices-actions{position:static!important;right:auto!important;top:auto!important;display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;margin-left:auto;}
.recent-invoices-actions .btn{margin:0;white-space:nowrap;}
.seller-dashboard .pull-up .card-body{padding:18px;}
.seller-dashboard .is-empty-card .card{background:linear-gradient(145deg,#ffffff,#f8fbff);}
.seller-dashboard .is-empty-card .value{color:#9aa5b1;}
@media (max-width: 767.98px){
    .seller-dashboard #recent-buyers{max-height:none;}
    .dashboard-empty-state.chart-empty{min-height:220px;}
    .recent-invoices-actions{width:100%;justify-content:flex-start;}
}
</style>
