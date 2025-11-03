<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
?><!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
 <!-- <style>
    @media screen and (max-width: 767px) {
        .table-responsive {
            display: block;
        }
    }
 </style> -->

<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
        <?php

                $total_amount = 0;
                $nonpayed_amount = 0;
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i> MY STATEMENT </h3>

                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-12 table-responsive">

                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        
                                                        <th>Rec. ID</th>
                                                        <th>Serial No.</th>
                                                        <th>Patient</th>
                                                        <th>Services Details</th>
                                                        <th>Amount</th>
                                                        <th>Created On</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <!-- <tr>
                                                        <td colspan="3" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr> -->

                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($opd_trans)) {
                                                        foreach ($opd_trans as $row) {
                                                                // $isAlreadyPayed = $row['submitted_for_accounts'];
                                                                $total_amount += $row['amount'];
                                                                // if($isAlreadyPayed == 0 ){
                                                                //     $nonpayed_amount+= $row['amount'];
                                                                    
                                                                // }
                                                                $counter++;
                                                                ?>
                                                                <tr>
                                                                   
                                                                    <td><?= $row['closing_transaction_id'] ?> </td>
                                                                    <td>
                                                                        <?=$counter //$row['serial_number_doctor'] ?> </td>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            foreach ($patients as $patient) {
                                                                                if($patient['id'] == $row['patient_id']) {
                                                                
                                                                            ?>
                                                                
                                                                            <?= $patient['pateint_name'] ?>
                                                                                <?php
                                                                                }
                                                                        } ?> 
                                                                    </td>
                                                                    <td><?= $row['service_name'] ?> </td>
                                                                    <td><?= $row['amount'] ?></td>
                                                                    <td><?= $row['created_on'] ?></td>
                                                                    
                                                                </tr>
                                                            <?php 
                                                            
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="6">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>

                                                    <tr>
                                                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                 
                                                    
                                                    
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


               
<style>
    .fa-2x {
        font-size: 1em !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>"/>
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>