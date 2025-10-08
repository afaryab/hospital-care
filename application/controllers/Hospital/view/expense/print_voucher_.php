
<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="invoice in_sm">
                <div class="row">
                    <div class="col-xs-12 text-center invoice-header">
                        <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                        <hr style="border-top: 3px solid #000;"/>
                        <h3>-VOUCHER PAYMENT-</h3>
                        <hr style="border-top: 3px solid #000;"/>
                    </div>
                    <div class="col-xs-12 invoice-payment">
                        <p style="font-family: barcode_fontregular;font-size: 130px;height: 150px;"><?= str_pad($transaction['id'], 11, '0', STR_PAD_LEFT); ?></p>

                        <hr style="border-top: 3px solid #000;"/>
                        <table class="table">
                            <tr>
                                <td><strong>Voucher #: </strong></td>
                                <td><?= $transaction['voucher_id'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Expense Note: </strong></td>
                                <td><?= str_pad($transaction['payment_reference'], 11, '0', STR_PAD_LEFT) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dated: </strong></td>
                                <td><?= date('d-m-Y h:i a',strtotime($transaction['created_on'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid: </strong></td>
                                <td><?= $transaction['amount_received_num'] ?> PKR</td>
                            </tr>
                            <tr>
                                <td><strong>Amount in words: </strong></td>
                                <td><?= $transaction['amount_received_words'] ?></td>
                            </tr>
<tr>
<td colspan="2">
<hr style="border-top: 3px solid #000;"/>
</td>
</tr>
<tr>
                                <td><strong>Paid Through</strong></td>
                                <td><?= $transaction['payment_type'] == 'CHECK' ? 'CHEQUE' : $transaction['payment_type'] ?></td>
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
            <div class="col-xs-12 text-left hidden-print">
                <a class="btn btn-success" onclick="javascript:window.print();">Print Token</a>
            </div>
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

