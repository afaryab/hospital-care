
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
                $test_to_be_share = [];
                if($test_id != 'all'){
                    foreach($tests as $doc){
                        if($doc['id'] == $test_id){
                            $test_to_be_share = $doc;
                        }
                    }
                }
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
                                <h5>Income Report By Laboratory <?= $test_id == 'all' ? '' : '"'.$test_to_be_share['name'].'"' ?>.</h5>
                                <hr/>
                                <h6><?= $date_range ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center"> Patient Transactions </td>
                                    </tr>
                                    <tr>
                                        <td>Id</td>
                                        <td>Original Amount</td>
                                        <td>Created On</td>
                                        <td class="text-right">Amount</td>
                                    </tr>
                                    <?php
                                    if(!empty($transactions1)) {
                                        foreach ($transactions1 as $row) {
                                            foreach($row['id'] as $key=>$ids){
                                                $ids1[] = $ids;
                                            }
                                            $total_amount += $row['bill_amount_figure'];
                                            ?>
                                            <tr>
                                                <td > <?= implode(',', $row['id']) ?> </td>
                                                <td><?= $row['actual_amount'] ?></td>
                                                <td><?= date('Y-m-d h:i a',strtotime($row['created_on'])) ?></td>
                                                <td class="text-right">
                                                    <strong><?= $row['bill_amount_figure'] ?></strong></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 invoice-block" style="text-align: center;margin: 5px 0;color: #fff;background-color: #000;padding: 5px;font-size: 24px;">Total: <?= $total_amount ?> Rs Only</div>
                            <div class="col-xs-12 invoice-block" style="text-align: center;margin: 5px 0;color: #fff;background-color: #000;padding: 5px;font-size: 24px;">Share Amount: <?= (50 * $total_amount)/100 ?> Rs Only</div>
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
            });
        })
    </script>
