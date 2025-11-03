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
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="service" id="service" required>
                                        <option value="">Select Service Type</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "opd" ) ? 'selected' : '' ?> value="opd">OPD</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "dental" ) ? 'selected' : '' ?> value="dental">DENTAL</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "ultra" ) ? 'selected' : '' ?> value="ultra">ULTRASOUND</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "emr" ) ? 'selected' : '' ?> value="emr">EMERGENCY</option>  
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "inpd" ) ? 'selected' : '' ?> value="inpd">INPATIENT</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "reces" ) ? 'selected' : '' ?> value="reces">RECESTATION</option>                
                                    </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="opd">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="oserviceid" id="oserviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($opd_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="dntl">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="dserviceid" id="dserviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($dental_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="emr">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="eserviceid" id="eserviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($emergency_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="ult">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="userviceid" id="userviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($ultra_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="inpd">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="iserviceid" id="iserviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($inpatient_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="reces">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="rserviceid" id="rserviceid" >
                                        <option value="">Select Service</option>
                                        <?php foreach ($reces_services as $service) {
                                            if($service['is_deleted'] != 1){ ?>
                                                <option value="<?=$service['id'] ?>" ><?= $service['name'] ?></option>
                                        <?php }
                                        } ?>
                                        </select>                                            
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
        <div class="row">
        <?php
                $total_amount = 0;
                $exp_ammount = 0;
                $inc_ammount = 0;
                $total_cash = 0;
                
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-pawn fa-2x m-r-2"></i> SERVICES INCOME STATEMENT </h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    
                                    <div class="row">
                                            <div class="col-lg-12">
                        

                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Reciept No.</th>
                                                        <th>Name</th>
                                                        <th>Service</th>
                                                        <th>Created On</th>
                                                        <th>Recieved Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                        <?php if(!empty($reports)){
                                                            foreach($reports as $report){ 
                                                            ?>
                                                            <tr>
                                                            <td><?= $report['closing_transaction_id'] ?> </td>
                                                            <?php
                                                                foreach ($patients as $patient) {
                                                                    if($patient['id'] == $report['patient_id']) {
                                                                ?>
                                                                    <td><?= $patient['pateint_name'] ?> </td>
                                                            <?php
                                                                }
                                                            } ?> 
                                                            <?php
                                                                if($report['type'] == 'OPD') {
                                                                    foreach ($opd_services as $service) {
                                                                        if($service['id'] == $report['service_id']) {
                                                                ?>
                                                                    <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }elseif($report['type'] == 'EMER') {
                                                                foreach ($emergency_services as $service) {
                                                                    if($service['id'] == $report['service_id']) {
                                                            ?>
                                                                <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }elseif($report['type'] == 'INPT') {
                                                                foreach ($inpatient_services as $service) {
                                                                    if($service['id'] == $report['service_id']) {
                                                            ?>
                                                                <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }elseif($report['type'] == 'DENTAL') {
                                                                foreach ($dental_services as $service) {
                                                                    if($service['id'] == $report['service_id']) {
                                                            ?>
                                                                <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }elseif($report['type'] == 'ULTRA') {
                                                                foreach ($ultra_services as $service) {
                                                                    if($service['id'] == $report['service_id']) {
                                                            ?>
                                                                <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }elseif($report['type'] == 'RECES') {
                                                                foreach ($reces_services as $service) {
                                                                    if($service['id'] == $report['service_id']) {
                                                            ?>
                                                                <td><?= $service['name'] ?> </td>
                                                            <?php  }
                                                                }
                                                            }  ?>  
                                                            <td><?= $report['created_on'] ?> </td>
                                                            <?php $total_cash += $report['amount'] ?>
                                                            <td><?= $report['amount'] ?> </td>
                                                        </tr>

                                                        <?php }
                                                        } ?>
                                                        
                                                

                                                </tbody>
                                            </table>
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
                                                        <td width="30%"><?= $total_cash ?></td>
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
        
<!-- <script>
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
</script> -->
<script type="application/javascript">

        jQuery(document).ready(function() {
            
            $('#opd').hide();
            //$('#serviceid').removeAttr('required');
            $('#dntl').hide();
            // $('#dntlservice').removeAttr('required');
            $('#emr').hide();
            // $('#emrservice').removeAttr('required');
            $('#ult').hide();
            // $('#ultservice').removeAttr('required');
            $('#inpd').hide();
            // $('#inpdservice').removeAttr('required');
            $('#reces').hide();

            $('#service').on('change',function () {
                if($(this).val() == 'opd'){
                    $('#opd').show();
                    //$('#serviceid').attr("required","required");
                    $('#dntl').hide();
                    $('#emr').hide();
                    $('#ult').hide();
                    $('#inpd').hide();
                    $('#reces').hide();
                }else if($(this).val() == 'emr'){
                    $('#emr').show();
                    //$('#serviceid').attr("required","required");
                    $('#opd').hide();
                    $('#dntl').hide();
                    $('#ult').hide();
                    $('#inpd').hide();
                    $('#reces').hide();
                }else if($(this).val() == 'inpd'){
                    $('#inpd').show();
                    //$('#serviceid').attr("required","required");
                    $('#opd').hide();
                    $('#dntl').hide();
                    $('#emr').hide();
                    $('#ult').hide();
                    $('#reces').hide();
                }else if($(this).val() == 'ultra'){
                    $('#ult').show();
                    //$('#serviceid').attr("required","required");
                    $('#opd').hide();
                    $('#dntl').hide();
                    $('#emr').hide();
                    $('#inpd').hide();
                    $('#reces').hide();
                }else if($(this).val() == 'dental'){
                    $('#dntl').show();
                    //$('#serviceid').attr("required","required");
                    $('#opd').hide();
                    $('#emr').hide();
                    $('#ult').hide();
                    $('#inpd').hide();
                    $('#reces').hide();
                }else if($(this).val() == 'reces'){
                    $('#reces').show();
                    //$('#serviceid').attr("required","required");
                    $('#opd').hide();
                    $('#emr').hide();
                    $('#ult').hide();
                    $('#inpd').hide();
                    $('#dntl').hide();
                }
                


            })
             $('#service').val('');
            

        });
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