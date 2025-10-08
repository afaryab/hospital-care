<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
$i = 0;
$count = 0;
$j = 0;
$count2 = 0;
$k = 0;
$count3 = 0;
$p = 0;
$count4 = 0;
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $total_amount = 0;
                    $actual_amount = 0;                    
                    ?>
                    <div class="invoice in_sm printfull statementdisplay" style="margin: 0 auto;" id="firstSection">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            
                            
                            <div class="portlet-body form">
                                <div class="row">
                                    <div class="col-lg-12 text-center invoice-header">
                                        <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                                        <hr style="border-top: 3px solid #000;"/>
                                        <h3>-COUNTER STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-lg-12 text-center invoice-header">
                                        <?php foreach ($counterusers as $ctuser) { ?>
                                            <?php if ($ctuser->id == $recep['user_id']) { ?>
                                                <h3  style="background: #ccc !important;"><?= $ctuser->name ?></h3>
                                            <?php } ?>    
                                        <?php } ?> 
                                        
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-lg-12 text-center invoice-header">
                                        <p style="font-size: 15px;"><strong><?= $recep['created_on'] ?></strong></p>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-lg-12 text-center invoice-header">
                                        <h3  style="background: #ccc !important;">-INCOME STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    
                                     <!-- inp -->
                                     <div id="inpDiv" class="col-lg-12">
                                     <div class="col-lg-12" style="font-size: 15px;"><strong>Inpatient</strong></div>
                                     <table id="example" class="inpTable table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
            <tr style="height: 12px;">
                        <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                        <th style="font-size: 10px;"><strong>Mr No.<strong></th>
                        <th style="font-size: 10px;"><strong>Name<strong></th>
                        <th style="font-size: 10px;"><strong>Services<strong></th>
                        <th style="font-size: 10px;"><strong>Created On<strong></th>
                        <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                        <th style="font-size: 10px;"><strong>Original Amount<strong></th>
                        <th style="font-size: 10px;"><strong>Balance<strong></th>
                        
                    </tr>
            </thead>
            <tbody>
             
                <?php
               $inptotal = 0;
                if(!empty($counter_transactions)) {
                    foreach ($counter_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            //$total_amount = $total_amount + $row['amount'];
                            
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
                                                <?php if($elements['edited_amount'] != NULL) { ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                                <?php }else{ ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                <?php } ?>
                                                <td><?= $org ?></td>
                                                <td><?= $bal ?></td>
                                                
                                            </tr>
                                            <?php $total_amount = $total_amount + $elements['amount']; 
                                            $inptotal = $inptotal + $elements['amount'];
                                            ?>
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
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $inptotal ?></td>
                                        </tr>
                                        </table>
                                    </div>
                    
                                    
                                     <!-- inp -->

                                     <div class="col-lg-12">

                                     <?php 
                                      
                                     foreach ($users as $user) { ?>
                    
                    <div class="row">
    
        <div id="myTable1div<?= $i ?>" class="col-lg-12">
    
        <div class="col-lg-12" style="font-size: 15px;"><strong>OPD - <?= $user->name ?></strong></div>
            <table id="example" class="Table1<?=$i?> table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr style="height: 12px;">
                        <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                        <th style="font-size: 10px;"><strong>Sr No.<strong></th>
                        <th style="font-size: 10px;"><strong>Name<strong></th>
                        <th style="font-size: 10px;"><strong>Services<strong></th>
                        <th style="font-size: 10px;"><strong>Created On<strong></th>
                        <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                        
                    </tr>
                </thead>
                <tbody>
                 
                    <?php
                   $opddoc = 0;
                    if(!empty($counter_transactions)) {
                        foreach ($counter_transactions as $row) {
                            if($row['income_or_expence'] == 'INCOME'){
                               // $total_amount = $total_amount + $row['amount'];
                                
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
                                                    <?php if($elements['edited_amount'] != NULL) { ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                                    <?php }else{ ?> 
                                                        <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                    <?php } ?>
                                                    
                                                </tr>
                                                <?php $total_amount = $total_amount + $elements['amount']; 
                                                 $opddoc = $opddoc + $elements['amount'];
                                                ?>
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
            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $opddoc ?></td>
                                        </tr>
                                        </table>
        </div>
    
    </div>
    <?php 
     $i = $i+1;
     $count = $count+1;
        } ?> 

                                     </div>

                                     <!-- opdDoc -->
                                     <div class="col-lg-12">

                                     <?php foreach ($opd_services as $opd) { 
                    if ($opd['is_doctor_selectable'] != 1) { ?>
                <div class="row">

    <div id="myTable2div<?= $j ?>" class="col-lg-12">

    <div class="col-lg-12" style="font-size: 15px;"><strong>OPD - <?= $opd['name'] ?></strong></div>
        <table id="example" class="Table2<?=$j?> table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
            <tr style="height: 12px;">
                        <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                        <th style="font-size: 10px;"><strong>Sr No.<strong></th>
                        <th style="font-size: 10px;"><strong>Name<strong></th>
                        <th style="font-size: 10px;"><strong>Services<strong></th>
                        <th style="font-size: 10px;"><strong>Created On<strong></th>
                        <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                        
                    </tr>
            </thead>
            <tbody>
             
                <?php
                $opdser = 0;
                if(!empty($counter_transactions)) {
                    foreach ($counter_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            // $total_amount = $total_amount + $row['amount'];
                           // $total_amount = $total_amount + $row['amount'];
                            ?>
                                
                                <?php
                                    if(!empty($row['rows'])) {
                                        foreach ($row['rows'] as $elements) {
                                            
                                            ?>
                                            <?php if($elements['type'] == 'OPD') {
                                                if($elements['service_id'] == $opd['id'] && $elements['doctor_id'] == NULL) {
                                                    // $total_amount += $elements['amount']; ?>
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
                                                <?php if($elements['edited_amount'] != NULL) { ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                                <?php }else{ ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                <?php } ?>
                                                
                                            </tr>
                                            <?php $total_amount = $total_amount + $elements['amount']; 
                                            $opdser = $opdser + $elements['amount'];
                                            ?>
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
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $opdser ?></td>
                                        </tr>
                                        </table>
    </div>

</div>
<?php 
$j = $j+1;
$count2 = $count2+1;
    }
} ?> 

                                     </div>

                                     <!-- opdSer -->
                                     <div class="col-lg-12">

                                     <?php 
                                      
                                     foreach ($dusers as $duser) { ?>
                    
                    <div class="row">
    
        <div id="myTable3div<?= $k ?>" class="col-lg-12">
    
        <div class="col-lg-12" style="font-size: 15px;"><strong>DENTAL - <?= $duser->name ?></strong></div>
            <table id="example" class="Table3<?=$k?> table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr style="height: 12px;">
                        <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                        <th style="font-size: 10px;"><strong>Sr No.<strong></th>
                        <th style="font-size: 10px;"><strong>Name<strong></th>
                        <th style="font-size: 10px;"><strong>Services<strong></th>
                        <th style="font-size: 10px;"><strong>Created On<strong></th>
                        <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                        
                    </tr>
                </thead>
                <tbody>
                 
                    <?php
                   $dentaldoc = 0;
                    if(!empty($counter_transactions)) {
                        foreach ($counter_transactions as $row) {
                            if($row['income_or_expence'] == 'INCOME'){
                               // $total_amount = $total_amount + $row['amount'];
                                
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
                                                    <?php if($elements['edited_amount'] != NULL) { ?> 
                                                    <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                                    <?php }else{ ?> 
                                                        <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                    <?php } ?>
                                                    
                                                </tr>
                                                <?php $total_amount = $total_amount + $elements['amount']; 
                                                      $dentaldoc = $dentaldoc + $elements['amount'];
                                                ?>
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
            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $dentaldoc ?></td>
                                        </tr>
                                        </table>
        </div>
    
    </div>
    <?php 
     $k = $k+1;
     $count3 = $count3+1;
        } ?> 

                                     </div>


                                     <!-- Dental --> 

        <div class="col-lg-12">

            <?php 

            foreach ($ultusers as $ultuser) { ?>

            <div class="row">

            <div id="myTable4div<?= $p ?>" class="col-lg-12">

            <div class="col-lg-12" style="font-size: 15px;"><strong>ULTRASOUND - <?= $ultuser->name ?></strong></div>
            <table id="example" class="Table4<?=$p?> table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
            <tr style="height: 12px;">
            <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
            <th style="font-size: 10px;"><strong>Sr No.<strong></th>
            <th style="font-size: 10px;"><strong>Name<strong></th>
            <th style="font-size: 10px;"><strong>Services<strong></th>
            <th style="font-size: 10px;"><strong>Created On<strong></th>
            <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>

            </tr>
            </thead>
            <tbody>

            <?php
            $ultradoc = 0;
            if(!empty($counter_transactions)) {
            foreach ($counter_transactions as $row) {
            if($row['income_or_expence'] == 'INCOME'){
            // $total_amount = $total_amount + $row['amount'];

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
                        <?php if($elements['edited_amount'] != NULL) { ?> 
                            <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                        <?php }else{ ?> 
                            <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                        <?php } ?>
                        
                    </tr>
                    <?php $total_amount = $total_amount + $elements['amount']; 
                            $ultradoc = $ultradoc + $elements['amount'];
                    ?>
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
            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <tr>
                <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                <td style="width:30%" class="text-left"><?= $ultradoc ?></td>
            </tr>
            </table>
            </div>

            </div>
            <?php 
            $p = $p+1;
            $count4 = $count4+1;
            } ?> 

            </div>

                                     <!-- Ultrasound -->

                                     <div id="resDiv" class="col-lg-12">

<div class="col-lg-12" style="font-size: 15px;"><strong>Recestation</strong></div>
    <table id="example" class="resTable table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
            <tr style="height: 12px;">
                    <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                    <th style="font-size: 10px;"><strong>Mr No.<strong></th>
                    <th style="font-size: 10px;"><strong>Name<strong></th>
                    <th style="font-size: 10px;"><strong>Doctor<strong></th>
                    <th style="font-size: 10px;"><strong>Services<strong></th>
                    <th style="font-size: 10px;"><strong>Created On<strong></th>
                    <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                    
                </tr>
                
            </tr>
        </thead>
        <tbody>
         
            <?php
            $recser = 0;
            if(!empty($counter_transactions)) {
                foreach ($counter_transactions as $row) {
                    if($row['income_or_expence'] == 'INCOME'){
                        // $total_amount = $total_amount + $row['amount'];
                        
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
                                            <?php if($elements['edited_amount'] != NULL) { ?> 
                                                <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                            <?php }else{ ?> 
                                                <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                            <?php } ?>
                                            
                                        </tr>
                                        <?php $total_amount = $total_amount + $elements['amount']; 
                                        $recser = $recser + $elements['amount'];
                                        ?>
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
    <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                        <td style="width:30%" class="text-left"><?= $recser ?></td>
                                    </tr>
                                    </table>



                                 </div>
                                <div class="col-lg-12">
                                <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:70%;" class="text-right"><strong>Total Income : </strong></td>
                                        <td style="width:30%" class="text-left"><?= $total_amount ?></td>
                                    </tr>
                                    </table>
                                </div>    


                                     <!-- RECESTATION -->

                                     

                                     <div id="emrDiv" class="col-lg-12">

    <div class="col-lg-12" style="font-size: 15px;"><strong>Emergency</strong></div>
        <table id="example" class="emrTable table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                <tr style="height: 12px;">
                        <th style="font-size: 10px;"><strong>Reciept No.<strong></th>
                        <th style="font-size: 10px;"><strong>Name<strong></th>
                        <th style="font-size: 10px;"><strong>Services<strong></th>
                        <th style="font-size: 10px;"><strong>Created On<strong></th>
                        <th style="font-size: 10px;"><strong>Recieved Amount<strong></th>
                        
                    </tr>
                    
                </tr>
            </thead>
            <tbody>
             
                <?php
                $emrser = 0;
                if(!empty($counter_transactions)) {
                    foreach ($counter_transactions as $row) {
                        if($row['income_or_expence'] == 'INCOME'){
                            // $total_amount = $total_amount + $row['amount'];
                            
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
                                                <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                
                                            </tr>
                                            <?php $total_amount = $total_amount + $elements['amount']; 
                                            $emrser = $emrser + $elements['amount'];
                                            ?>
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
        <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $emrser ?></td>
                                        </tr>
                                        </table>


                                     </div>
                                    <div class="col-lg-12">
                                    <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="width:70%;" class="text-right"><strong>Total Income : </strong></td>
                                            <td style="width:30%" class="text-left"><?= $total_amount ?></td>
                                        </tr>
                                        </table>
                                    </div>    

                                     <!-- ermer -->

                                    <div class="col-lg-12 text-center invoice-header">
                                        <h3  style="background: #ccc !important;">-EXPENSE STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-lg-12">
                                        <table id="example"  class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                            <tr>
                                                <th>R #</th>
                                                <th>Expense Details</th>
                                                <th>Amount</th>
                                                <th>Balance</th>
                                            </tr>
                                            <?php
                                            foreach ($counter_transactions as $row) {
                                                if($row['income_or_expence'] == 'EXPENSE' || $row['income_or_expence'] == 'EXP'){
                                                    $total_amount = $total_amount - $row['amount'];
                                                    ?>
                                                
                                                    <tr >
                                                        <td> <?= $row['id'] ?> </td>
                                                        <td style="padding:0;"  width="50%">
                                                        <table style="margin-bottom:0;" class="table table-bordered table-responsive table-striped" >
                                                        <?php
                                                            if(!empty($row['rows'])) {
                                                                foreach ($row['rows'] as $elements) {
                                                                    if($elements['type'] == 'VOUCHER-PAY'){ ?>
                                                                        <tr >
                                                                        <td><strong>V No. </strong><?= $elements['exp_array']['voucher_id'] ?><strong> Purpose </strong>"<?= $elements['exp_category']['name'] ?>" <strong> Paid To  </strong>"
                                                                            <?php if($elements['exp_voucher_array']['employee_id'] != 0){
                                                                                    if($elements['exp_voucher_array']['employee_id'] != NULL){ ?>
                                                                                <?=$elements['employee']['name'] ?> 
                                                                            <?php }
                                                                            }elseif($elements['exp_voucher_array']['payed_to_others'] != NULL){ ?>
                                                                                <?= $elements['exp_voucher_array']['payed_to_others'] ?> 
                                                                            <?php
                                                                            } ?>" 
                                                                            <?php if($elements['exp_voucher_array']['inpatient_file_id'] != 0){
                                                                                if($elements['exp_voucher_array']['inpatient_file_id'] != NULL){ ?>
                                                                                <strong> Mr No. </strong> <?= $elements['exp_voucher_array']['inpatient_file_id'] ?> 
                                                                                <?php } 
                                                                                }?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php }elseif($elements['type'] == 'INPT-EXP'){ ?>
                                                                            <tr >
                                                                                <!-- <td>Inpatient Expence</td> -->
                                                                                <?php if($elements['inpExp']['payment_refference'] != NULL){ ?>
                                                                                <td><?= $elements['inpExp']['payment_refference'] ?>&nbsp;  ( File #  <?= $elements['inpExp']['file_id'] ?> ) </td>
                                                                                <?php }else{ ?>
                                                                                    <td>Paid for File # <?= $elements['inpExp']['file_id'] ?></td>
                                                                                <?php }
                                                                                ?>
                                                                            </tr>
                                                                    <?php }else{ ?>
                                                                            <tr >
                                                                                <td><?= $elements['exp_array']['payment_reference'] ?></td>
                                                                            </tr>
                                                                    <?php }
                                                                }
                                                            }
                                                        ?></table></td><?php if($row['edited_amount'] != NULL) { ?> 
                                                            <td class="bg-warning"><?= $row['amount'] ?> &nbsp;&nbsp;<del><b><?= $row['edited_amount'] ?></b></del></td>
                                                        <?php }else{ ?> 
                                                            <td class="bg-warning"><?= $row['amount'] ?></td>
                                                        <?php } ?>
                                                        <td  class="bg-warning"><?= $total_amount ?></td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                        
                                        ?>
                                    </tbody>
                                    

                                
                                </table>
                                <div class="text-center" style="text-aling:center;">
                                <table class="table">
                                    <tr class="text-center" style="border-top: 3px solid #000;">
                                        <td><strong class="text-warning">Counter Opening Cash: </strong></td><td><?= $recep['opening_amount'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-success">Closing Cash: </strong></td><td><?= $recep['closing_amount_cash'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-success">Closing Bank: </strong></td><td><?= $recep['closing_amount_card'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-success">Closing Card: </strong></td><td><?= $recep['closing_amount_creditcard'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-success">Closing Cheque: </strong></td><td><?= $recep['closing_amount_atm'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-danger">Expense Payed: </strong></td><td><?= $recep['expense_payed'] ?></td>
                                    </tr>
                                    <tr class="text-center">
                                        <td><strong class="text-success">Total: </strong></td><td><?= $recep['closing_amount'] ?></td>
                                    </tr>
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
    <script>
        var cont = <?php echo $count; ?>;
        var cont2 = <?php echo $count2; ?>;
        var cont3 = <?php echo $count3; ?>;
        var cont4 = <?php echo $count4; ?>;
        var i = 0;
        $(document).ready(function(){
            if(!$('.inpTable tbody').find('tr').length){ 
                $('#inpDiv').hide();
            }
            if(!$('.emrTable tbody').find('tr').length){ 
                $('#emrDiv').hide();
            }
            if(!$('.resTable tbody').find('tr').length){ 
                $('#resDiv').hide();
            }
            console.log(cont);
            for(i=0;i <= cont;i++){
                if(!$('.Table1'+i).find('tbody tr').length){ 
                    $('#myTable1div'+i).hide();
                }
            }
            for(j=0;j <= cont2;j++){
                if(!$('.Table2'+j).find('tbody tr').length){ 
                    $('#myTable2div'+j).hide();
                }
            }
            for(k=0;k <= cont3;k++){
                if(!$('.Table3'+k).find('tbody tr').length){ 
                    $('#myTable3div'+k).hide();
                }
            }
            for(p=0;p <= cont4;p++){
                if(!$('.Table4'+p).find('tbody tr').length){ 
                    $('#myTable4div'+p).hide();
                }
            }
        })

  
        // $(function(){
        //     var hide = true;

        //     $('.Table1 td').each(function(){
        //             var td_content = $(this).text();

        //             if(td_content!=""){
        //                 hide = false;
        //             }
        //     });
            

        //     if(hide){
        //         $('.myTable1div').hide();
             
        //     }
        // })

    </script>    
    <style>
    #firstSection{
        width: calc(100% - 280px) !important;
        display: inline-block;
        vertical-align: top;
    }
    #secondSection{
        width: 270px;
        display: inline-block;
        vertical-align: top;
        padding: 0px 15px 15px 15px;
    }
    .page-content{
        padding:15px;
    }
    .invoice.in_sm table td:first-child{
        padding-left:10px !important;
    }
    /* .table{ 
        empty-cells: hide; 
    } */
    </style>
