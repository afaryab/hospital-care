<?php
$i = 0;
$count = 0;
$j = 0;
$count2 = 0;
$index = 0;
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
							<div class="caption font-red-sunglo">
								<i class="fab fa-superpowers font-red-sunglo"></i>
								<span class="caption-subject bold uppercase"> New Expense Voucher</span>
							</div>
						</div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="row">
                                    
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <select class="form-control " name="categories_id" id="categories_id" required>
                                                        <option value=""></option>
                                                            <?php foreach ($categories as $category) {
                                                            if($category['is_deleted'] != 1){
                                                                if($category['type'] != 'INPT'){ ?>
                                                                    <option <?php $data_obj[] = $category ?> value="<?=$category['id'] ?>" ><?= str_pad($category['id'], 3, "0", STR_PAD_LEFT); ?> - <?= $category['name'] ?></option>
                                                                   
                                                        <?php 
                                                        $index = $index + 1;
                                                                }
                                                            }
                                                    } ?>
                                                    </select>
                                                    <label for="form_control_1">Expense Category.</label>
                                                    <span class="help-block">Category of Expense</span>
                                                    <i class="fab fa-servicestack"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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
                                                        <?php foreach ($users as $user) {
                                                            if($user->is_doctor != 1){ ?>
                                                                <option id="select_user<?= $i ?>" value="<?= $user->id ?>"><?= $user->name ?></option>
                                                        <?php 
                                                         $i = $i+1;
                                                         $count = $count+1;
                                                        }
                                                            if($user->is_doctor == 1 && $user->name != 'M.O' && $user->name != 'W.M.O' && $user->name != 'E.O'){ ?>
                                                                <option id="select_doc<?= $j ?>" value="<?= $user->id ?>"><?= $user->name ?></option>
                                                        <?php 
                                                            $j = $j+1;
                                                            $count2 = $count2+1;    
                                                            }
                                                        } ?>
                                                        <option id="select_other" value="others">Other</option>
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
                                        <div class="col-md-6" id="payed_to_doctor_container">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" name="inpt_id_selection" id="inpt_id_selection">
                                                    <label for="form_control_1">Inpatient File Number </label>
                                                    <span class="help-block">Please Provide Inpatient File Number for Reference.</span>
                                                    <i class="fas fa-comments"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="comments_section">
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
                                    <div class="form-actions noborder text-right">
                                        <button type="submit" class="btn blue"><i class="fab fa-amazon-pay fa-3x"></i> Make Voucher</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <!-- END SAMPLE FORM PORTLET-->

                </div>
            </div>
        </div>
    </div>
</div>
    <script type="application/javascript">
        jQuery(document).ready(function() {
            $('#payed_to_doctor_container').hide();
            $('#inpt_id_selection').hide();
            $('#comments_section').hide();
            $('#comments_section').removeAttr('required');
            $('#payed_to_other_con').hide();
            $('#payed_to_other').removeAttr('required');

            var cont = <?php echo $count; ?>;
            var cont2 = <?php echo $count2; ?>;
            var index = <?php echo $index; ?>;

            $('#categories_id').on('change',function () {
                //console.log(e);
                var abc = $(this).val();
                console.log(abc);
                var json = '<?php echo json_encode($data_obj); ?>';
                var res = JSON.parse(json);
                //var ab = $(this).val(find(abc));
                //console.log(res['abc']);
                //console.log(res[abc-1]);
                // if(res.id == abc){
                //     console.log(res);
                // }
                for(var i = 0;i<index;i++ )
                {
                    if(res[i].id == abc)
                    {
                        var arr = res[i];
                        //console.log(res[i]);
                    }
                }
                //console.log(res.id);
                //var arr = res[index-1];
                console.log(arr);
                if(arr.add_comments == 1){
                    $('#comments_section').show();
                    $('#comments_section').attr("required","required");
                }else{
                    $('#comments_section').hide();
                }

                for(i=0;i <= cont;i++){
                    // if(!$('.Table1'+i).find('tbody tr').length){ 
                    //     $('#myTable1div'+i).hide();
                    // }
                    if(arr.pay_users == 1){
                        $('#select_user'+i).show();
                    }else{
                        $('#select_user'+i).hide();
                    }
                }
                for(j=0;j <= cont2;j++){
                    if(arr.pay_doc == 1){
                        $('#select_doc'+j).show();
                    }else{
                        $('#select_doc'+j).hide();
                    }

                }

                // if(arr.pay_users == 1){
                //     $('#select_user').show();
                // }else{
                //     $('#select_user').hide();
                // }

                // if(arr.pay_doc == 1){
                //     $('#select_doc').show();
                // }else{
                //     $('#select_doc').hide();
                // }

                if(arr.pay_others == 1){
                    $('#select_other').show();
                }else{
                    $('#select_other').hide();
                }
            })
            
            $('#payed_to').on('change',function () {
                if($(this).val() == 'others'){
                    $('#payed_to_other_con').show();
                    $('#payed_to_other').focus();
                    $('#payed_to_doctor_container').hide();
                    $('#inpt_id_selection').hide()
                    $('#payed_to_other').attr("required","required");
                }else{
                    $('#payed_to_other_con').hide();
                    $('#payed_to_other').removeAttr('required');
                    $('#payed_to_doctor_container').hide();
                    $('#inpt_id_selection').hide()
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

        function isInptDoctor(){
            alert("Doctotr");
        }


    </script>