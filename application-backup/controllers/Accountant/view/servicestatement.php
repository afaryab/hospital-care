<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
?><!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-settings font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Report Filter </span>
                    </div>
                    
                    
                </div>
                <div class="portlet-body form">
                    <form method="GET">
                        <div class="form-group">
                            <div class="input-icon"  id="defaultrange_modal">
                                <label for="form_control_1">Report Duration</label>
                                <input type="text" class="form-control" name="date_range" required>
                                <input type="hidden" name="dtype" value="R">
                                <span class="help-block">you can change report duration from here.</span>
                                <i class="fa fa-bell-o"></i>
                            </div>
                        </div>
                        <div class="form-group has-success">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
        <?php
                $total_amount = 0;
                $exp_ammount = 0;
                $inc_ammount = 0;
                $total_cash = 0;
                
                
                foreach ($report_transactions as $row) {
                    
                        if($row['income_or_expence'] == 'EXPENSE'){
                            $exp_ammount += $row['amount'];
                        }
                        elseif($row['income_or_expence'] == 'INCOME'){
                            $inc_ammount += $row['amount'];
                        }   
                }
                $total_cash = $inc_ammount - $exp_ammount; 
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-pawn fa-2x m-r-2"></i> <?= $business_name ?> - SERVICES INCOME STATEMENT </h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    
                                <div class="row">
<div class="col-lg-12">
                

 
                <div class="row">
                <div class="col-lg-12" style="text-align: center;"><strong>Services Income Details</strong></div>
    <div class="col-lg-12">
        

    <div class="col-lg-12" style="font-size: 20px;"><strong>Inpatient</strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    <th>Mr No. </th>
                    <th>Name</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    <th>Original Amount</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            $total_amount += $row['amount'];
                            $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'INPT') { ?>
                                            

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                                <?php
                                                foreach ($inpatients as $inpatient) {
                                                    if($inpatient['patient_id'] == $row['patient_id']) {
                                                        $sum=0;
                                                ?>
                                    
                                                    <td><?= $inpatient['id'] ?> </td>
                                                    <?php
                                                        foreach ($inptrans as $inptran) {
                                                            if($inptran['file_id'] == $inpatient['id']) {
                                                                $sum += $inptran['amount_in_num'];
                                                    
                                                      
                                                        }
                                                    } ?>
                                                    <?php $org = $inpatient['file_charges'];
                                                    $bal = $inpatient['file_charges']-$sum;
                                                    ?>
                                                    <?php
                                                    }
                                             } ?>
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 

                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                <td><?= $org ?></td>
                                                <td><?= $bal ?></td>
                                            </tr>
                                                    <?php
                                                } ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>
</div>

                

                <!-- OPD -->

                
                <?php foreach ($users as $user) { ?>
                    
                <div class="row">

    <div class="col-lg-12">

    <div class="col-lg-12" style="font-size: 20px;"><strong>OPD - <?= $user->name ?></strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            $total_amount += $row['amount'];
                            $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'OPD' && $elements['doctor_id'] == $user->id) { ?>
                                            

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                               
                                    
                                                    <td><?= $elements['serial_number_doctor'] ?> </td>
                                               
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 

                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                
                                            </tr>
                                                    <?php
                                                } ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>

</div>
<?php } ?> 

              

                <!-- opd end -->

                
                <!-- OPD services -->

                
                <?php foreach ($opd_services as $opd) { 
                    if ($opd['is_doctor_selectable'] != 1) { ?>
                <div class="row">

    <div class="col-lg-12">

    <div class="col-lg-12" style="font-size: 20px;"><strong>OPD - <?= $opd['name'] ?></strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            $total_amount += $row['amount'];
                            $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'OPD') {
                                                if($elements['service_id'] == $opd['id'] && $elements['doctor_id'] == NULL) { ?>

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                               
                                    
                                                    <td><?= $elements['serial_number_doctor'] ?> </td>
                                               
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 

                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                
                                            </tr>
                                                    <?php
                                                }
                                            } ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>

</div>
<?php }
    }
