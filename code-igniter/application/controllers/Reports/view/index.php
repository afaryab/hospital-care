
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
								<span class="caption-subject bold uppercase"> Income Report By User (Un Closed)</span>
							</div>
						</div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <?php
                                        $user = $this->aauth->list_users();
                                        ?>
                                        <select class="form-control" id="form_control_1" name="user_id">
                                            <?php
                                            foreach($user as $row){
                                                ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="1" name="report_type">
                                        <label for="form_control_1">Select User</label>
                                        <span class="help-block">Select user whom you want to be reported</span>
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
                                <span class="caption-subject bold uppercase"> Income Report By User & Date</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <?php
                                        $user = $this->aauth->list_users();
                                        ?>
                                        <select class="form-control" id="form_control_1" name="user_id">
                                            <?php
                                            foreach($user as $row){
                                                ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="2" name="report_type">
                                        <label for="form_control_1">Select User</label>
                                        <span class="help-block">Select user whom you want to be reported</span>
                                    </div>
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Date Range</label>
                                            <span class="help-block">Select Date Range</span>
                                            <i class="icon-calendar"></i>
                                        </div>
                                    </div>
                                    <div class="form-group form-md-checkboxes">
                                        <label>Additional Options</label>
                                        <div class="md-checkbox-list">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkbox1" class="md-check" name="closed-by-reception" checked="checked">
                                                <label for="checkbox1">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>
                                                    Closed By Reception </label>
                                            </div>
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkbox2" class="md-check" name="closed-by-accounts" checked="checked">
                                                <label for="checkbox2">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>
                                                    Closed By Accounts </label>
                                            </div>
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
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Income Report By Doctors</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <select class="form-control" id="form_control_1" name="doctor_id">
                                            <?php
                                            foreach($doctors as $row){
                                                ?>
                                                <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="3" name="report_type">
                                        <label for="form_control_1">Select Doctor</label>
                                        <span class="help-block">Select Doctor Name</span>
                                    </div>
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal1">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Date Range</label>
                                            <span class="help-block">Select Date Range</span>
                                            <i class="icon-calendar"></i>
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
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Income Report By Service</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="form-group form-md-line-input has-info">
                                        <select class="form-control" id="form_control_1" name="service_id">
                                            <?php
                                            foreach($services as $row){
                                                ?>
                                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="5" name="report_type">
                                        <label for="form_control_1">Select Service</label>
                                        <span class="help-block">Select Service Name</span>
                                    </div>
                                    <div class="form-group form-md-line-input has-info">
                                        <div class="input-icon right" id="defaultrange_modal2">
                                            <input type="text" class="form-control" name="date_range">
                                            <label class="control-label">Date Range</label>
                                            <span class="help-block">Select Date Range</span>
                                            <i class="icon-calendar"></i>
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
