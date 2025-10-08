<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
?><!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-settings font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Report Filter </span>
                    </div>
                    
                    
                </div>
                <div class="portlet-body form">
                    <form method="GET">
                        <div class="form-group">
                            <div class="input-icon"  id="defaultrange_modal">
                                <label for="form_control_1">Report Duration</label>
                                <input type="text" class="form-control" name="date_range" required>
                                <input type="hidden" name="dtype" value="R">
                                <span class="help-block">you can change report duration from here.</span>
                                <i class="fa fa-bell-o"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="doc" id="doc" required>
                                        <option value=""></option>
                                            <?php foreach ($users as $user) { ?>
                                                <?php if ($user->is_opd_doctor == 1 || $user->is_dentist == 1) { ?>
                                                    <option <?= (array_key_exists('doc',$_GET) && $_GET['doc'] == $user->id) ? 'selected' : '' ?> value="<?= $user->id ?>"><?= $user->name ?></option>
                                                <?php } ?>    
                                            <?php } ?>    
                                    </select>
                                    <label for="form_control_1">Select Doctor</label>
                                    <i class="fab fa-servicestack"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group form-md-line-input ">
                                <div class="input-icon">
                                    <select class="form-control" name="service" id="service" required>
                                        <option value="">Select Service Type</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "opd" ) ? 'selected' : '' ?> value="opd">OPD</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "dental" ) ? 'selected' : '' ?> value="dental">DENTAL</option>
                                        <option <?= (array_key_exists('service',$_GET) && $_GET['service'] == "ultra" ) ? 'selected' : '' ?> value="ultra">ULTRASOUND</option>             
                                    </select>                                            
                                </div>
                            </div>
                        </div>
                        <div class="form-group has-success">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
        <?php
        if(!empty($opd_trans)) {
                $total_amount = 0;
                $nonpayed_amount = 0;
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i> DOCTOR INCOME STATEMENT </h3>
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-user fa-2x m-r-2"></i> <?= $selectedDoctor->name ?></h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-12">
                                        <form method="POST" action="<?= site_url($CREATE_OPD_VOUCHER) ?>">
                                        <input type="hidden" name="doctor_id" value="<?= $selectedDoctor->id ?>" />
                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Reception Transaction ID</th>
                                                        <th>Services Details</th>
                                                        <th>Amount</th>
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <!-- <tr>
                                                        <td colspan="3" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="5"><strong>Income</strong></td>
                                                    </tr>
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($opd_trans)) {
                                                        foreach ($opd_trans as $row) {
                                                                $isAlreadyPayed = $row['submitted_for_accounts'];
                                                                $total_amount += $row['amount_in_num'];
                                                                if($isAlreadyPayed == 0 ){
                                                                    $nonpayed_amount+= $row['amount_in_num'];
                                                                    
                                                                }
                                                                $counter++;
                                                                ?>
                                                                <tr class="<?= $isAlreadyPayed == 1 ? 'bg-danger' : ''  ?>">
                                                                    <?php if($isAlreadyPayed == 0){ ?>
                                                                    <td><input onchange="calculate_amount()" type="checkbox" name="payed_ids[]" value="<?= $row['id'] ?>" data-amount="<?= $row['amount_in_num'] ?>" <?= $isAlreadyPayed == 0 ? 'checked' : '' ?> /></td>
                                                                    <?php }else{ ?>
                                                                        <td></td>
                                                                    <?php } ?>
                                                                    <td><?= $row['reception_transaction_id'] ?> </td>
                                                                    <td><?= $row['service_name'] ?> </td>
                                                                    <td><?= $row['amount_in_num'] ?></td>
                                                                    <td><?= $nonpayed_amount ?></td>
                                                                </tr>
                                                            <?php 
                                                            
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="5">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                     
                                                    <tr class="bg-warning lighter">
                                                        <td colspan="5"><strong>Statement Summery</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Duration</strong></td>
                                                        <td><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total Revenue</strong></td>
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Sahreable Revenue</strong></td>
                                                        <td><?= $nonpayed_amount ?></td>
                                                    </tr>
                                                    
                                                    
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Doctor</strong></td>
                                                        <td><?= $selectedDoctor->name.' '. ($selectedDoctor->id .' | ' . $selectedDoctor->email) ?></td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"><strong>Percentage</strong></td>
                                                        <td><input type="number" class="form-control" max="100" min="0" name="percentage" id="percentage" value="<?= $selectedDoctor->opd_charges_amount ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount</strong></td>
                                                        <td><input type="number" name="voucher_amount" class="form-control" id="percentage_value" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount In words</strong></td>
                                                        <td><input type="text" name="voucher_amount_in_words" class="form-control" id="percentage_value_inwords" /></td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right">
                                                            <div class="form-group form-md-line-input has-success">
                                                                <label><input type="checkbox" name="do_agree" required id="can_click" /> I agree, Please generate doctor voucher.</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right"><button type="submit" class="btn btn-default"> Generate Voucher</button></td>
                                                    </tr>
                                                    
                                                    
                                                </tbody>
                                            </table>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>

    var percentage = document.getElementById('percentage');
    var percentageValue = document.getElementById('percentage_value');
    var percentageValueWords = document.getElementById('percentage_value_inwords');
    var selectedVouchers = $('input[name="payed_ids"]');

    function calculate_amount(){

        var Selectedvalues = 0;

        console.log(Selectedvalues);
        $("input[type='checkbox']").each(function() {
            if ($(this).is(":checked"))
            {
                Selectedvalues += parseFloat($(this).data('amount'));
            }
        });
        console.log(Selectedvalues);
        var perc = parseFloat(percentage.value == "" ? 0 : percentage.value);
        console.log(perc);
        var amount = parseFloat(Selectedvalues) * parseFloat(perc);
        console.log(amount);
        amount = amount / 100;
        console.log(amount);
        percentageValue.value = amount;
        percentageValueWords.value = toWords(amount) + 'PKR only/-';


    }


    $(function(){

        calculate_amount();
        $('#percentage, #percentage_value, input[name="payed_ids"]').bind('change',function () {

            calculate_amount();

        });
        

        $('#defaultrange_modal').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2020',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
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
</script>
            <?php
        }
                ?>
                <!-- dental -->
                <?php
        if(!empty($dental_trans)) {
                $total_amount = 0;
                $nonpayed_amount = 0;
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i> DOCTOR INCOME STATEMENT </h3>
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-user fa-2x m-r-2"></i> <?= $selectedDoctor->name ?></h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                 </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-12">
                                        <form method="POST" action="<?= site_url($CREATE_OPD_VOUCHER) ?>">
                                        <input type="hidden" name="doctor_id" value="<?= $selectedDoctor->id ?>" />
                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Reception Transaction ID</th>
                                                        <th>Services Details</th>
                                                        <th>Amount</th>
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <!-- <tr>
                                                        <td colspan="3" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="5"><strong>Income</strong></td>
                                                    </tr>
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($dental_trans)) {
                                                        foreach ($dental_trans as $row) {
                                                                $isAlreadyPayed = $row['submitted_for_accounts'];
                                                                $total_amount += $row['amount_in_num'];
                                                                if($isAlreadyPayed == 0 ){
                                                                    $nonpayed_amount+= $row['amount_in_num'];
                                                                    
                                                                }
                                                                $counter++;
                                                                ?>
                                                                <tr class="<?= $isAlreadyPayed == 1 ? 'bg-danger' : ''  ?>">
                                                                    <?php if($isAlreadyPayed == 0){ ?>
                                                                    <td><input onchange="calculate_amount()" type="checkbox" name="payed_ids[]" value="<?= $row['id'] ?>" data-amount="<?= $row['amount_in_num'] ?>" <?= $isAlreadyPayed == 0 ? 'checked' : '' ?> /></td>
                                                                    <?php }else{ ?>
                                                                        <td></td>
                                                                    <?php } ?>
                                                                    <td><?= $row['reception_transaction_id'] ?> </td>
                                                                    <td><?= $row['service_name'] ?> </td>
                                                                    <td><?= $row['amount_in_num'] ?></td>
                                                                    <td><?= $nonpayed_amount ?></td>
                                                                </tr>
                                                            <?php 
                                                            
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="5">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                     
                                                    <tr class="bg-warning lighter">
                                                        <td colspan="5"><strong>Statement Summery</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Duration</strong></td>
                                                        <td><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total Revenue</strong></td>
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Sahreable Revenue</strong></td>
                                                        <td><?= $nonpayed_amount ?></td>
                                                    </tr>
                                                    
                                                    
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Doctor</strong></td>
                                                        <td><?= $selectedDoctor->name.' '. ($selectedDoctor->id .' | ' . $selectedDoctor->email) ?></td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"><strong>Percentage</strong></td>
                                                        <td><input type="number" class="form-control" max="100" min="0" name="percentage" id="percentage" value="<?= $selectedDoctor->opd_charges_amount ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount</strong></td>
                                                        <td><input type="number" name="voucher_amount" class="form-control" id="percentage_value" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount In words</strong></td>
                                                        <td><input type="text" name="voucher_amount_in_words" class="form-control" id="percentage_value_inwords" /></td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right">
                                                            <div class="form-group form-md-line-input has-success">
                                                                <label><input type="checkbox" name="do_agree" required id="can_click" /> I agree, Please generate doctor voucher.</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right"><button type="submit" class="btn btn-default"> Generate Voucher</button></td>
                                                    </tr>
                                                    
                                                    
                                                </tbody>
                                            </table>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>

    var percentage = document.getElementById('percentage');
    var percentageValue = document.getElementById('percentage_value');
    var percentageValueWords = document.getElementById('percentage_value_inwords');
    var selectedVouchers = $('input[name="payed_ids"]');

    function calculate_amount(){

        var Selectedvalues = 0;

        console.log(Selectedvalues);
        $("input[type='checkbox']").each(function() {
            if ($(this).is(":checked"))
            {
                Selectedvalues += parseFloat($(this).data('amount'));
            }
        });
        console.log(Selectedvalues);
        var perc = parseFloat(percentage.value == "" ? 0 : percentage.value);
        console.log(perc);
        var amount = parseFloat(Selectedvalues) * parseFloat(perc);
        console.log(amount);
        amount = amount / 100;
        console.log(amount);
        percentageValue.value = amount;
        percentageValueWords.value = toWords(amount) + 'PKR only/-';


    }


    $(function(){

        calculate_amount();
        $('#percentage, #percentage_value, input[name="payed_ids"]').bind('change',function () {

            calculate_amount();

        });
        

        $('#defaultrange_modal').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2020',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
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
</script>
            <?php
        }
                ?>
    
                <!-- dental_end -->

                <!-- ultrasound start -->

                <?php
        if(!empty($ultra_trans)) {
                $total_amount = 0;
                $nonpayed_amount = 0;
                ?>
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-chess fa-2x m-r-2"></i> DOCTOR INCOME STATEMENT </h3>
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-user fa-2x m-r-2"></i> <?= $selectedDoctor->name ?></h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-12">
                                        <form method="POST" action="<?= site_url($CREATE_OPD_VOUCHER) ?>">
                                        <input type="hidden" name="doctor_id" value="<?= $selectedDoctor->id ?>" />
                                            <table id="example" class="table table-bordered table-responsive table-striped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Reception Transaction ID</th>
                                                        <th>Services Details</th>
                                                        <th>Amount</th>
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <!-- <tr>
                                                        <td colspan="3" class="text-right"><strong>Opening Balance</strong></td>
                                                        <td class="bg-danger lighter"></td>
                                                        <td>0</td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="5"><strong>Income</strong></td>
                                                    </tr>
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($ultra_trans)) {
                                                        foreach ($ultra_trans as $row) {
                                                                $isAlreadyPayed = $row['submitted_for_accounts'];
                                                                $total_amount += $row['amount_in_num'];
                                                                if($isAlreadyPayed == 0 ){
                                                                    $nonpayed_amount+= $row['amount_in_num'];
                                                                    
                                                                }
                                                                $counter++;
                                                                ?>
                                                                <tr class="<?= $isAlreadyPayed == 1 ? 'bg-danger' : ''  ?>">
                                                                    <?php if($isAlreadyPayed == 0){ ?>
                                                                    <td><input onchange="calculate_amount()" type="checkbox" name="payed_ids[]" value="<?= $row['id'] ?>" data-amount="<?= $row['amount_in_num'] ?>" <?= $isAlreadyPayed == 0 ? 'checked' : '' ?> /></td>
                                                                    <?php }else{ ?>
                                                                        <td></td>
                                                                    <?php } ?>
                                                                    <td><?= $row['reception_transaction_id'] ?> </td>
                                                                    <td><?= $row['service_name'] ?> </td>
                                                                    <td><?= $row['amount_in_num'] ?></td>
                                                                    <td><?= $nonpayed_amount ?></td>
                                                                </tr>
                                                            <?php 
                                                            
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="5">No Income Transaction during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                     
                                                    <tr class="bg-warning lighter">
                                                        <td colspan="5"><strong>Statement Summery</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Duration</strong></td>
                                                        <td><?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total Revenue</strong></td>
                                                        <td><?= $total_amount ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Shareable Revenue</strong></td>
                                                        <td><?= $nonpayed_amount ?></td>
                                                    </tr>
                                                    
                                                    
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Doctor</strong></td>
                                                        <td><?= $selectedDoctor->name.' '. ($selectedDoctor->id .' | ' . $selectedDoctor->email) ?></td>
                                                    </tr>

                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"><strong>Percentage</strong></td>
                                                        <td><input type="number" class="form-control" max="100" min="0" name="percentage" id="percentage" value="<?= $selectedDoctor->opd_charges_amount ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount</strong></td>
                                                        <td><input type="number" name="voucher_amount" class="form-control" id="percentage_value" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Share Amount In words</strong></td>
                                                        <td><input type="text" name="voucher_amount_in_words" class="form-control" id="percentage_value_inwords" /></td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right">
                                                            <div class="form-group form-md-line-input has-success">
                                                                <label><input type="checkbox" name="do_agree" required id="can_click" /> I agree, Please generate doctor voucher.</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        
                                                        <td colspan="4" class="text-right"></td>
                                                        <td class="text-right"><button type="submit" class="btn btn-default"> Generate Voucher</button></td>
                                                    </tr>
                                                    
                                                    
                                                </tbody>
                                            </table>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>

    var percentage = document.getElementById('percentage');
    var percentageValue = document.getElementById('percentage_value');
    var percentageValueWords = document.getElementById('percentage_value_inwords');
    var selectedVouchers = $('input[name="payed_ids"]');

    function calculate_amount(){

        var Selectedvalues = 0;

        console.log(Selectedvalues);
        $("input[type='checkbox']").each(function() {
            if ($(this).is(":checked"))
            {
                Selectedvalues += parseFloat($(this).data('amount'));
            }
        });
        console.log(Selectedvalues);
        var perc = parseFloat(percentage.value == "" ? 0 : percentage.value);
        console.log(perc);
        var amount = parseFloat(Selectedvalues) * parseFloat(perc);
        console.log(amount);
        amount = amount / 100;
        console.log(amount);
        percentageValue.value = amount;
        percentageValueWords.value = toWords(amount) + 'PKR only/-';


    }


    $(function(){

        calculate_amount();
        $('#percentage, #percentage_value, input[name="payed_ids"]').bind('change',function () {

            calculate_amount();

        });
        

        $('#defaultrange_modal').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2020',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
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
</script>
            <?php
        }
                ?>
    

                <!-- ultrasound end -->
    </div>
</div>
<script>

    
    $(function(){

        

        $('#defaultrange_modal').daterangepicker({
                opens: 'left',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2020',
                maxDate: '12/31/<?= date('Y') + 2 ?>',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    })
</script>
<style>
    .fa-2x {
        font-size: 1em !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>"/>
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>