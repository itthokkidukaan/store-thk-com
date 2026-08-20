<?php
$due = false;
if ($this->input->get('due')) {
    $due = true;
} ?>
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><a
                        href="<?php echo base_url('customers') ?>"
                        class="mr-5">
                    <?php echo $this->lang->line('Clients') ?></a> <a
                        href="<?php echo base_url('customers/create') ?>"
                        class="btn btn-primary btn-sm rounded">
                    <?php echo $this->lang->line('Add new') ?></a> <a
                        href="<?php echo base_url('customers?due=true') ?>"
                        class="btn btn-danger btn-sm rounded">
                    <?php echo $this->lang->line('Due') ?><?php echo $this->lang->line('Clients') ?></a></h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                        <!--  <li>     <a href="#sendMail" data-toggle="modal" data-remote="false"
                           class="btn btn-info btn-sm rounded"
                           data-lang="<?php echo $this->lang->line('Email Selected') ?>"> <span class="fa fa-envelope"></span>
                            <?php echo $this->lang->line('Email Selected') ?></a></li> -->
							<?php
							 if ($this->aauth->get_user()->id ==1) {
								 
           echo '<li> <a href="#assignCustomerModal" data-toggle="modal" data-remote="false"
                           class="btn btn-info btn-sm rounded"
                           data-lang="Assign Customers"> <span class="fa fa-mobile"></span>
                            Assign Employee</a></li>';
        }
                       ?>
							
							
							<li>     <a href="#sendSmsS" data-toggle="modal" data-remote="false"
                           class="btn btn-success btn-sm rounded"
                           data-lang="<?php echo $this->lang->line('SMS Selected') ?>"> <span class="fa fa-mobile"></span>
                            <?php echo $this->lang->line('SMS Selected') ?></a></li>
                    <li><a id="delete_selected"
                           href="#"
                           class="btn btn-danger btn-sm rounded"
                           data-lang="<?php echo $this->lang->line('Delete Selected') ?>">  <span class="fa fa-trash-o"></span>
                            <?php echo $this->lang->line('Delete Selected') ?></a></li>							<li><a id="sendwhatsappaa"
                           href="#sendwhatsapp"
                           class="btn btn-success btn-sm rounded"
                           data-lang="Send Today Price List" data-toggle="modal">  <span class="fa-brands fa-whatsapp"></span> Send Today Price List</a></li>

                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>

                <div class="message"></div>
            </div>
            <div class="card-body">

                <table id="clientstable" class="table table-striped table-bordered zero-configuration" cellspacing="0"
                       width="100%">
                    <thead>
                    <tr>
                        <th> # <input type="checkbox" id="headcheck" class="clientcheck checkbox" username=""></th>
                        <th><?php echo $this->lang->line('Name') ?></th>
                        <?php if ($due) {
                            echo '  <th>' . $this->lang->line('Due') . '</th>';
                        } ?>
                        <th><?php echo $this->lang->line('Address') ?></th>
                        <th><?php echo $this->lang->line('Email') ?></th>
                        <th><?php echo $this->lang->line('Phone') ?></th>
                        <th>Assigned Employee</th>
                        <th>Created By Seller</th>
                        <th><?php echo $this->lang->line('Settings') ?></th>


                    </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('Name') ?></th>
                        <?php if ($due) {
                            echo '  <th>' . $this->lang->line('Due') . '</th>';
                        } ?>
                        <th><?php echo $this->lang->line('Address') ?></th>
                        <th>Email</th>
                        <th><?php echo $this->lang->line('Phone') ?></th>
                        <th>Assigned Employee</th>
                        <th>Created By Seller</th>
                        <th><?php echo $this->lang->line('Settings') ?></th>


                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>

<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Delete Customer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php echo $this->lang->line('are_you_sure_delete_customer') ?></p>
            </div>
            <div class="modal-footer">
                   <input type="hidden" class="form-control"
                           id="object-id" name="deleteid" value="0">
                <input type="hidden" id="action-url" value="customers/delete_i">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete-confirm"><?php echo $this->lang->line('Delete') ?></button>
                <button type="button" data-dismiss="modal" class="btn"><?php echo $this->lang->line('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<div id="sendMail" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Email Selected') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="sendmail_form"><input type="hidden"
                                                name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                                value="<?php echo $this->security->get_csrf_hash(); ?>">



                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Subject') ?></label>
                            <input type="text" class="form-control"
                                   name="subject" id="subject">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Message') ?></label>
                            <textarea name="text" class="summernote" id="contents" title="Contents"></textarea></div>
                    </div>




                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                <button type="button" class="btn btn-primary"
                        id="sendNowSelected"><?php echo $this->lang->line('Send') ?></button>
            </div>
        </div>
    </div>
    </div>			


