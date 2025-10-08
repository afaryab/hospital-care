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
                                <span class="caption-subject bold uppercase"> Panel Companies</span>
                                <!-- <li class="btn btn-primary pull-right"> -->
                                    
                                <!-- </li> -->
                            </div>
                            <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($CREATE_PANEL) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">Create Panel Company</span>
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
                                if(!empty($panels)) {
                                ?>  
                                
                                    <tbody>
                                    <?php
                                        foreach ($panels as $panel) {
                                            ?>
                                            <tr>
                                                <td> <?= $panel['id'] ?> </td>
                                                <td><?= $panel['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_PANEL.$panel['id']) ?>" >
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
    