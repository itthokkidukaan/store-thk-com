<div class="content-body">
    <div class="card">
        <?php if (!empty($account['id'])) { ?>
        <div class="card-header">
            <h4 class="card-title"><?php echo $account['holder'] ?></h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="<?php echo base_url('accounts/edit?id=' . $account['id']) ?>" class="btn btn-warning btn-sm"><i class="icon-pencil"></i> <?php echo $this->lang->line('Edit') ?></a>
                        <a href="<?php echo base_url('accounts') ?>" class="btn btn-secondary btn-sm"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line('Accounts') ?></a>
                    </div>
                </div>
                <hr>
                <?php
                $fields = [
                    'Account No' => $account['acn'],
                    'Name' => $account['holder'],
                    'Code' => $account['code'],
                    'Account Type' => $account['account_type'],
                    'Balance' => amountExchange($account['lastbal'], 0, $this->aauth->get_user()->loc),
                    'Owner' => $account['owner_name'] ? $account['owner_name'] : 'Admin',
                    'Date' => $account['adate']
                ];
                foreach ($fields as $label => $value): ?>
                    <div class="row mb-2">
                        <div class="col-md-3 font-weight-bold"><?php echo $this->lang->line($label) ?: $label ?></div>
                        <div class="col-md-9"><?php echo $value ?></div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="card-content">
            <div class="card-body">
                <div class="alert alert-warning">Account not found.</div>
                <a href="<?php echo base_url('accounts') ?>" class="btn btn-secondary btn-sm"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line('Accounts') ?></a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
