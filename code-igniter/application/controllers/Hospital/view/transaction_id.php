
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/dropzone/css/dropzone.css') ?>"/>
<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
            <div class="row">
            <?php if($recieptId == 0){  ?>
                <div class="col-md-12 ">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Edit </span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            
                                <form method="GET">
                                    <div class="form-group form-md-line-input">
                                        <input type="name" class="form-control" name="receipt_id" >
                                        <label for="form_control_1">Receipt ID</label>
                                        <span class="help-block">Please provide reciept id.</span>
                                        <i class="fa fa-bell-o"></i>

                                    </div>
                                    <div class="form-group has-success">
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            
                        </div>    


                        
                    </div>
                </div>
                <?php }else{ ?>
                    <div class="col-md-12 text-center invoice-header">
                        <p style="font-size:18px;color:#E26A6A;text-align: left;"><b>&nbsp;Transactions</b></p>   
                        </br>      
                    </div>
                                    
                    <div class="col-md-12">
                        <table  class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($receptionTransactionElements as $ele) { 
                                    if($ele['type'] == 'OPD') {
                                ?>
                                    <tr>
                                        <td>OPD</td>
                                        <?php if($ele['doctor_id'] != NULL){ ?>
                                            <td> <?= $user->name ?> </td>
                                        <?php }else{ ?>
                                            <td> <?= $opd_services['name'] ?> </td>
                                        <?php } ?>
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td>
                                    </tr>
                                    <?php } elseif($ele['type'] == 'ULTRA') {?>
                                    <tr>
                                        <td>ULTRASOUND</td>
                                        <?php if($ele['doctor_id'] != NULL){ ?>
                                            <td> <?= $user->name ?> </td>
                                        <?php }else{ ?>
                                            <td> <?= $ultrasound_services['name'] ?> </td>
                                        <?php } ?>
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td>
                                    <tr>    
                                    <?php } elseif($ele['type'] == 'DENTAL') {?>
                                    <tr>
                                        <td>DENTAL</td>
                                        <?php if($ele['doctor_id'] != NULL){ ?>
                                            <td> <?= $user->name ?> </td>
                                        <?php }else{ ?>
                                            <td> <?= $dental_services['name'] ?> </td>
                                        <?php } ?>
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td>    
                                    </tr>
                                    <?php } elseif($ele['type'] == 'EMER') {?>
                                    <tr>
                                        <td>EMERGENCY</td>
                                        <td> <?= $emergency_services['name'] ?> </td> 
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td> 
                                    </tr>
                                    <?php } elseif($ele['type'] == 'INPT') {?>
                                    <tr>
                                        <td>INPATIENT</td>
                                        <td> <?= $inpd_services['name'] ?> </td> 
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td>   
                                    </tr>
                                    <?php } elseif($ele['type'] == 'RECES') {?>
                                    <tr>
                                        <td>RECESTATION</td>
                                        <?php if($ele['doctor_id'] != NULL){ ?>
                                            <td> <?= $user->name ?> </td>
                                        <?php }else{ ?>
                                            <td> <?= $recestation_services['name'] ?> </td>
                                        <?php } ?>
                                        <td> <?= $ele['amount'] ?></td>
                                        <td><?php ?>
                                            <a href="<?= site_url($PAY_EDIT).$ele['id'] ?>" class="btn btn-success pull-right">Edit Payment </a>
                                        <?php ?></td>    
                                    </tr>
                                    <?php } ?>
                                <?php } ?>    
                            </tbody>   
                        </table>    
                <?php } ?>
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
<script type="application/javascript">

    var paymentBoxContainerButton = $('#payment_box_container_button');

    var PricinfInNumbers = '#price-in-numbers'
    var PricinfInFigures = '#price-in-figures'
    var CurstomerPaidAmount = '#customer_payed_amount'
    var ChangeAmount = '#change_amount'
    
    
    function setPriceToBeBilled(price){
        jQuery(PricinfInNumbers).val(price);
        jQuery(PricinfInFigures).val(toWords(price)+' rs only /- ')
        
    }
    function calculateBill(){
        var billedAmount = jQuery(PricinfInNumbers).val();
        var customerPaid = jQuery(CurstomerPaidAmount).val();
        console.log(customerPaid);
        var x = parseInt(customerPaid) - parseInt(billedAmount);
        if(parseInt(customerPaid) > 0 && x >= 0){
            jQuery(ChangeAmount).val(parseInt(customerPaid) - parseInt(billedAmount));
        }
        if(billedAmount > <?= $counter['closing_amount_cash'] ?>){
            console.log( billedAmount)
            errorMsg('You cannot pay this voucher dont have enough cash in counter');    
            paymentBoxContainerButton.css({ display: 'none' });
        }else{
            paymentBoxContainerButton.css({ display: 'inline-block' });
        }

    }

    function calculateBillSpecial(){
        var customerPaid = jQuery(CurstomerPaidAmount).val();
        var billedAmount = jQuery(PricinfInNumbers).val();
        if(billedAmount == "" || billedAmount == 0){
            jQuery(PricinfInNumbers).val(customerPaid);
            jQuery(PricinfInFigures).val(toWords(customerPaid)+' rs only /- ')
        }
        calculateBill();

    }

    function toWords(s) {

        var th = ['', 'thousand', 'million', 'billion', 'trillion'];

        var dg = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];

        var tn = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];

        var tw = ['twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

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
    
    function calculateTax(cost, vat = 20){
        return (cost * vat / 100);
    }
    

    jQuery(document).ready(function() {
        paymentBoxContainerButton.css({ display: 'none' });
        jQuery(PricinfInNumbers).bind('change',function(){
            setPriceToBeBilled(jQuery(this).val());
        });

    });
</script>
<style>
.dataTables_length{
    display: none;
}
#example_filter{
    display: none;
}
</style>
<script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/plugins/jquery-mask/dist/jquery.mask.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/datatables/media/css/jquery.dataTables_themeroller.css') ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/jquery-mask/dist/jquery.mask.min.js') ?>"/>