<style>
    .abelText {
         margin-top: 40px;
    }
    .abelTextRemove {
        margin-top: 35px;
    }
    .add_field_button {
        color: white !important;
    }
    .add-more-note {
        margin-left: -120px !important;
    }
</style>
<div class="content-body">

    <div class="card card-block bg-white">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <form method="post" id="product_action" class="card-body">


            <h5><?php echo $this->lang->line('Permission Details') ?> </h5>
            <hr>
            
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label class="form-label"
                        for="name"><?php echo $this->lang->line('GroupName') ?>
                        </label>
                        <input type="text"
                            class="form-control margin-bottom required" name="group_name" value="<?php echo $permission->group_name;?>" id="group_name"
                            placeholder="group name">
                    </div>
                    <div class="col-sm-8">
                        <label class="form-label abelText"></label>
                        <span class="form-control-note"><strong>Note: [ Example:</strong> Demo or Some Name <strong>Like:</strong> Dashboard, Sales, Stock,Project etc ]</span>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label abelText"></label>
                        <span class="form-control-note">If You Need More Field For Permission Name , Please Click Here For Add More...</span>
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label abelText"></label>
                        <a class="btn btn btn-dark btn-sm add_field_button">
                        <?php echo $this->lang->line('AddMore');?>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label abelText"></label>
                        <span class="form-control-note add-more-note"><strong>Note: </strong>[<strong>Like:</strong> Dashboard-View, New-Invoice, Manage-Invoice etc ]</span>
                    </div>
                </div>
                <div class="" id="input_fields_wrap">
                    <?php 
                        foreach($permissions as $key => $permission){
                    ?>
                        <div class="" id="input_fields_wrap_<?php echo $key;?>">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label class="form-label"></label>
                                    <input type="text" class="form-control margin-bottom required" name="name[]" value="<?php echo $permission['name']?>" id="name_<?php echo $key;?>" placeholder="name">
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label abelTextRemove"></label>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm remove_field" data-no="<?php echo $key;?>">Remove</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            <div class="form-group row">

                <div class="col-sm-4">
                    <input type="submit" id="profile_update" class="btn btn-success margin-bottom"
                                       value="<?php echo $this->lang->line('Update') ?>"
                                       data-loading-text="Updating...">
                </div>
            </div>


        </form>
    </div>
    <input type="hidden" id="last_id" value="<?php echo count($permissions);?>">
</div>

<script>
    $(function(){
        var wrapper = $("#input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID
        var count = $('#last_id').val();
        var i = count; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            
           $(wrapper).append('<div class="" id="input_fields_wrap_'+i+'"><div class="form-group row"><div class="col-sm-4"><label class="form-label"></label><input type="text" class="form-control margin-bottom required" name="name[]" id="name_'+i+'" placeholder="name"></div><div class="col-sm-2"><label class="form-label abelTextRemove"></label><a href="javascript:void(0)" class="btn btn-danger btn-sm remove_field" data-no="'+i+'">Remove</a></div></div></div>');
            // $("#name_"+i).prop("required", "true");    
            i++;
        });
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); 
            var num = $(this).attr('data-no');
            $('#input_fields_wrap_'+num).remove();
            // $('#name_'+num).prop("required", "false");
        })
    });
</script>


<script type="text/javascript">
    $("#profile_update").click(function (e) {
        var pId = '<?php echo $id;?>' 
        e.preventDefault();
        var actionurl = baseurl + 'permission/update?pId=' + pId;
        
        actionProduct1(actionurl);
    });
</script>

<script>

    function actionProduct1(actionurl) {

        $.ajax({

            url: actionurl,
            type: 'POST',
            data: $("#product_action").serialize(),
            dataType: 'json',
            success: function (data) {
                $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                $("#notify").removeClass("alert-warning").addClass("alert-success").fadeIn();


                $("html, body").animate({scrollTop: $('html, body').offset().top}, 200);
                $("#product_action").remove();
            },
            error: function (data) {
                $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
                $("#notify").removeClass("alert-success").addClass("alert-warning").fadeIn();
                $("html, body").animate({scrollTop: $('#notify').offset().top}, 1000);

            }

        });


    }
</script>