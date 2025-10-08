<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>

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
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> File Details</span>
                        </div>
                        <a href="<?= site_url($NEW_DENTAL_TREATMENT.$files['id']) ?>" class="btn btn-success pull-right"><i class="far fa-plus-square"></i> Add New Treatment</a>
                    </div>
                    <div class="portlet-body form">
                    <form method="POST" onsubmit="return confirm('Do you really want to discharge this patient?');">
                        <div class="row" >
                            <div class="col-md-12">
                                <table id="example" class="table display table-bordered " cellspacing="50" width="100%">
                                    <tr>
                                        <td><b>File ID :</b></td>
                                        <td><?= str_pad($files['id'], 8, '0', STR_PAD_LEFT) ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Status:</b></td>
                                        <td><?= $files['status'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Patient Name :</b></td>
                                        <td><?= $patient['pateint_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Guardian :</b></td>
                                        <td><?= $patient['guardian'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Patient Contact :</b></td>
                                        <td><?= $patient['patient_contact_mobile'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>CNIC :</b></td>
                                        <td><?= $patient['patient_cnic'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Patient Address :</b></td>
                                        <td><?= $patient['patient_address'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Service Name :</b></td>
                                        <td><?= $files['service_name'] ?></td>
                                    </tr>
                                    <?php
                                        if($files['closed_on'] != NULL ){ 
                                    ?>
                                    <tr>
                                        <td ><b>Date of Discharge :</b></td>
                                        <td><?= date('d-m-Y', strtotime($files['closed_on'])) ?></td>
                                    </tr>
                                    <?php 
                                    }
                                    ?>
                                </table>
                            </div>
               
                            
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 " style="text-align: center;">
                            <?php if($files['panel_name'] != NULL){ ?>
                                        <div><h3><p>On the panel of <span class="caption-subject font-red-sunglo bold uppercase" ><?= $files['panel_name']?></span></p></h3></div>
                                        </br>
                                        <?php } ?>  
                            </div>
                        </div>  
                         
                        

                        <div class="row">
                            
                            <div class="col-md-12 ">
                                <div class="portlet">
                                <div class="portlet-title">
                                        <div class="caption font-red-sunglo">
                                            <i class="fas fa-book-medical font-red-sunglo"></i>
                                            <span class="caption-subject bold uppercase"> Payments</span>
                                        </div>
                                    </div>
                                    <div id="treatments-table-div" class="portlet-body form">
                                        <table id="example" class="table table-bordered table-strapped display" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                
                                                <th colspan='2' style="text-align: center;">Transactions</th>
                                
                                                <!-- <th>Balance</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="text-align: center;"><strong>Amount</strong></td>
                                                <td style="text-align: center;"><strong>Date</strong></td>
                                                <!-- <th>Balance</th> -->
                                            </tr>
                                            <?php foreach ($trans as $tran){ ?>
                                            <tr>

                                                
                                                <td style="text-align: center;"><?= $tran['amount_in_num'] ?></td>
                                                <td style="text-align: center;"><?= $tran['created_on'] ?></td>
                
                                            </tr>
                                            <?php } ?>
                                            
                                            </tbody>
                                            
                                        
                                        </table>
                                    </div>
                                </div>
                                <!-- BEGIN SAMPLE FORM PORTLET-->
                            </div>

                            
                        </div>
                        <div class="row">
                            
                            <div class="col-md-12 ">
                                <div class="portlet">
                                <div class="portlet-title">
                                        <div class="caption font-red-sunglo">
                                            <i class="fas fa-book-medical font-red-sunglo"></i>
                                            <span class="caption-subject bold uppercase"> Treatments</span>
                                        </div>
                                    </div>
                                    <div id="treatments-table-div" class="portlet-body form">
                                        <table id="example" class="table table-bordered table-strapped display" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                
                                                <th style="text-align: center;">Name</th>
                                                <th style="text-align: center;">Description</th>
                                                <th style="text-align: center;">Created On</th>
                                                <!-- <th>Balance</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($treats as $treat){ ?>
                                            <tr>

                                                
                                                <td style="text-align: center;"><?= $treat['name'] ?></td>
                                                <td style="text-align: center;"><?= $treat['description'] ?></td>
                                                <td style="text-align: center;"><?= $treat['created_on'] ?></td>
                
                                            </tr>
                                            <?php } ?>
                                            
                                            </tbody>
                                            
                                        
                                        </table>
                                    </div>
                                </div>
                                <!-- BEGIN SAMPLE FORM PORTLET-->
                            </div>

                            
                        </div>
                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption font-red-sunglo">
                                            <i class="fas fa-book-medical font-red-sunglo"></i>
                                            <span class="caption-subject bold uppercase"> Appointments</span>
                                        </div>
                                        
                                    </div>
                                    <div id="treatments-table-div" class="portlet-body form">
                                        <table id="example" class="table table-bordered table-strapped display" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">Purpose</th>
                                                    <th style="text-align: center;">Doctor</th>
                                                    <th style="text-align: center;">Date and Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($appointments as $appointment){ ?>
                                            <tr>

                                                
                                                <td style="text-align: center;"><?= $appointment['appointment_notes'] ?></td>
                                                <td style="text-align: center;"><?= $this->aauth->get_user($appointment['doctor_id'])->name ?></td>
                                                <td style="text-align: center;"><?= $appointment['start_date'] ?></td>
                                            </tr>
                                            <?php } ?>
                                            
                                            </tbody>
                                            
                                        
                                        </table>
                                    </div>
                                </div>
                                <!-- BEGIN SAMPLE FORM PORTLET-->
                            </div>

                            

                            <div class="col-md-offset-8 col-md-4 ">
                                <table class="table">
                                    <tr>
                                        <td colspan="3">Statement</td>
                                    </tr>
                                    <tr>
                                        <td>Account</td>
                                        <td>Amount</td>
                                        <td>Balance</td>
                                    </tr>
                                    <?php $blance = $files['file_charges']; ?>
                                    <tr>
                                        <td>File Charges :</td>
                                        <td><?= $files['file_charges'] ?></td>
                                        <td><?= $blance?></td>
                                    </tr>
                                    <tr>
                                        <td>Charges Paid :</td>
                                        <?php 
                                        $sum=0; 
                                        foreach ($trans as $tran){
                                          
                                        $sum += $tran['amount_in_num']; 
                                        }
                                        // foreach($recestrans as $rtrans){
                                        // $sum += $rtrans['amount_in_num']; 
                                        // }
                                            
                                        $blance = $blance - $sum; ?>
                                        <td><?= $sum ?></td>
                                        <td><?= $blance ?></td>
                                    </tr>
                                    <tr>
                                    <?php $f_expenses = 0;
                                    $voucherpaymenttotal = 0;
                                    // foreach ($exp_trans as $exp_tran){
                                    //     $f_expenses += $exp_tran['amount_in_num'];
                                    // }
                                    
                                    // $blance = $f_expenses + $blance;?>
                                        <td>File Expenses :</td>
                                        <td><?= $f_expenses ?></td>
                                        <td><?= $blance?></td>
                                    </tr>
                                    <tr>
                                        <td ><b>Amount Remaining :</b></td>
                                        <td></td>
                                        <td><?= $blance?></td>
                                        <input id="prodId" name="remaining_ammount" type="hidden" value="<?= $blance ?>">
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Gain :</b></td>
                                        <td colspan="2"><?= $sum - $voucherpaymenttotal ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Expected Gain :</b></td>
                                        <td colspan="2"><?= $files['file_charges'] - $voucherpaymenttotal ?></td>
                                    </tr>
                                    
                                </table>
                            </div>
                        </div>
                        <div class="form-actions noborder text-right">
                                <?php if($files['panel_name'] != NULL){ ?>
                                    <button type="submit" class="btn btn-warning">Discharge</button>
                                <?php }elseif($blance == 0){ ?>
                                    <button type="submit" class="btn btn-success">Discharge</button>
                                <?php }elseif($blance != 0){ ?>
                                    <input name="declare_loss" type="hidden" value="<?= $blance ?>">
                                    <div class="col-sm-12 form-group form-md-line-input has-success">
                                        <label><input type="checkbox" name="do_agree" required id="can_click" /> I acknowlege that i am diclaring loss with concent.</label>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Discharge and Declare Loss</button>
                                <?php } ?>
                            </div>
                    </form>
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
