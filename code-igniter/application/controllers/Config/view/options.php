
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">

            <div class="col-md-12 border-special">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-users font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> <?php echo $title; ?></span>
                        </div>

                    </div>
                    <div class="portlet-body form">
                        <div class="col-sm-12">

                        <?php foreach ($licence as $key=>$row){ ?>

                        <div class="form-group">
                            <h3><?= ucwords(str_replace("_", " ", $key)) ?></h3>
                            <hr/>
                        </div>

                            <?php if(!is_string($row)){ ?>

                                <?php foreach ($row as $name=>$val){ ?>
                                    <?php
                                    $pos = strpos('id', $name);
                                    if ($pos === false) {
                                    ?>
                                            <div class="form-group">
                                                <label><?= ucwords(str_replace("_", " ", $name)) ?></label>
                                                <div class="form-control"><?= $val ?></div>
                                            </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php }else{
                                ?>
                                <div class="form-group">
                                    <div class="form-control"><?= $row ?></div>
                                </div>
                                <?php
                            } ?>

                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

