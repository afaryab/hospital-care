
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 " id="payment_form">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
							<div class="caption font-red-sunglo">
								<i class="fab fa-superpowers font-red-sunglo"></i>
								<span class="caption-subject bold uppercase"> Pay Expense</span>
							</div>
						</div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="number" class="form-control" name="voucher_no" required id="voucher_number">
                                                    <label for="form_control_1">Enter Voucher.</label>
                                                    <span class="help-block">Enter Voucher Number To be Paid.</span>
                                                    <i class="fa fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="categories_id" id="categories_id" required disabled="disabled">
                                                        <option value=""></option>
                                                        <?php foreach ($categories as $category) { ?>
                                                            <option value="<?= $category['id'] ?>"><?= str_pad($category['id'], 3, "0", STR_PAD_LEFT); ?> - <?= $category['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <label for="form_control_1">Expense Category.</label>
                                                    <span class="help-block">Category of Expense</span>
                                                    <i class="fab fa-servicestack"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="number" class="form-control" name="amount_received_num" required id="price-in-numbers">
                                                    <label for="form_control_1">Enter Amount.</label>
                                                    <span class="help-block">Enter Amount To be Paid.</span>
                                                    <i class="fa fa-dollar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" name="amount_received_words" required id="price-in-figures">
                                                    <label for="form_control_1">Enter Amount in words</label>
                                                    <span class="help-block">Enter Amount To be Paid in words.</span>
                                                    <i class="fab fa-autoprefixer"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control" name="payed_to" id="payed_to" required>
                                                        <option value=""></option>
                                                        <?php foreach ($users as $user) { ?>
                                                            <option value="<?= $user->id ?>"><?= $user->name ?></option>
                                                        <?php } ?>
                                                        <option value="others">Other</option>
                                                    </select>
                                                    <label for="form_control_1">Payed To <small>(Only if payed to employee)</small>.</label>
                                                    <i class="fab fa-servicestack"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="payed_to_other_con">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" name="payed_to_other" id="payed_to_other">
                                                    <label for="form_control_1">Other Name (Payed to) </label>
                                                    <span class="help-block">Please Provide name of other person.</span>
                                                    <i class="fas fa-comments"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" name="payment_reference">
                                                    <label for="form_control_1">Comments</label>
                                                    <span class="help-block">Please Provide possible details for accounts department.</span>
                                                    <i class="fas fa-comments"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <select class="form-control" name="payment_type" required>
                                                    <option value="">Select</option>
                                                    <option value="CARD">CARD</option>
                                                    <option value="CHECK">CHECK</option>
                                                    <option value="CASH">CASH</option>
                                                    <option value="CREDITCARD">CARD</option>
                                                </select>
                                                <label for="form_control_1">Please Select Payment Type.</label>
                                                <span class="help-block">Payment in form of?</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder text-right" id="voucher_payment">
                                        <button type="submit" class="btn blue"><i class="fab fa-amazon-pay fa-3x"></i> Make Payment</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <!-- END SAMPLE FORM PORTLET-->

                </div>
            </div>
            <div class="col-sm-4" id="voucher_info">

            </div>
        </div>
    </div>
</div>
    <script type="application/javascript">
        jQuery(document).ready(function() {

            $('#voucher_info').hide();
            $('#categories_id').attr('disabled','disabled');
            $('#payed_to').attr('disabled','disabled');
            $('#payed_to_other').attr('disabled','disabled');

            $('#voucher_number').on('change',function () {
                var URL_TOGET = "<?= site_url($HOSPITAL_EXPENSE_VOUCHER_JSON) ?>/"+$(this).val();
                $.get(URL_TOGET, function(resp, status){
                    var JSONRES = $.parseJSON(resp);
                    console.log(JSONRES);
                    if(JSONRES.status == 1) {
                        var data = JSONRES.data;
                        var html = JSONRES.html;
                        console.log(data);
                        $('#payment_form').removeClass('col-md-12');
                        $('#payment_form').addClass('col-md-8');
                        $('#voucher_info').show();
                        $('#voucher_info').html(html);
                        $('#categories_id').removeAttr('disabled');
                        $('#categories_id').val(data.exp_category_id);
                        $('#categories_id').attr('disabled','disabled');

                        $('#price-in-numbers').removeAttr('disabled');
                        $('#price-in-numbers').val(data.exp_amount_numbers)
                        $('#price-in-numbers').change();

                        if(data.payed_to_employee == 1) {
                            $('#payed_to').removeAttr('disabled');
                            $('#payed_to').val(data.employee_id)
                            $('#payed_to').attr('disabled','disabled');
                        }else{
                            $('#payed_to').removeAttr('disabled');
                            $('#payed_to').val("others");
                            $('#payed_to').attr('disabled','disabled');

                            $('#payed_to_other').removeAttr('disabled');
                            $('#payed_to_other').val(data.payed_to_others);
                            $('#payed_to_other').attr('disabled','disabled');
                        }

                        if(data.exp_amount_numbers > "<?= $counter['closing_amount_cash'] ?>"){
                            errorMsg('You cannot pay this voucher dont have enough cash in counter');    
                            $('#voucher_payment').css({ opacity: 0});
                        }else{
                            $('#voucher_payment').css({ opacity: 1});
                        }
                    }else{
                        $('#voucher_info').hide();
                        errorMsg('Expense Voucher Number is not valid');
                    }
                });
            })

            $('#payed_to').on('change',function () {
                if($(this).val() == 'others'){
                    $('#payed_to_other_con').show();
                    $('#payed_to_other').focus();
                    $('#payed_to_other').attr("required","required");
                }else{
                    $('#payed_to_other_con').hide();
                    $('#payed_to_other').removeAttr('required');
                }
            })


            var PricinfInNumbers = '#price-in-numbers'
            var PricinfInFigures = '#price-in-figures'

            var TotalAmount = 0;
            $('#price-in-numbers-xray, #price-in-numbers-consultation, #price-in-numbers-implant, #price-in-numbers-others, #price-in-numbers-prev, #price-in-numbers-ortho').on('change',function(){
                var XrayAmount = $('#price-in-numbers-xray').val();
                var ConsultationAmount = $('#price-in-numbers-consultation').val();
                var ImplantAmount = $('#price-in-numbers-implant').val();
                var OthersAmount = $('#price-in-numbers-others').val();
                var PrevAmount = $('#price-in-numbers-prev').val();
                var OrthoPayment = $('#price-in-numbers-ortho').val();
                console.log(XrayAmount);
                TotalAmount = parseInt(XrayAmount) + parseInt(ConsultationAmount) + parseInt(ImplantAmount) + parseInt(OthersAmount) + parseInt(PrevAmount) + parseInt(OrthoPayment);
                console.log(TotalAmount);
                setPriceToBeBilled(TotalAmount);
            });






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