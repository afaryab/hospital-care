<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $total_amount = 0;
                    $actual_amount = 0;
                    ?>
                    <div class="dontprint" id="firstSection">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption font-red-sunglo">
                                    <i class="icon-settings font-red-sunglo"></i>
                                    <span class="caption-subject bold uppercase"> Transactions</span>
                                </div>
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="row">
                                    <div class="col-xs-12 text-center invoice-header">
                                        <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                                        <hr style="border-top: 3px solid #000;"/>
                                        <h3>-COUNTER STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-xs-12 text-center invoice-header">
                                        <h3  style="background: #ccc !important;">-INCOME STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-xs-12">
                                        <table  class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>R #</th>
                                                    <th>Patient & Service</th>
                                                    <th>Amount</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($counter_transactions)) {
                                                    foreach ($counter_transactions as $row) {
                                                        $total_amount += $row['amount'];
                                                        if($row['income_or_expence'] == 'INCOME'){
                                                        ?>
                                                        <tr >
                                                            <td> <?= $row['id'] ?> </td>
                                                            <td style="padding:0;"  width="50%">
                                                                <table style="margin-bottom:0;" class="table table-bordered table-responsive table-striped" >
                                                                    
                                                                    <?php
                                                                    if(!empty($row['rows'])) {
                                                                        
                                                                        foreach ($row['rows'] as $elements) {
                                                                            ?>
                                                                            <tr >
                                                                                <td width="30%">
                                                                                    <?= $elements['type'] == 'INPT' ? $elements['pateint_name'].' (File # '.$elements['file_id'].', P # '.$elements['patient_id'].') ' : '' ?> 
                                                                                    <?= $elements['type'] == 'OPD' ? $elements['pateint_name'].' (P #'.$elements['patient_id'].')' : '' ?> 
                                                                                    <?= $elements['type'] == 'EMER' ? $elements['pateint_name'].' (P #'.$elements['patient_id'].')' : '' ?> 
                                                                                    <?= $elements['type'] == 'DENTAL' ? $elements['pateint_name'].' (P #'.$elements['patient_id'].')' : '' ?>
                                                                                    <?= $elements['type'] == 'ULTRA' ? $elements['pateint_name'].' (P #'.$elements['patient_id'].')' : '' ?>
                                                                                    <?= $elements['type'] == 'RECES' ? $elements['pateint_name'].' (P #'.$elements['patient_id'].')' : '' ?>
                                                                                </td>
                                                                                
                                                                                <td width="40%"><?= $row['income_or_expence'] == 'INCOME' ? $elements['service_name'] : ($elements['exp_array']['payment_reference'] == '' ? print_array($elements['exp_array'],1) : '') ?> (<?= $elements['type'] ?> Dpt)</td>
                                                                                <td><?= $elements['amount'] ?></td>
                                                                            </tr>
                                                                        <?php } ?> 
                                                                        
                                                                        
                                                                        <?php
                                                                    } ?>
                                                                </table>
                                                            </td>
                                                            <td  class="bg-success"><?= $row['amount'] ?></td>
                                                            <td  class="bg-success"><?= $total_amount ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                } ?>
                                                <tr>
                                                    <td colspan="3" class="text-right">Total Income : </td>
                                                    <td class="text-left"><?= $total_amount ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12 text-center invoice-header">
                                        <h3  style="background: #ccc !important;">-EXPENSE STATEMENT-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                    </div>
                                    <div class="col-xs-12">
                                        <table id="example"  class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                            <tr>
                                                <th>R #</th>
                                                <th>Epense Details</th>
                                                <th>Amount</th>
                                                <th>Balance</th>
                                            </tr>
                                            <?php
                                            foreach ($counter_transactions as $row) {
                                                if($row['income_or_expence'] == 'EXPENSE' || $row['income_or_expence'] == 'EXP'){ ?>
                                                
                                                    <tr >
                                                        <td> <?= $row['id'] ?> </td>
                                                        <td style="padding:0;"  width="50%">
                                                        <table style="margin-bottom:0;" class="table table-bordered table-responsive table-striped" >
                                                        <?php
                                                            if(!empty($row['rows'])) {
                                                                foreach ($row['rows'] as $elements) {
                                                                    if($elements['type'] == 'VOUCHER-PAY'){ ?>
                                                                        <tr >
                                                                            <td>Voucher #<?= $elements['exp_array']['voucher_id'] ?> Payment comments "<?= $elements['exp_array']['payment_reference'] ?>"</td>
                                                                        </tr>
                                                                    <?php }else{ ?>
                                                                            <tr >
                                                                                <td><?= $elements['exp_array']['payment_reference'] ?></td>
                                                                            </tr>
                                                                    <?php }
                                                                }
                                                            }
                                                        ?></table></td>
                                                        <td  class="bg-warning"><?= $row['amount'] ?></td>
                                                        <td  class="bg-warning"><?= $total_amount ?></td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12 col-md-4 col-md-offset-8">
                                        <h3  style="background: #ccc !important;" class="text-center">-STATEMENT SUMMERY-</h3>
                                        <hr style="border-top: 3px solid #000;"/>
                                        <table class="table table-bordered">
                                            <tr class="text-center" style="border-top: 3px solid #000;">
                                                <td><strong class="text-warning">Opening Cash: </strong></td><td><?= $counter['opening_amount'] ?></td>
                                            </tr>
                                            <tr class="text-center">
                                                <td><strong class="text-success">Closing Cash: </strong></td><td><?= $counter['closing_amount_cash'] ?></td>
                                            </tr>
                                            
                                            <tr class="text-center">
                                                <td><strong class="text-danger">Expense Payed: </strong></td><td><?= $counter['expense_payed'] ?></td>
                                            </tr>
                                            <tr class="text-center">
                                                <td><strong class="text-success">Total: </strong></td><td><?= $counter['closing_amount'] ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-12 m-t-4 m-b-4">
                                            <div class="col-md-11">
                                                <div class="form-group form-md-line-input has-success">
                                                <label><input type="checkbox" name="do_agree" required id="can_click" /> I agree, Please close my counter and logmeout.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-success btn-block">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                
                        </div>
                    </div>
                    <div class="invoice in_sm printfull" style="margin: 0 auto;" id="secondSection">
                        <div class="portlet light bordered row">
                            <div class="col-xs-12 text-center invoice-header">
                                <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                                <hr style="border-top: 3px solid #000;"/>
                                <h3>-COUNTER STATEMENT-</h3>
                                <hr style="border-top: 3px solid #000;"/>
                            </div>
                            <div class="col-xs-12 invoice-payment">

                                <h3 class="text-center"><?= str_pad($counter['id'], 11, '0', STR_PAD_LEFT); ?></h3>

                                
                                <table class="table">
                                    <tr>
                                        <td class="text-center" colspan="2"><strong>Transactions</strong></td>
                                    </tr>
                                    <?php if(!empty($counter_transactions)){ ?>

                                        <tr>
                                            <td><strong>Number</strong></td>
                                            <td><strong>Amount</strong></td>
                                        </tr>
                                        <?php
                                        foreach ($counter_transactions as $payment){
                                            
                                            ?>
                                            <tr>
                                                <td><?= $payment['id'] ?></td>
                                                <td><?= $payment['amount'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <tr>
                                            <td colspan="2">NILL</td>
                                        </tr>
                                    <?php } ?>
                                    
                                    <tr style="border-top: 3px solid #000;">
                                        <td><strong class="text-warning">Opening Cash: </strong></td><td><?= $counter['opening_amount'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-success">Opening Cash: </strong></td><td><?= $counter['closing_amount_cash'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-success">Opening Card: </strong></td><td><?= $counter['closing_amount_card'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-success">Closing Cheque: </strong></td><td><?= $counter['closing_amount_atm'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-danger">Expense Payed: </strong></td><td><?= $counter['expense_payed'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-success">Total: </strong></td><td><?= $counter['closing_amount'] ?></td>
                                    </tr>

                                </table>
                            </div>
                            <div class="col-xs-12 text-center invoice-header">
                                <hr style="border-top: 3px solid #000;"/>
                                <small style="font-size: 10px;"><?= business_contact_numbers ?></small>
                                <p style="font-size: 10px;"><?= business_contact_address ?></p>
                                <p style="font-size: 10px;"><?= business_contact_email ?></p>
                                <hr style="border-top: 3px solid #000;"/>
                                <p style="font-size: 10px;"> Powered By: Processton.com</p>
                                <p style="font-size: 10px;"> Ahmad Faryab Kokab - +923061105155</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>

    <script>
        jQuery(function(){
            $('#example').DataTable({
                "order": [[ 0, "desc" ]]
            });
        })
    </script>
    <style>
    #firstSection{
        width: calc(100% - 280px) !important;
        display: inline-block;
        vertical-align: top;
    }
    #secondSection{
        width: 270px;
        display: inline-block;
        vertical-align: top;
        padding: 0px 15px 15px 15px;
    }
    .page-content{
        padding:15px;
    }
    </style>
