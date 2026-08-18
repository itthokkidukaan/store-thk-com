<script>
    $("#checkPermissionAll").click(function(){
        if($(this).is(':checked')){
            $('input[type=checkbox]').prop('checked', true);
        }else{
            $('input[type=checkbox]').prop('checked', false);
        }
    });

    function checkPermissionByGroup(className, checkThis){
        const groupIdName = $("#"+checkThis.id);
        const classCheckBox = $('.'+className+' input');
        if(groupIdName.is(':checked')){
            classCheckBox.prop('checked', true);
        }else{
            classCheckBox.prop('checked', false);
        }
        implementAllChecked();
    }

    function checkSinglePermission(groupClassName, groupID, countTotalPermission) {
        const groupIDCheckBox = $("#"+groupID);
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

        if($('input[type="checkbox"]:checked').length >= (countPermissions + countPermissionGroups)){
            $("#checkPermissionAll").prop('checked', true);
        }else{
            $("#checkPermissionAll").prop('checked', false);
        }
    }

    $(document).ready(function () {
        implementAllChecked();
    });
</script>