?> 

              

                


                <!-- Services end -->
                
                <!-- Emergency -->

                
                               
                <div class="row">

    <div class="col-lg-12">

    <div class="col-lg-12" style="font-size: 20px;"><strong>Emergency</strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    
                    <th>Name</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            // $total_amount += $row['amount'];
                            // $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'EMER') {?>

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                               
                                    
                                                    
                                               
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 

                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                
                                            </tr>
                                                    <?php
                                                }
                                            ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>

</div>
                <!-- Emergence end -->
                <!-- Dental -->

                <?php foreach ($dusers as $duser) { ?>
                    
                    <div class="row">
    
        <div class="col-lg-12">
    
        <div class="col-lg-12" style="font-size: 20px;"><strong>DENTAL - <?= $duser->name ?></strong></div>
            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Reciept No.</th>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Services</th>
                        <th>Created On</th>
                        <th>Recieved Amount</th>
                        
                    </tr>
                </thead>
                <tbody>
                 
                    <?php
                    $counter = 0;
                    if(!empty($report_transactions)) {
                        foreach ($report_transactions as $row) {
                            if($row['income_or_expence'] == 'INCOME'){
                                // $total_amount += $row['amount'];
                                // $counter++;
                                ?>
                                    
                                    <?php
                                        if(!empty($row['rows'])) {
                                            foreach ($row['rows'] as $elements) {
                                                
                                                ?>
                                                <?php if($elements['type'] == 'DENTAL' && $elements['doctor_id'] == $duser->id) { ?>
                                                
    
                                                <tr>
                                                    <td><?= $elements['closing_transaction_id'] ?> </td>
                                                   
                                        
                                                        <td><?= $elements['serial_number_doctor'] ?> </td>
                                                   
                                                    <?php
                                                    foreach ($patients as $patient) {
                                                        if($patient['id'] == $row['patient_id']) {
                                        
                                                    ?>
                                        
                                                        <td><?= $patient['pateint_name'] ?> </td>
                                                        <?php
                                                        }
                                                 } ?> 
    
                                                    <td><?= $elements['service_name'] ?></td>
                                                    <td><?= $elements['created_on'] ?></td>
                                                    <td><?= $elements['amount'] ?></td>
                                                    
                                                </tr>
                                                        <?php
                                                    } ?>
                                                
                                                <?php
                                            } ?> 
    
                                            
                                            
                                            
                                            <?php
                                        } ?>
                                    
                                
                            <?php 
                            }
                        } ?> 
                        
                        
                        <?php
                    }
                    
                    ?>
    
                </tbody>
            </table>
        </div>
    
    </div>
    <?php } ?> 

    <!-- dental end -->

   <!-- OPD blocked services -->

                
   <?php foreach ($opd_services as $opd) { 
                    if ($opd['is_deleted'] != 1) { ?>
                <div class="row">

    <div class="col-lg-12">

    <div class="col-lg-12" style="font-size: 20px;"><strong>OPD - <?= $opd['name'] ?></strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            $total_amount += $row['amount'];
                            $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'OPD') {
                                                if($elements['service_id'] == $opd['id'] && $elements['doctor_id'] == NULL) { ?>

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                               
                                    
                                                    <td><?= $elements['serial_number_doctor'] ?> </td>
                                               
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 

                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                
                                            </tr>
                                                    <?php
                                                }
                                            } ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>

</div>
<?php }
    }
