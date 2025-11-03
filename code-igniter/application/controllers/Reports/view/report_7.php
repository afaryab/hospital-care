
<?php
$ids1 = [];
$ids2 = [];
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <?php
                $total_amount = 0;
                ?>
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
                                <h5>In Patient Income Report By Date Range.</h5>
                                <hr/>
                                <h6><?= $date_range ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <?php foreach($transactions2 as $trans){ ?>
                                    <table class="table table-hover table-stripped">
                                        <tr>
                                            <td>MR Number: </td>
                                            <td><strong><?= $trans['patient']['id'].'-'.$trans['case']['id'] ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Name: </td>
                                            <td><?= $trans['patient']['name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Age: </td>
                                            <td><?= $trans['patient']['age'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Sex: </td>
                                            <td><?= $trans['patient']['sex'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Guardian: </td>
                                            <td><?= $trans['patient']['son_off'] == 1 ? 'S/O' : '' ?><?= $trans['patient']['daughter_off'] == 1 ? 'D/O' : '' ?><?= $trans['patient']['wife_off'] == 1 ? 'W/O' : '' ?><?= $trans['patient']['mother_off'] == 1 ? 'M/O' : '' ?><?= $trans['patient']['father_off'] == 1 ? 'F/O' : '' ?>&nbsp;<?= $trans['patient']['guardian'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient CNIC: </td>
                                            <td><?= $trans['patient']['cnic'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Contact: </td>
                                            <td><?= $trans['patient']['phone'] ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Address:&nbsp;<?= $trans['patient']['address'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient By: </td>
                                            <td><?= $trans['case']['case_by'] == 'Panel' ? 'Panel' : 'cash'; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Patient Doctor: </td>
                                            <td><?php
                                                foreach($doctors as $doc){
                                                    if($trans['case']['doctor'] == $doc['id']){
                                                        echo $doc['name'];
                                                    }
                                                }
                                                ?></td>
                                        </tr>
                                        <?php if($trans['case']['case_by'] == 'Panel'){ ?>
                                            <tr>
                                                <td>Panel Company: </td>
                                                <td><?php
                                                    foreach($panel as $doc){
                                                        if($trans['case']['panel_company'] == $doc['id']){
                                                            echo $doc['name'];
                                                        }
                                                    }
                                                    ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    <table class="table table-hover">
                                        <tbody>
                                        <tr>
                                            <td colspan="2" class="text-center"> Patient Transactions </td>
                                        </tr>
                                        <tr>
                                            <td>Date</td>
                                            <td class="text-right">Amount</td>
                                        </tr>
                                        <?php
                                        if(!empty($trans['transactions'])) {
                                            $this_total = 0;
                                            foreach ($trans['transactions'] as $row) {
                                                $total_amount += $row['payment_in_figure'];
                                                $this_total += $row['payment_in_figure'];
                                                ?>
                                                <tr>
                                                    <td><?= date('Y-m-d h:i a',strtotime($row['created_on'])) ?></td>
                                                    <td class="text-right">
                                                        <strong><?= $row['payment_in_figure'] ?></strong></td>
                                                </tr>
                                                <?php
                                            } ?>
                                            <tr>
                                                <td><strong>Total Amount Payed By this Patient :</strong></td>
                                                <td><strong><?= $this_total ?></strong></td>
                                            </tr>
                                            <?php
                                        }else{
                                        ?>
                                            <tr>
                                                <td colspan="2"> No Transactions</td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <hr style="border-top: 3px solid #000;"/>
                                <?php } ?>

                            </div>
                            <div class="col-xs-12 invoice-block" style="text-align: center;margin: 5px 0;color: #fff;background-color: #000;padding: 5px;font-size: 24px;">Total: <?= $total_amount ?> Rs Only</div>
                            <div class="col-xs-12 invoice-header">
                                <small>For administration use only.</small>
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
