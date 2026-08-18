<div class="content-body">
    <div class="card card-body">
        <div class="">
            <h4><?php echo $product['title'] . ' ';
                echo $this->lang->line('Statements') ?></h4>
            <div class="card ">


                <div class="row ">
                    

<div class="col-md-12 m-1">
                							

<form action="<?php echo base_url() ?>productcategory/warehouse_report?id=<?=$_GET['id']?>" method="post" role="form">    
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"        value="<?php echo $this->security->get_csrf_hash(); ?>">    
<input name="id" type="hidden" value="<?php echo $product['id'] ?>">    

<div class="form-group row">        
<label class="col-sm-2 col-form-label" for="pay_cat">Type
</label>        

<div class="col-md-4">            
<select name="r_type" id="r_type" class="form-control">                

<option value='1'>Sales</option>                

<option value='2'>Purchase</option>                
<option value='4'>Purchase and Sales</option>                

<option value='3'>Stock Transfer</option>            
</select>        
</div>        

<div class="col-md-4">            
<select name="reportwise" id="reportwise" class="form-control">                

<option value='1'>Date Wise Report</option>             

<option value='2'>Product Wise Report</option>                         

<option value='3' class="customer-opt d-none">Customer Wise Report</option>                

<option value='4' class="supplier-opt d-none">Supplier Wise Report</option>            
</select>        
</div>    
</div>    

<div class="form-group row">   
   
<label class="col-md-2 control-label" for="sdate">From Date
</label>        

<div class="col-md-4">            
<input type="text" class="form-control required" placeholder="Start Date" name="s_date" id="sdate"                autocomplete="false">        
  
</div> 
     
      

<div class="col-md-4">            
<input type="text" id="product-search" class="form-control" placeholder="Search Product" autocomplete="off">            
<input type="hidden" name="product_id" id="product_id">            

<div id="product-results" class="autocomplete-box">
</div>        
 
</div>  

   
</div>    

<div class="form-group row">        
<label class="col-md-2 control-label" for="edate">To Date
</label>        

<div class="col-md-4">            
<input type="text" class="form-control required" placeholder="End Date" name="e_date" id="edate"                data-toggle="datepicker" autocomplete="false">        
</div>    
</div>    
<!-- Dynamic Customer/Supplier Search Field -->    

<div class="form-group row" id="party-wrapper" style="display: none;">        
<label class="col-md-2 control-label" for="party-search">Search
</label>        

<div class="col-md-4">            
<input type="text" id="party-search" class="form-control" placeholder="Search..." autocomplete="off">            
<input type="hidden" name="party_id" id="party-id">            

<div id="party-results" class="autocomplete-box">
</div>        
</div>    
</div>    

<div class="form-group row">        
<label class="col-md-2 col-form-label">
</label>        

<div class="col-md-4">            
<input type="submit" class="btn btn-primary btn-md" value="View">        
</div>    
</div>
</form>
                    
</div>
                </div>

            </div>

        </div>

    </div>

</div>


<script>
$(document).ready(function () {
    const $productSearch = $("#product-search");
    const $productResults = $("#product-results");
    const token = "<?php echo $this->security->get_csrf_hash(); ?>";

    $productSearch.on("keyup", function () {
        const keyword = $(this).val();
        if (keyword.length < 2) {
            $productResults.html("");
            return;
        }
        $.ajax({
            url: "<?php echo base_url('search_products/productserchnew'); ?>",
            method: "GET",
            data: { keyword: keyword, ci_csrf_token: token },
            success: function (data) {
                $productResults.html(data);
            },
        });
    });

    window.selectProduct = function (id, name) {
        $("#product_id").val(id);
        $("#product-search").val(name);
        $productResults.html("");
    };
});


$(document).ready(function () {
    const $rType = $("#r_type");
    const $reportwise = $("#reportwise");
    const $partyWrapper = $("#party-wrapper");
    const $partySearch = $("#party-search");
    const $partyResults = $("#party-results");
    let partyType = "";
    function updateReportwiseOptions() {
        const rType = $rType.val();
        $(".customer-opt, .supplier-opt").addClass("d-none");
        $reportwise.val("1");
        if (rType === "1") {
            $(".customer-opt").removeClass("d-none");
        } else if (rType === "2") {
            $(".supplier-opt").removeClass("d-none");
        }
    }
    function showPartyInput(type) {
        $partyWrapper.show();
        partyType = type;
        const placeholder = type === "customer" ? "Search Customer..." : "Search Supplier...";
        $partySearch.attr("placeholder", placeholder);
        $partySearch.val("");
        $("#party-id").val("");
        $partyResults.html("");
    }
    function hidePartyInput() {
        $partyWrapper.hide();
        $partySearch.val("");
        $("#party-id").val("");
        $partyResults.html("");
    }
    $rType.on("change", function () {
        updateReportwiseOptions();
        hidePartyInput();
    });
    $reportwise.on("change", function () {
        const selected = $(this).val();
        if (selected === "3" && $rType.val() === "1") {
            showPartyInput("customer");
        } else if (selected === "4" && $rType.val() === "2") {
            showPartyInput("supplier");
        } else {
            hidePartyInput();
        }
    });
    $partySearch.on("keyup", function () {
        const keyword = $(this).val();
        const token = "<?php echo $this->security->get_csrf_hash(); ?>";
        if (keyword.length < 2) {
            $partyResults.html("");
            return;
        }
        const url = partyType === "customer" ? "<?= base_url('search_products/csearch') ?>" : "<?= base_url('search_products/supplier') ?>";
        $.ajax({
            url: url,
            method: "GET",
            data: { keyword: keyword, ci_csrf_token: token },
            success: function (data) {
                $partyResults.html(data);
            },
        });
    });
    window.selectCustomer = function (id, name) {
        $("#party-id").val(id);
        $("#party-search").val(name);
        $("#party-results").html("");
    };
    window.selectSupplier = function (id, name) {
        $("#party-id").val(id);
        $("#party-search").val(name);
        $("#party-results").html("");
    };
    updateReportwiseOptions();
});


</script>







<style>.autocomplete-box {    border: 1px solid #ccc;    max-height: 200px;    overflow-y: auto;    background: #fff;    position: absolute;    z-index: 1000;    width: 100%;}.autocomplete-box ol {    list-style: none;    padding-left: 0;    margin: 0;}.autocomplete-box li {    padding: 8px;    cursor: pointer;    border-bottom: 1px solid #eee;}.autocomplete-box li:hover {    background-color: #f1f1f1;}#party-results {    border: 1px solid #ccc;    background: #fff;    max-height: 200px;    overflow-y: auto;    padding: 0;    margin-top: 5px;    border-radius: 4px;    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);    position: absolute;    width: 100%;    z-index: 1000;}#party-results li {    padding: 10px 15px;    cursor: pointer;    list-style: none;    border-bottom: 1px solid #eee;}#party-results li:last-child {    border-bottom: none;}#party-results li:hover {    background: #f0f0f0;}#party-results p {    margin: 0;    font-size: 14px;    color: #333;}#party-results li span{		display:none;}</style>