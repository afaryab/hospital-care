
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/global/fonts/stylesheet.css') ?>"/>
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
                                <h5>Date (<?= $dateRange ?>).</h5>
                                <hr/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-bordered">
                                    <tr class="dark">
                                        <td><strong>Patient Name</strong></td>
                                        <td><strong>Appointments</strong></td>
                                        <td><strong>Schedules</strong></td>
                                    </tr>
                                    <?php
                                    if(!empty($rep_appointments)){
                                        foreach ($rep_appointments as $row) {
                                            ?>
                                            <tr>
                                                <td><?= $row['pateint_name'].' (ID: '.$row['id'].')' ?></td>
                                                <td><?= count($row['appointments']) ?></td>
                                                <td>
                                                    <?php foreach ($row['appointments'] as $app){ ?>
                                                        <?php if($app['status'] == 1){ ?>
                                                            <label class="label label-success"><?= $app['start_date'] ?></label>
                                                        <?php } ?>
                                                        <?php if($app['status'] == 2){ ?>
                                                            <label class="label label-info"><?= $app['start_date'] ?></label>
                                                        <?php } ?>
                                                        <?php if($app['status'] == 3){ ?>
                                                            <label class="label label-danger"><?= $app['start_date'] ?></label>
                                                        <?php } ?>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <?php
                                        }


                                    }
                                    ?>
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
