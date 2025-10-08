
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
                            <span class="caption-subject bold uppercase">Health Card Patients</span>
                        </div>
                        
                       

                    </div>
                    <div class="portlet-body form">
                        <div class="temo"></div>
                        <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Contact No</th>
                                <th>Antenatal Status</th>
                                <th>Last Visit</th>
                                <th>Created On</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>CNIC</th>
                                <th>Contact No</th>
                                <th>Antenatal Status</th>
                                <th>Last Visit</th>
                                <th>Created On</th>
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
            "ajax": "<?= site_url($HEALTHCARD_PATIENTS_JSON_URL) ?>",
            "initComplete": function(){
                $('a[title]').tooltip();
            },
            "order": [
                [ 0, "desc" ],
                [ 1, "desc" ]
            ]
        } );

    })

</script>