<!-- Assign Customers Modal -->
<div id="assignCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Customers to Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignCustomerForm">
				<h4>Selected Customers:</h4>
<ul id="selectedCustomersList"></ul>

                    <div class="form-group">
                        <label for="employeeSelect">Select Employee:</label>
                        <select id="employeeSelect" class="form-control" name="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            <!-- Employee options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                      
                        <ul id="selectedCustomers" class="list-group">
                            <!-- Selected customers will be displayed here -->
                        </ul>
					<div id="selectedCustomersList"></div>
                    </div>
					
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="assignCustomersButton">Assign</button>
            </div>
        </div>
    </div>
</div>


	<div id="sendwhatsapp" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Send Today Price</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="sendprice_form"><input type="hidden"
                                                name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                                value="<?php echo $this->security->get_csrf_hash(); ?>">



                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote">Category</label> <div class="dropdown">
    <button class="form-control" type="button">Select Categories</button>
    <div class="selectedcat"></div>
    <div class="dropdown-content">
        <label>
            <input type="checkbox" id="select-all" value="0"> Select All
        </label>
        <?php 
        foreach($categorylist as $nrow){ 
            echo '<label><input type="checkbox" class="category-checkbox" value="'.$nrow['id'].'" data-name="'.$nrow['name'].'"> '.$nrow['name'].'</label>';
        } 
        ?>
    </div>
</div>
                         <input type="hidden" Name="catid" id="catid">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Message') ?></label>
                            <textarea name="clientext" class="form-control" id="contents" title="Contents"></textarea></div>
                    </div>

