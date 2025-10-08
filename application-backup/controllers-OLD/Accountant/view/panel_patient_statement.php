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
                                <input type="text" class="form-control" name="date_range">
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
        
                
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase">PANEL PATIENT STATEMENT </h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-12">
                                        <table id="example" class="table table-bordered table-striped" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>MR No.</th>
                                                    <th>Patient Name</th>
                                                    <th>Service Name</th>
                                                    <th>Status</th>
                                                    <th>Admitted On</th>
                                                    <th>Discharged On</th>
                                                    <!-- <th>Amount Paid</th>
                                                    <th>Original Amount</th> -->
                                                    <th>Difference Paid</th>
                                                    <th>Bill Submitted</th>
                                                    <th>Balance</th>
                                                    <th>Bill Recieved</th>
                                                    <th>Bill Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(!empty($inpstatement)) { 
                                                foreach($inpstatement as $file){
                                                    if($file['panel_name'] != NULL){
                                                    
                                                 ?>
                                                <tr>
                                                
                                                    <td><?= $file['id'] ?></td>
                                                    <?php foreach($patients as $patient){
                                                    if($file['patient_id'] == $patient['id']){
                                                        ?>
                                                    <td><?= $patient['pateint_name'] ?></td> 
                                                    <?php
                                                    }
                                                }
                                                    ?>
                                                    <td><?= $file['service_name'] ?></td>
                                                    
                                                    <?php
                                                    if($file['panel_name'] != NULL){
                                                        ?>
                                                        <?php
                                                            if($file['status'] == 'OPEN'){
                                                        ?>
                                                            <td><?= $file['status'] ?></br><strong>Panel:</strong><?= $file['panel_name'] ?></br><strong>Room:</strong><?= $file['room_name'] ?> </td>
                                                        <?php
                                                            }else{
                                                        ?>
                                                            <td><?= $file['status'] ?></br><strong>Panel:</strong><?= $file['panel_name'] ?> </td>
                                                        <?php
                                                        }
                                                        ?>

                                                    <?php
                                                    }else{
                                                    ?>
                                                        
                                                        <?php
                                                            if($file['status'] == 'OPEN'){
                                                        ?>
                                                            <td><?= $file['status'] ?></br><strong>Room:</strong><?= $file['room_name'] ?> </td>
                                                        <?php
                                                            }else{
                                                        ?>
                                                            <td><?= $file['status'] ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                    <?php
                                                    }
                                                    ?> 
                                                    <td><?= $file['created_on'] ?></td>
                                                    <td><?= $file['closed_on'] ?></td>   
                                                    <?php $blance = $file['file_charges'];
                                                    
                                                        $sum=0;
                                                        foreach($transactions as $tran){ 
                                                            if ($file['id'] == $tran['file_id']){
                                                                $sum += $tran['amount_in_num']; 
                                                            }
                                                        }
                                                        foreach($recestrans as $rtrans){
                                                            if ($file['id'] == $rtrans['mr_no']){
                                                                $sum += $rtrans['amount_in_num']; 
                                                            }
                                                        }
                                                     
                                                        $blance = $blance - $sum; ?>
                                                    <td><?= $sum ?></td>
                                                    <td><?= $file['file_charges'] ?></td>
                                                    <td><?= $blance ?></td>
                                                    <?php $bill_recieved='';
                                                          $cheque_no='';
                                                          $recieving_date='';
                                                        foreach($panel_payments as $pp){ 
                                                            if ($file['id'] == $pp['mr_no']){
                                                                if ($pp['status'] != 'PENDING'){
                                                                    $bill_recieved = $pp['amount_recieved']; 
                                                                    $cheque_no = $pp['payment_reference']; 
                                                                    $recieving_date = $pp['modified_on']; 
                                                                }
                                                            }
                                                        }?>
                                                        <td><?= $bill_recieved ?></td>
                                                        <td>
                                                            <?php if($recieving_date!=''){ ?>
                                                                <strong>Cheque No. : </strong><?= $cheque_no ?>
                                                            <?php } ?>
                                                            <?php if($recieving_date!=''){ ?>
                                                                <br><strong>Recieving Date : </strong><?= date('Y-m-d',strtotime($recieving_date)); ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php }
                                                    }
                                                }
                                        
                                                 ?>
                                                
                                                  
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
