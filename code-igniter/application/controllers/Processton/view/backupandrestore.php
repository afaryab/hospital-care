
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
                            <span class="caption-subject bold uppercase"> OPD Patients</span>
                        </div>
                        <?php if($this->aauth->is_allowed('Create Patient', 'OPD Reception')){ ?>
                            <a href="<?= site_url($CREATE_OPD_PATIENT) ?>" class="btn btn-success pull-right">Create Patient</a>
                        <?php } ?>
                    </div>
                    <div class="portlet-body form">
                        <div class="temo"></div>
                        <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                            <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Last Visit</th>
                                <th>Next Appointment</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Sr</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Last Visit</th>
                                <th>Next Appointment</th>
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
            "ajax": "<?= site_url($OPD_PATIENTS_JSON_URL) ?>",
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
