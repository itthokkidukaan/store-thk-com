<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4><?php echo $this->lang->line('Add new') ?> <?php echo $this->lang->line('Accounts') ?></h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="card-content">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <div class="message"></div>
            </div>
            <div class="card-body">
                <form method="post" id="data_form">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Account No') ?> <span style="color: red;">*</span></label>
                            <input type="text" class="form-control required" name="accno" placeholder="<?php echo $this->lang->line('Account No') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Name') ?> <span style="color: red;">*</span></label>
                            <input type="text" class="form-control required" name="holder" placeholder="<?php echo $this->lang->line('Name') ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label">Opening Balance</label>
                            <input type="text" class="form-control" name="intbal" value="0" onkeypress="return isNumber(event)">
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Code') ?></label>
                            <input type="text" class="form-control" name="acode" placeholder="<?php echo $this->lang->line('Code') ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Account Type') ?></label>
                            <select name="account_type" class="form-control">
                                <option value="Basic"><?php echo $this->lang->line('Basic') ?></option>
                                <option value="Assets"><?php echo $this->lang->line('Assets') ?></option>
                                <option value="Expenses"><?php echo $this->lang->line('Expenses') ?></option>
                                <option value="Income"><?php echo $this->lang->line('Income') ?></option>
                                <option value="Liabilities"><?php echo $this->lang->line('Liabilities') ?></option>
                                <option value="Equity"><?php echo $this->lang->line('Equity') ?></option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Category') ?></label>
                            <select name="category" class="form-control">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categorylist as $row) { ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php if (count($locations) > 1) { ?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label"><?php echo $this->lang->line('Location') ?></label>
                            <select name="lid" class="form-control">
                                <?php foreach ($locations as $row) { ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['cname'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <input type="submit" id="submit-data" class="btn btn-success btn-lg margin-bottom" value="<?php echo $this->lang->line('Add new') ?>" data-loading-text="Please Wait...">
                            <input type="hidden" value="accounts/addacc" id="action-url">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
