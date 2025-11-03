
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-users"></i>
                    <span>Users Management</span>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <i class="fa fa-user-plus"></i>
                    <span class="uppercase"><?= $title; ?></span>
                    <i class="fa fa-angle-down"></i>
                </li>
            </ul>

        </div>
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> <?= $title ?></span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="User Name" name="user_name" value="<?= $user->name ?>" required>
                                                    <label for="form_control_1">Full Name</label>
                                                    <span class="help-block">Enter User's Name Here!.</span>
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="email" class="form-control" placeholder="Users's Email" name="user_email" value="<?= $user->email ?>" required>
                                                    <label for="form_control_1">User Email.</label>
                                                    <span class="help-block">Enter User Like: example@example.com</span>
                                                    <i class="fa fa-at"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-checkboxes form-md-line-input" style="padding-top: 0px !important;">
                                                <label>Group Permissions</label>
                                                <div class="md-checkbox-inline text-center">
                                                    <div class="md-checkbox-list">

                                                        <?php foreach ($groups as $key=>$grop){ ?>
                                                            <div class="md-checkbox">
                                                                <input type="checkbox" 
                                                                id="checkbox<?= $key ?>" 
                                                                class="md-check" 
                                                                name="is_<?= $grop->name ?>" 
                                                                title="<?= $grop->name ?>"
                                                                <?php if($this->aauth->is_member($grop->name, $user->id)){ ?>
                                                                	checked="checked"
                                                                <?php } ?>
                                                                >
                                                                <label for="checkbox<?= $key ?>">
                                                                <span></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span>
                                                                <?= $grop->name ?></label>
                                                            </div>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-checkboxes form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>Departments to Handle</h4>
                                                <hr/>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check1" value="1" name="is_receptionist" class="md-check" 
                                                            <?php if($user->is_receptionist == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check1">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_receptionist </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check7" value="1" name="is_opd_doctor" class="md-check" 
                                                            <?php if($user->is_opd_doctor == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check7">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_opd_doctor </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check8" value="1" name="is_inpatient_doctor" class="md-check" 
                                                            <?php if($user->is_inpatient_doctor == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check8">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_inpatient_doctor </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check9" value="1" name="is_emergency_doctor" class="md-check" 
                                                            <?php if($user->is_emergency_doctor == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check9">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_emergency_doctor </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check10" value="1" name="is_xray_tech" class="md-check" 
                                                            <?php if($user->is_xray_tech == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check10">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_xray_tech </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check11" value="1" name="is_dentist" class="md-check" 
                                                            <?php if($user->is_dentist == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check11">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_dentist </label>
                                                        </div>
                                                        <div class="md-checkbox">
                                                            <input type="checkbox" id="check12" value="1" name="is_ultrasound_doc" class="md-check" 
                                                            <?php if($user->is_ultrasound_doc == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                            <label for="check12">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> is_ultrasound_doctor </label>
                                                        </div>
                                                        
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>

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
