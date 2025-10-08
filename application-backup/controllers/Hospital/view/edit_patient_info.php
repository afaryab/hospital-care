
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Edit Patient</span>
                            </div>
                           
                        </div>
                       
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                
                                    <div class="row">
                                    
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                            
                                                <div class="input-icon">
                                                
                                                    <input type="text" class="form-control" placeholder="Patient Name" name="name" value="<?= $patients['pateint_name'] ?>" required>
                                                    <label for="form_control_1">Patient Name</label>
                                                    <span class="help-block">Enter Patient Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Guardian Name" name="guardian" value="<?= $patients['guardian'] ?>" >
                                                    <label for="form_control_1">Guardian Name</label>
                                                    <span class="help-block">Enter Guardian Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Age" name="age" value="<?= $patients['age_days'] ?>">
                                                    <label for="form_control_1">Age </label>
                                                    <span class="help-block">Enter Age Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="radio" class="form-control" id="male" name="gender" value="M" <?= $patients['gender'] == 'M' || $patients['gender'] == 'm' ? 'checked' : '' ?>>
                                                    <label for="male">Male</label>
                                                    <input type="radio" class="form-control" id="female" name="gender" value="F" <?= $patients['gender'] == 'F' || $patients['gender'] == 'f' ? 'checked' : '' ?>>
                                                    <label for="female">Female</label>
                                                    <input type="radio" class="form-control" id="other" name="gender" value="B" <?= $patients['gender'] == 'B' || $patients['gender'] == 'b' ? 'checked' : '' ?>>
                                                    <label for="other">Other</label>
                                                    <span class="help-block">Select Gender Here!.</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                       
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Contact No." name="contact" value="<?= $patients['patient_contact_mobile'] ?>">
                                                    <label for="form_control_1">Contact No. </label>
                                                    <span class="help-block">Enter Contact No. Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success" >
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="CNIC" name="cnic" value="<?= $patients['patient_cnic'] ?>">
                                                    <label for="form_control_1">CNIC </label>
                                                    <span class="help-block">Enter CNIC Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Address" name="address" value="<?= $patients['patient_address'] ?>">
                                                    <label for="form_control_1">Address</label>
                                                    <span class="help-block">Enter Address Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
    

                                    </div>
                 


                                </div>
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Edit</button>
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
