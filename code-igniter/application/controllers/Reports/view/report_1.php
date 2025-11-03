
<?php
$ids1 = [];

?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <?php
                $total_amount = 0;
                $actual_amount = 0;
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
                                <h5>Income Report By User (<?= $reporteeUser->name ?>).</h5>
                                <hr/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Bill Id</strong></td>
                                        <td><strong>Patient Name</strong></td>
                                        <td><strong>Service</strong></td>
                                        <td><strong>Doctor</strong></td>
                                        <td><strong>Amount</strong></td>
                                    </tr>
                                    <?php
                                    if ($this->aauth->is_allowed('CASH', 'Accounts')){
                                    ?>
                                    <tr>
                                        <td colspan="5"><strong>CASH Report</strong></td>
                                    </tr>
                                    <?php
                                    $total_amount = 0;
                                    $ctotal = 0;
                                    if(!empty($transactions)){
                                        foreach ($transactions as $row) {
                                            if($row['transaction']['payment_type'] == 'CASH'){
                                                $ids1[] = $row['transaction']['id'];
                                                $total_amount += $row['transaction']['amount_in_num'];
                                                $ctotal += $row['transaction']['amount_in_num'];
                                                ?>
                                                <tr>
                                                    <td><?= $row['transaction']['id'] ?></td>
                                                    <td><?= $row['patient']['pateint_name'].' (ID: '.$row['patient']['id'].')' ?></td>
                                                    <td>REF: <?= $row['transaction']['payment_refference'] == '' ? '--' : $row['transaction']['payment_refference'] ?> | SER: <?php
                                                        echo $services[$row['transaction']['service_id']]['name'];
                                                        ?></td>
                                                    <td><?php $user = $this->aauth->get_user($row['transaction']['doctor_id']); echo $user->name; ?></td>
                                                    <td><?= $row['transaction']['amount_in_num'] ?> PKR</td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>Cash Total</strong></td>
                                        <td><strong><?= $ctotal ?> PKR</strong></td>
                                    </tr>
                                    <?php }
                                    if ($this->aauth->is_allowed('CARD', 'Accounts')){
                                    ?>
                                    <tr>
                                        <td colspan="5"><strong>CARD REPORT</strong></td>
                                    </tr>
                                    <?php
                                    $ctotal = 0;
                                    if(!empty($transactions)){
                                        foreach ($transactions as $row) {
                                            if($row['transaction']['payment_type'] == 'CARD'){
                                                $ids1[] = $row['transaction']['id'];
                                                $total_amount += $row['transaction']['amount_in_num'];
                                                $ctotal += $row['transaction']['amount_in_num'];
                                                ?>
                                                <tr>
                                                    <td><?= $row['transaction']['id'] ?></td>
                                                    <td><?= $row['patient']['pateint_name'].' (ID: '.$row['patient']['id'].')' ?></td>
                                                    <td>REF: <?= $row['transaction']['payment_refference'] == '' ? '--' : $row['transaction']['payment_refference'] ?> | SER: <?php
                                                    echo $services[$row['transaction']['service_id']]['name'];
                                                    ?></td>
                                                    <td><?php $user = $this->aauth->get_user($row['transaction']['doctor_id']); echo $user->name; ?></td>
                                                    <td><?= $row['transaction']['amount_in_num'] ?> PKR</td>
                                                </tr>
                                                <?php
                                            }
                                        }


                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>Card Total</strong></td>
                                        <td><strong><?= $ctotal ?> PKR</strong></td>
                                    </tr>
                                    <?php }
                                    if ($this->aauth->is_allowed('CHEQUE', 'Accounts')){
                                    ?>
                                    <tr>
                                    <tr>
                                        <td colspan="5"><strong>CHECK Report</strong></td>
                                    </tr>
                                    <?php
                                    $ctotal = 0;
                                    if(!empty($transactions)){
                                        foreach ($transactions as $row) {
                                            if($row['transaction']['payment_type'] == 'CHECK'){
                                                $ids1[] = $row['transaction']['id'];
                                                $total_amount += $row['transaction']['amount_in_num'];
                                                $ctotal += $row['transaction']['amount_in_num'];
                                                ?>
                                                <tr>
                                                    <td><?= $row['transaction']['id'] ?></td>
                                                    <td><?= $row['patient']['pateint_name'].' (ID: '.$row['patient']['id'].')' ?></td>
                                                    <td>REF: <?= $row['transaction']['payment_refference'] == '' ? '--' : $row['transaction']['payment_refference'] ?> | SER: <?php
                                                    echo $services[$row['transaction']['service_id']]['name'];
                                                    ?></td>
                                                    <td><?php $user = $this->aauth->get_user($row['transaction']['doctor_id']); echo $user->name; ?></td>
                                                    <td><?= $row['transaction']['amount_in_num'] ?> PKR</td>
                                                </tr>
                                                <?php
                                            }
                                        }


                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>Check Total</strong></td>
                                        <td><strong><?= $ctotal ?> PKR</strong></td>
                                    </tr>
                                    <?php }
                                    ?>
                                    <tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>Total Amount</strong></td>
                                        <td><strong><?= $total_amount ?> PKR</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-xs-12 invoice-header">
                                <small>For administration use only.</small>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 50px;">
                            <div class="col-xs-12 invoice-block">
                                <a class="btn btn-lg blue hidden-print margin-bottom-5 print_button"> Clear This
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
                var ID1 = jQuery.parseJSON('<?= json_encode($ids1) ?>');

                jQuery.ajax({
                    method: "POST",
                    url: "<?= site_url('accounts/clear_current') ?>",
                    data: { patient_ids: JSON.stringify(ID1) },
                    success: function(data) {
                        console.log(data);
                    }
                })

            });
        })
    </script>
