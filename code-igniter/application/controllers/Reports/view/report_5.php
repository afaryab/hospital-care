
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/global/fonts/stylesheet.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
$total_amount = 0;
?>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <?php
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
                                <h5>Income Report By Services.</h5>
                                <hr/>
                                <h6><?= $date_range ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-hover table-bordered">
                                    <tbody>
                                    <tr class="dark">
                                        <td colspan="6" class="text-center"> Patient Transactions </td>
                                    </tr>
                                    <tr class="gray">
                                        <td>Id</td>
                                        <td>Patient Name</td>
                                        <td>Doctor Name</td>
                                        <td>Units</td>
                                        <td>Created On</td>
                                        <td class="text-right">Amount</td>
                                    </tr>
                                    <?php
                                    if(!empty($transactions)) {
                                        foreach ($transactions as $row) {
                                            $total_amount += $row['transaction']['amount_in_num'];
                                            ?>
                                            <tr>
                                                <td > <?= $row['transaction']['id'] ?> </td>
                                                <td><?= $row['patient']['pateint_name'].' (ID: '.$row['patient']['id'].')' ?></td>
                                                <td><?php $doc = $this->aauth->get_user((int)$row['transaction']['doctor_id']); echo $doc->name; ?></td>
                                                <td><?= $row['transaction']['units'] == NULL ? 'N/A' : $row['transaction']['units'] ?></td>
                                                <td><?= date('Y-m-d h:i a',strtotime($row['transaction']['created_on'])) ?></td>
                                                <td class="text-right">
                                                    <strong><?= $row['transaction']['amount_in_num'] ?> PKR</strong></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr class="dark">
                                        <td colspan="4"></td>
                                        <td class="text-right"><strong>Total Amount</strong></td>
                                        <td class="text-right"><strong><?= $total_amount ?> PKR</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
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
