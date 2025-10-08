
<div class="page-content-wrapper">
    <div class="page-content row">
        <!-- BEGIN PAGE CONTENT-->
        <div class="">
            <?php if(
            ($cg->id == 0 && $this->aauth->is_allowed('Create Permission', 'Users Management')) ||
            ($cg->id != 0 && $this->aauth->is_allowed('Edit Permission', 'Users Management'))
            ){ ?>
                <div class="col-md-12 hidden">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-users font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> <?php echo ($cg->id == 0 && $this->aauth->is_allowed('Create Permission', 'Users Management') ? 'Create:' : 'Edit:').$cg->name; ?></span>
                            </div>

                        </div>
                        <div class="portlet-body form">
                            <form method="post">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Name" name="Name" required value="<?= $cg->name ?>">
                                            <label for="form_control_1">Name</label>
                                            <span class="help-block">Please Enter Title.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Definition" name="definition" required value="<?= $cg->definition ?>">
                                            <label for="form_control_1">Definition</label>
                                            <span class="help-block">Please Enter Definition.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Permission Group" name="perm_group" required value="<?= $cg->perm_group ?>">
                                            <label for="form_control_1">Permission Group</label>
                                            <span class="help-block">Please Enter Permission Group.</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="<?= $cg->id ?>">
                                <div class="col-sm-12 text-right">
                                    <br/>
                                    <input type="submit" name="updatePermissions" class="btn btn-primary">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php if($this->aauth->is_allowed('View Permissions', 'Users Management')){ ?>
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-users font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> <?php echo $title; ?></span>
                        </div>
                    </div>
                    <div class="portlet-body form">


                        <table id="example" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Definition</th>
                                <th>Group</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Definition</th>
                                <th>Group</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            if(!empty($permissions)){
                                foreach ($permissions as $row) {
                                        ?>
                                        <tr>
                                            <td> <?= $row->name ?></td>
                                            <td> <?= $row->definition ?></td>
                                            <td> <?= $row->perm_group ?></td>

                                        </tr>
                                        <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>


                        <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
                        <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/dataTables.semanticui.min.css') ?>"/>
                        <script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>
                        <script type="text/javascript" src="<?php echo base_url('public/scripts/dataTables.semanticui.min.js') ?>"></script>

                        <script>
                            jQuery(function(){
                                $('#example').DataTable();
                            })
                        </script>

                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>