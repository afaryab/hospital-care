
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Create New Expense Category</span>
                            </div>
                        </div>
                    
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Expense Name" name="name" required>
                                                    <label for="form_control_1">Expense Name</label>
                                                    <span class="help-block">Enter Expense Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkdoc" value="1" name="pay_doc" class="md-check">
                                                <label for="checkdoc">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span></i>&nbsp;&nbsp;&nbsp;&nbsp; Pay for Doctor ? </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkother" value="1" name="pay_others" class="md-check">
                                                <label for="checkother">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span></i>&nbsp;&nbsp;&nbsp;&nbsp; Pay for Others ? </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkuser" value="1" name="pay_users" class="md-check">
                                                <label for="checkuser">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span></i>&nbsp;&nbsp;&nbsp;&nbsp; Pay for Users ? </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkcomments" value="1" name="add_comments" class="md-check">
                                                <label for="checkcomments">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span></i>&nbsp;&nbsp;&nbsp;&nbsp; Can add comments ? </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        <h4> Inpatient Expense Category </h4>
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkinpt" value="INPT" name="type_inpt" class="md-check">
                                                <label for="checkinpt">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span></i>&nbsp;&nbsp;&nbsp;&nbsp; Inpatient Expense </label>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Room Charges" name="charges" required>
                                                    <label for="form_control_1">Room Charges</label>
                                                    <span class="help-block">Enter Room Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div> -->

                                    </div>
                                </div>
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- END SAMPLE FORM PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.css') ?>"/>
<script type="text/javascript" src="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.js') ?>"></script>