?> 

              

                


                <!-- blocked Services end -->

                 <!-- ultrasound -->

    <?php foreach ($ultusers as $ultuser) { ?>
                    
                    <div class="row">
    
        <div class="col-lg-12">
    
        <div class="col-lg-12" style="font-size: 20px;"><strong>ULTRASOUND - <?= $ultuser->name ?></strong></div>
            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Reciept No.</th>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Services</th>
                        <th>Created On</th>
                        <th>Recieved Amount</th>
                        
                    </tr>
                </thead>
                <tbody>
                 
                    <?php
                    $counter = 0;
                    if(!empty($report_transactions)) {
                        foreach ($report_transactions as $row) {
                            if($row['income_or_expence'] == 'INCOME'){
                                // $total_amount += $row['amount'];
                                // $counter++;
                                ?>
                                    
                                    <?php
                                        if(!empty($row['rows'])) {
                                            foreach ($row['rows'] as $elements) {
                                                
                                                ?>
                                                <?php if($elements['type'] == 'ULTRA' && $elements['doctor_id'] == $ultuser->id) { ?>
                                                
    
                                                <tr>
                                                    <td><?= $elements['closing_transaction_id'] ?> </td>
                                                   
                                        
                                                        <td><?= $elements['serial_number_doctor'] ?> </td>
                                                   
                                                    <?php
                                                    foreach ($patients as $patient) {
                                                        if($patient['id'] == $row['patient_id']) {
                                        
                                                    ?>
                                        
                                                        <td><?= $patient['pateint_name'] ?> </td>
                                                        <?php
                                                        }
                                                 } ?> 
    
                                                    <td><?= $elements['service_name'] ?></td>
                                                    <td><?= $elements['created_on'] ?></td>
                                                    <td><?= $elements['amount'] ?></td>
                                                    
                                                </tr>
                                                        <?php
                                                    } ?>
                                                
                                                <?php
                                            } ?> 
    
                                            
                                            
                                            
                                            <?php
                                        } ?>
                                    
                                
                            <?php 
                            }
                        } ?> 
                        
                        
                        <?php
                    }
                    
                    ?>
    
                </tbody>
            </table>
        </div>
    
    </div>
    <?php } ?> 

    <!-- ultrasound_end -->

    
                <!-- Recestation -->

                
                               
                <div class="row">

    <div class="col-lg-12">

    <div class="col-lg-12" style="font-size: 20px;"><strong>Recestation</strong></div>
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Reciept No.</th>
                    <th>Mr No.</th>
                    <th>Name</th>
                    <th>Doctor</th>
                    <th>Services</th>
                    <th>Created On</th>
                    <th>Recieved Amount</th>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $counter = 0;
                if(!empty($report_transactions)) {
                    foreach ($report_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            // $total_amount += $row['amount'];
                            // $counter++;
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'RECES') {?>

                                            <tr>
                                                <td><?= $elements['closing_transaction_id'] ?> </td>
                                                <?php
                                                    foreach ($restrans as $restran) {
                                                        if($restran['reception_transaction_id'] == $elements['closing_transaction_id']) {
                                                            $mr_no = $restran['mr_no'];
                                                            $doc_id = $restran['doctor_id'];
                                                    }
                                                } ?>
                                                    <td><?= $mr_no ?></td>
                                    
                                                    
                                               
                                                <?php
                                                foreach ($patients as $patient) {
                                                    if($patient['id'] == $row['patient_id']) {
                                    
                                                ?>
                                    
                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                    <?php
                                                    }
                                             } ?> 
                                            <?php
                                                foreach ($resusers as $resuser) {
                                                    if($resuser->id == $doc_id) { ?>
                                                        <td><?= $resuser->name ?></td>
                                            
                                            <?php }
                                            } ?>
                                                <td><?= $elements['service_name'] ?></td>
                                                <td><?= $elements['created_on'] ?></td>
                                                <td><?= $elements['amount'] ?></td>
                                                
                                            </tr>
                                                    <?php
                                                }
                                            ?>
                                            
                                            <?php
                                        } ?> 

                                        
                                        
                                        
                                        <?php
                                    } ?>
                                
                            
                        <?php 
                        }
                    } ?> 
                    
                    
                    <?php
                }
                
                ?>

            </tbody>
        </table>
    </div>

</div>
                <!-- Recestation end -->

</div>
</div>
                                      <div class="row">

                                        <div class="col-lg-12">
                                        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                
                                                <tbody>
                                                    <tr style="background-color:powderblue;">
                                                        <td width="70%" style="border-right:none;"><strong>Services Income Statement Summary</strong></td>
                                                        <td width="30%" style="border-left:none;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%" style="text-align: right;"><strong>Duration</strong></td>
                                                        <td width="30%"><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%" style="text-align: right;"><strong>Income</strong></td>
                                                        <td width="30%"><?= $inc_ammount ?></td>
                                                    <tr>
                                                    


                                                </tbody>


                                             </table>
                                        </div>
                                    </div>  
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
    $(function(){

        $('#defaultrange_modal').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2020',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
</script>
<style>
    .fa-2x {
        font-size: 1em !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>"/>
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>