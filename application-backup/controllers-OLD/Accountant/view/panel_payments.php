
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
                            <span class="caption-subject bold uppercase"> Panel Payments</span>
                            
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="temo"></div>
                        <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                            <thead>
                            <tr>
                                <th style="text-align: center;">ID</th>
                                <th style="text-align: center;">MR No</th>
                                <th style="text-align: center;">Name</th>
                                <th style="text-align: center;">Company</th>
                                <th style="text-align: center;">Amount Recieved</th>
                                <th style="text-align: center;">Cheque No.</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                            </thead>
                            
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
            "ajax": "<?= site_url($PANEL_PAYMENTS_JSON) ?>",
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
