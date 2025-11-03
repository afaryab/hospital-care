

    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="fab fa-audible font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase">Amount Receivable (<?= $receaveable ?> PKR)</span>
                            </div>

                        </div>
                        <div class="portlet-body">
                            <table id="example" class="table table-responsive table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Patient Contact</th>
                                    <th>Treatment Total</th>
                                    <th>Amount Paid</th>
                                    <th>Pending Payment</th>
                                    <th>Detail</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Patient Contact</th>
                                    <th>Treatment Total</th>
                                    <th>Amount Paid</th>
                                    <th>Pending Payment</th>
                                    <th>Detail</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                <?php $total_pending = 0; ?>
                                <?php foreach($patients as $patient){ ?>
                                    <tr>
                                        <td><?= $patient['id'] ?></td>
                                        <td><?= $patient['name'] ?></td>
                                        <td><?= $patient['contact'] ?></td>
                                        <td><?= $patient['treatments'] ?></td>
                                        <td><?= $patient['amount_payed'] ?></td>
                                        <td><?= $patient['pending'] ?></td>
                                        <td><a class="btn btn-default" href="<?= site_url($OPD_PATIENT_PROFILE.$patient['id']) ?>" target="_blank" >Info</a><a class="btn btn-primary" href="<?= site_url($OPD_PATIENT_HISTORY_PRINT.$patient['id']) ?>" target="_blank" >History</a></td>
                                    </tr>
                                    <?php $total_pending = $total_pending + $patient['pending']; } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- END SAMPLE FORM PORTLET-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
