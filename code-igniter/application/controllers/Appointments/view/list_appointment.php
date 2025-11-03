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
        
                
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase">My Appointments </h3>
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
                                                    <th>File No.</th>
                                                    <th>Patient Name</th>
                                                    <th>Patient Contact</th>
                                                    <th>Purpose</th>
                                                    <th>Appointment On</th>
                                                    <th>Actions</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(!empty($appointment)) { 
                                                foreach($appointment as $apt){
                                              
                                                    
                                                 ?>
                                                <tr>
                                                
                                                    <td><?= $apt['file_id'] ?></td>
                                                    <?php foreach($patients as $patient){
                                                        if($apt['patient_id'] == $patient['id']){
                                                            ?>
                                                        <td><?= $patient['pateint_name'] ?></td> 
                                                        <td><?= $patient['patient_contact_mobile'] ?></td> 
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $apt['appointment_notes'] ?></td>
                                                    
                                                    <td><?= $apt['start_date'] ?></td>
                                                    <td style="text-align: center; "><a href="<?= site_url($DENTAL_PATIENT_PROFILE.$apt['id']) ?>" ><i class="fas fa-id-badge"></i></a></td>
                                                    
                                                </tr>
                                                <?php
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