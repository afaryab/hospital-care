
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
                                <h5>Treatment Name (<?= $diagnosisName ?>).</h5>
                                <hr/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Patient Age</strong></td>
                                        <td><strong>Patient Name</strong></td>
                                        <td><strong>Patient Contact</strong></td>
                                        <td><strong>Treatments</strong></td>
                                    </tr>
                                    <?php
                                        if(!empty($treatments)){
                                            foreach ($treatments as $row) {
                                                ?>

                                                <tr>
                                                    <td><?= $row['age_dob'] ?></td>
                                                    <td><?= $row['pateint_name'].' (ID: '.$row['id'].')' ?></td>
                                                    <td><?= $row['patient_contact_mobile'] ?></td>
                                                    <td>
                                                        <?php foreach ($row['treatments'] as $app){ ?>
                                                            <label class="label label-info"><?= $app['name'] ?></label>

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
