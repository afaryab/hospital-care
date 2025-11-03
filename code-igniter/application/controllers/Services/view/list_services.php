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
                                <span class="caption-subject bold uppercase"> Services</span>
                                <!-- <li class="btn btn-primary pull-right"> -->
                                    
                                <!-- </li> -->
                            </div>
                            <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($CREATE_SERVICE) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">Create New Service</span>
                                    </a>
                                </li>    
                            </div>

                        </div>
                        <div class="portlet-body form">
                            <table id="example" class="display table table-responsive table-bordered" cellspacing="0" width="100%">
                                <thead> 
                                    <tr class="bg-brand">
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                        
                                <?php
                                if(!empty($opdservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>OPD</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($opdservices as $opd) {
                                            ?>
                                            <tr>
                                                <td> <?= $opd['id'] ?> </td>
                                                <td><?= $opd['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_OPD_SERVICE.$opd['id']) ?>" >
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

                                <?php
                                if(!empty($emerservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>EMERGENCY</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($emerservices as $emer) {
                                            ?>
                                            <tr>
                                                <td> <?= $emer['id'] ?> </td>
                                                <td><?= $emer['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_EMER_SERVICE.$emer['id']) ?>" >
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

                                <?php
                                if(!empty($inpservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    
                                <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>INPATIENT</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot> -->
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($inpservices as $inp) {
                                            ?>
                                            <tr>
                                                <td> <?= $inp['id'] ?> </td>
                                                <td><?= $inp['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_INP_SERVICE.$inp['id']) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">EDIT</span>
                                    </a>
                                </li>    
                            </div></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                    </tbody>
                                    <!-- </table> -->
                                    <?php
                                    }
                                ?>

                                <?php
                                if(!empty($xrayservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>XRAY</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($xrayservices as $xray) {
                                            ?>
                                            <tr>
                                                <td> <?= $xray['id'] ?> </td>
                                                <td><?= $xray['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_XRAY_SERVICE.$xray['id']) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">EDIT</span>
                                    </a>
                                </li>    
                            </div></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                    </tbody>
                                    <!-- </table> -->
                                    <?php
                                    }
                                ?>

                                <?php
                                if(!empty($testservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>PATHOLOGY</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($testservices as $test) {
                                            ?>
                                            <tr>
                                                <td> <?= $test['id'] ?> </td>
                                                <td><?= $test['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_TEST_SERVICE.$test['id']) ?>" >
                                        <i class="fas fa-notes-medical"></i>
                                        <span class="d-inline">EDIT</span>
                                    </a>
                                </li>    
                            </div></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                    </tbody>
                                    <!-- </table> -->
                                    <?php
                                    }
                                ?>

<?php
                                if(!empty($dentalservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>DENTAL</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($dentalservices as $dent) {
                                            ?>
                                            <tr>
                                                <td> <?= $dent['id'] ?> </td>
                                                <td><?= $dent['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_DENTAL_SERVICE.$dent['id']) ?>" >
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

                                
<?php
                                if(!empty($ultraservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>ULTRASOUND</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($ultraservices as $ult) {
                                            ?>
                                            <tr>
                                                <td> <?= $ult['id'] ?> </td>
                                                <td><?= $ult['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_ULTRA_SERVICE.$ult['id']) ?>" >
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

<?php
                                if(!empty($recestationservices)) {
                                ?>  
                                <!-- <table id="example" class="display" cellspacing="0" width="100%"> -->
                                    <!-- <thead>  -->
                                    <tr>
                                        <td class="text-center bg-grey" colspan="3"><strong>RECESTATION</strong></td>
                                    </tr>
                                    <!-- <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead> -->
                                    <!-- <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </tfoot> -->
                                    <tbody>
                                    <?php
                                        foreach ($recestationservices as $res) {
                                            ?>
                                            <tr>
                                                <td> <?= $res['id'] ?> </td>
                                                <td><?= $res['name'] ?></td>
                                                <td class="text-right">
                                                <div>
                                <li class="btn btn-primary pull-right">
                                    <a href="<?= site_url($EDIT_RECES_SERVICE.$res['id']) ?>" >
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
    