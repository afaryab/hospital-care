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
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="doc" id="doc" required>
                                        <option value=""></option>
                                            <?php foreach ($users as $user) { ?>
                                                <?php if ($user->is_doctor == 1) { ?>
                                                    <option <?= (array_key_exists('doc',$_GET) && $_GET['doc'] == $user->id) ? 'selected' : '' ?> value="<?= $user->id ?>"><?= $user->name ?></option>
                                                <?php } ?>    
                                            <?php } ?>    
                                    </select>
                                    <label for="form_control_1">Select Doctor</label>
                                    <i class="fab fa-servicestack"></i>
                                </div>
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
        <?php
        // $selectedDoctor = NULL;
            if(!empty($voucher_payments)) {
        ?>        
        <div class="row">
        <?php
                $total_amount = 0;
                $exp_ammount = 0;
                $inc_ammount = 0;
                $total_cash = 0;
              
                $total_cash = $inc_ammount - $exp_ammount; 
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i> DOCTOR SHARE STATEMENT </h3>
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-user fa-2x m-r-2"></i> <?= $selectedDoctor->name ?></h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table id="example" class="table table-bordered table-responsive table-stipped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Purpose</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($voucher_payments)) {
                                                        foreach ($voucher_payments as $row) {
                                                           
                                                            $total_amount += $row['exp_amount_numbers'];
                                                            $counter++;
                                                            ?>
                                                            <tr>
                                                                <td> <?= $row['id'] ?> </td>
                                                                <?php 
                                                                    if($row['inpatient_file_id'] != NULL || $row['inpatient_file_id'] != 0) {
                                                                ?>
                                                                    <td><?= $row['expense_notes'] ?><strong> (MR NO. <?= $row['inpatient_file_id'] ?>)</strong></td> 
                                                                <?php    
                                                                    }else{
                                                                ?>
                                                                <td><?= $row['expense_notes'] ?></td>
                                                                <?php    
                                                                    }
                                                                ?>
                                                                <td><?= $row['created_on'] ?></td>
                                                                <td><?= $row['exp_amount_numbers'] ?></td>
                                                            </tr>
                                                            <?php
                                                            
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="4">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                    
                                                    
                                                    <tr class="bg-warning lighter">
                                                        <td style="text-align: center;" colspan="4"><strong>Doctor Paid Share </strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Duration</strong></td>
                                                        <td><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Amount</strong></td>
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
        <?php
        }elseif(!empty($selectedDoctor->name)){ ?>
        <div class="row">
            <div class="col-lg-12">
                <table id="example" class="table table-bordered table-responsive table-stipped" cellspacing="0" width="100%">
                <tr class="bg-danger">
                    <td colspan="4">No Income Transaction during this time.</td>
                </tr>
                </table>
            </div>
        </div>

        <?php
        }
        ?>

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