
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
                            <span class="caption-subject bold uppercase"> Counter</span>
                            <span class="caption-helper">Select of create Patient MR</span>
                        </div>
                    </div>
                    <div id="treatments-table-div" class="portlet-body form">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 m-t-4 m-b-4">
                                    
                                    
                                </div>
                                <div class="col-md-12" id="ServiceGrid>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/siren.png') ?>" alt="Emergency Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Emergency Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/opd.png') ?>" alt="OPD Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">OPD Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/id.png') ?>" alt="Inpatient Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Inpatient Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/dental.png') ?>" alt="Dental Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Dental Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/x-ray.png') ?>" alt="X-Ray Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">X-Ray Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <!-- Content for fifth column -->
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/lab.png') ?>" alt="Laboratory Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Laboratory Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/ultrasound.png') ?>" alt="Ultrasound Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Ultrasound Services</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    var ordersListContainer = $('#order-list-container');

    var ServiceGrid = $('#ServiceGrid');

    var PricinfInNumbers = '#price-in-numbers'
    var PricinfInFigures = '#price-in-figures'
    var CurstomerPaidAmount = '#customer_payed_amount'
    var ChangeAmount = '#change_amount'
    
    var PatientIdElement = $('#patient_id');
    var PatientNumberElement = $('#patient_number');
    
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
            'DENTAL' : [],
            'ULTRA' : [],
        };
        var html = '';
        var opd_html = '';
        var inpt_html = '';
        var emer_html = '';
        var radp_html = '';
        var path_html = '';
        var others_html = '';
        var dental_html = '';
        var ultra_html = '';
        
        
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
                opd_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("OPD", index ,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"OPD", value.fix_amount,value.is_fileable)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");

            }else if(value.id.startsWith("INPT")){
                htmlArray['INPT'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    pakage: value.pakage,
                    doctor: 0
                });
                inpt_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("INPT", index,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"INPT", value.fix_amount,value.is_fileable,1,value.pakage)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("EMER")){
                htmlArray['EMER'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                emer_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td class='bg-danger lighter'></td>"+makeAmountFeild(value.charges, index,"EMER", value.fix_amount,value.is_fileable)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("RADP")){
                htmlArray['RADP'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                radp_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("RADP", index,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"RADP", value.fix_amount,value.is_fileable)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("PATH")){
                htmlArray['PATH'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges
                })
                path_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td class='bg-danger lighter'></td>"+makeAmountFeild(value.charges, index,"PATH", value.fix_amount)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
            }else if(value.id.startsWith("DENTAL")){
                htmlArray['DENTAL'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    pakage:value.pakage,
                    doctor: 0
                })
                dental_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("DENTAL", index ,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"DENTAL", value.fix_amount,value.is_fileable,0,value.pakage)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");


            }else if(value.id.startsWith("ULTRA")){
                htmlArray['ULTRA'].push({
                    id: value.id,
                    name: value.name,
                    quantity: value.quantity,
                    charges: value.charges,
                    doctor: 0
                })
                ultra_html += ("<tr><td>"+makeServiceIdFeild(value.id, index)+"</td><td>"+value.name+"</td>"+makeQtyFeild(value.quantity, index, value.is_quantityable)+"<td>"+makeServiceProviderSelection("ULTRA", index ,value.is_doctor_selectable)+"</td>"+makeAmountFeild(value.charges, index,"ULTRA", value.fix_amount,value.is_fileable)+"<td>"+currentBalance+"</td><td>"+currentTax+"</td></tr>");
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
        if(dental_html != ''){
            html += '<tr><td  colspan="7"><strong>DENTAL</strong></td></tr>' + dental_html;
        }
        if(ultra_html != ''){
            html += '<tr><td  colspan="7"><strong>ULTRASOUND</strong></td></tr>' + ultra_html;
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
        
        ServiceGrid.show();

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
        
        ServiceGrid.hide();

        PatientIdElement.val("");
        PatientNumberElement.val("");
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
            if(type == "OPD"){
                
                
                selectUserHtml += '<option value="">Select OPD Doctor</option>';
                <?php foreach($opd_doctors as $doctor){ ?>
                    <?php if($doctor->is_dentist != 1 && $doctor->is_ultrasound_doc != 1){ ?>
                        selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                    <?php } ?>
                <?php } ?>
                
            }else if(type == "INPT"){
                selectUserHtml += '<option value="">Select Inpatient Doctor</option>';
                <?php foreach($inpatient_doctors as $doctor){ ?>
                    selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                <?php } ?>
                
            }else if(type == "RADP"){
                selectUserHtml += '<option value="">Select Xray Technician</option>';
                <?php foreach($xray_tech as $doctor){ ?>
                    selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                <?php } ?>
            }else if(type == "DENTAL"){
                selectUserHtml += '<option value="">Select Doctor</option>';
                <?php foreach($dentists as $doctor){ ?>
                    selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                <?php } ?>
            }else if(type == "ULTRA"){
                selectUserHtml += '<option value="">Select Doctor</option>';
                <?php foreach($ultradocs as $doctor){ ?>
                    selectUserHtml += '<option value="<?= $doctor->id ?>"><?= $doctor->name ?></option>';
                <?php } ?>
            }
            selectUserHtml += '</select>';

            
        }
        return selectUserHtml;
    }
    function makeRoomSelection(type,index){
        let selectRoomHtml = '';
        if(type == "INPT")
        {    
            selectRoomHtml += '<div class="input-group"><span class="input-group-addon">Room</span><select class="input-sm form-control" name="cart_services['+ index +'][selected_room]" >';
            selectRoomHtml += '<option value="">Select Room</option>';
            <?php foreach($inpd_rooms as $room) {
                    if($room['is_allotted'] == 1) { ?>
                selectRoomHtml += '<option value="" disabled><span>&#128308;</span><?= $room['name'] ?></option>';
            <?php }else{ ?>
                selectRoomHtml += '<option value="<?= $room['id'] ?>"><span>&#128994;</span><?= $room['name'] ?></option>';
            <?php
                }
            } ?>
            selectRoomHtml += '</select></div>';
        }
       
        

        return selectRoomHtml;
    }
    function makePanelSelection(type,index){
        let selectPanelHtml = '';
        if(type == "INPT")
        {    
            selectPanelHtml += '<div class="input-group"><span class="input-group-addon">Is Visiting</span><select class="input-sm form-control" name="cart_services['+ index +'][is_visiting]" required><option value="">Please select</option><option value="1">Yes</option><option value="0">No</option></select></div><div class="input-group"><span class="input-group-addon">Panel</span><select class="input-sm form-control" name="cart_services['+ index +'][selected_panel]" >';
            selectPanelHtml += '<option value="">Select Panel</option>';
            <?php foreach($panel_companies as $panel) { ?>
                selectPanelHtml += '<option value="<?= $panel['name'] ?>"><?= $panel['name'] ?></option>';
            <?php } ?>
            selectPanelHtml += '</select></div>';
        }
       
        

        return selectPanelHtml;
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
    function makeAmountFeild(amount,index,type ,fix_amount, is_fileable, allowedpostpaid = 0, pakageamount){
        let amounthtml = '<td';
        let returnhtml = '';
        if(fix_amount == 0){
            returnhtml = '>'+makePanelSelection(type,index)+makeRoomSelection(type,index)+'<div class="input-group"><span class="input-group-addon">Paid</span><input type="number" onchange="changeAmount('+index+', this)" class="input-sm form-control input-small" name="cart_services['+ index +'][billedamount]" value="'+amount+'" /><input type="hidden" class="input-sm form-control input-small" name="cart_services['+ index +'][orignal_amount]" value="'+amount+'" /></div>';
        }else{
            returnhtml = 'class="bg-warning lighter"><div class="input-group"><span class="input-group-addon">Paid</span><input type="number" class="input-sm form-control input-small disabled" disabled="disabled" name="cart_services['+ index +'][billedamount]" value="'+amount+'" /><input type="hidden" class="input-sm form-control input-small" name="cart_services['+ index +'][orignal_amount]" value="'+amount+'" /></div>';
        }
        
        let allowedreturnhtml = '';
        if(allowedpostpaid != 0){
            if(fix_amount == 0){
                allowedreturnhtml = '<div class="input-group"><span class="input-group-addon">Total Service Charges</span><input type="number" class="input-sm form-control input-small" name="cart_services['+ index +'][pakage_amount]" value="'+pakageamount+'" /></div>';
            }else{
                allowedreturnhtml = '<div class="input-group"><span class="input-group-addon">Total Service Charges</span><input type="number" disabled="disabled" class="input-sm form-control input-small" name="cart_services['+ index +'][pakage_amount]" value="'+pakageamount+'" /></div>';
            }
        }

        if(type == "DENTAL"){
            // console.log(is_fileable)
            if(is_fileable == 1){
                
                allowedreturnhtml = '<div class="input-group"><span class="input-group-addon">Total Service Charges</span><input type="number" class="input-sm form-control input-small" name="cart_services['+ index +'][pakage_amount]" value="'+pakageamount+'" /></div>';
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
                    is_fileable: optionelval.is_fileable,
                    fix_amount: optionelval.fix_amount ? optionelval.fix_amount : 0,
                    tax_rate: optionelval.tax_rate,
                    charges_including_tax: optionelval.charges_including_tax,
                    is_doctor_selectable: optionelval.is_doctor_selectable,

                });
            }else{
                PosServices = $.grep(PosServices, function(event ){ 
                    return event.id != e.removed.id; 
                });
            }
            reRenderCart();
        });

        PatientNumberElement.on('keyup change', function () {
            var i = 0;
                table
                    .column(i)
                    .search( this.value )
                    .draw();
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
            "ajax": {
                "url": "<?= site_url($HOSPITAL_REC_PATIENTS_JSON_URL) ?>",
                "data": function(d){
                    // append patient number value to every ajax request
                    d.patient_number = PatientNumberElement.val();
                }
            },
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