<input type="hidden" id="selectedClients" name="selectedClients" value="">


                </form>
            </div>			<a href="<?=base_url()?>billing/printproductlist?catid=All" id="pricelistlink" target="_blank">Download Price</a>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                <button type="button" class="btn btn-primary"
                        id="sendpricelist"><?php echo $this->lang->line('Send') ?></button>
            </div>
        </div>
    </div>
    </div>

        <div id="sendSmsS" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('SMS Selected') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body">
                <form id="sendsms_form"><input type="hidden"
                                                name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                                value="<?php echo $this->security->get_csrf_hash(); ?>">



                    <div class="row">
                        <div class="col mb-1"><label
                                    for="shortnote"><?php echo $this->lang->line('Message') ?></label>
                            <textarea name="message" class="form-control" rows="3" cols="60"></textarea></div>
                    </div>


                    <input type="hidden" id="action-url" value="communication/send_general">


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                <button type="button" class="btn btn-primary"
                        id="sendSmsSelected"><?php echo $this->lang->line('Send') ?></button>
            </div>
        </div>
    </div>

      </div> <style>        .dropdown {            position: relative;            display: inline-block;        }        .dropdown-content {            display: none;            position: absolute;            background-color: #f9f9f9;            min-width: 250px;            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);            z-index: 1;            padding: 10px;            border: 1px solid #ccc;            max-height: 200px;            overflow-y: auto;        }        .dropdown:hover .dropdown-content {            display: block;        }        .dropdown-content label {            display: block;            margin: 5px 0;        }  

.selectedcat {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.selectedcat .category-tag {
    display: inline-flex;
    align-items: center;
    background-color: #007bff;
    color: #fff;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 14px;
    font-weight: 500;
}

.selectedcat .category-tag .remove-btn {
    margin-left: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
}

.selectedcat .category-tag .remove-btn:hover {
    color: #ff0000;
}


	  </style>

    <script type="text/javascript">
	$(document).ready(function () {
    let selectedCustomers = [];

    // Update the selected customers list UI
    function updateSelectedCustomersUI() {
        let selectedList = $('#selectedCustomersList');
        selectedList.empty();

        selectedCustomers.forEach(function (customer) {
            if (customer.id && customer.username) {
                selectedList.append('<li id="customer-'+ customer.id+'">'+customer.username +'<button class="remove-customer" data-id="'+customer.id+'">&times;</button></li>');
            }
        });
    }

    // Toggle customer selection
    function toggleCustomerSelection(customerId, customerUsername, isChecked) {
        if (isChecked) {
            if (!selectedCustomers.some(c => c.id === customerId)) {
                selectedCustomers.push({ id: customerId, username: customerUsername });
            }
        } else {
            selectedCustomers = selectedCustomers.filter(c => c.id !== customerId);
        }
        updateSelectedCustomersUI();
    }

    // Handle individual checkbox change
    $('#clientstable').on('change', '.clientcheck', function () {
		
	
        let customerId = $(this).attr('id');
        let customerUsername = $(this).attr('username');

        toggleCustomerSelection(customerId, customerUsername, $(this).is(':checked'));
        if ($('.clientcheck:checked').length === $('.clientcheck').length) {
            $('#headcheck').prop('checked', true);
        } else {
            $('#headcheck').prop('checked', false);
        }
    });

    // Handle Select All checkbox
    $('#headcheck').on('change', function () {
        let isChecked = $(this).is(':checked');
        selectedCustomers = []; // Reset selected customers array

        $('.clientcheck').each(function () {
            $(this).prop('checked', isChecked);
            let customerId = $(this).attr('id');
            let customerUsername = $(this).attr('username');

            if (isChecked && customerId && customerUsername) {
                selectedCustomers.push({ id: customerId, username: customerUsername });
            }
        });

        updateSelectedCustomersUI();
    });

    // Handle remove customer button click
    $(document).on('click', '.remove-customer', function () {
        let customerId = $(this).data('id');
        selectedCustomers = selectedCustomers.filter(c => c.id !== customerId);
        $(`.clientcheck[value="${customerId}"]`).prop('checked', false);

        updateSelectedCustomersUI();
    });

    // Handle Assign Customers button click
    $('#assignCustomersButton').on('click', function () {
        let employeeId = $('#employeeSelect').val();

        if (!employeeId) {
            alert('Please select an employee');
            return;
        }

        if (selectedCustomers.length === 0) {
            alert('Please select at least one customer');
            return;
        }

        // Send selected customers to the server
        $.ajax({
            url: '<?=base_url("customers/assign_customers")?>',
            method: 'POST',
            data: {
                employee_id: employeeId,
                customers: selectedCustomers.map(c => c.id),
            },
            success: function (response) {
                alert('Customers assigned successfully!');
              //  location.reload();
            },
            error: function () {
                alert('An error occurred while assigning customers.');
            }
        });
    });
});



	
	$(document).ready(function () {
		
	/* 	$('#assignCustomersButton').click(function () {
    let employeeId = $('#employeeSelect').val();

    if (!employeeId) {
        alert('Please select an employee');
        return;
    }

    if (selectedCustomers.length === 0) {
        alert('Please select at least one customer');
        return;
    }

    $.ajax({
        url: '<?=base_url('customers/assign_customers')?>', // Backend endpoint for assigning customers
        method: 'POST',
        data: {
            employee_id: employeeId,
            customers: selectedCustomers,
        },
        success: function (response) {
            alert('Customers assigned successfully!');
            $('#assignCustomerModal').modal('hide');
            location.reload(); // Reload the page to reflect changes
        },
        error: function () {
            alert('Failed to assign customers');
        }
    });
}); */

 $('#assignCustomerModal').on('show.bs.modal', function () {
    $.ajax({
        url: '<?=base_url('customers/get_employees')?>', // Backend endpoint to fetch employees
        method: 'GET',
        success: function (response) {
            let data;
            try {
                // Parse response if it's a JSON string
                data = typeof response === 'string' ? JSON.parse(response) : response;
            } catch (error) {
                alert('Failed to parse server response');
                console.error(error);
                return;
            }

            let employeeSelect = $('#employeeSelect');
            employeeSelect.empty(); // Clear the dropdown
            employeeSelect.append('<option value="">-- Select Employee --</option>'); // Default option

            if (Array.isArray(data)) {
                data.forEach(function (employee) {
                    employeeSelect.append('<option value="' + employee.id + '">' + employee.username + '</option>');
                });
            } else {
                alert('Unexpected data format received');
            }
        },
        error: function () {
            alert('Failed to fetch employees');
        }
    });
});





});

	
		
		 function sendCallRequest(mobileNumber) {
       
        const mobileRegex = /^[6-9]\d{9}$/;
        if (!mobileRegex.test(mobileNumber)) {
            alert("Invalid mobile number!");
            return;
        }

      
        const confirmCall = confirm("Do you want to send a call request?");
        if (!confirmCall) {
            return;
        }

    
        $("#loader").show();

      
        $.ajax({
            url: "<?=base_url('customers/send_request')?>", 
            method: "POST",
            data: { mobile: mobileNumber },
            dataType: "json",
            success: function (response) {
                // Hide loader
                $("#loader").hide();

                if (response.Status === "Success") {
                    alert(`Request successful! Ref no: ${response.refno}`);
                } else {
                    alert(`Error: ${response.errorMessage || "Unknown error"}`);
                }
            },
            error: function () {
                // Hide loader
                $("#loader").hide();
                alert("An error occurred while sending the request.");
            },
        });
    }
		
		
	
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 100,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['fullscreen', ['fullscreen']],
                ['codeview', ['codeview']]
            ]
        });



        $('#clientstable').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            responsive: true,
            <?php datatable_lang();?>
            'order': [],
            'ajax': {
                'url': "<?php echo site_url('customers/load_list')?>",
                'type': 'POST',
                'data': {'<?=$this->security->get_csrf_token_name()?>': crsf_hash <?php if ($due) echo ",'due':true" ?> }
            },
            'columnDefs': [
                {
                    'targets': [0],
                    'orderable': false,
                },
            ], dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ],
        });


        $(document).on('click', "#delete_selected", function (e) {
            e.preventDefault();
                if ($("#notify").length == 0) {
        $("#c_body").html('<div id="notify" class="alert" style="display:none;"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="message"></div></div>');
    }
            alert($(this).attr('data-lang'));
            jQuery.ajax({
                url: "<?php echo site_url('customers/delete_i')?>",
                type: 'POST',
                data: $("input[name='cust[]']:checked").serialize() + '&<?=$this->security->get_csrf_token_name()?>=' + crsf_hash + '<?php if ($due) echo "&due=true" ?>',
                  dataType: 'json',
                success: function (data) {
                    $("input[name='cust[]']:checked").closest('tr').remove();
                       $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                            $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                    $("html, body").animate({scrollTop: $('#notify').offset().top}, 1000);
                }
            });
        });


        //uni sender
