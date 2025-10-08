<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
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
                $actual_amount = 0;
                ?>
                <div class="col-md-8 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="icon-settings font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Transactions</span>
                            </div>
                            <a href="<?= site_url($OPD_CLOSING_PRINT) ?>" class="btn btn-success pull-right">Closing Time</a>
                        </div>
                        <div class="portlet-body form">
                            <table id="example" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    if(!empty($counter_transactions)) {
                                        foreach ($counter_transactions as $row) {
                                            $total_amount += $row['amount'];
                                            ?>
                                            <tr>
                                                <td> <?= $row['id'] ?> </td>
                                                <td><?= $row['amount'] ?></td>
                                                <td><?= $row['income_or_expence'] ?></td>
                                                <td><?= $row['type'] ?></td>
                                                <td class="text-right">
                                                    <a href="<?= site_url('/Hospital/Reception/PrintReciept/Index/'.$row['id']) ?>" target="_blank">Print Bill</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 invoice in_sm" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col-xs-12 text-center invoice-header">
                            <img class="img-responsive logo" style="display: inline-block; margin: 10px 0;max-width: 100px;" src="<?= $PRINT_IMAGE_64; ?>">
                            <hr style="border-top: 3px solid #000;"/>
                            <h3>-COUNTER STATEMENT-</h3>
                            <hr style="border-top: 3px solid #000;"/>
                        </div>
                        <div class="col-xs-12 invoice-payment">

                            <h3 class="text-center"><?= str_pad($counter['id'], 11, '0', STR_PAD_LEFT); ?></h3>

                            
                            <table class="table">
                                <tr>
                                    <td class="text-center" colspan="2"><strong>Transactions</strong></td>
                                </tr>
                                <?php if(!empty($counter_transactions)){ ?>

                                    <tr>
                                        <td><strong>Number</strong></td>
                                        <td><strong>Amount</strong></td>
                                    </tr>
                                    <?php
                                    foreach ($counter_transactions as $payment){
                                        
                                        ?>
                                        <tr>
                                            <td><?= $payment['id'] ?></td>
                                            <td><?= $payment['amount'] ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <tr>
                                        <td colspan="2">NILL</td>
                                    </tr>
                                <?php } ?>
                                
                                <tr style="border-top: 3px solid #000;">
                                    <td><strong class="text-danger">Opening Cash: </strong></td><td><?= $counter['opening_amount'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong class="text-danger">Opening Cash: </strong></td><td><?= $counter['closing_amount_cash'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong class="text-warning">Opening Bank: </strong></td><td><?= $counter['closing_amount_card'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong class="text-warning">Opening Card: </strong></td><td><?= $counter['closing_amount_creditcard'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong class="text-success">Closing Cheque: </strong></td><td><?= $counter['closing_amount_atm'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong class="text-success">Total: </strong></td><td><?= $counter['closing_amount'] ?></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center invoice-header">
                            <hr style="border-top: 3px solid #000;"/>
                            <small style="font-size: 10px;"><?= business_contact_numbers ?></small>
                            <p style="font-size: 10px;"><?= business_contact_address ?></p>
                            <hr style="border-top: 3px solid #000;"/>
                            <p style="font-size: 10px;"> Powered By: Processton.com</p>
                            <p style="font-size: 10px;"> info@processton.com - +923061105155</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo base_url('public/scripts/jquery.dataTables.min.js') ?>"></script>

    <script>
        jQuery(function(){
            $('#example').DataTable({
                "order": [[ 0, "desc" ]]
            });
        })
    </script>
