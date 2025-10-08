
<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <?php if($recieptId == 0){  ?>
                    <div class="col-md-12 ">
                        <form method="GET">
                            <div class="row" id="payment_box_container">
                                <div class="col-md-12 m-t-4 m-b-4">
                                    <div class="col-md-11">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="name" class="form-control" name="receipt_id" value="<?= $recieptId == 0 ? '' : $recieptId ?>">
                                                <label for="form_control_1">Receipt ID</label>
                                                <span class="help-block">Please provide reciept id.</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-success btn-block">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php }else{ ?>
                    <?php if( $receptionTransaction['income_or_expence'] == 'EXPENSE' || $receptionTransaction['income_or_expence'] == 'EXP'){?> 
                        <div class="invoice in_sm" style="margin: 0 auto;">
                            <div class="row">
                                <div class="col-xs-12 text-center invoice-header">
                                    <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 185px;" src="<?= $PRINT_IMAGE_64; ?>">
                                    <hr style="border-top: 3px solid #000;"/>
                                    <h3>-DUPLICATE-</h3>
                                    <hr style="border-top: 3px solid #000;"/>
                                </div>
                                <div class="col-xs-12 invoice-payment">

                                    <p style="font-family: barcode_fontregular;font-size: 130px;height: 150px;"><?= str_pad($receptionTransaction['id'], 11, '0', STR_PAD_LEFT); ?></p>

                                    <hr style="border-top: 3px solid #000;"/>
                                    <table class="table">
                                        <?php foreach($receptionTransactionElements as $ele) {
                                            if($ele['type'] == 'VOUCHER-PAY'){
                                            ?>
                                            <tr>
                                                <td colspan="2"><strong>Voucher # <?= str_pad($voucher['id'], 8, '0', STR_PAD_LEFT); ?></strong><span class="pull-right"><?= $voucher['exp_amount_numbers'] ?> PKR</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><?= $voucher['expense_notes'] ?></td>
                                            </tr>
                                            <?php if( $voucher['payed_to_employee'] == 1){?>
                                                <tr>
                                                    <td><strong>Payed to: </strong></td>
                                                    <td><?= $voucher['expense_notes'] ?></td>
                                                </tr>
                                                <?php }else{?>
                                                    <td><strong>Payed to: </strong></td>
                                                    <td><?= $voucher['payed_to_others'] ?></td>
                                                <?php }?>
                                                <?php if( $voucher['inpatient_file_id'] != 0){?>
                                                    <td><strong>Mr No.: </strong></td>
                                                    <td><?= $voucher['inpatient_file_id'] ?></td>
                                                <?php }?>
                                                <tr>
                                                    <td><strong>Voucher  Dated: </strong></td>
                                                    <td><?= date('d-m-Y h:i a',strtotime($voucher['created_on'])) ?></td>
                                                </tr>
                                                <?php }elseif($ele['type'] == 'INPT-EXP'){
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-center" style="font-size: 15px;"><strong>MR No. : &nbsp;&nbsp;&nbsp;&nbsp;<?= $inpexp['file_id'] ?></strong></td>
                                            </tr>
                                            <tr>
                                            
                                                            
                                                    <td><strong>Service</strong></td>
                                                    <td><strong>Amount</strong></td>
                                            </tr>
                                            <tr>
                                                <td >INPT-EXP</td>
                                                <td ><?= $inpexp['amount_in_num'] ?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="2" class="text-center"><strong>Purpose </strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center"><?= $inpexp['payment_refference'] ?></td>
                                            </tr>
                                               

                                        <?php }else{
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-center">Non Vouchered Payment For</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center"><?= $payments['payment_reference'] ?></td>
                                            </tr>

                                            <?php
                                        }
                                    } ?>
                                        
                                        

                                        
                                    <hr style="border-top: 3px solid #000;"/>
                                    <table class="table">
                                        <tr>
                                            <td class="text-center" colspan="2"><strong>Payments</strong></td>
                                        </tr>
                                        <?php
                                        $totalpayed = 0;
                                        if(!empty($payments)){ ?>

                                            <tr>
                                                <td><strong>Date</strong></td>
                                                <td><strong>Amount</strong></td>
                                            </tr>
                                            
                                                <tr>
                                                    <td><?= date('d-m-Y h:i a',strtotime($payments['created_on'])) ?></td>
                                                    <td><?= $payments['amount_received_num'] ?></td>
                                                </tr>
                                            <?php $totalpayed += $payments['amount_received_num'];  ?>
                                        <?php }elseif(!empty($inpexp)){ ?>
                                            <tr>
                                                <td><strong>Date</strong></td>
                                                <td><strong>Amount</strong></td>
                                            </tr>
                                            
                                                <tr>
                                                    <td><?= date('d-m-Y h:i a',strtotime($inpexp['created_on'])) ?></td>
                                                    <td><?= $inpexp['amount_in_num'] ?></td>
                                                </tr>
                                            <?php $totalpayed += $inpexp['amount_in_num'];  ?>
                                        <?php } else{ ?>
                                            <tr>
                                                <td colspan="2">No Transaction found</td>
                                            </tr>
                                        <?php } ?>
                                            <tr style="border-top: 3px solid #000;">
                                                <td>Total Payed: </td>
                                                <td><?= $totalpayed ?></td>
                                            </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center invoice-header">
                                    <hr style="border-top: 3px solid #000;"/>
                                    <small style="font-size: 10px;"><?= business_contact_numbers ?></small>
                                    <p style="font-size: 10px;"><?= business_contact_address ?></p>
                                    <p style="font-size: 10px;"><?= business_contact_email ?></p>
                                    <hr style="border-top: 3px solid #000;"/>
                                    <p style="font-size: 10px;"> Powered By: Processton.com</p>
                                    <p style="font-size: 10px;"> info@processton.com - +923061105155</p>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>   
                        <?php if( $receptionTransaction['income_or_expence'] == "INCOME"){?>
                            <div class="col-md-12 ">
                                <div class="portlet light bordered">
                                    <div class="invoice in_sm" style="margin: 0 auto;">
                                        <div class="row">
                                            <div class="col-xs-12 text-center invoice-header">

                                                <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                                                <div class="col-xs-12 text-center invoice-header">
                                                    <!-- <hr style="border-top: 3px solid #000;"/> -->
                                                    <p style="font-size: 10px;color:black;">21-Shama Road ,Rasool Park ,Ichra ,Lahore</p>
                                                    <p style="font-size: 10px;color:black;"> PH - 042-37501597 ,98 ,99</p>
                                                    <p>&nbsp;</p>
                                                </div>
                                                <hr style="border-top: 3px solid #000;"/>
                                                <h3>-DUPLICATE-</h3>
                                                <hr style="border-top: 3px solid #000;"/>
                                            </div>
                                            <div class="col-xs-12 invoice-payment">

                                                <p style="font-family: barcode_fontregular;font-size: 130px;height: 150px;"><?= str_pad($receptionTransaction['id'], 11, '0', STR_PAD_LEFT); ?></p>
                                                <?php foreach ($receptionTransactionElements as $ele){ ?>
                                                    <?php if($ele['type'] == 'INPT'){ ?>
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p class="text-center" style="text-alling:center;">MR-<?= str_pad($inpatient_file['id'], 8, '0', STR_PAD_LEFT); ?></p>
                                                    <?php } ?>
                                                    <?php if($ele['type'] == 'OPD'){ ?>
                                                        <?php if($serial['serial_number_doctor'] != NULL ){ ?>
                                                            <hr style="border-top: 3px solid #000;"/>
                                                            <p style="text-alling:center;"><strong>Serial No. : &nbsp;</strong> <?= $serial['serial_number_doctor'] ?></p>
                                                        <?php } ?>
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p style="text-alling:center;"><strong>Doctor : </strong> <?= $user->name ?></p>
                                                    <?php } ?>
                                                    <?php if($ele['type'] == 'DENTAL'){ ?>
                                                    <?php if(!empty($dental_patient_file)){ ?>
                                                            <hr style="border-top: 3px solid #000;"/>
                                                            <p class="text-center" style="text-alling:center;">File No. :-<?= str_pad($dental_patient_file['id'], 8, '0', STR_PAD_LEFT); ?></p>
                                                        <?php } ?>
                                                        <?php if($serial['serial_number_doctor'] != NULL ){ ?>
                                                            <hr style="border-top: 3px solid #000;"/>
                                                            <p style="text-alling:center;"><strong>Serial No. : &nbsp;</strong> <?= $serial['serial_number_doctor'] ?></p>
                                                        <?php } ?>
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p style="text-alling:center;"><strong>Doctor : </strong> <?= $user->name ?></p>
                                                    <?php } ?>
                                                    <?php if($ele['type'] == 'ULTRA'){ ?>
                                                        <?php if($serial['serial_number_doctor'] != NULL ){ ?>
                                                            <hr style="border-top: 3px solid #000;"/>
                                                            <p style="text-alling:center;"><strong>Serial No. : &nbsp;</strong> <?= $serial['serial_number_doctor'] ?></p>
                                                        <?php } ?>
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p style="text-alling:center;"><strong>Doctor : </strong> <?= $user->name ?></p>
                                                    <?php } ?>
                                                    <?php if($ele['type'] == 'RECES'){ ?>
                                                        <?php if($serial['serial_number_doctor'] != NULL ){ ?>
                                                            <hr style="border-top: 3px solid #000;"/>
                                                            <p style="text-alling:center;"><strong>Serial No. : &nbsp;</strong> <?= $serial['serial_number_doctor'] ?></p>
                                                        <?php } ?>
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p class="text-center" style="text-alling:center;">MR-<?= str_pad($recestationtransactions['mr_no'], 8, '0', STR_PAD_LEFT); ?></p>
                                                    
                                                        <hr style="border-top: 3px solid #000;"/>
                                                        <p style="text-alling:center;"><strong>Doctor : </strong> <?= $user->name ?></p>
                                                    <?php } ?>
                                                    
                                                <?php } ?>
                                                <table class="table">
                                                    <tr>
                                                        <td><strong>Name : </strong></td>
                                                        <td><?= $patients['pateint_name'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Contact : </strong></td>
                                                        <td><?= $patients['patient_contact_mobile'] ?></td>
                                                    </tr>
                                                
                                                    <tr>
                                                        <td><strong>Dated: </strong></td>
                                                        <td><?= $patients['created_on'] ?> </td>
                                                    </tr>
                                                <hr style="border-top: 3px solid #000;"/>
                                                <table class="table" >
                                                    <tr>
                                                        <td class="text-center" colspan="3"><strong>Payments</strong></td>
                                                    </tr>
                                                    <?php if(!empty($receptionTransactionElements)){ ?>

                                                        <tr>
                                                            
                                                            <td><strong>INV #</strong></td>
                                                            
                                                            <td><strong>Services</strong></td>
                                                            <td><strong>Amount</strong></td>
                                                        </tr>
                                                        <?php foreach ($receptionTransactionElements as $payment){ ?>
                                                            <tr>
                                                                <td><?= $payment['id'] ?></td>
                                                            
                                                                <?php if($payment['type'] == 'OPD'){ ?>
                                                                <td>OPD-<?= $opd_services['name'] ?></td>
                                                                <?php }elseif($payment['type'] == 'INPT'){ ?>
                                                                    <td>INPD-<?= $inpd_services['name'] ?></td>
                                                                <?php } elseif($payment['type'] == 'EMER'){ ?>
                                                                <td>EMER-<?= $emergency_services['name'] ?></td>
                                                                <?php }elseif($payment['type'] == 'DENTAL'){ ?>
                                                                <td>DENTAL-<?= $dental_services['name'] ?></td>
                                                                <?php }elseif($payment['type'] == 'ULTRA'){ ?>
                                                                <td>ULTRASOUND-<?= $ultrasound_services['name'] ?></td>
                                                                <?php }elseif($payment['type'] == 'RECES'){ ?>
                                                                <td>RECES-<?= $recestation_services['name'] ?></td>
                                                                <?php } ?>
                                                                <td><?= $payment['amount'] ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                            <tr style="border-top: 3px solid #000;">
                                                                <td colspan="2" class="text-right" style="vertical-align: middle;text-align: center;"><strong>Total Amount: </strong></td>
                                                                <td style="font-size: large;"><strong><?= $receptionTransaction['amount'] ?></strong></td>
                                                            </tr>
                                                    <?php }else{ ?>
                                                        <tr>
                                                            <td colspan="3">NILL</td>
                                                        </tr>
                                                        

                                                    <?php } ?>

                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 text-center invoice-header">
                                                <hr style="border-top: 3px solid #000;"/>
                                                <small style="font-size: 10px;"><?= business_contact_numbers ?></small>
                                                <p style="font-size: 10px;"><?= business_contact_address ?></p>
                                                <p style="font-size: 10px;"><?= business_contact_email ?></p>
                                                <hr style="border-top: 3px solid #000;"/>
                                                <p style="font-size: 10px;"> Powered By: Processton.com</p>
                                                <p style="font-size: 10px;"> info@processton.com - +923061105155</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }else{ ?> 
                            <div class="portlet light bordered">
                                <div class="invoice in_sm" style="margin: 0 auto;">
                                    <div class="row">
                                        <div class="col-xs-12 text-center invoice-header">
                                            <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 185px;" src="<?= $PRINT_IMAGE_64; ?>">
                                            <hr style="border-top: 3px solid #000;"/>
                                            <h3>-DUPLICATE-</h3>
                                            <hr style="border-top: 3px solid #000;"/>
                                        </div>
                                        <div class="col-xs-12 invoice-payment">

                                            <p style="font-family: barcode_fontregular;font-size: 130px;height: 150px;"><?= str_pad($receptionTransaction['id'], 11, '0', STR_PAD_LEFT); ?></p>

                                            <hr style="border-top: 3px solid #000;"/>
                                            <table class="table">
                                                <tr>
                                                    <td><strong>Amount: </strong></td>
                                                    <td><?= $receptionTransaction['amount'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Dated: </strong></td>
                                                    <td></td>
                                                </tr>
                                            <hr style="border-top: 3px solid #000;"/>
                                            <table class="table">
                                                <tr>
                                                    <td class="text-center" colspan="2"><strong>Payments</strong></td>
                                                </tr>
                                                <?php if(!empty($receptionTransactionElements)){ ?>

                                                    <tr>
                                                        <td><strong>Type</strong></td>
                                                        <td><strong>Amount</strong></td>
                                                    </tr>
                                                    <?php foreach ($receptionTransactionElements as $payment){ ?>
                                                        <tr>
                                                        <td><?= $payment['type'] ?></td>
                                                            <td><?= $payment['amount'] ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php }else{ ?>
                                                    <tr>
                                                        <td colspan="2">NILL</td>
                                                    </tr>
                                                <?php } ?>

                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 text-center invoice-header">
                                            <hr style="border-top: 3px solid #000;"/>
                                            <small style="font-size: 10px;"><?= business_contact_numbers ?></small>
                                            <p style="font-size: 10px;"><?= business_contact_address ?></p>
                                            <p style="font-size: 10px;"><?= business_contact_email ?></p>
                                            <hr style="border-top: 3px solid #000;"/>
                                            <p style="font-size: 10px;"> Powered By: Processton.com</p>
                                            <p style="font-size: 10px;"> info@processton.com - +923061105155</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>  
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    
<!-- END CONTENT -->
<!-- BEGIN QUICK SIDEBAR -->
<script>
    jQuery(document).ready(function() {

        jQuery('.panel-company').hide();

        $('select[name="case_by"]').bind('change',function(){
            if(jQuery(this).val() == 'Panel'){
                jQuery('.panel-company').show();
            }else{
                jQuery('.panel-company').hide();
            }
        });

        var PricinfInNumbers = '#price-in-numbers'
        var PricinfInFigures = '#price-in-figures'

        jQuery(PricinfInNumbers).bind('change',function(){
            setPriceToBeBilled(jQuery(this).val());
        });


        function setPriceToBeBilled(price){
            jQuery(PricinfInNumbers).val(price);
            jQuery(PricinfInFigures).val(toWords(price)+' rs only /- ')
        }

         //
        // Amounnt Converter
        //

        var th = ['', 'thousand', 'million', 'billion', 'trillion'];

        var dg = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];

        var tn = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];

        var tw = ['twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        function toWords(s) {
            s = s.toString();
            s = s.replace(/[\, ]/g, '');
            if (s != parseFloat(s)) return 'not a number';
            var x = s.indexOf('.');
            if (x == -1) x = s.length;
            if (x > 15) return 'too big';
            var n = s.split('');
            var str = '';
            var sk = 0;
            for (var i = 0; i < x; i++) {
                if ((x - i) % 3 == 2) {
                    if (n[i] == '1') {
                        str += tn[Number(n[i + 1])] + ' ';
                        i++;
                        sk = 1;
                    } else if (n[i] != 0) {
                        str += tw[n[i] - 2] + ' ';
                        sk = 1;
                    }
                } else if (n[i] != 0) {
                    str += dg[n[i]] + ' ';
                    if ((x - i) % 3 == 0) str += 'hundred ';
                    sk = 1;
                }
                if ((x - i) % 3 == 1) {
                    if (sk) str += th[(x - i - 1) / 3] + ' ';
                    sk = 0;
                }
            }
            if (x != s.length) {
                var y = s.length;
                str += 'point ';
                for (var i = x + 1; i < y; i++) str += dg[n[i]] + ' ';
            }
            return str.replace(/\s+/g, ' ');

        }

    });
</script>

