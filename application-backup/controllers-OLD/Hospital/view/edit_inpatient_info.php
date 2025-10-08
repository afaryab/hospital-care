
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Edit Inpatient</span>
                            </div>
                            <?php ?>
                                <a href="<?= site_url($INP_PAY).$files['id'] ?>" class="btn btn-success pull-right">Recieve Payment </a>
                            <?php ?>
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
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="service_id" >
                                                        <option value="" selected><?= $files['service_name'] ?></option>
                                                        <?php foreach ($inpatient_services as $inpatient_service) { ?>
                                                            
                                                            <option value="<?= $inpatient_service['id'] ?>" <?= ($files['service_id'] == $inpatient_service['id']) ? "selected='selected'" : '' ?>><?= $inpatient_service['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="form_control_1">Change Service</label>
                                                    <span class="help-block">Service</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="panel" >
                                                        <?php if ($files['panel_name'] == NULL) { ?>
                                                            
                                                            <option value="" selected>On Cash</option>
                                                        <?php } else{ ?>
                                                            <option value="" selected>On the panel of <?=$files['panel_name'] ?></option>
                                                        <?php } ?>    
                                                        <option value="">CASH</option>
                                                        <optgroup label="Panel">
                                                        <?php foreach ($panel_companies as $panel_companie) { ?>
                                                            
                                                            <option value="<?= $panel_companie['name'] ?>" <?= ($files['panel_name'] == $panel_companie['name']) ? "selected='selected'" : '' ?>><?= $panel_companie['name'] ?></option>
                                                        <?php } ?>
                                                        </optgroup>
                                                    </select>
                                                    <label for="form_control_1">Patient Type (Panel / Cash )</label>
                                                    <span class="help-block">Select Patient Type</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="is_visiting" >
                                                        <?php if ($files['is_visiting'] == 1) { ?>
                                                            
                                                            <option value="0">No</option>
                                                            <option value="1" selected>Yes</option>
                                                        <?php } else{ ?>
                                                            <option value="0" selected>No</option>
                                                            <option value="1">Yes</option>
                                                        <?php } ?>    
                                                        
                                                    </select>
                                                    <label for="form_control_1">Is Visiting</label>
                                                    <span class="help-block">Select Is Visiting</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        

                                        
                                        
                                        <div class="row">
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success">
                                            
                                                <div class="input-icon">
                                                <label for="form_control_1" style="color:#888888;font-size: 13px;opacity:1;" > Status </label>
                                                    <input type="radio" class="form-control" id="open" name="status" value="OPEN" <?= $files['status'] == 'OPEN' ? 'checked' : '' ?>>
                                                    <label for="open">Open</label>
                                                    <input type="radio" class="form-control" id="closed" name="status" value="CLOSED" <?= $files['status'] == 'CLOSED' ? 'checked' : '' ?>>
                                                    <label for="closed">Closed</label>
                                                    <input type="radio" class="form-control" id="cancel" name="status" value="CANCELED" <?= $files['status'] == 'CANCELED' ? 'checked' : '' ?>>
                                                    <label for="cancel">Canceled</label>
                                                    <span class="help-block">Change Status </span>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Package Amount" name="package" value="<?= $files['file_charges'] ?>">
                                                    <label for="form_control_1">File Total Package Amount </label>
                                                    <span class="help-block">Change Package Amount Here.! </span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="room_id" >
                                                        <option value="" selected disabled><?= $files['room_name'] ?></option>
                                                        

                                                        <?php foreach($inpd_rooms as $inpd_room) {
                                                                if($inpd_room['is_allotted'] == 1) { ?>
                                                                    <option value="" disabled><span>&#128308;</span><?= $inpd_room['name'] ?></option>
                                                        <?php }else{ ?>
                                                            <option value="<?= $inpd_room['id'] ?>"><span>&#128994;</span><?= $inpd_room['name'] ?></option>
                                                        <?php
                                                            }
                                                        } ?>
                                                    </select>
                                                    <label for="form_control_1">Change Room</label>
                                                    <span class="help-block">Room</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- <div class="row"> -->
                                            <!-- <div class="col-md-12" >
                                                <div class="portlet-title">
                                                    <div class="caption font-red-sunglo">
                                                        <span class="caption-subject bold uppercase"> Edit Transactions</span>
                                                    </div>
                                                </div>

                                            </div> -->
                                        <!-- </div> -->


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
