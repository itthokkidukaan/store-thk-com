

<div class="content-body">    

<div class="card">        

<div class="card-header">            <h4 class="card-title"><?php echo $this->lang->line('Purchase Order') ?> <a                        href="<?php echo base_url('purchase/create') ?>"                        class="btn btn-primary btn-sm rounded">                    <?php echo $this->lang->line('Add new') ?></a></h4>            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>            

<div class="heading-elements">                
<ul class="list-inline mb-0">                    
<li><a data-action="collapse"><i class="ft-minus"></i></a></li>                    
<li><a data-action="expand"><i class="ft-maximize"></i></a></li>                    
<li><a data-action="close"><i class="ft-x"></i></a></li>                
</ul>            
</div>        
</div>        

<div class="card-content">            

<div id="notify" class="alert alert-success" style="display:none;">                <a href="#" class="close" data-dismiss="alert">&times;</a>                

<div class="message">
</div>            
</div>            

<div class="card-body">                

<div class="row">                    

<div class="col-md-2"><select name="datetype" id="datetype" class="form-control form-control-sm" autocomplete="off"> 

<option value="invoicedate"> Invoice Date</option>
<option value="invoiceduedate"> Due Date</option>
</select>
</div>                    

<div class="col-md-1">                        
<input type="text" name="start_date" id="start_date"                               class="date30 form-control form-control-sm" autocomplete="off"/>                    
</div>                    

<div class="col-md-1">                        
<input type="text" name="end_date" id="end_date" class="form-control form-control-sm"                               data-toggle="datepicker" autocomplete="off"/>                    
</div> 



<div class="col-md-2">  

                
<select name="supplier" id="supplier" class="form-control form-control-sm" autocomplete="off"> 

<option value="All"> All Supplier</option>
<?php 
foreach($supplierlist as $rows){
	
	echo '<option value="'.$rows['id'].'">'.$rows['name'].' ('.$rows['phone'].') '.'</option>';
	
}

?>
</select>                   
</div> 


<div class="col-md-1">  

                
<select class="form-control form-control-sm" id="txttype" name="txttype">
					<option value="All">All</option>
					<option value="due">Due</option>
					<option value="partial">Partial</option>
					<option value="paid">Paid</option>
					
					</select>               
</div>                    

<div class="col-md-2">                        
<input type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm"/>                    
</div>										

<div class="col-md-2">                        

<div id="totalSale">
</div>
<div id="totalpaid">
</div>

 <div id="totalbal">
</div>  
<div id="totalpaidcash">
</div>                  
</div>                
</div>                
<hr>                
<table id="invoices" class="table table-striped table-bordered zero-configuration " width="100%">                    
<thead>                    
<tr>                        
<th><?php echo $this->lang->line('No') ?>
</th>                        
<th> Order #
</th>                        
<th><?php echo $this->lang->line('Supplier') ?>
</th>                        
<th><?php echo $this->lang->line('Date') ?>
</th> 


   <th>Due Date</th>                        
<th>Total
<th>Balance
</th>                        
<th><?php echo $this->lang->line('Status') ?>
<th>Created Date</th> 
</th>                        
<th class="no-sort"><?php echo $this->lang->line('Settings') ?>
</th>                    
</tr>                    </thead>                    <tbody>                    </tbody>                    <tfoot>                    
<tr>                        
<th><?php echo $this->lang->line('No') ?>
</th>                        
<th> Order #
</th>                        
<th><?php echo $this->lang->line('Supplier') ?>
</th>                        
<th><?php echo $this->lang->line('Date') ?>
</th>  

<th>Due Date
</th>                        
<th>

<div id="totalAmount">
</div>
</th>       
<th>
<div id="totalBal">
</div>

</th>                 
<th><?php echo $this->lang->line('Status') ?>
</th>  
<th>Created Date</th>                       
<th class="no-sort"><?php echo $this->lang->line('Settings') ?>
</th>                    
</tr>                    </tfoot>                
</table>            
</div>        
</div>    
</div>
</div>

<div id="delete_model" class="modal fade">    

