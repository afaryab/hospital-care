
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
                            <span class="caption-subject bold uppercase"> Recestation Counter</span>
                        </div>
                    </div>
                    <div id="treatments-table-div" class="portlet-body form">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 m-t-4 m-b-4">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-fingerprint"></i></span>
                                            <input id="patient_id" name="patient_id" class="form-control" placeholder="Patient Token/ID Number" type="text">
                                            <span class="input-group-addon bg-danger cursor-pointer" onclick="clearPatient()"><i class="fas fa-broom text-white"></i></span>
                                        </div>
                                        <p class="help-block">Please provide patient id number</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-signature"></i></span>
                                            <input id="mr_no" name="mr_no" required class="form-control" placeholder="Inpatient File No." type="text">
                                        </div>
                                        <p class="help-block">Please provide Inpatient File No.</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-check-square"></i></span>
                                            <!-- <input id="gender" name="gender" class="form-control" placeholder="Patient Sex" type="text"> -->
                                            <input type="radio" class="form-control" id="exist" name="addamount" value="0" required>
                                            <label for="exist">Already added in package</label>
                                            <input type="radio" class="form-control" id="dontexist" name="addamount" value="1">
                                            <label for="dontexist">Add amount to package</label>
                                        
                                        </div>
                                        <p class="help-block">Include Charges To File Package ?</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-signature"></i></span>
                                            <input id="patient_name" name="patient_name" required class="form-control" placeholder="Patient Name" type="text">
                                        </div>
                                        <p class="help-block">Please provide patient Name</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-user-shield"></i></span>
                                            <input id="guardian" name="guardian" class="form-control" placeholder="Guardian Name" type="text">
                                        </div>
                                        <!-- <p class="help-block">S / O , D / O , W / O</p> -->
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon">Relation</i></span>
                                            <!-- <input id="gender" name="gender" class="form-control" placeholder="Patient Sex" type="text"> -->
                                            <input type="radio" class="form-control" id="son" name="relation" value="Son">
                                            <label for="son">S / O</label>
                                            <input type="radio" class="form-control" id="daughter" name="relation" value="Daughter">
                                            <label for="daughter">D / O</label>
                                            <input type="radio" class="form-control" id="wife" name="relation" value="Wife">
                                            <label for="wife">W / O</label>
                                            <input type="hidden" name="relation" value="" />
                                        
                                        </div>
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                                            <input id="patient_contact" name="patient_contact" class="form-control" placeholder="Patient Contact" type="text">
                                        </div>
                                        <p class="help-block">Please provide patient contact</p>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-id-card-alt"></i></span>
                                            <input id="patient_cnic" name="patient_cnic" class="form-control" placeholder="Patient CNIC Number" type="text">
                                        </div>
                                        <p class="help-block">Please patient cnic number</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-house-user"></i></span>
                                            <input id="patient_address" name="patient_address" class="form-control" placeholder="Patient Address" type="text">
                                        </div>
                                        <p class="help-block">Please provide patient Address</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-child"></i></span>
                                            <input id="age_days" name="age_days" class="form-control" placeholder="Patient Age" type="text">
                                        </div>
                                        <p class="help-block">Please provide patient age</p>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-venus-double"></i></span>
                                            <!-- <input id="gender" name="gender" class="form-control" placeholder="Patient Sex" type="text"> -->
                                            <input type="radio" class="form-control" id="male" name="gender" checked value="male">
                                            <label for="male">Male</label>
                                            <input type="radio" class="form-control" id="female" name="gender" value="female">
                                            <label for="female">Female</label>
                                            <input type="radio" class="form-control" id="other" name="gender" value="BiSexual">
                                            <label for="other">Other</label>
                                        
                                        </div>
                                        <p class="help-block">Please select patient sex</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-control-group">
                                            <select class="selected_services" id="selected_services" style="width:100%" name="selected_services[]" multiple="true" required>
                                                <optgroup label="RECESTATION">
                                                    <?php foreach($recestation_services as $service) { ?>
                                                        <?php if($service['is_deleted'] != 1) { ?>
                                                            <option data_obj='<?= json_encode($service) ?>' value="RECES-<?= $service['id'] ?>"><?= $service['name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <p class="help-block">Please Select Service</p>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6 m-t-4 m-b-4">
                                    <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                                        <thead>
                                        <tr>
                                            <th><i class="fas fa-fingerprint"></i></th>
                                            <th><i class="fas fa-signature"></i> Name</th>
                                            <th><i class="fas fa-phone"></i> Contact</th>
                                            <th><i class="fas fa-id-card-alt"></i> CNIC</th>
                                            <th class="text-center"><i class="fas fa-bolt"></i></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                        <th>ID</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>CNIC</th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                        <table class="table table-bordered display" cellspacing="1" width="100%">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Service</th>
                                                <th>Qty</th>
                                                <th>Provider</th>
                                                <th>Amount</th>
                                                <th>Balance</th>
                                                <th>Tax</th>
                                            </tr>
                                            </thead>
                                            <tbody id="order-list-container">
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="number" class="form-control" name="amount_received_num" required id="price-in-numbers">
                                            <label for="form_control_1">Billed Amount Received</label>
                                            <span class="help-block">Billed Amount Received To Customer.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" name="amount_received_words" required id="price-in-figures"  onchange="calculateBill()">
                                            <label for="form_control_1">Billed Received in words</label>
                                            <span class="help-block">Billed Amount Received To Customer in words.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <select class="form-control" name="payment_type" required>
                                                <option value="">Select</option>
                                                <option value="CASH">CASH</option>
                                                <option value="CARD">BANK</option>
                                               
                                            </select>
                                            <label for="form_control_1">Please Select Payment Type.</label>
                                            <span class="help-block">Payment in form of?</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" name="payment_reference">
                                            <label for="form_control_1">Reference</label>
                                            <span class="help-block">Enter Ref Like Cheque Number.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="number" class="form-control" name="customer_payed_amount" required id="customer_payed_amount" onchange="calculateBill()" require="require">
                                            <label for="form_control_1">Customer Paid</label>
                                            <span class="help-block">Amount Payed By Customer</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input has-success">
                                        <div class="input-icon">
                                            <input type="number" class="form-control" name="change_amount" id="change_amount">
                                            <label for="form_control_1">Change</label>
                                            <span class="help-block">Change to be returned.</span>
                                            <i class="fa fa-bell-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions noborder text-right">
                                <?php if(!empty($closingArray)){ ?>
                                    <button type="submit" class="btn btn-success">Pay</button>
                                <?php }else{ ?>
                                    <button type="button" class="btn btn-danger">You cannot perform any transaction</button>
                                <?php } ?>
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
    var ordersListContainer = $('#order-list-container');

    var PricinfInNumbers = '#price-in-numbers'
    var PricinfInFigures = '#price-in-figures'
    var CurstomerPaidAmount = '#customer_payed_amount'
    var ChangeAmount = '#change_amount'
    
    var PatientIdElement = $('#patient_id');
    var PatientNameElement = $('#patient_name');
    var PatientContactElement = $('#patient_contact');
    var PatientCnicElement = $('#patient_cnic');
    var PatientAgeElement = $('#age_days');
    var PatientGenderElement = $('#gender');
    var PatientGuardianElement = $('#guardian');
    var PatientAddressElement = $('#patient_address');
    var PatientRelationElement = $('#relation');

    var PosServices = [];
    
    function reRenderCart(){
        var balance = 0;
        var taxBalance = 0;
        var minumumToBePaid = 0;
        var htmlArray = {
            'OPD' : [],
            'INPT' : [],
            'EMER' : [],
            'RADP' : [],
            'PATH' : [],
            'OTHERS' : [],
            'RECES' : [],
        };
        var html = '';
        var opd_html = '';
        var inpt_html = '';
        var emer_html = '';
        var radp_html = '';
        var path_html = '';
        var others_html = '';
        var recestation_html = '';
        
        
        $.each(PosServices, function(index,value){
            
            console.log(value);

            var currentBalance = 0;
            
            var currentTax = 0;
            var taxRate = parseInt(value.tax_rate);

            if(value.charges_including_tax == 1){
                currentBalance = parseInt(value.charges) - calculateTax(parseInt(value.charges),taxRate);
                currentTax = calculateTax(parseInt(value.charges),taxRate);
                balance += currentBalance;
                taxBalance += currentTax;
            }else{
                currentBalance = parseInt(value.charges);
                currentTax = calculateTax(parseInt(value.charges),taxRate);
                balance += currentBalance;
                taxBalance += currentTax;
            }

            if(value.id.startsWith("OPD")){
                htmlArray['OPD'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                opd_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("OPD", index ,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"OPD", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");

            }else if(value.id.startsWith("INPT")){
                htmlArray['INPT'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    pakage: value.pakage,
                    doctor: 0
                });
                inpt_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("INPT", index,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"INPT", value.fix_amount,1,value.pakage)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("EMER")){
                htmlArray['EMER'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                emer_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td class='bg-danger lighter'></td>"+makeAmountFeild(value.charges, index,"EMER", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("RADP")){
                htmlArray['RADP'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                radp_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("RADP", index,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"RADP", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("PATH")){
                htmlArray['PATH'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges
                })
                path_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td class='bg-danger lighter'></td>"+makeAmountFeild(value.charges, index,"PATH", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("RECES")){
                htmlArray['RECES'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                recestation_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+makeAmountFeild(value.charges, index,"RECES", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");

            }else{
                htmlArray['OTHERS'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges
                })
                others_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+value.doctor+"</td>"+makeAmountFeild(value.charges, index,"OTHERS", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }

            
            // html += ("<tr><td>"+value.id+"</td><td>"+value.name+"</td><td>"+value.quantity+"</td><td>"+value.charges+"</td><td>"+balance+"</td></tr>");
        });

        
        if(opd_html != ''){
            html += '<tr><td  colspan="7"><strong>OPD</strong></td></tr>' + opd_html;
        }
        if(inpt_html != ''){
            html += '<tr><td  colspan="7"><strong>Inpatient</strong></td></tr>' + inpt_html;
        }
        if(emer_html != ''){
            html += '<tr><td  colspan="7"><strong>Emergency</strong></td></tr>' + emer_html;
        }
        if(radp_html != ''){
            html += '<tr><td  colspan="7"><strong>Radiology</strong></td></tr>' + radp_html;
        }
        if(path_html != ''){
            html += '<tr><td  colspan="7"><strong>Pthology</strong></td></tr>' + path_html;
        }
        if(others_html != ''){
            html += '<tr><td  colspan="7"><strong>Others</strong></td></tr>' + others_html;
        }
        if(recestation_html != ''){
            html += '<tr><td  colspan="7"><strong>Recestation</strong></td></tr>' + recestation_html;
        }
        html += '<tr><td colspan="4"></td><td class="text-right">Sub Total (Balance | Tax):</td><td class="text-left">'+balance+'</td><td>'+taxBalance+'</td></tr>';
        html += '<tr><td colspan="4"></td><td class="text-right">Toal:</td><td colspan="2" class="text-left">'+(taxBalance + balance)+'</td></tr>';
        

        console.log(ordersListContainer)
        $(PricinfInNumbers).val(balance + taxBalance);
        setPriceToBeBilled(balance + taxBalance);
        $('#order-list-container').html(html);
        jQuery(CurstomerPaidAmount).attr("min",(taxBalance + balance));


    }
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
    function selectPatient (patient_id, patient_name,guardian, relation, patient_contact, patient_cnic,patient_address,age_days,gender){
        console.log(patient_id);
        PatientIdElement.val(patient_id);
        PatientNameElement.val(patient_name);
        PatientGuardianElement.val(guardian);
        PatientRelationElement.val(relation);
        PatientContactElement.val(patient_contact);
        PatientCnicElement.val(patient_cnic);
        PatientAddressElement.val(patient_address);
        PatientAgeElement.val(age_days);
        PatientGenderElement.val(gender);
        
        
        
    }
    function clearPatient (){
        
        PatientIdElement.val("");
        PatientNameElement.val("");
        PatientGuardianElement.val("");
        PatientRelationElement.val("");
        PatientContactElement.val("");
        PatientCnicElement.val("");
        PatientAddressElement.val("");
        PatientAgeElement.val("");
        PatientGenderElement.val("");
       
    }
    function makeServiceProviderSelection(type, index, doctorSelectable){
        let selectUserHtml = '';
        console.log(doctorSelectable);
        if(doctorSelectable == 1)
        {
            selectUserHtml += '<select class="input-sm form-control" name="cart_services['+ index +'][service_provider]" required >';
            if(type == "RECES"){
                selectUserHtml += '<option value="">Select Doctor</option>';
                <?php foreach($inpatient_doctors as $doctor){ ?>
                    <?php if($doctor->is_dentist != 1 && $doctor->is_ultrasound_doc != 1){ ?>
                        selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                    <?php } ?>
                <?php } ?>
            }
            selectUserHtml += '</select>';
        }
        return selectUserHtml;
    }
    function makeServiceIdFeild(serviceString,index){
        let _services = serviceString.split('-');
        return serviceString + '<input type="hidden" name="cart_services['+ index +'][servicetype]" value="'+_services[0]+'" /><input type="hidden" name="cart_services['+ index +'][serviceid]" value="'+_services[1]+'" />';
    }
    function makeQtyFeild(quantity,index, qty_allowed){
        if(qty_allowed == 1){
            return '<td><input type="number" required class="input-sm form-control input-small" name="cart_services['+ index +'][quantity]" value="'+quantity+'" /></td>';
        }else{
            return '<td class="bg-danger lighter"></td>';
        }
        
    }
    function makeAmountFeild(amount,index,type ,fix_amount, allowedpostpaid = 0, pakageamount){
        let amounthtml = '<td';
        let returnhtml = '';
        if(fix_amount == 0){
            returnhtml = '><div class="input-group"><span class="input-group-addon">Paid</span><input type="number" onchange="changeAmount('+index+', this)" class="input-sm form-control input-small" name="cart_services['+ index +'][billedamount]" value="'+amount+'" /><input type="hidden" class="input-sm form-control input-small" name="cart_services['+ index +'][orignal_amount]" value="'+amount+'" /></div>';
        }else{
            returnhtml = 'class="bg-warning lighter"><div class="input-group"><span class="input-group-addon">Paid</span><input type="number" class="input-sm form-control input-small disabled" disabled="disabled" name="cart_services['+ index +'][billedamount]" value="'+amount+'" /><input type="hidden" class="input-sm form-control input-small" name="cart_services['+ index +'][orignal_amount]" value="'+amount+'" /></div>';
        }
        console.log(allowedpostpaid);
        let allowedreturnhtml = '';
        if(allowedpostpaid != 0){
            if(fix_amount == 0){
                allowedreturnhtml = '<div class="input-group"><span class="input-group-addon">Total Service Charges</span><input type="number" class="input-sm form-control input-small" name="cart_services['+ index +'][pakage_amount]" value="'+pakageamount+'" /></div>';
            }else{
                allowedreturnhtml = '<div class="input-group"><span class="input-group-addon">Total Service Charges</span><input type="number" disabled="disabled" class="input-sm form-control input-small" name="cart_services['+ index +'][pakage_amount]" value="'+pakageamount+'" /></div>';
            }
        }

        
        amounthtml += returnhtml
        amounthtml += allowedreturnhtml
        console.log(amounthtml)
        amounthtml += '</td>';
        return amounthtml;
        
    }
    function calculateTax(cost, vat = 20){
        return (cost * vat / 100);
    }
    function changeAmount(index, el){
        PosServices[index].charges = $(el).val();

        reRenderCart();
    }
    

    jQuery(document).ready(function() {

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

        serivcesSelector.select2({
            width: 'element'
        });
        serivcesSelector.on('change', function (e) {
            console.log(e);
            if(e.added){
                var optionel = $('body option[value="'+e.added.id+'"]');
                var optionelval = $.parseJSON(optionel.attr('data_obj'));
                console.log(optionelval);
                PosServices.push({
                    id: e.added.id,
                    name: e.added.text,
                    charges: optionelval.charges,
                    pakage: optionelval.charges,
                    quantity: 1,
                    allowed_multiple: optionelval.is_multiple,
                    fix_amount: optionelval.fix_amount ? optionelval.fix_amount : 0,
                    tax_rate: optionelval.tax_rate,
                    charges_including_tax: optionelval.charges_including_tax,
                    is_doctor_selectable: optionelval.is_doctor_selectable ? optionelval.is_doctor_selectable : 0,

                });
            }else{
                PosServices = $.grep(PosServices, function(event ){ 
                    return event.id != e.removed.id; 
                });
            }
            reRenderCart();
        });

        PatientIdElement.on('keyup change', function () {
            var i = 0;
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
        })

        PatientGuardianElement.on('keyup change', function () {
            var i = 2;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })

        PatientRelationElement.on('keyup change', function () {
            var i = 3;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })

        PatientContactElement.on('keyup change', function () {
            var i = 4;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })
        PatientCnicElement.on('keyup change', function () {
            var i = 5;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })
        PatientAddressElement.on('keyup change', function () {
            var i = 6;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })

        PatientAgeElement.on('keyup change', function () {
            var i = 7;
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        })
        PatientGenderElement.on('keyup change', function () {
            var i = 8;
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
            "ajax": "<?= site_url($HOSPITAL_REC_PATIENTS_JSON_URL) ?>",
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
<script type="text/javascript" src="<?php echo base_url('public/plugins/select2/select2.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/plugins/jquery-mask/dist/jquery.mask.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/datatables/media/css/jquery.dataTables_themeroller.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/select2/select2.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/jquery-mask/dist/jquery.mask.min.js') ?>"/>