<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>

        <div class="message"></div>
    </div>
    <div class="card-body">


        <form method="post" id="data_form" class="form-horizontal">

            <h5><?php echo $this->lang->line('Edit') ?></h5>
            <hr>

            <div class="form-group row">

                <label class="col-sm-2 col-form-label" for="name"><?php echo $this->lang->line('Name') ?></label>

                <div class="col-sm-4">
                    <input type="text" placeholder="Name"
                           class="form-control margin-bottom  required" name="name" value="<?php echo $attribute['value'] ?>">
                </div>
            </div>
   <div class="form-group row">                <label class="col-sm-2 col-form-label"                       for="pvars"><?php echo $this->lang->line('Variations') ?>*</label>                <div class="col-sm-4">                    <select name="pvars" class="form-control round">                        <?php                        foreach ($variations as $row) {                            $cid = $row['atrid'];                            $title = $row['atrname'];														if($attribute['attribute_id']== $row['atrid']){																$nss= "SELECTED";							}else{																$nss= "";							}                            echo "<option value='$cid' ".$nss.">$title</option>";                        }                        ?>                    </select>                </div>            </div>
            <div class="form-group row">

                <label class="col-sm-2 col-form-label"></label>

                <div class="col-sm-4">
                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                           value="<?php echo $this->lang->line('Edit') ?>" data-loading-text="Adding...">
                    <input type="hidden" value="units/edit" id="action-url">
                    <input type="hidden" value="<?php echo $attribute['id'] ?>" name="id">
                </div>
            </div>


        </form>
    </div>
</div>
