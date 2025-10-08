<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>

    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="fas fa-clipboard-list"></i>
                                <span class="caption-subject bold uppercase"> Expense Categories</span>
                                <!-- <li class="btn btn-primary pull-right"> -->
                                    
                                <!-- </li> -->
                            </div>
                            <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($CREATE_EXPENSE) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">Create New Category</span>
                                    </a>
                                </li>    
                            </div>

                        </div>
                        <div class="portlet-body form">
                            <table id="example" class="display" cellspacing="0" width="100%">
                                <thead> 
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                        
                                <?php
                                if(!empty($expenses)) {
                                ?>  
                                
                                    <tbody>
                                    <?php
                                        foreach ($expenses as $expense) {
                                            ?>
                                            <tr>
                                                <td> <?= str_pad($expense['id'], 3, "0", STR_PAD_LEFT); ?> </td>

                                                <td><?php
                                                    if($expense['is_deleted'] == '1') {
                                                ?><del><p style="color:red"> <?= $expense['name'] ?></p></del>
                                                <?php } else{
                                                ?>    
                                                    <?= $expense['name'] ?>
                                                <?php }
                                                ?>    
                                                <?php
                                                    if($expense['type'] == 'INPT') {
                                                ?><p style="color:green"> ( Inpatient Expense Category ) </p>
                                                <?php }
                                                ?>  </td>

                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_EXPENSE.$expense['id']) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">EDIT</span>
                                    </a>
                                </li>    
                            </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                    </tbody>
                                    <!-- </table> -->
                                    <?php
                                    }
                                ?>

                                
                                    

                                
                                
                                
                            </table>




                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>

<script>
    jQuery(function(){
        $('#example').DataTable({
            "order": [[ 0, "desc" ]]
        });
    })
</script>
    