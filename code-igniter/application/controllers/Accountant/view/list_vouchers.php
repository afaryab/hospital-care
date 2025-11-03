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
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <i class="fab fa-superpowers font-red-sunglo"></i>
                                <span class="caption-subject bold uppercase"> Expense Vouchers</span>
                            </div>
                            <a href="<?= site_url($ADD_INP_VOUCHER) ?>" class="btn btn-success pull-right" style="display:inline-block;margin-left:5px"><i class="far fa-plus-square"></i> New Inpatient Voucher</a>
                            <a href="<?= site_url($ADD_VOUCHER) ?>" class="btn btn-success pull-right"><i class="far fa-plus-square"></i> New Voucher</a>
                        </div>
                        <div class="portlet-body form">
                            <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        if(!empty($transactions)) {
                            foreach ($transactions as $row) {
                                $total_amount += $row['exp_amount_numbers'];
                                ?>
                                <tr>
                                    <td> <?= $row['id'] ?> </td>
                                    <td><?= $row['exp_amount_numbers'] ?></td>
                                    <td><?= $row['expense_notes'] ?></td>
                                    <td class="text-right">
                                        <a class="btn btn-sm btn-primary" href="<?= site_url($PRINT_EXPENSE_TOKEN_URL.$row['id']) ?>" target="_blank">Print Vocuher</a>
                                        <a class="btn btn-sm btn-primary" href="<?= site_url($EDIT_VOUCHER.$row['id']) ?>" target="_blank">Edit Vocuher</a>

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