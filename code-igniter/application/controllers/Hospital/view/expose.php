
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
                            <span class="caption-subject bold uppercase"> Select service for PS: <?php echo $patient['ps_number'] ?? ''; ?> <?php echo $patient['pateint_name'] ?? ''; ?></span>
                        </div>
                    </div>
                    <div id="treatments-table-div" class="portlet-body form">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 m-t-4 m-b-4">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fas fa-fingerprint"></i></span>
                                            <input id="patient_number" value="<?php echo $patient['ps_number'] ?? ''; ?>" name="patient_number" class="form-control" placeholder="Patient Number e.g: 2025/10/{SER}/{SR-NO} or 2025/10/{SR-NO}" type="text">
                                            <input id="patient_id" name="patient_id" class="form-control" placeholder="" type="hidden">
                                        </div>
                                        <p class="help-block">Please provide MR Number e.g: 2025/10/{SER}/{SR-NO}</p>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Patient Department PSIDs Tree -->
                                        <div class="portlet" style="margin-top: 5px;">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-sitemap"></i>
                                                    <span class="caption-subject bold"></span>
                                                </div>
                                                <div class="tools">
                                                    <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="patient-tree">
                                                    <!-- Main Patient -->
                                                    <div class="tree-node main-patient">
                                                        <div class="tree-content">
                                                            <i class="fa fa-user-circle text-primary"></i>
                                                            <strong>Patient: <?php echo $patient['pateint_name'] ?? 'N/A'; ?></strong>
                                                            <span class="badge badge-primary"><?php echo $patient['ps_number'] ?? 'N/A'; ?></span>
                                                        </div>
                                                        
                                                        <!-- Departments Tree -->
                                                        <div class="tree-children">
                                                            
                                                            <!-- OPD Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-stethoscope text-success"></i>
                                                                    <span class="department-name">OPD Services</span>
                                                                    <span class="badge badge-success"><?php echo $patient['opd']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['opd']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['opd']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['opd']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- Inpatient Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-bed text-warning"></i>
                                                                    <span class="department-name">Inpatient Services</span>
                                                                    <span class="badge badge-warning"><?php echo $patient['inpatient']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['inpatient']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['inpatient']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['inpatient']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- Emergency Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-ambulance text-danger"></i>
                                                                    <span class="department-name">Emergency Services</span>
                                                                    <span class="badge badge-danger"><?php echo $patient['emergency']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['emergency']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['emergency']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['emergency']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- Dental Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-tooth text-info"></i>
                                                                    <span class="department-name">Dental Services</span>
                                                                    <span class="badge badge-info"><?php echo $patient['dental']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['dental']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['dental']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['dental']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- X-Ray Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-x-ray text-secondary"></i>
                                                                    <span class="department-name">X-Ray Services</span>
                                                                    <span class="badge badge-secondary"><?php echo $patient['xray']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['xray']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['xray']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['xray']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- Laboratory Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-flask text-purple"></i>
                                                                    <span class="department-name">Laboratory Services</span>
                                                                    <span class="badge badge-purple"><?php echo $patient['lab']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['lab']['tests'] ?? []); ?> tests)</span>
                                                                </div>
                                                                <?php if (!empty($patient['lab']['tests'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['lab']['tests'] as $test): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-vial text-info"></i>
                                                                            <span><?php echo $test['test_name'] ?? 'Lab Test'; ?></span>
                                                                            <small class="text-muted"><?php echo $test['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <!-- Ultrasound Department -->
                                                            <div class="tree-node department">
                                                                <div class="tree-content">
                                                                    <i class="fa fa-heartbeat text-dark"></i>
                                                                    <span class="department-name">Ultrasound Services</span>
                                                                    <span class="badge badge-dark"><?php echo $patient['ultrasound']['psid'] ?? 'N/A'; ?></span>
                                                                    <span class="treatment-count">(<?php echo count($patient['ultrasound']['treatments'] ?? []); ?> treatments)</span>
                                                                </div>
                                                                <?php if (!empty($patient['ultrasound']['treatments'])): ?>
                                                                <div class="tree-children treatments">
                                                                    <?php foreach ($patient['ultrasound']['treatments'] as $treatment): ?>
                                                                    <div class="tree-node treatment">
                                                                        <div class="tree-content">
                                                                            <i class="fa fa-file-medical text-info"></i>
                                                                            <span><?php echo $treatment['treatment_name'] ?? 'Treatment'; ?></span>
                                                                            <small class="text-muted"><?php echo $treatment['created_on'] ?? ''; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-6" id="ServiceGrid">
                                    <div class="caption font-red-sunglo" style="margin-bottom: 15px;">
                                        <span class="caption-subject bold uppercase"> New service for PS: <?php echo $patient['ps_number'] ?? ''; ?></span>
                                        
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_EMERGENCY_COUNTER.'?pid='.$patient['id']) ?>" class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/siren.png') ?>" alt="Emergency Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Emergency Services</h5>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_OPD_COUNTER.'?pid='.$patient['id']) ?>" class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/opd.png') ?>" alt="OPD Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">OPD Services</h5>
                                                        </div>
                                                    </div>
                                                </abs>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_NEW_INPT_COUNTER.'?pid='.$patient['id']) ?>" class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/id.png') ?>" alt="Inpatient Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Inpatient Services</h5>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_DENTAL_COUNTER.'?pid='.$patient['id']) ?>" class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/dental.png') ?>" alt="Dental Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Dental Services</h5>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_XRAY_COUNTER.'?pid='.$patient['id']) ?>"  class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/x-ray.png') ?>" alt="X-Ray Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">X-Ray Services</h5>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_XRAY_COUNTER.'?pid='.$patient['id']) ?>"  class="form-group">
                                                    <!-- Content for fifth column -->
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/lab.png') ?>" alt="Laboratory Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Laboratory Services</h5>
                                                        </div>
                                                    </div>
                                                </abs>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="<?= site_url($HOSPITAL_XRAY_COUNTER.'?pid='.$patient['id']) ?>"  class="form-group">
                                                    <div class="card" style="max-width: 300px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <div class="card-body text-center" style="padding: 20px;">
                                                            <img src="<?php echo base_url('public/img/ultrasound.png') ?>" alt="Ultrasound Services" style="width: 60px; height: 60px; margin-bottom: 15px; object-fit: contain;"/>
                                                            <h5 class="card-title" style="margin: 0; color: #333; font-weight: bold;">Ultrasound Services</h5>
                                                        </div>
                                                    </div>
                                                </a>
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
    
    /* Patient Tree Styles */
    .patient-tree {
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
    }
    
    .tree-node {
        margin: 8px 0;
        position: relative;
    }
    
    .tree-content {
        padding: 8px 12px;
        border-radius: 4px;
        background: #f8f9fa;
        border-left: 3px solid #007bff;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }
    
    .tree-content:hover {
        background: #e9ecef;
    }
    
    .main-patient .tree-content {
        background: #e3f2fd;
        border-left-color: #1976d2;
        font-size: 16px;
        font-weight: bold;
    }
    
    .department .tree-content {
        background: #f5f5f5;
        border-left-width: 2px;
        margin-left: 20px;
        font-weight: 600;
    }
    
    .treatment .tree-content {
        background: #fafafa;
        border-left-width: 1px;
        border-left-color: #ccc;
        margin-left: 40px;
        font-size: 14px;
        padding: 6px 10px;
    }
    
    .tree-children {
        margin-left: 15px;
        border-left: 1px dashed #ddd;
        padding-left: 10px;
    }
    
    .treatments .tree-children {
        margin-left: 25px;
    }
    
    .department-name {
        font-weight: 600;
        color: #333;
    }
    
    .treatment-count {
        font-size: 12px;
        color: #6c757d;
        font-style: italic;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
    }
    
    .badge-primary { background-color: #007bff; color: white; }
    .badge-success { background-color: #28a745; color: white; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-danger { background-color: #dc3545; color: white; }
    .badge-info { background-color: #17a2b8; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
    .badge-purple { background-color: #6f42c1; color: white; }
    .badge-dark { background-color: #343a40; color: white; }
    
    .tree-node i {
        width: 16px;
        text-align: center;
    }
    
    .text-purple { color: #6f42c1 !important; }
    
    /* Collapsible functionality */
    .tree-node.collapsed .tree-children {
        display: none;
    }
    
    .tree-node .tree-content {
        cursor: pointer;
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

        // Tree functionality
        $('.department .tree-content').click(function(e) {
            e.stopPropagation();
            $(this).parent().toggleClass('collapsed');
        });

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
