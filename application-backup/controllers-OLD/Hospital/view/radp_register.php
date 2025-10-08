
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/datatables/media/css/jquery.dataTables_themeroller.css') ?>"/>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> OPD REGISTER</span>
                            
                        </div>
                        <?php if($this->aauth->is_allowed('Create Patient', 'OPD Reception')){ ?>
                            <a href="<?= site_url($ADD_NEW_TREATMENT) ?>" class="btn btn-success pull-right">Create Treatment</a>
                        <?php } ?>
                    </div>
                    <div class="portlet-body form">
                        <div class="temo"></div>
                        <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                            <thead>
                            <tr>
                                <th>Treatment ID</th>
                                <th>Doctor</th>
                                <th>Patient Name</th>
                                <th>Contact</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Scheduled On</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Treatment ID</th>
                                <th>Doctor</th>
                                <th>Patient Name</th>
                                <th>Contact</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Scheduled On</th>
                                <th>Actions</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('public/plugins/datatables/all.min.js') ?>"></script>
<script>
    jQuery(function(){
        var table = $('#example').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "<?= site_url($HOSPITAL_REC_RAD_REGISTER_JSON_URL) ?>",
            "initComplete": function(){
                $('a[title]').tooltip();
            },
            "order": [
                [ 3, "desc" ],
                [ 4, "desc" ]
            ]
        } );

    })

</script>
