<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/jquery.dataTables.min.css') ?>"/>
<?php
$ids1 = [];
$ids2 = [];
?><!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- <div class="page-bar">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-settings font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Report Filter </span>
                    </div>
                    
                    
                </div>
                <div class="portlet-body form">
                    <form method="GET">
                        <div class="form-group">
                            <div class="input-icon"  id="defaultrange_modal">
                                <label for="form_control_1">Report Duration</label>
                                <input type="text" class="form-control" name="date_range">
                                <input type="hidden" name="dtype" value="R">
                                <span class="help-block">you can change report duration from here.</span>
                                <i class="fa fa-bell-o"></i>
                            </div>
                        </div>
                        <div class="form-group has-success">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
        
            <div class="col-md-12 ">
                        <!-- BEGIN SAMPLE FORM PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="font-red-sunglo">
                                    <h3 class="caption-subject bold uppercase"><i class="fas fa-pawn fa-2x m-r-2"></i>  Counter Recieving </h3>
                                    <h4 class="caption-subtitle uppercase m-t-4"> Date: <?php if(is_array($date)){ echo date('d-m-Y', strtotime($date['start'])).' <strong>TO</strong> '.date('d-m-Y h:i s', strtotime($date['end'])); }else{ echo date('d-m-Y', strtotime($date)); } ?> </h4>
                                </div>
                                
                                
                            </div>
                            <div class="portlet-body form">
                                <div class="content">
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table id="example" class="table table-bordered table-responsive table-stipped" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Status</th>
                                                        <th>Recieving Status</th>
                                                        <th>Closing Amount</th>
                                                        <th>Recieved Amount</th>
                                                        <th>Difference</th>
                                                        <th>Cloasing Date and Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <?php
                                                    $counter = 0;
                                                    if(!empty($counterrecieving)) {
                                                        foreach ($counterrecieving as $row) {
                                                            if($row['status']=="CLOSED"){
                                                                $counter++;
                                                            ?>
                                                            <?php if($row['cash_recieved_amount']<$row['closing_amount'] && $row['is_cash_recieved']==1){ ?>
                                                                <tr style="background-color: #E26A6A;">
                                                                    <td> <?= $row['id'] ?> </td>
                                                                    <?php 
                                                                    foreach ($users as $user) {
                                                                        if($row['user_id'] == $user->id){
                                                                    ?>
                                                                        <td><?= $user->name ?></td>
                                                                    <?php }
                                                                    }
                                                                    ?>
                                                                    <td><?= $row['status'] ?></td>
                                                                    <td><?php if($row['is_cash_recieved']==1){ ?> Recieved <?php }else{ ?> Not Submitted <?php } ?></td>
                                                                    <td><?= $row['closing_amount'] ?></td>
                                                                    <td><?= $row['cash_recieved_amount'] ?></td>
                                                                    <td><?= $row['cash_recieving_difference'] ?></td>
                                                                    <td><?= $row['created_on'] ?></td>
                                                                    <td>
                                                                        <a class="btn btn-primary"  href="<?= site_url($REC_TRANS.$row['id']) ?>">Summary</a>
                                                                        <?php if($row['is_cash_recieved']==1){ ?> 
                                                                            <a class="btn btn-primary"  href="<?= site_url('/Accountant/EditCounterRecievings/Index/'.$row['id']) ?>">Edit</a>
                                                                        <?php }else{ ?>    
                                                                            <a class="btn btn-primary"  href="<?= site_url('/Accountant/AddCounterRecievings/Index/'.$row['id']) ?>">Add</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <!-- <td><a class="btn btn-primary"  href="">Summary</a></td> -->

                                                                </tr>
                                                                <?php }else{ ?>
                                                                    <tr >
                                                                        <td> <?= $row['id'] ?> </td>
                                                                        <?php 
                                                                        foreach ($users as $user) {
                                                                            if($row['user_id'] == $user->id){
                                                                        ?>
                                                                            <td><?= $user->name ?></td>
                                                                        <?php }
                                                                        }
                                                                        ?>
                                                                        <td><?= $row['status'] ?></td>
                                                                        <td><?php if($row['is_cash_recieved']==1){ ?> Recieved <?php }else{ ?> Not Submitted <?php } ?></td>
                                                                        <td><?= $row['closing_amount'] ?></td>
                                                                        <td><?= $row['cash_recieved_amount'] ?></td>
                                                                        <td><?= $row['cash_recieving_difference'] ?></td>
                                                                        <td><?= $row['created_on'] ?></td>
                                                                        <td>
                                                                            <a class="btn btn-primary"  href="<?= site_url($REC_TRANS.$row['id']) ?>">Summary</a>
                                                                            <?php if($row['is_cash_recieved']==1){ ?> 
                                                                                <a class="btn btn-primary"  href="<?= site_url('/Accountant/EditCounterRecievings/Index/'.$row['id']) ?>">Edit</a>
                                                                            <?php }else{ ?>    
                                                                                <a class="btn btn-primary"  href="<?= site_url('/Accountant/AddCounterRecievings/Index/'.$row['id']) ?>">Add</a>
                                                                            <?php } ?>
                                                                        </td>

                                                                    </tr>
                                                                <?php } ?>
                                                            <?php
                                                            }
                                                        } ?> 
                                                        
                                                        
                                                        <?php
                                                    }
                                                    if($counter == 0){ ?>
                                                        <tr class="bg-danger">
                                                            <td colspan="9">No activity during this time.</td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
    // $(function(){

    //     $('#defaultrange_modal').daterangepicker({
                
    //             opens: 'left',
    //             format: 'MM/DD/YYYY',
    //             singleDatePicker: true,
    //             separator: ' to ',
    //             startDate: moment(),
    //             //autoApply : true,
                
    //         },
    //         function (start, end) {
    //             $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    //         }
    //     );
    // })
</script>
<style>
    .fa-2x {
        font-size: 1em !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>"/>
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />