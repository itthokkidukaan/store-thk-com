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
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Edit Seller Permissions</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('manage-seller') ?>">Seller</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 main-content">
                    <div class="card content-area p-4">
                        <div class="card-header border-0">
                            <h5 class="mb-0">Seller Details</h5>
                        </div>
                        <div class="card-innr">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($seller['username']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Store Name</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($seller['store_name']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($seller['email']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($seller['mobile']) ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card content-area p-4 mt-3">
                        <div class="card-header border-0">
                            <h5 class="mb-0">Permissions</h5>
                        </div>
                        <div class="card-innr">
                            <?php if ($this->session->flashdata('message')): ?>
                                <div class="alert alert-success">
                                    <?= $this->session->flashdata('message') ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" action="<?= base_url('sellers/update_permissions') ?>">
                                <input type="hidden" name="seller_id" value="<?= (int)$seller['user_id'] ?>">

                                <h6 class="mb-2">Seller Settings</h6>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="require_products_approval" <?= !empty($seller['permissions']['require_products_approval']) ? 'checked' : '' ?>>
                                        Require Products Approval
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="customer_privacy" <?= !empty($seller['permissions']['customer_privacy']) ? 'checked' : '' ?>>
                                        Hide Customer Details
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="view_order_otp" <?= !empty($seller['permissions']['view_order_otp']) ? 'checked' : '' ?>>
                                        Allow View Order OTP
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="assign_delivery_boy" <?= !empty($seller['permissions']['assign_delivery_boy']) ? 'checked' : '' ?>>
                                        Allow Assign Delivery Boy
                                    </label>
                                </div>

                                <h6 class="mt-4 mb-2">Permissions (same as Role)</h6>
                                <div class="form-check form-switch mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="checkPermissionAll" value="1">
                                    <label class="form-check-label" for="checkPermissionAll">Select All</label>
                                </div>
                                <div class="form-group row">
                                    <?php
                                        $i = 1;
                                        foreach ($permission_groups as $groupname => $group) {
                                            $this->db->select('id,name');
                                            $this->db->from('geopos_permissions');
                                            $this->db->where('group_name', $group['name']);
                                            $query = $this->db->get();
                                            $permissions = $query->result_array();
                                            $j = 1;
                                            $hasPermission = true;
                                            foreach ($permissions as $permission) {
                                                if (!in_array($permission['name'], $seller_permission_names, true)) {
                                                    $hasPermission = false;
                                                    break;
                                                }
                                            }
                                    ?>
                                    <div class="col-md-12 permission-group">
                                        <h4 class="card-title group-title">
                                            <div class="form-check form-switch mb-3 group-form-switch" dir="ltr">
                                                <?php echo $group['name']?>
                                                <input type="checkbox" class="form-check-input group-checkbox" id="management_<?php echo $i;?>"
                                                onclick="checkPermissionByGroup('role-<?php echo $i;?>-management-checkbox', this)" <?php echo $hasPermission ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="management_<?php echo $i;?>"></label>
                                            </div>
                                        </h4>
                                        <div class="col-md-12 row role-<?php echo $i;?>-management-checkbox" style="margin-left: 20px;">
                                            <?php foreach ($permissions as $permission) {
                                                $str = $permission['name'];
                                                $parts = explode('-', $str);
                                                $capitalized = array_map('ucfirst', $parts);
                                                $permissionName = implode('-', $capitalized);
                                                $checked = in_array($permission['name'], $seller_permission_names, true);
                                            ?>
                                            <div class="col-md-2">
                                                <div class="form-check form-switch mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input"
                                                           id="checkPermission_<?php echo $permission['id']?>"
                                                           name="permissions[]"
                                                           value="<?php echo $permission['id']?>"
                                                           <?php echo $checked ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="checkPermission_<?php echo $permission['id']?>"><?php echo $permissionName?></label>
                                                </div>
                                            </div>
                                            <?php $j++; } ?>
                                        </div>
                                    </div>
                                    <?php $i++; } ?>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                                    <a href="<?= base_url('manage-seller') ?>" class="btn btn-secondary ml-2">Back to List</a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<?php $this->load->view('seller/permissions-script'); ?>
