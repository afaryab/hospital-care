
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
                            <span class="caption-subject bold uppercase"> Activity</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="temo"></div>
                        <table id="example" class="table table-bordered display" cellspacing="1" width="100%">
                            <thead>
                            <tr>
                                <th>Activity No</th>
                                <th>Message</th>
                                <?php if($this->aauth->is_member('ROLE_ADMIN') || $this->aauth->is_member('ROLE_SUPER_ADMIN') || $this->aauth->is_member('ROLE_DEVELOPER')){ ?>
                                    <th>User</th>
                                <?php } ?>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Activity No</th>
                                <th>Message</th>
                                <th>User</th>
                                <th>Date</th>
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
            "ajax": "<?= site_url($LIST_NOTIFICATIONS_JSON) ?>",
            "initComplete": function(){
                $('a[title]').tooltip();
            }
        } );

    })

</script>
