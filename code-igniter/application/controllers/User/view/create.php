
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar subBar">
            <ul class="page-breadcrumb">
                <li class="btn btn-default">
                    <a href="<?= site_url($USERS_LIST) ?>">
                        <i class="fas fa-list"></i>
                        <span class="d-inline">Users List</span>
                    </a>
                </li>
                <li class="btn btn-primary active">
                    <a>
                        <i class="fas fa-user-plus"></i>
                        <span class="d-inline">Create New User</span>
                    </a>
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
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Full Name" name="user_name" required>
                                                    <label for="form_control_1">Full Name</label>
                                                    <span class="help-block">Enter User's Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="parent_id">
                                                        <option value="0">Report To</option>
                                                        <?php foreach ($users as $u){ ?>
                                                            <option value="<?= $u->id ?>"><?= $u->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="parent_id">Report To</label>
                                                    <span class="help-block">Select Supervisor</span>
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="act_as_id">
                                                        <option value="0">Indipendent Profile</option>
                                                        <?php foreach ($users as $u){ ?>
                                                            <option value="<?= $u->id ?>"><?= $u->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="act_as_id">Act As</label>
                                                    <span class="help-block">Select Action User</span>
                                                    <i class="fas fa-id-badge"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="email" class="form-control" placeholder="Users's Email" name="user_email" required>
                                                    <label for="form_control_1">User Email.</label>
                                                    <span class="help-block">Enter User Like: example@example.com</span>
                                                    <i class="fa fa-at"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="Password" class="form-control" placeholder="Users's Password" name="user_password" required>
                                                    <label for="form_control_1">User's Password.</label>
                                                    <span class="help-block">User's Password: Don't chose easy password.</span>
                                                    <i class="fas fa-key"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="Password" class="form-control" placeholder="Retype User's Password" name="ret_user_password" required>
                                                    <label for="form_control_1">Retype User's Password.</label>
                                                    <span class="help-block">Password must be matched with previous one.</span>
                                                    <i class="fas fa-check-double"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-checkboxes form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>Permission Profile</h4>
                                                <hr/>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <?php foreach ($groups as $key=>$grop){ ?>
                                                            <div class="md-checkbox">
                                                                <input type="checkbox" id="checkbox<?= $key ?>" class="md-check" name="is_<?= $grop->name ?>" title="<?= $grop->department ?>">
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
                                            <div class="form-group form-md-checkboxes" style="padding-top: 0px !important;">
                                                <h4>User Contribution As</h4>
                                                <hr/>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkadministrator" value="1" name="is_super_admin" class="md-check" onclick="toggleAdminOptions()">
                                                            <label for="checkadministrator">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp; Administrator </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkrecptionist" value="1" name="is_receptionist" class="md-check ReceptionCheckBox" onchange="toggleReceptionsOptions()">
                                                            <label for="checkrecptionist">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-astronaut"></i>&nbsp;&nbsp;&nbsp;&nbsp; Receptionist </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkdoctor" value="1" name="is_doctor" class="md-check" onchange="toggleDoctorOptions()">
                                                            <label for="checkdoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp; Doctor </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checknursing" value="1" name="is_nurse" class="md-check">
                                                            <label for="checknursing">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-nurse"></i>&nbsp;&nbsp;&nbsp;&nbsp; Nurse </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkxraytech" value="1" name="is_xray_tech" class="md-check">
                                                            <label for="checkxraytech">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-nurse"></i>&nbsp;&nbsp;&nbsp;&nbsp; Xray Tech </label>
                                                        </div>
                                                        
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 ReceptionOptionsBox" style="display: none;" >
                                            <div class="form-group form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>Reception Schedule</h4>
                                                <hr/>
                                                <div id="reception-counter-planner"></div>
                                                <input id="reception-counter-planner-input" name="reception_schedule" type="hidden"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 DoctorOptionsBox" style="display: none;">
                                            <div class="form-group form-md-checkboxes form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>Doctor Deparments</h4>
                                                <hr/>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkopddoctor" value="1" name="is_opd_doctor" class="md-check" onchange="toggleDoctorOPDOptions()">
                                                            <label for="checkopddoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-clinic-medical"></i>&nbsp;&nbsp;&nbsp;&nbsp; OPD </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkinptdoctor" value="1" name="is_inpatient_doctor" class="md-check">
                                                            <label for="checkinptdoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-hospital-symbol"></i>&nbsp;&nbsp;&nbsp;&nbsp; INPD </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkemergencydoctor" value="1" name="is_emergency_doctor" class="md-check" onchange="toggleDoctorEmergencyOptions()">
                                                            <label for="checkemergencydoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-first-aid"></i>&nbsp;&nbsp;&nbsp;&nbsp; Emergency </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkdentaldoctor" value="1" name="is_dentist" class="md-check">
                                                            <label for="checkdentaldoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-first-aid"></i>&nbsp;&nbsp;&nbsp;&nbsp; Dental </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkultradoctor" value="1" name="is_ultrasound_doc" class="md-check">
                                                            <label for="checkultradoctor">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-first-aid"></i>&nbsp;&nbsp;&nbsp;&nbsp; Ultrasound </label>
                                                        </div>
                                                        

                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 DoctorOPDOptionsBox" style="display: none;">
                                            <div class="form-group form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>OPD Schedule</h4>
                                                <hr/>
                                                <div id="doctor-opd-planner"></div>
                                                <input id="doctor-opd-planner-input" name="doctor_opd_schedule" type="hidden"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 DoctorEmergencyOptionsBox" style="display: none;">
                                            <div class="form-group form-md-line-input" style="padding-top: 0px !important;">
                                                <h4>Emergency Schedule</h4>
                                                <hr/>
                                                <div id="doctor-emergency-planner"></div>
                                                <input id="doctor-emergency-planner-input" name="doctor_emergency_schedule" type="hidden"/>
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
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.css') ?>"/>
<script type="text/javascript" src="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.js') ?>"></script>
<script>
jQuery(function(){
    $("#reception-counter-planner").jqs({
        days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"],
        hour: 12,
        periodDuration: 30,// 15/30/60
        data: [],
        periodOptions:true,
        periodColors: [],
        periodTitle:"",
        periodBackgroundColor:"rgba(82, 155, 255, 0.5)",
        periodBorderColor:"#2a3cff",
        periodTextColor:"#000",
        periodRemoveButton:"Remove",
        periodDuplicateButton:'Duplicate',
        periodTitlePlaceholder:"Title",
        onAddPeriod:function (period) {
            exportReceptionSchedule();
        },
    });
    $("#doctor-opd-planner").jqs({
        days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"],
        hour: 12,
        data: [],
        onAddPeriod:function (period) {
            exportDoctorOPDSchedule();
        },
    });
    $("#doctor-emergency-planner").jqs({
        days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"],
        hour: 12,
        data: [],
        onAddPeriod:function (period) {
            exportDoctorEmergencySchedule();
        },
    });
    
});
function toggleAdminOptions(){
    console.log('toggleAdminOptions');
    // toggleElementBySelection("AdminCheckBox","AdminOptionsBox");
}
function toggleReceptionsOptions(){
    console.log('toggleReceptionsOptions');
    toggleElementBySelection("checkrecptionist","ReceptionOptionsBox");
}
function exportReceptionSchedule(){
    var exportAray = $("#reception-counter-planner").jqs('export');
    console.log(exportAray)
    console.log($("#reception-counter-planner-input"))
    $("#reception-counter-planner-input").val(exportAray);
}
function toggleDoctorOptions(){
    console.log('toggleDoctorOptions');
    toggleElementBySelection("checkdoctor","DoctorOptionsBox");
    $("#checkopddoctor").prop("checked", false);
    $("#checkemergencydoctor").prop("checked", false);
    $('#checkinptdoctor').prop("checked", false);
    $('#checkdentaldoctor').prop("checked", false);
    $('#checkultradoctor').prop("checked", false);
    toggleDoctorOPDOptions();
    toggleDoctorEmergencyOptions();
}
function toggleDoctorOPDOptions(){
    toggleElementBySelection('checkopddoctor', "DoctorOPDOptionsBox");
}
function exportDoctorOPDSchedule(){
    var exportAray = $("#doctor-opd-planner").jqs('export');
    $("#doctor-opd-planner-input").val(exportAray);
}
function toggleDoctorEmergencyOptions(){
    toggleElementBySelection("checkemergencydoctor","DoctorEmergencyOptionsBox");
}
function exportDoctorEmergencySchedule(){
    var exportAray = $("#doctor-emergency-planner").jqs('export');
    $("#doctor-emergency-planner-input").val(exportAray);
}
function toggleNursingOptions(){
    console.log('toggleNursingOptions');
    toggleElementBySelection("AdminCheckBox","AdminOptionsBox");
}
function checkemergencydoctor(){
    toggleElementBySelection("AdminCheckBox","AdminOptionsBox");
}

function toggleElementBySelection(element1Id, element2Class){
    var checkBox = document.getElementById(element1Id);
    // Get the output text
    var elementToToggle = document.getElementsByClassName(element2Class);

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
        for (var i = 0; i < elementToToggle.length; i++) {
            
            var c = elementToToggle[i];
            c.style.display = "block";
            
        }
    } else {
        for (var i = 0; i < elementToToggle.length; i++) {
            
            var c = elementToToggle[i];
            c.style.display = "none";
            
        }
    }
}
</script>