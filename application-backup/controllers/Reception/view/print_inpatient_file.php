
<link href="<?php echo base_url('public/fonts/stylesheet.css') ?>" rel="stylesheet" type="text/css"/>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                
                    <div class="col-md-12 ">
                        <form method="GET">
                            <div class="row" id="payment_box_container">
                                <div class="col-md-12 m-t-4 m-b-4">
                                    <div class="col-md-11">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="name" class="form-control" name="receipt_id" value="<?= $recieptId == 0 ? '' : $recieptId ?>">
                                                <label for="form_control_1">Mr No</label>
                                                <span class="help-block">Please provide Mr No.</span>
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

