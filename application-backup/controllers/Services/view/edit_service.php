
<div class="page-content-wrapper">
    <div class="page-content">
        
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> Edit Service</span>
                            </div>
                        </div>
                       
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                <?php
                                
                               
                                    if ($type == 'test') {
                                        
                                    ?>
                                    <div class="row">
                                    
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                            
                                                <div class="input-icon">
                                                
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $test_services['name'] ?>" required>
                                                    <label for="form_control_1">Service Name</label>
                                                    <span class="help-block">Enter Service Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $test_services['charges'] ?>" required>
                                                    <label for="form_control_1">Service Charges</label>
                                                    <span class="help-block">Enter Charges Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
  
                                       
                                        <div class="col-md-6 shortcode" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Test Code" name="shrt_code" value="<?= $test_services['shrt_code'] ?>">
                                                    <label for="form_control_1">Test Short Code </label>
                                                    <span class="help-block">Enter Test Short Code Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 samplename" >
                                            <div class="form-group form-md-line-input has-success" >
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Sample Name" name="sample" value="<?= $test_services['sample'] ?>">
                                                    <label for="form_control_1">Test Sample Name </label>
                                                    <span class="help-block">Enter Test Sample Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 reporttime" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Reporting Time" name="reporting_time" value="<?= $test_services['reporting_time'] ?>">
                                                    <label for="form_control_1">Reporting Time For Test</label>
                                                    <span class="help-block">Enter Reporting Time For Test Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
    

                                    </div>
                                <?php
                                    }
                                    else{
                                    ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                  
                                                <?php
                                                
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $opd_services['name'] ?>" required>
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $inpatient_services['name'] ?>" required>

                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $emergency_services['name'] ?>" required>

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $xray_services['name'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                ?>
                                                        <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $dental_services['name'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                ?>
                                                        <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $ultrasound_services['name'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'reces') {
                                                ?>
                                                            <input type="text" class="form-control" placeholder="Service Name" name="name" value="<?= $recestation_services['name'] ?>" required>
                                                    <?php }
                                                ?>
                                                    <label for="form_control_1">Service Name</label>
                                                    <span class="help-block">Enter Service Name Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                <?php
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $opd_services['charges'] ?>" required>
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $inpatient_services['charges'] ?>" required>

                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $emergency_services['charges'] ?>" required>

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $xray_services['charges'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $dental_services['charges'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $ultrasound_services['charges'] ?>" required>
                                                <?php }
                                                    elseif ($type == 'reces') {
                                                ?>
                                                        <input type="text" class="form-control" placeholder="Service Charges" name="charges" value="<?= $recestation_services['charges'] ?>" required>
                                                    <?php }
                                                ?>
                                                    <label for="form_control_1">Service Charges</label>
                                                    <span class="help-block">Enter Charges Here!.</span>
                                                    <i class="fas fa-signature"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 postkey" >
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <!-- <input type="text" class="form-control" placeholder="Service Short Name" name="post_key"> -->
                                                <?php
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $opd_services['post_key'] ?>">
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $inpatient_services['post_key'] ?>" >

                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $emergency_services['post_key'] ?>" >

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $xray_services['post_key'] ?>" >
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $dental_services['post_key'] ?>">
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                ?>
                                                    <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $ultrasound_services['post_key'] ?>">
                                                <?php }
                                                    elseif ($type == 'reces') {
                                                ?>
                                                            <input type="text" class="form-control" placeholder="Service Short Name" name="post_key" value="<?= $recestation_services['post_key'] ?>">
                                                    <?php }
                                                ?>
                                                    <label for="form_control_1">Service Short Code Name</label>
                                                    <span class="help-block">Enter Service Short Code Name Here!.</span>
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
                                                            <!-- <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"> -->
                                                <?php
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($opd_services['is_doctor_selectable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($inpatient_services['is_doctor_selectable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($emergency_services['is_doctor_selectable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($xray_services['is_doctor_selectable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                    ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($dental_services['is_doctor_selectable'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                    ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($ultrasound_services['is_doctor_selectable'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'reces') {
                                                    ?>
                                                    <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"
                                                    <?php if($recestation_services['is_doctor_selectable'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                ?>
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
                                                            <!-- <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"> -->
                                                <?php
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($opd_services['is_fileable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($inpatient_services['is_fileable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($emergency_services['is_fileable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($xray_services['is_fileable'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                    ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($dental_services['is_fileable'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                    ?>
                                                    <input type="checkbox" id="isfileable" value="1" name="is_fileable" class="md-check"
                                                    <?php if($ultrasound_services['is_fileable'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                ?>
                                                            <label for="isfileable">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp;Is Service Fileable </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-md-12 blockservice" >
                                            <div class="form-group form-md-checkboxes" style="padding-top: 0px !important;">
                                                <h4>Block Service ? </h4>
                                                <div class="md-checkbox-inline text-left">
                                                    <div class="md-checkbox-list">
                                                        <div class="md-checkbox d-block">
                                                            <!-- <input type="checkbox" id="isdocslct" value="1" name="is_doctor_selectable" class="md-check"> -->
                                                <?php
                                                    if ($type == 'opd') {
                                                ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($opd_services['is_deleted'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'inp'){
                                                ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($inpatient_services['is_deleted'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif($type == 'emer'){
                                                ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($emergency_services['is_deleted'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>

                                                <?php }
                                                    elseif($type == 'xray'){
                                                ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($xray_services['is_deleted'] == '1'){ ?>
                                                                	checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'dental') {
                                                    ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($dental_services['is_deleted'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                <?php }
                                                    elseif ($type == 'ultra') {
                                                    ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($ultrasound_services['is_deleted'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                    <?php }
                                                    elseif ($type == 'reces') {
                                                    ?>
                                                    <input type="checkbox" id="isblocked" value="1" name="is_deleted" class="md-check"
                                                    <?php if($recestation_services['is_deleted'] == '1'){ ?>
                                                                    checked="checked"
                                                                <?php } ?>>
                                                    <?php }
                                                ?>
                                                            <label for="isblocked">
                                                                <span class="inc"></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> <i class="fas fa-user-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp;Block Service </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                       

                                    </div>

                                    <?php
                                    }
                                    ?>


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
    toggleElementBySelection("checkopd","blockservice");
     // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleInpOptions(){
    // console.log('toggleInpOptions');
    toggleElementBySelection("checkinp","postkey");
    toggleElementBySelection("checkinp","doctorselect");
    toggleElementBySelection("checkopd","blockservice");
    // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleEmerOptions(){
   // console.log('toggleEmerOptions');
    toggleElementBySelection("checkemer","postkey");
    toggleElementBySelection("checkemer","doctorselect");
    toggleElementBySelection("checkopd","blockservice");
    // document.getElementsByClassName("postkey");
    // document.getElementsByClassName("doctorselect");
}
function toggleXrayOptions(){
    // console.log('toggleXrayOptions');
    toggleElementBySelection("checkxray","postkey");
    toggleElementBySelection("checkxray","doctorselect");
    toggleElementBySelection("checkopd","blockservice");
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