$('#sendMail').on('click', '#sendNowSelected', function (e) {
       e.preventDefault();
         $("#sendMail").modal('hide');
                     if ($("#notify").length == 0) {
        $("#c_body").html('<div id="notify" class="alert" style="display:none;"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="message"></div></div>');
    }
            jQuery.ajax({
                url: "<?php echo site_url('customers/sendSelected')?>",
                type: 'POST',
                data: $("input[name='cust[]']:checked").serialize() + '&'+$("#sendmail_form").serialize(),
                  dataType: 'json',
                success: function (data) {
                   $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                        $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                    $("html, body").animate({scrollTop: $('#notify').offset().top}, 1000);
                }
            });
});

$('#sendSmsS').on('click', '#sendSmsSelected', function (e) {
       e.preventDefault();
         $("#sendSmsS").modal('hide');
                     if ($("#notify").length == 0) {
        $("#c_body").html('<div id="notify" class="alert" style="display:none;"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="message"></div></div>');
    }
            jQuery.ajax({
                url: "<?php echo site_url('customers/sendSmsSelected')?>",
                type: 'POST',
                data: $("input[name='cust[]']:checked").serialize() + '&'+$("#sendsms_form").serialize(),
                  dataType: 'json',
                success: function (data) {
                   $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                        $("#notify").removeClass("alert-danger").addClass("alert-success").fadeIn();
                    $("html, body").animate({scrollTop: $('#notify').offset().top}, 1000);
                }
            });
});



    });
	
	
	$(document).ready(function () {
    $('#sendpricelist').on('click', function () {

        if (!$('#selectedClients').val()) {
            showResponseMessage('error', 'Please select at least one customer with a valid phone number.');
            return;
        }

        let $button = $(this);
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');


        let formData = $('#sendprice_form').serialize();
   $.ajax({
    url: '<?= base_url("communication/whatsapp_pricelist") ?>',
    type: 'POST',
    data: formData,
    dataType: 'json',   // ⭐ IMPORTANT
    success: function (response) {

        let results = response.results || [];
        let successCount = results.filter(function (r) { return r.status === 'success'; }).length;
        let failCount = results.length - successCount;

        if (response.status === 'success' && failCount === 0) {
            alert('Message sent successfully to ' + successCount + ' customer(s)!');
        } else if (successCount > 0) {
            showResponseMessage('error', 'Sent to ' + successCount + ' customer(s), failed for ' + failCount + '. Check the number(s) and try again.');
        } else {
            showResponseMessage('error', response.message || 'Message sending failed! Please check the selected customer(s) phone number.');
        }
    },
    error: function () {
        showResponseMessage('error', 'Something went wrong!');
    },
    complete: function () {
        $button.prop('disabled', false).html('Send');
    }
});

    });

    function showResponseMessage(type, message) {
        let messageClass = type === 'success' ? 'alert-success' : 'alert-danger';
        let $messageDiv = $('<div>')
            .addClass(`alert ${messageClass} mt-3`)
            .text(message)
            .hide()
            .fadeIn();

        $('.modal-body').append($messageDiv);

        // Auto-hide the message after 3 seconds
        setTimeout(() => {
            $messageDiv.fadeOut(() => $messageDiv.remove());
        }, 3000);
    }
});


    $(document).ready(function () {        

	$('#headcheck').on('click', function () {   
	var checkedStatus = this.checked;             
	
	$('#clientstable tbody input[type="checkbox"]').each(function () {            


    this.checked = checkedStatus;         


    });					 


	});     


	});		
	
	</script>
  <script>$(document).ready(function () {
    function updateCatidAndUrl() {
        // Get selected category IDs
        let catidArray = $('.category-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        let catid = catidArray.length > 0 ? catidArray.join(',') : 'All';
        $('#catid').val(catid);

        let nurl = '<?= base_url() ?>billing/printproductlist?catid=' + $('#catid').val();
        $('#pricelistlink').attr('href', nurl);

        
        let selectedNames = $('.category-checkbox:checked').map(function () {
            return '<span class="category-tag" data-value="'+$(this).val()+'">'+$(this).data("name")+'<span class="remove-btn" data-value="'+$(this).val()+'">&times;</span></span>';
        }).get();

        $('.selectedcat').html(selectedNames.join(''));
    }


    $('#select-all').on('change', function () {
        $('.category-checkbox').prop('checked', this.checked);
        updateCatidAndUrl();
    });


    $('.category-checkbox').on('change', function () {
        if (!$(this).is(':checked')) {
            $('#select-all').prop('checked', false);
        }

        if ($('.category-checkbox:checked').length === $('.category-checkbox').length) {
            $('#select-all').prop('checked', true);
        }

        updateCatidAndUrl();
    });

    $('.selectedcat').on('click', '.remove-btn', function () {
        let value = $(this).data('value');
        $(`.category-checkbox[value="${value}"]`).prop('checked', false);
        updateCatidAndUrl();
    });

    updateCatidAndUrl(); 
});
/* $(document).ready(function () {
    
    $('#clientstable').on('change', '.clientcheck', function () {
        updateHiddenInput();
    });

    
    $('#headcheck').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.clientcheck').prop('checked', isChecked);
        updateHiddenInput();
    });

    function updateHiddenInput() {
        let selectedValues = [];
        
       
        $('.clientcheck:checked').each(function () {
            selectedValues.push($(this).val());
        });

       
        let uniqueValues = [...new Set(selectedValues)].filter(value => value.trim() !== '');

      
        $('#selectedClients').val(uniqueValues.join(','));
    }
}); */





$(document).ready(function () {
    
    $('#clientstable').on('change', '.clientcheck', function () {
        updateHiddenInput();
    });

 
    $('#headcheck').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.clientcheck').prop('checked', isChecked);
        updateHiddenInput();
    });

    function updateHiddenInput() {
        let selectedValues = [];

        // Only real customer rows carry name="cust[]"; the header "select all"
        // checkbox shares the .clientcheck class but has no phone number.
        $('.clientcheck[name="cust[]"]:checked').each(function () {
            selectedValues.push($(this).val());
        });

        let uniqueValues = [...new Set(selectedValues)].filter(value => value.trim() !== '');

        let formattedValues = uniqueValues.map(function (value) {
            // Strip everything except digits so spaces/dashes/+ don't break formatting
            let digits = value.replace(/\D/g, '');

            // Already has the 91 country code (12 digits starting with 91) - keep as is
            if (digits.length === 12 && digits.startsWith('91')) {
                return digits;
            }

            // Strip a single leading 0 (local dialing prefix) before adding the country code
            if (digits.length === 11 && digits.startsWith('0')) {
                digits = digits.substring(1);
            }

            return '91' + digits;
        }).filter(function (value) {
            // A valid Indian mobile number formatted with country code is 12 digits (91 + 10)
            return value.length === 12;
        });

        $('#selectedClients').val(formattedValues.join(','));
    }
});


  </script>		