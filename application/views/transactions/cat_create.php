<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>

        <div class="message"></div>
    </div>
    <div class="card-body">


        <form method="post" id="data_form" class="form-horizontal">

            <h5><?php echo $this->lang->line('New Transactions Category') ?></h5>
            <hr>
  <div class="form-group row">                <label class="col-sm-2 col-form-label"                       for="product_cat_name"><?php echo $this->lang->line('Category Name') ?></label>                <div class="col-sm-8">                      <select name="parent_category" class="form-control">                                       <option value='Assets'><?php echo $this->lang->line('Assets') ?> - Assets</option>                    <option value='Expenses'><?php echo $this->lang->line('Expenses') ?> - Expenses</option>                    <option value='Income'><?php echo $this->lang->line('Income') ?> - Income</option>                    <option value='Liabilities'><?php echo $this->lang->line('Liabilities') ?> - Liabilities</option>                    <option value='Equity'><?php echo $this->lang->line('Equity') ?> - Equity</option>                </select>                </div>            </div>  			
            <div class="form-group row">

                <label class="col-sm-2 col-form-label"
                       for="catname">Sub Category</label>

                <div class="col-sm-6">
                    <input type="text" placeholder="Transactions Category Name"
                           class="form-control margin-bottom  required" name="catname">
                </div>
            </div>

            <div class="form-group row">

                <label class="col-sm-2 col-form-label"></label>

                <div class="col-sm-4">
                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                           value="<?php echo $this->lang->line('Add') ?>" data-loading-text="Adding...">
                    <input type="hidden" value="transactions/save_createcat" id="action-url">
                </div>
            </div>


        </form>
    </div>
</div>


