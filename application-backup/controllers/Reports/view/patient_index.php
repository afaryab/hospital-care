
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
								<i class="icon-settings font-red-sunglo"></i>
								<span class="caption-subject bold uppercase"> Patients Report By Treatments</span>
							</div>
						</div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <select class="form-control miltiselect" id="diagnosis_id" name="diagnosis_id">
                                            <?php
                                            foreach($diagnosis as $row){
                                                ?>
                                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="1" name="report_type">
                                        <label for="diagnosis_id">Diagnosis</label>
                                        <span class="help-block">Select diagnosis whom you want to be reported</span>
                                    </div>
                                </div>
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Submit</button>
                                </div>
                            </form>
                        </div>
                    <!-- END SAMPLE FORM PORTLET-->
                </div>
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Patients Report By Appointments</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Date Range</label>
                                            <span class="help-block">Select Date Range of Appointment</span>
                                            <i class="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="2" name="report_type">
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- END SAMPLE FORM PORTLET-->
                    </div>
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Patient Report By Visits</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal1">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Date Range</label>
                                            <span class="help-block">Select Date Range for Treatments Logs</span>
                                            <i class="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="3" name="report_type">
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- END SAMPLE FORM PORTLET-->
                    </div>
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Patient Report By Treatment Name</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right">
                                            <input type="text" class="form-control" placeholder="Treatment Name"  name="treatment_name">
                                            <label class="control-label">Treatment Name</label>
                                            <span class="help-block">NAME:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exact Match <br/>%NAME%:&nbsp;&nbsp;Contain <br/>%NAME:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ends With <br/>NAME%:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Starts With</span>
                                            <i class="fa fa-card"></i>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="4" name="report_type">
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- END SAMPLE FORM PORTLET-->
                    </div>
                    <div class="portlet light bordered hidden">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Income Report By Custom</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal2">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Customer First Arrival Date</label>
                                            <span class="help-block">Select Date Range</span>
                                            <i class="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 up18 under18" style="float: none;">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Age"  name="patient_age">
                                            <input type="hidden" class="form-control" placeholder="Patient Age In Months"  name="patient_age_months" max="11" value="1">
                                            <input type="hidden" class="form-control" placeholder="Patient Age In Days" name="patient_age_days" max="31" value="1">
                                            <label for="form_control">Patient Age</label>
                                            <span class="help-block">Please Enter Patient Age.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 under18">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="School" name="patient_school">
                                            <label for="form_control_1">Your School Name</label>
                                            <span class="help-block">Please Enter Your School Name.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 up18">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Profession"  name="patient_profession" >
                                            <label for="form_control_1">Enter Your Profession</label>
                                            <span class="help-block">Please Enter Your Profession.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- END SAMPLE FORM PORTLET-->
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
                minDate: '01/01/2012',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        $('#defaultrange_modal1').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal1 input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        $('#defaultrange_modal2').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal2 input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        $('#defaultrange_modal3').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal3 input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        $('#defaultrange_modal4').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal4 input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
</script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>"/>
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>
<!-- END CONTENT -->
