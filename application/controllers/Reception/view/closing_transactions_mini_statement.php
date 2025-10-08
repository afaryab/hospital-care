<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
$i = 0;
$count = 0;
$j = 0;
$count2 = 0;
$k = 0;
$count3 = 0;
$p = 0;
$count4 = 0;
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
            <div class="col-md-12 ">
    <div class="portlet light bordered">
        <div class="invoice in_sm" style="margin: 0 auto;">
            <div class="row">
                <div class="col-xs-12 text-center invoice-header">

                    <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                    
                    <hr style="border-top: 3px solid #000;"/>
                    <h3>-Counter Closing-</h3>
                    <hr style="border-top: 3px solid #000;"/>
                </div>
                <div class="col-xs-12 invoice-payment">

                    
                    <table class="table">
                    <table class="table" >
                      
                        <?php
                        $total_amount_mini=0;
                        $total_amount_cot=0;
                        $total_amount_conslt=0;?>
                            <tr>
                                
                                <td><strong>INV #</strong></td>
                                
                                <td><strong>Patient</strong></td>
                                <td><strong>Service</strong></td>
                                <td><strong>Amount</strong></td>
                            </tr>
                            <?php if(!empty($counter_transactions)) {
                                foreach ($counter_transactions as $row) {
                                    if($row['income_or_expence'] == 'INCOME'){
                                ?>
                                    
                                    <?php
                                        if(!empty($row['rows'])) {
                                            foreach ($row['rows'] as $elements) {
                                                
                                                                ?>
                                            <?php if($elements['type'] == 'OPD' ) { ?>
                                                <tr>
                                                    <td><?= $elements['closing_transaction_id'] ?>-[<?=$elements['serial_number_doctor'];?>]</td>
                                                    <?php
                                                        foreach ($patients as $patient) {
                                                            if($patient['id'] == $row['patient_id']) {
                                                            
                                                        ?>
                                        
                                                        <td><?= $patient['pateint_name'] ?> </td>
                                                        <?php
                                                        }
                                                    } ?>
                                                    <?php
                                                    $service="OPD";
                                                    if($elements['service_name']=="Consultation"){
                                                        $service="OPD";
                                                    }else{
                                                        $service="Cot.";
                                                    }
                                                    ?> 
                                                    <td>  <?=$service;?></td>
                                                    <?php if($elements['edited_amount'] != NULL) { ?> 
                                                                        <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong> &nbsp;&nbsp;<del><b><?= $elements['edited_amount'] ?></b></del></td>
                                                                    <?php }else{ ?> 
                                                                        <td><?= $elements['amount'] ?><strong> <?php if($row['type']=="CARD"){ echo "-BANK"; }elseif($row['type']=="CREDITCARD"){ echo "-CARD"; } ?></strong></td>
                                                                    <?php } ?>
                                                    <?php 
                                                    $total_amount_conslt = $total_amount_conslt + $elements['amount'];
                                                    $total_amount_mini = $total_amount_mini + $elements['amount']; ?>
                                                </tr>
                                            <?php }
                                            }
                                        }
                                }
                            }} ?>
                                <tr style="border-top: 2px solid #000;">
                                    <td colspan="3" class="text-right" style="vertical-align: middle;text-align: center;"><strong>Total: </strong></td>
                                    <td style=""><strong><?= $total_amount_conslt ?></strong></td>
                                </tr>
                                
                          
                               
                                <tr style="border-top: 3px solid #000;">
                                    <td colspan="3" class="text-right" style="vertical-align: middle;text-align: center;"><strong>Total Income: </strong></td>
                                    <td style="font-size: large;"><strong><?= $total_amount_mini ?></strong></td>
                                </tr>
                            <!-- expense -->
                                <tr style="">
                                <td colspan="4" class="text-center" style="font-size:medium;"><strong>-Expense-</strong></td>
                            </tr>
                            <tr>
                                
                                <td><strong>INV #</strong></td>
                                
                                <td colspan="2"><strong>Detail</strong></td>
                                <td><strong>Amount</strong></td>
                            </tr>
                            <?php
                                            foreach ($counter_transactions as $row) {
                                                $total_amount_expense=0;
                                                if($row['income_or_expence'] == 'EXPENSE' || $row['income_or_expence'] == 'EXP'){
                                                    $total_amount_expense = $total_amount_expense + $row['amount'];
                                                    ?>
                                                
                                                    <tr >
                                                        <td> <?= $row['id'] ?> </td>
                                                        <td colspan="2" style="padding:0;"  width="50%">
                                                        <table style="margin-bottom:0;" class="table table-bordered table-responsive table-striped" >
                                                        <?php
                                                            if(!empty($row['rows'])) {
                                                                foreach ($row['rows'] as $elements) {
                                                                    if($elements['type'] == 'VOUCHER-PAY'){ ?>
                                                                        <tr >
                                                                            <td><strong>V No. </strong><?= $elements['exp_array']['voucher_id'] ?><strong> Purpose </strong>"<?= $elements['exp_category']['name'] ?>" <strong> Paid To  </strong>"
                                                                            <?php if($elements['exp_voucher_array']['employee_id'] != 0){
                                                                                    if($elements['exp_voucher_array']['employee_id'] != NULL){ ?>
                                                                                <?=$elements['employee']['name'] ?> 
                                                                            <?php }
                                                                            }elseif($elements['exp_voucher_array']['payed_to_others'] != NULL){ ?>
                                                                                <?= $elements['exp_voucher_array']['payed_to_others'] ?> 
                                                                            <?php
                                                                            } ?>" 
                                                                            <?php if($elements['exp_voucher_array']['inpatient_file_id'] != 0){
                                                                                if($elements['exp_voucher_array']['inpatient_file_id'] != NULL){ ?>
                                                                                <strong> Mr No. </strong> <?= $elements['exp_voucher_array']['inpatient_file_id'] ?> 
                                                                                <?php } 
                                                                                }?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php }elseif($elements['type'] == 'INPT-EXP'){ ?>
                                                                            <tr >
                                                                                <!-- <td>Inpatient Expence</td> -->
                                                                                <?php if($elements['inpExp']['payment_refference'] != NULL){ ?>
                                                                                <td><?= $elements['inpExp']['payment_refference'] ?>&nbsp;  ( File #  <?= $elements['inpExp']['file_id'] ?> ) </td>
                                                                                <?php }else{ ?>
                                                                                    <td>Paid for File # <?= $elements['inpExp']['file_id'] ?></td>
                                                                                <?php }
                                                                                ?>
                                                                            </tr>
                                                                    <?php }else{ ?>
                                                                            <tr >
                                                                                <td><?= $elements['exp_array']['payment_reference'] ?></td>
                                                                            </tr>
                                                                    <?php }
                                                                }
                                                            }
                                                        ?></table></td>
                                                        <?php if($row['edited_amount'] != NULL) { ?> 
                                                            <td class="bg-warning"><?= $row['amount'] ?> &nbsp;&nbsp;<del><b><?= $row['edited_amount'] ?></b></del></td>
                                                        <?php }else{ ?> 
                                                            <td class="bg-warning"><?= $row['amount'] ?></td>
                                                        <?php } ?>
                                                        
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            $grand_total=0;
                                            $grand_total=$total_amount_mini-$total_amount_expense;
                                            ?>
                                            <tr style="border-top: 3px solid #000;">
                                                <td colspan="3" class="text-right" style="vertical-align: middle;text-align: center;"><strong>Total Expense: </strong></td>
                                                <td style="font-size: large;"><strong><?= $total_amount_expense ?></strong></td>
                                            </tr>
                                            <tr style="border-top: 3px solid #000;">
                                                <td colspan="3" class="text-right" style="font-size: large;vertical-align: middle;text-align: center;"><strong>Total: </strong></td>
                                                <td style="font-size: large;"><strong><?= $grand_total ?></strong></td>
                                            </tr>
                        <!-- expense -->

                    </table>
                    
                    
                </div>
                
            </div>
            
        </div>
        <form method="POST">
                                    <div class="row dontprint">
                                        <div class="col-md-12 m-t-4 m-b-4">
                                            <div class="col-md-11">
                                                <div class="form-group form-md-line-input has-success">
                                                <label><input type="checkbox" name="do_agree" required id="can_click" /> I agree, Please close my counter and logmeout.</label>
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
    </div>
    <script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>

    <!-- <script>
        jQuery(function(){
            $('#example').DataTable({
                "order": [[ 0, "desc" ]]
            });
        })
    </script>
    <style>
    #firstSection{
        width: calc(100% - 280px) !important;
        display: inline-block;
        vertical-align: top;
    }
    #secondSection{
        width: 270px;
        display: inline-block;
        vertical-align: top;
        padding: 0px 15px 15px 15px;
    }
    .page-content{
        padding:15px;
    }
    </style> -->
    <script>
        var cont = <?php echo $count; ?>;
        var cont2 = <?php echo $count2; ?>;
        var cont3 = <?php echo $count3; ?>;
        var cont4 = <?php echo $count4; ?>;
        var i = 0;
        $(document).ready(function(){
            if(!$('.inpTable tbody').find('tr').length){ 
                $('#inpDiv').hide();
            }
            if(!$('.emrTable tbody').find('tr').length){ 
                $('#emrDiv').hide();
            }
            if(!$('.resTable tbody').find('tr').length){ 
                $('#resDiv').hide();
            }
            console.log(cont);
            for(i=0;i <= cont;i++){
                if(!$('.Table1'+i).find('tbody tr').length){ 
                    $('#myTable1div'+i).hide();
                }
            }
            for(j=0;j <= cont2;j++){
                if(!$('.Table2'+j).find('tbody tr').length){ 
                    $('#myTable2div'+j).hide();
                }
            }
            for(k=0;k <= cont3;k++){
                if(!$('.Table3'+k).find('tbody tr').length){ 
                    $('#myTable3div'+k).hide();
                }
            }
            for(p=0;p <= cont4;p++){
                if(!$('.Table4'+p).find('tbody tr').length){ 
                    $('#myTable4div'+p).hide();
                }
            }
        })

  

    </script>    
    <style>
    #firstSection{
        width: calc(100% - 290px) !important;
        display: inline-block;
        vertical-align: top;
    }
    #secondSection{
        width: 270px;
        display: inline-block;
        vertical-align: top;
        padding: 0px 15px 15px 15px;
    }
    .page-content{
        padding:15px;
    }
    .invoice.in_sm table td:first-child{
        padding-left:10px !important;
    }
    /* .table{ 
        empty-cells: hide; 
    } */
    </style>

