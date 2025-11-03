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
                                <span class="help-block">you can change rport duration from here.</span>
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
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-pawn fa-2x m-r-2"></i> <?= $business_name ?> - INCOME STATEMENT </h3>
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
                                                        <th>Payment Method</th>
                                                        <th>Amount</th>
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr>
                                                        <td colspan="2" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4"><strong>Income</strong></td>
                                                    </tr>
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($report_transactions)) {
                                                        foreach ($report_transactions as $row) {
                                                            if($row['income_or_expence'] == 'INCOME'){
                                                            $total_amount += $row['amount'];
                                                            $counter++;
                                                            ?>
                                                            <tr>
                                                                <td> <?= $row['id'] ?> </td>
                                                                <td><?= $row['type'] ?></td>
                                                                <td><?= $row['amount'] ?></td>
                                                                <td><?= $total_amount ?></td>
                                                            </tr>
                                                            <?php
                                                            }
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="4">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                    <tr>
                                                        <td colspan="2" class="text-right"><strong>Income</strong></td>
                                                        <td><?= $inc_ammount ?></td>
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                    
                                                    <tr class="bg-warning lighter">
                                                        <td colspan="4"><strong>Income Statement Summery</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Duration</strong></td>
                                                        <td><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Income</strong></td>
                                                        <td><?= $inc_ammount ?></td>
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