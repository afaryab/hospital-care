
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Edit Room</span>
                            </div>
                        </div>
                       
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
          

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                        
                                                    <input type="text" class="form-control" placeholder="Room Name" name="name" value="<?= $rooms['name'] ?>" required>
                                                
                                                    <label for="form_control_1">Room Name</label>
                                                    <span class="help-block">Enter Room Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges"  required>
                                                    <label for="form_control_1">Service Charges</label>
                                                    <span class="help-block">Enter Charges Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div> -->
                                

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
