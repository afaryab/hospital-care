
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
                            <span class="caption-subject bold uppercase"> Inpatient Payments Counter</span>
                        </div>
                    </div>
                    <div id="treatments-table-div" class="portlet-body form">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 m-t-4 m-b-4">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-fingerprint"></i></span>
                                            <input id="medical_record" name="medical_record" class="form-control" placeholder="MR Number" type="text" required>
                                            <span class="input-group-addon bg-danger cursor-pointer" onclick="clearPatient()"><i class="fas fa-broom text-white"></i></span>
                                        </div>
                                        <p class="help-block">Please provide medical record number</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-fingerprint"></i></span>
                                            <input id="patient_id" name="patient_id" class="form-control" placeholder="Patient Token/ID Number" type="text" disabled>
                                        </div>
                                        <p class="help-block">Please provide patient id number</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-signature"></i></span>
                                            <input id="patient_name" name="patient_name" required class="form-control" placeholder="Patient Name" type="text" disabled>
                                        </div>
                                        <p class="help-block">Please provide patient Name</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                                            <input id="patient_contact" name="patient_contact" class="form-control" placeholder="Patient Contact" type="text" disabled>
                                        </div>
                                        <p class="help-block">Please provide patient contact</p>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-id-card-alt"></i></span>
                                            <input id="patient_cnic" name="patient_cnic" class="form-control" placeholder="Patient CNIC Number" type="text" disabled>
                                        </div>
                                        <p class="help-block">Please patient cnic number</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-id-card-alt"></i></span>
                                            <input id="file_charges" name="file_charges" class="form-control" placeholder="File Charges" type="number" disabled>
                                        </div>
                                        <p class="help-block">File Charges</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-id-card-alt"></i></span>
                                            <input id="file_charges_paid" name="file_charges_paid" class="form-control" placeholder="File Charges Paid" type="number" disabled>
                                        </div>
                                        <p class="help-block">File Charges Paid</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-id-card-alt"></i></span>
                                            <input id="file_charges_remaining" name="file_charges_remaining" class="form-control" placeholder="File Charges Remaining" type="number" disabled>
                                        </div>
                                        <p class="help-block">File Charges Remaining</p>
                                    </div>
                                </div>
                                <div class="col-md-6 m-t-4 m-b-4">
                                    <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                                        <thead>
                                        <tr>
                                            <th><i class="fas fa-fingerprint"></i></th>
                                            <th><i class="fas fa-signature"></i> Name</th>
                                            <th><i class="fas fa-phone"></i> Service</th>
                                            <th><i class="fas fa-id-card-alt"></i> File Number</th>
                                            <th class="text-center"><i class="fas fa-bolt"></i></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Service</th>
                                            <th>File Number</th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" id="payment_box_container">
                                <div class="col-md-12 m-t-4 m-b-4">
                                    
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
                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input has-success">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" name="payment_reference">
                                                <label for="form_control_1">Purpose</label>
                                                <span class="help-block">Enter purpose</span>
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="form-actions noborder text-right">
                                        <?php if(!empty($closingArray)){ ?>
                                            <button type="submit" class="btn btn-success" id="payment_box_container_button">Pay</button>
                                        <?php }else{ ?>
                                            <button type="button" class="btn btn-danger">You cannot perform any transaction</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="discharge_container">
                                <div class="col-md-12 m-t-4 m-b-4">
                                    <div class="col-md-6">
                                        <button class="btn btn-success">Discharge Patient</button>
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

    var serivcesSelector = $('#selected_services');
    var paymentBoxContainer = $('#payment_box_container');
    var paymentBoxContainerButton = $('#payment_box_container_button');
    var dischargeContainer = $('#discharge_container');

    var PricinfInNumbers = '#price-in-numbers'
    var PricinfInFigures = '#price-in-figures'
    var CurstomerPaidAmount = '#customer_payed_amount'
    var ChangeAmount = '#change_amount'
    
    var MedicalRecord = $('#medical_record');
    var PatientIdElement = $('#patient_id');
    var PatientNameElement = $('#patient_name');
    var PatientContactElement = $('#patient_contact');
    var PatientCnicElement = $('#patient_cnic');
    var PatientSelectedService = $('#selected_services');

    var fileCharges = $('#file_charges');
    var fileChargesPaid = $('#file_charges_paid');
    var fileChargesRemaining = $('#file_charges_remaining');

    var PosServices = [];
    
    
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
        //     errorMsg('You cannot pay this voucher dont have enough cash in counter');    
        //     paymentBoxContainerButton.css({ display: 'none' });
        // }else{
            paymentBoxContainerButton.css({ display: 'block' });
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
    function selectPatient (mr_number,patient_id, patient_name, patient_contact, patient_cnic, selected_service,file_charges,cile_charges_paid){
        console.log(selected_service);
        MedicalRecord.val(mr_number);
        PatientIdElement.val(patient_id);
        PatientNameElement.val(patient_name);
        PatientContactElement.val(patient_contact);
        PatientCnicElement.val(patient_cnic);
        PatientSelectedService.val(selected_service);

        fileCharges.val(file_charges);
        fileChargesPaid.val(cile_charges_paid);
        fileChargesRemaining.val(file_charges - cile_charges_paid);

        // $(PricinfInNumbers).attr({
        //     "max" : file_charges - cile_charges_paid,        // substitute your own
        //     "min" : 1          // values (or variables) here
        // });
        // if((file_charges - cile_charges_paid) <= 0){
        //     paymentBoxContainer.css({ display: 'none' });
        //     dischargeContainer.css({ display: 'block' });
        // }else{
            paymentBoxContainer.css({ display: 'block' });
        //     dischargeContainer.css({ display: 'none' });
        // }
        
    }
    function clearPatient (){
        MedicalRecord.val("");
        PatientIdElement.val("");
        PatientNameElement.val("");
        PatientContactElement.val("");
        PatientCnicElement.val("");
        PatientSelectedService.val("");
    }
    function calculateTax(cost, vat = 20){
        return (cost * vat / 100);
    }
    

    jQuery(document).ready(function() {

        paymentBoxContainer.css({ display: 'none' });
        dischargeContainer.css({ display: 'none' });

        PatientCnicElement.mask("00000r0000000r0", {
            translation: {
                'r': {
                    pattern: /[\/]/,
                    fallback: '-'
                },
                placeholder: "00000-0000000-0"
            }
        });
        PatientContactElement.mask("0000r0000000", {
            translation: {
                'r': {
                    pattern: /[\/]/,
                    fallback: '-'
                },
                placeholder: "0000-0000000"
            }
        });

        
        MedicalRecord.on('keyup change', function () {
            var i = 0;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })
        PatientIdElement.on('keyup change', function () {
            var i = 7;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })

        PatientNameElement.on('keyup change', function () {
            var i = 1;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        });

        PatientContactElement.on('keyup change', function () {
            var i = 5;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })
        PatientCnicElement.on('keyup change', function () {
            var i = 6;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })

        var table = $('#example').DataTable( {
            "processing": true,
            "serverSide": true,
            "pageLength": 3,
            "ajax": "<?= site_url($EXPENSE_FILE_SEARCH) ?>",
            "initComplete": function(){
                $('a[title]').tooltip();
            },
            "columnDefs": [
               { "targets": [4], orderable: false }
            ],
            "order": [
                [ 0, "desc" ],
                [ 1, "desc" ]
            ]
        } );

        

        



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