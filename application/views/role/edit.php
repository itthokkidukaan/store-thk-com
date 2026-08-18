<style>
    .check-all {
         margin-left: 30px;
         margin-top: -2px;
    }
    .group-checkbox {
        margin-left: 10px;
        margin-top: 2px;
    }
    .bold-text {
        font-weight: bold;
    }
</style>
<div class="content-body">

    <div class="card card-block bg-white">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <form method="post" id="product_action" class="card-body">


            <h5><?php echo $this->lang->line('Role Details') ?> </h5>
            <hr>
           
            <div class="form-group row">
                <input type="hidden" name="id" value="<?php echo $id;?>">
                <label class="col-sm-6 col-form-label"
                       for="name"><?php echo $this->lang->line('RoleName') ?>
                    <small class="error">(Use Only a-z)</small>
                </label>
                <div class="col-sm-10">
                    <input type="text"
                           class="form-control margin-bottom required" name="rolename"
                           placeholder="rolename" value="<?php echo $role->name;?>">
                </div>
            </div>
            <?php 
                $CI =& get_instance();
                $CI->load->model('Role_model');
                $allHasPermission = $CI->Role_model->roleHasPermissions($role, $all_permissions);
               
            ?>
            <div class="form-group row">
                <h5 class="card-title">Permission</h5>
                <div class="form-check form-switch check-all" dir="ltr">
                <input type="checkbox" class="form-check-input" id="checkPermissionAll" value="1" <?php echo $allHasPermission ? 'checked' : '' ?>>
                <label class="form-check-label" for="checkPermissionAll">Select All</label>
                </div>
            </div>
            <div class="form-group row">
                <?php
                    $i = 1;
                    foreach($permission_groups as $groupname => $group){
                    $this->db->select('id,name');
                    $this->db->from('geopos_permissions');
                    $this->db->where('group_name',$group['name']);
                    $query = $this->db->get();
                    $permissions = $query->result_array();
                    //echo '<pre>';print_r($permissions);die;
                    $j = 1;
                    $CI =& get_instance();
                    $CI->load->model('Role_model');
                    $hasPermission = $CI->Role_model->roleHasPermissions($role, $permissions);
                ?>
                <div class="col-md-12 permission-group">
                    <h4 class="card-title group-title">
                        <div class="form-check form-switch mb-3 group-form-switch bold-text" dir="ltr">
                            <?php echo $group['name']?>
                            <input type="checkbox" class="form-check-input group-checkbox" id="management_<?php echo $i;?>"
                            onclick="checkPermissionByGroup('role-<?php echo $i;?>-management-checkbox', this)" <?php echo $hasPermission ? 'checked' : '' ?>>
                            <label class="form-check-label" for="management_<?php echo $i;?>"></label>
                        </div>
                    </h4>
                    <div class="col-md-12 row role-<?php echo $i;?>-management-checkbox" style="margin-left: 20px;">
                        <?php
                           
                            foreach($permissions as $permission){
                                $str = $permission['name'];
                                $parts = explode('-', $str);
                                $capitalized = array_map('ucfirst', $parts);
                                $permissionName = implode('-', $capitalized);
                                $CI =& get_instance();
                                $CI->load->model('Role_model');
                                $hasPermissionTo = $CI->Role_model->hasPermissionTo($role->id, $permission['name']);
                        ?>
                        <div class="col-md-2">
                            <div class="form-check form-switch mb-3" dir="ltr">
                                <input type="checkbox" class="form-check-input" id="checkPermission_<?php echo $permission['id']?>" name="permissions[]" value="<?php echo $permission['id']?>" <?php echo $hasPermissionTo ? 'checked' : '' ?>>
                                <label class="form-check-label" for="checkPermission_<?php echo $permission['id']?>"><?php echo $permissionName?></label>
                            </div>
                        </div>
                        <?php $j++; } ?>
                    </div>
                </div>
                <?php $i++; } ?>
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

</div>

<script>
    $("#checkPermissionAll").click(function(){
        if($(this).is(':checked')){
            // check all the checkbox
            $('input[type=checkbox]').prop('checked', true);
        }else{
            // un check all the checkbox
            $('input[type=checkbox]').prop('checked', false);
        }
    });

    function checkPermissionByGroup(className, checkThis){
        const groupIdName = $("#"+checkThis.id);
        const classCheckBox = $('.'+className+' input');
        console.log(classCheckBox);
        if(groupIdName.is(':checked')){
                classCheckBox.prop('checked', true);
            }else{
                classCheckBox.prop('checked', false);
            }
        implementAllChecked();
    }

    function checkSinglePermission(groupClassName, groupID, countTotalPermission) {
        const classCheckbox = $('.'+groupClassName+ ' input');
        const groupIDCheckBox = $("#"+groupID);

        // if there is any occurance where something is not selected then make selected = false
        if($('.'+groupClassName+ ' input:checked').length == countTotalPermission){
            groupIDCheckBox.prop('checked', true);
        }else{
            groupIDCheckBox.prop('checked', false);
        }
        implementAllChecked();
    }

    function implementAllChecked() {
            const all_permissions = '<?php echo count($all_permissions);?>';
            const permission_groups = '<?php echo count($permission_groups);?>';
            const countPermissions = all_permissions;
            const countPermissionGroups = <?php echo count($permission_groups) ?>;

        //  console.log((countPermissions + countPermissionGroups));
        //  console.log($('input[type="checkbox"]:checked').length);

            if($('input[type="checkbox"]:checked').length >= (countPermissions + countPermissionGroups)){
            $("#checkPermissionAll").prop('checked', true);
        }else{
            $("#checkPermissionAll").prop('checked', false);
        }
    }
</script>




<script type="text/javascript">
    $("#profile_update").click(function (e) {
        var roleId = '<?php echo $id;?>' 
        e.preventDefault();
        var actionurl = baseurl + 'role/update?roleId=' + roleId;
        
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