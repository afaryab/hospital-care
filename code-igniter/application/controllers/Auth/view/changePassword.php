
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
            	<?php if(!empty($bc)){?>
                	<?php foreach ($bc as $l){ ?>
                    <li>
                        <i class="<?= $l['icon'] ?>"></i>
                        <span><a href="<?= site_url($l['url']) ?>"><?= $l['name'] ?></a></span>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <?php } ?>
                <?php } ?>
            </ul>

        </div>
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12 ">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption font-red-sunglo">
                                <span class="caption-subject bold uppercase"> <?= $title ?></span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form role="form" method="POST">
                                <div class="form-body">
                                    <div class="row">                                    
                                    	<?php if($currentUser->id == $user->id) {?>
                                    	<div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="password" class="form-control" placeholder="New Password" name="c_password" value="" required>
                                                    <label for="form_control_1">Current Password</label>
                                                    <span class="help-block">Please Enter Current Password!.</span>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="password" class="form-control" placeholder="New Password" name="password" value="" required>
                                                    <label for="form_control_1">New Password</label>
                                                    <span class="help-block">Please Enter New Password!.</span>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input has-success">
                                                <div class="input-icon">
                                                    <input type="password" class="form-control" placeholder="Confirm Password" name="conf_pass" value="" required>
                                                    <label for="form_control_1">Confirm Password</label>
                                                    <span class="help-block">Please Confirm New Password</span>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions noborder text-right">
                                    <button type="submit" class="btn blue">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- END SAMPLE FORM PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>
