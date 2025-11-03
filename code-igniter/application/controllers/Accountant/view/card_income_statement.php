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
                        <!-- <div class="form-group has-success">
                            <label for="form_control_2">Report Duration</label>
                            <select name="template" class="form-control" id='form_control_2'>
                                <option value="1">All Transactions Summary</option>
                                <option value="2">Department Level Summary</option>
                                <option value="3">Services Level Summary</option>
                            </select>
                        </div> -->
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
		        $headerIncomes = [];
                $departmentIncome = [];
		$headerExpences = [];
                
                foreach ($report_transactions as $row) {
                    
                    
                        if($row['income_or_expence'] == 'EXPENSE'){
                            $exp_ammount += $row['amount'];
			
                        }
                        elseif($row['income_or_expence'] == 'INCOME'){
                            $inc_ammount += $row['amount'];
                            foreach($row['rows'] as $slipitem){
                                if(array_key_exists($slipitem['service_name'], $headerIncomes)){
                                    $headerIncomes[$slipitem['service_name']] = $headerIncomes[$slipitem['service_name']]+ $slipitem['amount'];
                                }else{
                                    $headerIncomes[$slipitem['service_name']] = $slipitem['amount'];
                                }
                                if(array_key_exists($slipitem['type'], $departmentIncome)){
                                    $departmentIncome[$slipitem['type']] = $departmentIncome[$slipitem['type']]+ $slipitem['amount'];
                                }else{
                                    $departmentIncome[$slipitem['type']] = $slipitem['amount'];
                                }
                            }
                        }   
                }
                $total_cash = $inc_ammount - $exp_ammount; 

            
                ?>

            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i>CARD INCOME STATEMENT </h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div id="nav" class="row">
                                        <div class="col-lg-12">
                                            
                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Patient Name</th>
                                                        <th>Service Details</th>
                                                        <!-- <th>Service Name</th> -->
                                                        <th>Date</th>
                                                        <!-- <th>Payment Method</th> -->
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>

                                                <tbody >
                                                    
                                                    <!-- <tr>
                                                        <td colspan="3" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr> -->
                                              
                                                      
                                                    
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($report_transactions)) {
                                                        foreach ($report_transactions as $row) {
                                                            
                                                            if($row['income_or_expence'] == 'INCOME'){
                                                                $total_amount += $row['amount'];
                                                                $counter++;
                                                                ?>
                                                                <tr>
                                                                    <td><?= $row['id'] ?> </td>
                                                                    <?php
                                                                    foreach ($patients as $patient) {
                                                                        if($patient['id'] == $row['patient_id']) {
                                                                        
                                                                        ?>
                                                                        
                                                                        <td><?= $patient['pateint_name'] ?> </td>
                                                                        <?php
                                                                        }
                                                                    } ?> 
                                                                    <td style="padding:0;"  width="50%"><table style="margin-bottom:0;" class="table table-bordered table-responsive table-striped"><?php
                                                                        if(!empty($row['rows'])) {
                                                                            foreach ($row['rows'] as $elements) {
                                                                                
                                                                                ?>
                                                                                <tr>
                                                                                    <td width="30%"><?= $elements['type'] ?></td>
                                                                                    <td width="40%"><?= $elements['service_name'] ?></td>
                                                                                    <!-- <td><?= $elements['amount'] ?></td> -->
                                                                                </tr>
                                                                                
                                                                                <?php
                                                                            } ?> 
                                                                            
                                                                            
                                                                            <?php
                                                                        } ?></table></td>
                                                                    <td><?= $row['created_on'] ?></td>
                                                                    <!-- <td><?= $row['type'] ?></td> -->
                                                                    <td><?= $row['amount'] ?></td>
                                                                    <!-- <td><?= $total_amount ?></td> -->
                                                                </tr>
                                                                    
                                                                <?php 
                                                            }
                                                        } ?> 
                                                    
                                                     
                                                     
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="5">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                        <!-- <td><?= $inc_ammount ?></td> -->
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                </div>
                                                       
                                                    <tr class="bg-warning lighter">
                                                        <td colspan="5"><strong>Statement Summery</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Duration</strong></td>
                                                        <td colspan="2"><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Total</strong></td>
                                                        <td colspan="2"><?= $inc_ammount ?></td>
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
    function navFun() {
    var x = document.getElementById("nav");
        if (x.style.display === "none") {
            x.style.display = "block";
    } else {
            x.style.display = "none";
        }
    }
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
