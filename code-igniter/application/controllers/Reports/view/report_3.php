<?php

$ids1 = [];
$total_amount = 0;
$total_cash = 0;
$deduction_in_cash = 0;
$remaining_total_in_cash = 0;
$total_card = 0;
$deduction_in_card = 2.09;
$remaining_total_in_card = 0;
$total_check = 0;
$deduction_in_check = 0;
$remaining_total_in_check = 0;
$total_other_services_cash = 0;
$total_other_services_card = 0;
$total_other_services_check = 0;
$total_others_deduction = 0;
$doctor_share = $doc_to_be_share->salery_amount;
$remaining_total = 0;
?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/global/fonts/stylesheet.css') ?>"/>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <div class="invoice">
                        <div class="row">
                            <div class="col-xs-12 text-center invoice-header">
                            <h1 class="uppercase"><?= business_contact_name ?><small style="font-size: 15px;"> <?= location_name?> </small></h1>
                                            <hr/>
                                            <span class="caption-subject"><?= location_address ?> , <?= location_city ?> , <?= location_state ?></span></br>
                                            <span class="caption-subject"><?= location_contact ?></span></br>
                                            <span class="caption-subject"><?= location_email ?></span>
                                            <hr style="border-top: 3px solid #000;"/>
                                <h5>Income Report By Doctor "<?= $doc_to_be_share->name ?>".</h5>
                                <hr/>
                                <h6><?= $date_range ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-hover table-bordered">
                                    <tbody>
                                    <tr class="dark">
                                        <td colspan="6" class="text-center"><strong> Cash Transactions </strong></td>
                                    </tr>
                                    <tr class="gray">
                                        <td>Id</td>
                                        <td>Payment Reference</td>
                                        <td>Patient Name</td>
                                        <td>Units</td>
                                        <td>Created On</td>
                                        <td class="text-right">Amount</td>
                                    </tr>
                                    <?php
                                    if(!empty($cash)) {
                                        foreach ($cash as $row) {
                                            $total_cash += $row['amount_in_num'];
                                            if($row['service_id'] == 4){
                                                $total_other_services_cash += $row['amount_in_num'];
                                            }
                                            ?>
                                            <tr>
                                                <td > <?= $row['id'] ?> </td>
                                                <td>REF: <?= $row['payment_refference'] ?> | SER: <?php
                                                    if($row['service_id'] == 1){
                                                        echo 'X-Ray';
                                                    }elseif($row['service_id'] == 2){
                                                        echo 'Implant';
                                                    }elseif($row['service_id'] == 3){
                                                        echo 'Consultation';
                                                    }elseif($row['service_id'] == 4){
                                                        echo 'Others';
                                                    }else
                                                    ?></td>
                                                <td><?= $row['patient_name'].'(ID: '.$row['patient_id'].')' ?></td>
                                                <td><?= $row['units'] == NULL ? 'N/A' : $row['units'] ?></td>
                                                <td><?= date('Y-m-d h:i a',strtotime($row['created_on'])) ?></td>
                                                <td class="text-right"><?= $row['amount_in_num'] ?> PKR</td>
                                            </tr>
                                            <?php
                                        } ?>
                                        <tr class="info">
                                            <td colspan="4"></td>
                                            <td class="text-left">Total Amount in Cash</td>
                                            <td class="text-right"><strong><?= $total_cash ?> PKR</strong></td>
                                        </tr>
                                        <tr class="danger">
                                            <td colspan="4"></td>
                                            <td class="text-left">Deduction in Cash</td>
                                            <td class="text-right"><strong><?= $deduction_in_cash ?> &percnt;</strong></td>
                                        </tr>
                                        <tr class="success">
                                            <td colspan="4"></td>
                                            <td class="text-left">Remaining Total in Cash</td>
                                            <td class="text-right"><strong><?= $remaining_total_in_cash = $total_cash -  - (($total_cash * $deduction_in_cash) / 100) ?> PKR</strong></td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No Cash Transactions.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr class="gray">
                                        <td colspan="6" class="text-center"><strong> Card Transactions </strong></td>
                                    </tr>
                                    <tr>
                                        <td>Id</td>
                                        <td>Payment Reference</td>
                                        <td>Patient Name</td>
                                        <td>Units</td>
                                        <td>Created On</td>
                                        <td class="text-right">Amount</td>
                                    </tr>
                                    <?php
                                    if(!empty($card)) {
                                        foreach ($card as $row) {
                                            $total_card += $row['amount_in_num'];
                                            if($row['service_id'] == 4){
                                                $total_other_services_card += (($row['amount_in_num']* (100 - $deduction_in_card))/100);
                                            }
                                            ?>
                                            <tr>
                                                <td > <?= $row['id'] ?> </td>
                                                <td>REF: <?= $row['payment_refference'] ?> | SER: <?php
                                                    if($row['service_id'] == 1){
                                                        echo 'X-Ray';
                                                    }elseif($row['service_id'] == 2){
                                                        echo 'Implant';
                                                    }elseif($row['service_id'] == 3){
                                                        echo 'Consultation';
                                                    }elseif($row['service_id'] == 4){
                                                        echo 'Others';
                                                    }else
                                                    ?></td>
                                                <td><?= $row['patient_name'].'(ID: '.$row['patient_id'].')' ?></td>
                                                <td><?= $row['units'] == NULL ? 'N/A' : $row['units'] ?></td>
                                                <td><?= date('Y-m-d h:i a',strtotime($row['created_on'])) ?></td>
                                                <td class="text-right"><?= $row['amount_in_num'] ?> PKR</td>
                                            </tr>
                                            <?php
                                        } ?>
                                        <tr class="info">
                                            <td colspan="4"></td>
                                            <td class="text-left">Total Amount in Card</td>
                                            <td class="text-right"><strong><?= $total_card ?> PKR</strong></td>
                                        </tr>
                                        <tr class="danger">
                                            <td colspan="4"></td>
                                            <td class="text-left">Deduction in Card</td>
                                            <td class="text-right"><strong><?= $deduction_in_card ?> &percnt;</strong></td>
                                        </tr>
                                        <tr class="success">
                                            <td colspan="4"></td>
                                            <td class="text-left">Remaining Total in Card</td>
                                            <td class="text-right"><strong><?= $remaining_total_in_card = $total_card - (($total_card * $deduction_in_card) / 100) ?> PKR</strong></td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No Card Transactions.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr class="gray">
                                        <td colspan="6" class="text-center"><strong> Cheque Transactions </strong></td>
                                    </tr>
                                    <tr>
                                        <td>Id</td>
                                        <td>Payment Reference</td>
                                        <td>Patient Name</td>
                                        <td>Units</td>
                                        <td>Created On</td>
                                        <td class="text-right">Amount</td>
                                    </tr>
                                    <?php
                                    if(!empty($check)) {
                                        foreach ($check as $row) {
                                            $total_check += $row['amount_in_num'];
                                            if($row['service_id'] == 4){
                                                $total_other_services_check += $row['amount_in_num'];
                                            }
                                            ?>
                                            <tr>
                                                <td > <?= $row['id'] ?> </td>
                                                <td>REF: <?= $row['payment_refference'] ?> | SER: <?php
                                                    if($row['service_id'] == 1){
                                                        echo 'X-Ray';
                                                    }elseif($row['service_id'] == 2){
                                                        echo 'Implant';
                                                    }elseif($row['service_id'] == 3){
                                                        echo 'Consultation';
                                                    }elseif($row['service_id'] == 4){
                                                        echo 'Others';
                                                    }else
                                                    ?></td>
                                                <td><?= $row['patient_name'].'(ID: '.$row['patient_id'].')' ?></td>
                                                <td><?= $row['units'] == NULL ? 'N/A' : $row['units'] ?></td>
                                                <td><?= date('Y-m-d h:i a',strtotime($row['created_on'])) ?></td>
                                                <td class="text-right"><?= $row['amount_in_num'] ?> PKR</td>
                                            </tr>
                                            <?php
                                        } ?>
                                        <tr class="info">
                                            <td colspan="4"></td>
                                            <td class="text-left">Total Amount in Cheque</td>
                                            <td class="text-right"><strong><?= $total_check ?> PKR</strong></td>
                                        </tr>
                                        <tr class="danger">
                                            <td colspan="4"></td>
                                            <td class="text-left">Deduction in Cheque</td>
                                            <td class="text-right"><strong><?= $deduction_in_check ?> &percnt;</strong></td>
                                        </tr>
                                        <tr class="success">
                                            <td colspan="4"></td>
                                            <td class="text-left">Remaining Total in Cheque</td>
                                            <td class="text-right"><strong><?= $remaining_total_in_check = $total_check - (($total_check * $deduction_in_check) / 100) ?> PKR</strong></td>
                                        </tr>
                                        <?php
                                    }else{
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No Cheque Transactions.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000; background: #CCC;" colspan="6" class="text-center"><strong> Total Summery </strong></td>
                                    </tr>
                                    <tr class="info">
                                        <td><i class="fa fa-bars"></i> </td>
                                        <td colspan="2"></td>
                                        <td class="text-left">Total Amount</td>
                                        <td class="text-right" colspan="2"><strong><?= $total_check + $total_cash + $total_card ?> PKR</strong></td>
                                    </tr>
                                    <tr class="danger">
                                        <td><i class="fa fa-bars"></i> </td>
                                        <td colspan="2"></td>
                                        <td class="text-left">Total Card Deduction</td>
                                        <td class="text-right" colspan="2"><strong>- <?= ($total_check + $total_cash + $total_card) - (($total_cash - (($total_cash * $deduction_in_cash) / 100)) + ($total_card - (($total_card * $deduction_in_card) / 100)) + ($total_check - (($total_check * $deduction_in_check) / 100))) ?> PKR</strong></td>
                                    </tr>
                                    <tr class="danger">
                                        <td><i class="fa fa-bars"></i> </td>
                                        <td colspan="2"></td>
                                        <td class="text-left">Total Material Cost Deduction 10&percnt;</td>
                                        <td class="text-right" colspan="2"><strong>- <?= $total_others_deduction = (($total_other_services_check + $total_other_services_card + $total_other_services_cash) * 10) / 100 ?> PKR</strong></td>
                                    </tr>
                                    <tr class="success">
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;"><i class="fa fa-bars"></i> </td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" colspan="2"></td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-left">Remaining Total</td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-right" colspan="2"><strong><?= $remaining_total = ($remaining_total_in_cash + $remaining_total_in_check + $remaining_total_in_card) - $total_others_deduction ?> PKR</strong></td>
                                    </tr>
                                    <tr class="warning">
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;"><i class="fa fa-bars"></i> </td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" colspan="2"></td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-left">Share Percent</td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-right" colspan="2"><strong><?= $doctor_share ?> &percnt; PKR</strong></td>
                                    </tr>
                                    <tr class="success">
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;"><i class="fa fa-bars"></i> </td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" colspan="2"></td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-left">Share Amount</td>
                                        <td style="border-top: 3px solid #000000;border-bottom: 3px solid #000000;" class="text-right" colspan="2"><strong><?= ($doctor_share * $remaining_total)/100 ?> PKR</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 50px;">
                            <div class="col-xs-12 invoice-block">
                                <a class="btn btn-lg blue hidden-print margin-bottom-5 print_button"> Print This
                                    <i class="fa fa-print"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(function(){
            jQuery('.print_button').bind('click',function(){
                window.print();
            });
        })
    </script>
