
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Create New Service</span>
                            </div>
                        </div>
                        <!-- <div class="portlet-body form">
                            <form method="GET">
                            <div class="col-md-12">
                                            <div class="form-group form-md-checkboxes" style="padding-top: 0px !important;">
                                                <h4>Select Service Type</h4>
                                                <hr/>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkopd" value="1" name="opd" class="md-check servic" onclick="toggleOpdOptions()">
                                                            <label for="checkopd">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp; OPD </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkinp" value="1" name="inp" class="md-check servic" onchange="toggleInpOptions()">
                                                            <label for="checkinp">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-astronaut"></i>&nbsp;&nbsp;&nbsp;&nbsp; INPATIENT </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkemer" value="1" name="emer" class="md-check servic" onchange="toggleEmerOptions()">
                                                            <label for="checkemer">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp; EMERGENCY </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checkxray" value="1" name="xray" class="md-check servic" onchange="toggleXrayOptions()">
                                                            <label for="checkxray">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-nurse"></i>&nbsp;&nbsp;&nbsp;&nbsp; X-RAY </label>
                                                        </div>
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="checktest" value="1" name="test" class="md-check servic" onchange="toggleTestOptions()">
                                                            <label for="checktest">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-nurse"></i>&nbsp;&nbsp;&nbsp;&nbsp; PATHOLOGY </label>
                                                        </div>
                                                        
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>



                            </form>
                        </div> -->
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-control-group ">
                                                <div class="input-icon">
                                                    <select class="form-control" name="service" id="service" required>
                                                    <option value="">Select Service Type</option>
                                                        <option id="checkopd" value="opd" onselect="toggleOpdOptions()">OPD</option>
                                                        <option  id="checkinp" value="inp" onselect="toggleInpOptions()">INPATIENT</option>
                                                        <option id="checkemer" value="emer" onselect="toggleEmerOptions()">EMERGENCY</option>
                                                        <option id="checkxray" value="xray" onselect="toggleXrayOptions()">X-RAY</option>
                                                        <option id="checktest" value="test" onselect="toggleTestOptions()">PATHOLOGY</option> 
                                                        <option id="checktest" value="dental" onselect="toggleTestOptions()">DENTAL</option>
                                                        <option id="checkultra" value="ultra" onselect="toggleUltraOptions()">ULTRASOUND</option>   
                                                        <option id="checkreces" value="reces" onselect="toggleUltraOptions()">RECESTATION</option>             
                                                    </select>                                            
                                                </div>
                                            </div>
                                        </div>

                                    </div>
</br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" required>
                                                    <label for="form_control_1">Service Name</label>
                                                    <span class="help-block">Enter Service Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" required>
                                                    <label for="form_control_1">Service Charges</label>
                                                    <span class="help-block">Enter Charges Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 postkey" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key">
                                                    <label for="form_control_1">Service Short Code Name</label>
                                                    <span class="help-block">Enter Service Short Code Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-6 shortcode" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Test Code" name="shrt_code">
                                                    <label for="form_control_1">Test Short Code </label>
                                                    <span class="help-block">Enter Test Short Code Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 samplename" >
                                            <div class="form-group form-md-line-input has-success" >
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Sample Name" name="sample">
                                                    <label for="form_control_1">Test Sample Name </label>
                                                    <span class="help-block">Enter Test Sample Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reporttime" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Reporting Time" name="reporting_time">
                                                    <label for="form_control_1">Reporting Time For Test</label>
                                                    <span class="help-block">Enter Reporting Time For Test Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 doctorselect" >
                                            <div class="form-group form-md-checkboxes" style="padding-top: 0px !important;">
                                                <h4>Is Doctor Selectable ? </h4>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check">
                                                            <label for="isdocslct">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp;Is Doctor Selectable </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 fileable" >
                                            <div class="form-group form-md-checkboxes" style="padding-top: 0px !important;">
                                                <h4>Is Service Fileable ? </h4>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check">
                                                            <label for="isfileable">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp;Is Service Fileable </label>
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
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.css') ?>"/>
<script type="text/javascript" src="<?php echo base_url('public/plugins/dinamic_schedule_planner/dist/jquery.schedule.js') ?>"></script>
<script>
// jQuery(function(){
    
