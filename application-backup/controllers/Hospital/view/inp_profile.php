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
                            <span class="caption-subject bold uppercase"> MR Details</span>
                        </div>
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
                                        <td ><span class="caption-subject bold uppercase" > Treatment : </span></td>
                                        <td><?= $files['service_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td ><i class="fas fa-door-closed" ></i><span class="caption-subject bold uppercase" > Room : </span></td>
                                        <td><?= $files['room_name'] ?></td>
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
                            
                            <div class="col-md-6 ">
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

                            <div class="col-md-6">
                                <div class="portlet">
                                    <div class="portlet-title">
                                            <div class="caption font-red-sunglo" style="padding: 10px">
                                                <i class="fas fa-book-medical font-red-sunglo"></i>
                                                <span class="caption-subject bold uppercase">Voucher Payments </span>
                                            </div>
                                        </div>
                                        <div id="treatments-table-div" class="portlet-body form  m-t-0">
                                            <table id="example" class="table table-bordered table-strapped display  m-b-0" cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    
                                                    <th colspan="5" style="text-align: center;">Expences / Vouchers</th>
                                    
                                                    <!-- <th>Balance</th> -->
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td style="text-align: center;"><strong>Voucher No.</strong></td>
                                                    <td style="text-align: center;"><strong>Category</strong></td>
                                                    <td style="text-align: center;"><strong>Amount</strong></td>
                                                    <td style="text-align: center;"><strong>To</strong></td>
                                                    <td style="text-align: center;"><strong>Date</strong></td>
                                                    
                                                </tr>
                                                <?php 
                                                $voucherpaymenttotal = 0;
                                                foreach ($exp_vouchers as $exp_voucher){
                                                    // print_array($exp_voucher,1);
                                                    $voucherpaymenttotal += $exp_voucher['exp_amount_numbers'];
                                                    ?>
                                                <tr>

                                                    <td style="text-align: center;"><?= $exp_voucher['id'] ?></td>
                                                    <td style="text-align: center;"><?php foreach($expense_categories as $expense_categorie) {
                                                                                            if($exp_voucher['exp_category_id'] == $expense_categorie['id']) {
                                                                                                ?> <?= $expense_categorie['name'] ?> 
                                                                                    <?php }
                                                                                    } ?></td>
                                                    <td style="text-align: center;"><?= $exp_voucher['exp_amount_numbers'] ?></td>
                                                    <td style="text-align: center;"> <?php if($this->aauth->get_user($exp_voucher['employee_id'])->is_doctor == 1) { ?>
                                                    <?= $this->aauth->get_user($exp_voucher['employee_id'])->name ?> </td>
                                                    <?php 
                                                } ?>
                                                    <td style="text-align: center;"><?= $exp_voucher['created_on'] ?></td>
                    
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
                            
                            

                            <div class="col-md-6">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption font-red-sunglo">
                                            <i class="fas fa-book-medical font-red-sunglo"></i>
                                            <span class="caption-subject bold uppercase"> Extra Charges</span>
                                        </div>
                                        
                                    </div>
                                    <div id="treatments-table-div" class="portlet-body form">
                                        <table id="example" class="table table-bordered table-strapped display" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th colspan='4' style="text-align: center;">Extra Charges</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                
                                                <td style="text-align: center;width:30%;"><strong>Service</strong></td>
                                                <td style="text-align: center;width:14%;"><strong>Amount</strong></td>
                                                <td style="text-align: center;width:41%;"><strong>Date</strong></td>
                                                <!-- <th>Balance</th> -->
                                            </tr>
                                            <?php foreach ($recestation_services as $ser){ ?>
                                            <tr>

                                                
                                                <td style="text-align: center;"><?= $ser['name'] ?></td>
                                                
                                                <?php if(!empty($recestrans)){
                                                ?>
                                                <td colspan="3" style="text-align: center;margin-bottom: 0px;margin-top: 0px;padding: 0px;line-height:0;">
                                                    
                                                            
                                                            <table id="example" style="margin-bottom: 0px;margin-top: 0px;padding: 0px;line-height:0;" class="table table-bordered table-strapped display" cellspacing="0" width="100%">
                                                                
                                                                <?php foreach($recestrans as $rtrans){
                                                                    $count = 0;  
                                                                ?>
                                                                <tbody>
                                                                
                                                                    <tr>
                                                                    <?php if($rtrans['service_id'] == $ser['id']){
                                                                        $count = 1;
                                                                    ?>
                                                                        <td style="text-align: center;width:25%;"><?= $rtrans['amount_in_num'] ?><!-- <td style="text-align: center;width:42%;"> //$this->aauth->get_user($rtrans['doctor_id'])->name ?></td> --><td style="text-align: center;width:75%;"><?= $rtrans['created_on'] ?></td><br>
                                                                    <?php }
                                                                    ?>
                                                                    </tr>
                                                                </tbody>
                                                                <?php } ?>
                                                            </table>
                                                            
                                                            
                                                            <?php if($count != 1){ ?>
                                                                <p style="text-align: center;line-height:2.5;">UNPAID</p>
                                                            <?php }
                                                            ?>
                                                            
                                                        
                                                </td>
                                                <?php }else{ ?>
                                                    <td style="text-align: center;">UNPAID</td>
                                                <?php } ?>


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
                                    <button type="submit" class="btn btn-warning dontprint">Discharge</button>
                                <?php }elseif($blance == 0){ ?>
                                    <button type="submit" class="btn btn-success dontprint">Discharge</button>
                                <?php }elseif($blance != 0){ ?>
                                    <input name="declare_loss" type="hidden" value="<?= $blance ?>">
                                    <div class="col-sm-12 form-group form-md-line-input has-success">
                                        <label><input type="checkbox" name="do_agree" required id="can_click" /> I acknowlege that i am diclaring loss with concent.</label>
                                    </div>
                                    <button type="submit" class="btn btn-danger dontprint">Discharge and Declare Loss</button>
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
