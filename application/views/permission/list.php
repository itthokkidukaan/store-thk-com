<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5 class="title">
                <?php echo $this->lang->line('Permssion') ?> <a href="<?php echo base_url('permission/add') ?>"
                                                               class="btn btn-primary btn-sm rounded">
                    <?php echo $this->lang->line('Add new') ?>
                </a>
            </h5>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
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


                <table id="emptable" class="table table-striped table-bordered zero-configuration" cellspacing="0"
                       width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Permission Count</th>
                        <th><?php echo $this->lang->line('Actions') ?></th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1;

                    foreach ($permissions as $row) {
                       //echo '<pre>';print_r($row);die;
                        $pid = $row['id'];
                        $permission_count = $row['count'];
                        $str = $row['name'];
                        $parts = explode(' ', $str);
                        $capitalized = array_map('ucfirst', $parts);
                        $name = implode(' ', $capitalized);

                        echo "<tr>
                    <td>$i</td>
                    <td>$name</td>
                    <td>$permission_count</td>
                    <td><a href='" . base_url("permission/edit?id=$pid") . "' class='btn btn-success btn-xs'><i class='fa fa-pencil'></i> " . $this->lang->line('Edit') . "</a></td></tr>";
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                         <th>#</th>
                        <th>Name</th>
                        <th>Permission Count</th>
                        <th><?php echo $this->lang->line('Actions') ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            //datatables
            $('#emptable').DataTable({
                responsive: true, <?php datatable_lang();?> dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    }
                ],
            });


        });
    </script>


   