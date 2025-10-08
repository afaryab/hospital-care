<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>
<style>
@media print{
    table>tbody>tr>td{
        border-top: none !important;
        border-bottom: none !important;
        line-height: 50px;
        padding: 8px;
    }
    .logo{
        max-width: 200px !important;
        margin-top: 0px !important;
        padding-top: 0px !important;
    }
    hr{
        border-top: 1px solid #A6A5A5 !important;
    }
}
</style>
 
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<?php 
$count = 0; ?>
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="col-lg-12 text-center invoice-header">
                        <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 200px;" src="<?= $PRINT_IMAGE_64; ?>">
                    </div>
                    <div class="portlet-body form">
                    
                        <div class="row" >
                            <div class="col-md-12">
                                <table id="example" class=" display" cellspacing="50" width="100%">
                                    <?php $sp='&nbsp'; ?>
                                    <tr>
                                        <td><b>Room Number :</b></td>
                                        <td><?= isset($files['room_name']) ? $files['room_name'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                        <td class="text-center"><b>Registration No. :</b></td>
                                        <td><?= str_pad($files['id'], 8, '0', STR_PAD_LEFT) ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><b>Patient Name :</b></td>
                                        <td><?= isset($patient['pateint_name']) ? $patient['pateint_name'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                        <td class="text-center"><b>Age</b></td>
                                        <td><?= isset($patient['age_days']) ? $patient['age_days'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Husband / Father Name :</b></td>
                                        <td colspan="3"><?= isset($patient['guardian']) ? $patient['guardian'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Address:</b></td>
                                        <td colspan="3"><?= isset($patient['patient_address']) ? $patient['patient_address'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>CNIC :</b></td>
                                        <td><?= isset($patient['patient_cnic']) ? $patient['patient_cnic'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                        <td class="text-center"><b>Cell No.</b></td>
                                        <td><?= isset($patient['patient_contact_mobile']) ? $patient['patient_contact_mobile'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Provisional Diagnosis:</b></td>
                                        <td colspan="3"><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Final Diagnosis:</b></td>
                                        <td colspan="3"><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Operation / Procedure</b></td>
                                        <td colspan="3"><?= isset($files['service_name']) ? $files['service_name'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Consultant</b></td>
                                        <td colspan="3"><?= isset($this->aauth->get_user($files['treatment_by'])->name) ? $this->aauth->get_user($files['treatment_by'])->name : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Date of Procedure :</b></td>
                                        <td><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                        <td class="text-center"><b>Time</b></td>
                                        <td><?= $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                    <tr>
                                        <td><b>Date of Admission :</b></td>
                                        <td><?= isset($files['created_on']) ? $files['created_on'] : $sp; ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                        <td class="text-center"><b>Date of Discharge</b></td>
                                        <td><?php if($files['closed_on'] != NULL ){ echo date('d-m-Y', strtotime($files['closed_on'])); }else{ echo $sp; } ?><hr style="border-top: 2px solid #A6A5A5;"/></td>
                                    </tr>
                                   
                                    
                                </table>
                            </div>
               
                            
                            
                        </div>
                        

                    
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
<style>
    .dz-details{
        display: none !important;
    }
    .table-nopadding>tbody>tr, .table-nopadding>tbody>tr>td{
        padding: 0px !important;
    }
</style>
