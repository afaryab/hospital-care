
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/datatables/media/css/jquery.dataTables_themeroller.css') ?>"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                            <span class="caption-subject bold uppercase"> Export Database</span>
                        </div>
                        
  
                    </div>
                    <div class="portlet-title" >
                    <div class="text-center">
                        <?php if($this->aauth->is_allowed('Import Export', 'Advance')){ ?>
                            <a href="<?= site_url($EXPORT_DATABASE) ?>" class="btn btn-success" ><i class="fa fa-download"></i> Export  </a>
                        <?php } ?>
                    <div>
  
                    </div>

                    
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-settings font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> Import Database</span>
                        </div>
                        
  
                    </div>
                    <div class="portlet-body form">
                        <form role="form" method="POST">
                            <div class="form-body">
                                <div class="portlet-title form-group">
                                <div class="col-md-4">
                        </div>
                                        <div class="text-center">
                                            <input type="file" name="file" id="file" class="input-large text-center"></input>
                                        </div>
                                </div>
                                <div class="portlet-title" >
                                    <div class="text-center form-actions">
                                        <?php if($this->aauth->is_allowed('Import Export', 'Advance')){ ?>
                                            <a href="<?= site_url($IMPORT_DATABASE) ?>" type="submit" class="btn btn-success" ><i class="fa fa-upload"></i> Import  </a>
                                        <?php } ?>
                                    <div>
        
                                </div>
                            </div>
                        </form>
                    </div>

                    
                    
                </div>
            </div>
        </div>
    </div>
</div>