//     $("#doctor-opd-planner").jqs({
//         days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"],
//         hour: 12,
//         data: [],
//         onAddPeriod:function (period) {
//             exportDoctorOPDSchedule();
//         },
//     });
//     $("#doctor-emergency-planner").jqs({
//         days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"],
//         hour: 12,
//         data: [],
//         onAddPeriod:function (period) {
//             exportDoctorEmergencySchedule();
//         },
//     });
    
// });
// $('.servic').on('change', function() {
//         $('.servic').not(this).prop('checked', false);  
//     });
function toggleOpdOptions(){
    // console.log('toggleOpdOptions');
    toggleElementBySelection("checkopd","postkey");
    toggleElementBySelection("checkopd","doctorselect");
     // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleInpOptions(){
    // console.log('toggleInpOptions');
    toggleElementBySelection("checkinp","postkey");
    toggleElementBySelection("checkinp","doctorselect");
    // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleEmerOptions(){
   // console.log('toggleEmerOptions');
    toggleElementBySelection("checkemer","postkey");
    toggleElementBySelection("checkemer","doctorselect");
    // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleXrayOptions(){
    // console.log('toggleXrayOptions');
    toggleElementBySelection("checkxray","postkey");
    toggleElementBySelection("checkxray","doctorselect");
    // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleTestOptions(){
    // console.log('toggleTestOptions');
    toggleElementBySelection("checktest","shortcode");
    toggleElementBySelection("checktest","samplename");
    toggleElementBySelection("checktest","reporttime");
    // document.getElementsByClassName("shortcode");
    // document.getElementsByClassName("samplename");
    // document.getElementsByClassName("reporttime");
}
function toggleUltraOptions(){
    // console.log('toggleTestOptions');
    toggleElementBySelection("checkxray","postkey");
    toggleElementBySelection("checkxray","doctorselect");
    // document.getElementsByClassName("shortcode");
    // document.getElementsByClassName("samplename");
    // document.getElementsByClassName("reporttime");
}
// function exportReceptionSchedule(){
//     var exportAray = $("#reception-counter-planner").jqs('export');
//     console.log(exportAray)
//     console.log($("#reception-counter-planner-input"))
//     $("#reception-counter-planner-input").val(exportAray);
// }
// function toggleDoctorOptions(){
//     console.log('toggleDoctorOptions');
//     toggleElementBySelection("checkdoctor","DoctorOptionsBox");
//     $("#checkopddoctor").prop("checked", false);
//     $("#checkemergencydoctor").prop("checked", false);
//     $('#checkinptdoctor').prop("checked", false);
//     toggleDoctorOPDOptions();
//     toggleDoctorEmergencyOptions();
// }
// function toggleDoctorOPDOptions(){
//     toggleElementBySelection('checkopddoctor', "DoctorOPDOptionsBox");
// }
// function exportDoctorOPDSchedule(){
//     var exportAray = $("#doctor-opd-planner").jqs('export');
//     $("#doctor-opd-planner-input").val(exportAray);
// }
// function toggleDoctorEmergencyOptions(){
//     toggleElementBySelection("checkemergencydoctor","DoctorEmergencyOptionsBox");
// }
// function exportDoctorEmergencySchedule(){
//     var exportAray = $("#doctor-emergency-planner").jqs('export');
//     $("#doctor-emergency-planner-input").val(exportAray);
// }
// function toggleNursingOptions(){
//     console.log('toggleNursingOptions');
//     toggleElementBySelection("AdminCheckBox","AdminOptionsBox");
// }
// function checkemergencydoctor(){
//     toggleElementBySelection("AdminCheckBox","AdminOptionsBox");
// }

function toggleElementBySelection(element1Id,element2Class){
     var opt = document.getElementById(element1Id);
    // Get the output text
    var elementToToggle = document.getElementsByClassName(element2Class);

    // If the checkbox is checked, display the output text
    // if (opt.selected == true){
        for (var i = 0; i < elementToToggle.length; i++) {
            
            var c = elementToToggle[i];
            c.style.display = "block";
            
        }
    // } else {
    //     for (var i = 0; i < elementToToggle.length; i++) {
            
    //         var c = elementToToggle[i];
    //         c.style.display = "none";
            
    //     }
    // }
}
</script>