<div class="modal-dialog">        

<div class="modal-content">            

<div class="modal-header">
<h4 class="modal-title"><?php echo $this->lang->line('Delete Invoice') ?></h4>   
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">

<span aria-hidden="true">&times;</span></button>            
</div>            

<div class="modal-body">                

<p><?php echo $this->lang->line('delete this invoice') ?> ?</p>            
</div>            

<div class="modal-footer">                
<input type="hidden" id="object-id" value="">                
<input type="hidden" id="action-url" value="purchase/delete_i">                <button type="button" data-dismiss="modal" class="btn btn-primary"                        id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>                <button type="button" data-dismiss="modal"                        class="btn"><?php echo $this->lang->line('Cancel') ?></button>            
</div>        
</div>    
</div>
</div>

<script type="text/javascript">   
 $(document).ready(function () {
     draw_data();
     function draw_data(start_date =  '',  end_date =  '', supplier = 'All', txttype='All', datetype='invoicedate') {
        var table =  $('#invoices').DataTable({
             'processing':  true,
             'serverSide':  true,
             'stateSave':  true,
             lengthMenu:  [ [10,  25,  50,  - 1],  [10,  25,  50,  'All'] ],
             <?php datatable_lang(); ?>
			 responsive :  true,
             'order' :  [],
             'ajax':  {
                 'url':  "<?php echo site_url('purchase/ajax_list')?>",
                 'type' :  'POST',
                 'data' :  {
                     '<?=$this->security->get_csrf_token_name()?>' :  crsf_hash,
                     start_date :  start_date,
                     end_date:  end_date,
                     txttype:  txttype,
                     datetype:  datetype,
                     supplier:  supplier
                },
                'dataSrc':  function (json) {
                    if (json.totalsale !==  undefined) {
                         $('#totalSale').html('<span style="font-size:20px; font-weight:bold; color:green"> Total Purchase: ₹ ' + json.totalsale.toFixed(2) + '</span>');
                         $('#totalbal').html('<span style="font-size:20px; font-weight:bold; color:red"> Total Balance: ₹ ' + json.totalbal.toFixed(2) + '</span>');
                    }
                    else {
                         console.error('totalsale is undefined in the server response.');
                    }
                    if (Array.isArray(json.data)) {
                        return json.data;
                    }
                    else {
                         console.error('Data array is missing or not an array.');
                        return [];
                    }
                }
            },
             'columnDefs':  [ {
                 'targets':  [0],
                 'orderable':  false,
            },
             ],
             dom:  'Blfrtip',
             buttons:  [ {
                 extend:  'excelHtml5',
                 footer:  true,
                 exportOptions:  {
                     columns:  [1,  2,  3,  4,  5]
                }
            } ],
             drawCallback:  function () {
                 calculateTotalAmount();
            },
        });
         function calculateTotalAmount() {
            var total =  0;
             table.column(5,  {
                 search:  'applied'
            }).data().each(function(value,  index) {
                 total +=  parseFloat(value) ||  0;
            });
             $('#totalAmount').html('Total Amount: ' +  total.toFixed(2));


			 var totalbal =  0;
             table.column(6,  {
                 search:  'applied'
            }).data().each(function(value,  index) {
                 totalbal +=  parseFloat(value) ||  0;
            });
             $('#totalBal').html('Total Bal: ' +  totalbal.toFixed(2));
             $('#totalpaid').html('Total Paid: ' +  (total - totalbal));
             $('#totalpaidcash').html('Paid for Purchase: ' +  (total.toFixed(2)- totalbal.toFixed(2)));
        }
    };
     $('#search').click(function () {
        var start_date =  $('#start_date').val();
        var end_date =  $('#end_date').val();
        var txttype =  $('#txttype').val();
        var supplier =  $('#supplier').val();
        var datetype =  $('#datetype').val();
        if (start_date !=  '' &&  end_date !=  '') {
             $('#invoices').DataTable().destroy();
             draw_data(start_date,  end_date, supplier, txttype,datetype);
        }
        else {
             alert("Date range is Required");
        }
    });
});

</script>