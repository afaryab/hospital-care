
<div class="page-content-wrapper">
    <div class="page-content row">
        <!-- BEGIN PAGE CONTENT-->
        <div class="">

            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered col-sm-12">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="icon-users font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> <?php echo $title; ?></span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post">
                        <?php
                            foreach ($groups as $group){
                                ?>
                                <div class="col-sm-3 border-group">
                                    <h3><?= $group['name'] ?></h3>
                                    <?php
                                    foreach ($permissions as $key=>$row){
                                        ?>
                                        <h5><?= $key ?></h5>
                                        <?php
                                        foreach ($row as $permission) {
                                            $identity = $group['id'] . '-' . $permission['id'];
                                            ?>

                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkbox<?= $identity ?>" class="md-check"
                                                       <?php if($this->aauth->is_group_allowed($permission['id'],$group['id'])){
                                                           ?>
                                                           checked="checked"
                                                           <?php
                                                       } ?>
                                                       name="is_<?= $identity ?>" title="<?= $permission['name'] ?>">
                                                <label for="checkbox<?= $identity ?>">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>
                                                    <?= $permission['name'] ?></label>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <h3>&nbsp;</h3>
                                    <script>
                                        $('#checkbox<?= $group['id'] ?>-1').change(function () {
                                            if ($(this).prop('checked')) {
                                                $(this).closest('.col-sm-3').find('input[type="checkbox"]').prop('checked', true);
                                            } else {
                                                $(this).closest('.col-sm-3').find('input[type="checkbox"]').prop('checked', false);
                                            }
                                        });
                                        $('#checkbox<?= $group['id'] ?>-1').closest('.col-sm-3').find('input[type="checkbox"]').change(function () {
                                            if ($(this).prop('checked')) {

                                                var status = true;
                                                $(this).closest('.col-sm-3').find('input[type="checkbox"]:not("#checkbox<?= $group['id'] ?>-1")').each(function () {
                                                    if (!$(this).prop('checked')) {
                                                        status = false

                                                    }
                                                })
                                                if(status == true){
                                                    $(this).closest('.col-sm-3').find('#checkbox<?= $group['id'] ?>-1').prop('checked', true);
                                                }
                                            } else {
                                                $(this).closest('.col-sm-3').find('#checkbox<?= $group['id'] ?>-1').prop('checked', false);
                                            }
                                        });
                                    </script>
                                </div>
                                <?php
                            }
                        ?>
                            <div class="col-sm-12 text-right">
                                <br/>
                            <input type="submit" name="updatePermissions" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
