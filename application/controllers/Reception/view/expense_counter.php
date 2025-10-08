
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/dropzone/css/dropzone.css') ?>"/>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12 " id="makeMeSlideAlittleForPatientTokenPrint">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> Non Voucher Expense Payment</span>
                        </div>
                    </div>
                    <div id="treatments-table-div" class="portlet-body form">
                        <form method="POST">
                            <div class="row" id="payment_box_container">
                                <div class="col-md-12 m-t-4 m-b-4">
                                    <div class="col-md-12">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" name="payment_reference">
                                                <label for="form_control_1">Purpose of payment</label>
                                                <span class="help-block">Please provide purpose of payment.</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="number" class="form-control" name="amount_received_num" required id="price-in-numbers" onchange="calculateBill()">
                                                <label for="form_control_1">Amount of Payment</label>
                                                <span class="help-block">Amount of Payment.</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" name="amount_received_words" required id="price-in-figures"  onchange="calculateBill()">
                                                <label for="form_control_1">Amount of Payment in words</label>
                                                <span class="help-block">Amount of Payment in words.</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                
                                    <div class="form-actions noborder text-right">
                                        <?php if(!empty($closingArray)){ ?>
                                            <button type="submit" class="btn btn-success d-inline" id="payment_box_container_button">Pay</button>
                                        <?php }else{ ?>
                                            <button type="button" class="btn btn-danger">You cannot perform any transaction</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                    </form>
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
        // if(billedAmount > <?= $counter['closing_amount_cash'] ?>){
        //     console.log( billedAmount)
        //     errorMsg('You cannot pay this voucher dont have enough cash in counter');    
        //     paymentBoxContainerButton.css({ display: 'none' });
        // }else{
            paymentBoxContainerButton.css({ display: 'inline-block' });
        // }

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