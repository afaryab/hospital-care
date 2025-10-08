
<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title><?= (isset($title) && $title != '' ? $title : $business_name).' | '. $location_name ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="Processton Client Framework" name="description"/>
    <meta content="Processton Client" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?php echo base_url('public/fonts/fa5/css/all.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/fonts/Barlow/css.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/simple-line-icons/simple-line-icons.min.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/uniform/css/uniform.default.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/bootstrap-switch/css/bootstrap-switch.min.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/select2/select2.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/plugins/chart.js/Chart.css') ?>" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?php echo base_url('public/css/components.css') ?>" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/css/plugins.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/css/layout.css') ?>" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="<?php echo base_url('public/css/theme.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/css/custom.css') ?>" rel="stylesheet" type="text/css"/>
    <script src="<?php echo base_url('public/plugins/jquery.min.js') ?>" type="text/javascript"></script>
    <link href="<?= base_url('public/plugins/pnotify/pnotify.custom.min.css') ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?= base_url('public/plugins/pnotify/pnotify.custom.min.js') ?>" ></script>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="<?= base_url('web/favicon.ico') ?>"/>
    <script type="application/javascript">
        $(function(){
            <?php if(!empty($error)){ ?>
        		<?php foreach ($error as $i=>$e){ ?>
        		errorMsg("<?= $e ?>");
        		<?php }?>    
            <?php } ?>
            <?php if(!empty($warning)){ ?>
            	<?php foreach ($warning as $i=>$w){ ?>
            	warningMsg("<?= $w ?>");
            	<?php }?>    
            <?php } ?>
            <?php if(!empty($success)){ ?>
                <?php foreach ($success as $i=>$s){ ?>
                successMsg("<?= $s ?>");
                <?php }?>    
            <?php } ?>
        });
       
        function successMsg(msg, description = ''){
            var myStack = {"dir1":"down", "dir2":"right", "push":"top"};
            new PNotify({
                title: msg,
                type: "success",
                text: description,
                styling: "bootstrap3",
                desktop: {
                    history: true,
                    desktop: true,
                    fallback: true
                }
            })
        }
        function errorMsg(msg, description = ''){
            var myStack = {"dir1":"down", "dir2":"right", "push":"top"};
            new PNotify({
                title: msg,
                text: description,
                type: "error",
                styling: "bootstrap3",
                desktop: {
                    history: true,
                    desktop: true,
                    fallback: true
                }
            })
        }
        function infoMsg(msg, description = ''){
            var myStack = {"dir1":"down", "dir2":"right", "push":"top"};
            new PNotify({
                title: msg,
                text: description,
                type: "info",
                styling: "bootstrap3",
                desktop: {
                    history: true,
                    desktop: true,
                    fallback: true
                }
            })
        }
        function warningMsg(msg, description = ''){
            var myStack = {"dir1":"down", "dir2":"right", "push":"top"};
            new PNotify({
                title: msg,
                text: description,
                type: "warning",
                styling: "bootstrap3",
                desktop: {
                    history: true,
                    desktop: true,
                    fallback: true
                }
            })
        }
    </script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed page-quick-sidebar-over-content ">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo text-center" style="padding-right: 5px;background-color: #FCFCFC;">
            <a href="#" style="float: none;">
                <img src="<?= base_url('processton.png') ?>" class="img-responsive" style="height: 40px; margin-top: 4px;">
            </a>
            <div class="menu-toggler sidebar-toggler" >
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
<!--                <a href="javascript:;" >-->
                    <i class="fas fa-bars" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"></i>
<!--                </a>-->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <i class="fas fa-bars"></i>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown">
                    <a onclick="discturctionCall()" id="myModal_destruction_call">
                        <i class="fas fa-recycle"></i>
                    </a>
                </li>
				<li class="dropdown dropdown-extended quick-sidebar-toggler">
                    <a id="showAppointment">
                        <i class="fas fa-calendar"></i>
                    </a>
                </li>
                <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                    <a href="javascript:;"  class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="fas fa-dot-circle"></i>
                    </a>
                    <ul class="dropdown-menu">
                        
                        <?php foreach ($notifications as $notification){  ?>
                            <li class="text-left notificationItem">
                                
                                <span class="photo">
                                    <img width="30" height="30" src="<?= base_url($notification['user']->profile_img_path) ?>" class="img-circle" alt="">
                                </span>
                                <small class="message"><?= $notification['content'] ?></small>
                                
                            </li>
                        <?php } ?>
                        <li class="external">
                            <h3><span class="bold"><a href="<?= site_url($LIST_NOTIFICATIONS) ?>" target="_blank">Click</a></span> to view all notifications</h3>

                        </li>
                    </ul>
                </li>
                <?php if(isset($counter) && !empty($counter)){ ?>
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">

                        <span class="username username-hide-on-mobile">
                        <?php
                            echo $counter['opening_amount'] ."/".$counter['closing_amount'];
                            if($counter['opening_amount'] > $counter['closing_amount']){ ?>
                                <i class="fas fa-arrow-down"></i> 
                            <?php } else if($counter['opening_amount'] < $counter['closing_amount']){ ?>
                                <i class="fas fa-arrow-up"></i>
                            <?php } else if($counter['opening_amount'] == $counter['closing_amount']){ ?>
                                <i class="far fa-dot-circle"></i>
                            <?php } ?>
                        &nbsp;&nbsp;</span>
                            <i class="fas fa-sort-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default" style="width:400px;">
                            <li style="width:100%;" class="m-t-2 m-b-2 container">
                                <div class="row">
                                    <div class="col-md-6 text-right">
                                        <canvas id="receptionchart"></canvas>
                                    </div>
                                    <div class="col-md-6 text-left m-t-4">
                                        <p><stong class="text-danger">Opening Cash: </stong><?= $counter['opening_amount'] ?></p>
                                        <p><stong class="text-danger">Current Cash: </stong><?= $counter['closing_amount_cash'] ?></p>
                                        <p><stong class="text-warning">Current Bank: </stong><?= $counter['closing_amount_card'] ?></p>
                                        <p><stong class="text-warning">Current Card: </stong><?= $counter['closing_amount_creditcard'] ?></p>
                                        <p><stong class="text-success">Current Cheque: </stong><?= $counter['closing_amount_atm'] ?></p>

                                    </div>
                                    
                                    <div class="col-md-12 text-left">
                                        <canvas id="receptionbarchart"></canvas>
                                    </div>
                                </div>
                                
                                
                            </li>
                            <li>
                                <a href="<?= site_url($CLOSE_COUNTER) ?>" class="btn btn-warning" stylw="margin-top:20px">
                                    <i class="fas fa-store-slash"></i> Close Counter
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle profileImg" src="<?php echo base_url($currentUser->profile_img_path) ?>"/>

                    <span class="username username-hide-on-mobile">
					<?php
                    echo $currentUser->name;
                    ?>&nbsp;&nbsp;</span>
                        <i class="fas fa-sort-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                    	<?php if(!empty($top_nav)){
                    	    
                    	    uasort($top_nav, 'sortArrayByOrder');
                    	    foreach ($top_nav as $nav){ ?>
                        <li>
                            <a href="<?php echo site_url($nav['path']); ?>">
                                <i class="<?= $nav['icon']?>"></i><?= $nav['label']?></a>
                        </li>
                        <?php } }?>
                    </ul>
                </li>
                <li class="dropdown dropdown-quick-sidebar-toggler">
                    <a href="javascript:;" class="dropdown-toggle">
                        <i class="icon-logout"></i>
                    </a>
                </li>

            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">

            <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <li class="start text-center">
                    <img src="<?= $APP_LOGO_IMAGE_64 ?>" class="img-responsive company-logo">
                </li>
                <?php if($currentUser->email!="drnadeemcounter@hamzahospital.com" && $currentUser->email!="drnadeemsheikh@hamzahospital.com"){ ?>
                    <?php foreach ($navigations as $group => $nav){?>
                        <?php if(count($nav) > 0){ ?>
                            <?php $groupName = explode('|',$group); ?>
                            <li data-length="<?= json_encode($nav) ?>" class="divider"><a><small><?= $groupName[0]; ?></small><short><?= array_key_exists(1,$groupName) ? $groupName[1] : $groupName[0] ?></short></a></li>
                            <?php foreach ($nav as $link){?>
                                <?php $perGroup = array_key_exists('perm_group', $link) ? $link['perm_group'] : 'ANONYMOUS'; ?>
                                <?php
                                $show = FALSE;
                                if ($link['perm'] == '' || $link['perm'] == 'all' ||$this->aauth->is_allowed($link['perm'], $groupName[0])) {
                                    $show = TRUE;
                                }
                                
                                if(array_key_exists('user_config',$link) && $link['user_config'] != ''){
                                    $p = explode('|',$link['user_config']);
                                    foreach($p as $pc){
                                        if($logininUser->$pc == 1){
                                            $show = TRUE;
                                        }    
                                    }
                                    
                                }
                                if($show == TRUE){
                                ?>
                                    <li class="<?= array_key_exists('module', $link) && isset($module) && $link['module']  == $module ? 'active' : '' ?> <?= array_key_exists('module', $link) && isset($p_module) && $link['module']  == $p_module ? 'active' : '' ?>">
                                        <a href="<?= site_url($link['path']) ?>">
                                            <i class="<?= $link['icon'] ?>"></i>
                                            <span class="title"><?= $link['label'] ?></span>
                                            <span class="fas fa-caret-right right-arrow"></span>
                                        </a>
                                        <?php if(array_key_exists('children', $link)){ ?>
                                            <ul class="secendorybar">
                                            
                                                <?php foreach ($link['children'] as $link2){?>
                                                    <?php $perGroup2 = array_key_exists('perm_group', $link2) ? $link2['perm_group'] : 'ANONYMOUS'; ?>
                                                    <?php
                                                        $show2 = FALSE;
                                                        if ($link2['perm'] == '' || $link2['perm'] == 'all' ||$this->aauth->is_allowed($link2['perm'], $groupName[0])) {
                                                            $show2 = TRUE;
                                                        }
                                                        
                                                        if(array_key_exists('user_config',$link2) && $link2['user_config'] != ''){
                                                            $p = $link2['user_config'];
                                                            if($logininUser->$p == 1){
                                                                $show2 = TRUE;
                                                            }else{
                                                                $show2 = FALSE;
                                                            }
                                                            
                                                        }
                                                        if($show2 == TRUE){
                                                        ?>
                                                            <li class="<?= array_key_exists('module', $link2) && isset($module) && $link2['module']  == $module ? 'active' : '' ?>">
                                                                <a href="<?= site_url($link2['path']) ?>">
                                                                    <i class="<?= $link2['icon'] ?>"></i>
                                                                    <span class="title"><?= $link2['label'] ?></span>
                                                                    <span class="fas fa-caret-right right-arrow"></span>
                                                                </a>
                                                                <?php if(array_key_exists('chilren', $link2)){ ?>

                                                                <?php } ?>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                    <?php } ?>
                <?php }elseif($currentUser->email=="drnadeemcounter@hamzahospital.com"){ ?>
                    <li class="">
                        <a href="<?= site_url('Hospital/Reception/Counters/OpdCounter/Index'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">OPD Counter</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                    <li class="">
                        <a href="<?= site_url('Hospital/Reception/EditPayment/TransactionId/Index/'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">Edit Transaction</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                    <li class="">
                        <a href="<?= site_url('Hospital/Reception/Edit/EditPatient/Index/'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">Edit Patient</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                    <li class="">
                        <a href="<?= site_url('Reception/PrintRecieptDuplicate/Index/'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">Reciept Print</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                    <li class="">
                        <a href="<?= site_url('Accountant/AddNewVoucher'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">Expense Payment</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                    
                <?php }elseif($currentUser->email=="drnadeemsheikh@hamzahospital.com"){ ?>
                    <li class="">
                        <a href="<?= site_url('Accountant/MyStatement'); ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span class="title">My Statement</span>
                            <span class="fas fa-caret-right right-arrow"></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
    <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
        <div class="page-quick-sidebar">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab" aria-expanded="false"> <i class="fas fa-calendar"></i>
                        <?php if(count($my_appointments) != 0){ ?>
                            <span class="badge badge-warning"><?= count($my_appointments['today']) ?></span>
                        <?php } ?>
                    </a>
                </li>

            </ul>
            <div class="tab-content">
                <div class="page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                    <div class="page-quick-sidebar-list" style="position: relative; overflow: hidden; width: auto; height: 597px;"><div class="page-quick-sidebar-chat-users" data-rail-color="#ddd" data-wrapper-class="page-quick-sidebar-list" data-height="597" data-initialized="1" style="overflow: hidden; width: auto; height: 597px;">
                            <h3 class="list-heading">Today</h3>
                            <ul class="media-list list-items">
                                <?php if(!empty($my_appointments['today'])){ ?>
                                    <?php foreach ($my_appointments['today'] as $today){ ?>
									
                                    <li class="media">
                                        <h4 class="media-heading"><?= $today['doctor']->name ?></h4>
                                        <div class="media-heading-sub">P: <?= $today['patient']['pateint_name'] .'<a class="pull-right" title="'. $today['start_date'] .'">'. nicetime($today['start_date']).'</a>' ?></div>
                                    </li>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <li class="media">
                                        <h4 class="media-heading text-center">No Appointments Today</h4>
                                    </li>
                                <?php } ?>
                            </ul>
                            <h3 class="list-heading">Upcomming</h3>
                            <ul class="media-list list-items">
                                <?php if(!empty($my_appointments['upcoming'])){ ?>
                                    <?php foreach ($my_appointments['upcoming'] as $today){ ?>
                                        <li class="media">
                                            <div class="media-body">
                                                <h4 class="media-heading"><?= $today['doctor']->name ?></h4>
                                                <div class="media-heading-sub">P: <?= $today['patient']['pateint_name'] .'<a class="pull-right" title="'. $today['start_date'] .'">'. nicetime($today['start_date']).'</a>' ?></div>
                                            </div>
                                        </li>

                                    <?php } ?>
                                <?php }else{ ?>
                                    <li class="media">
                                        <h4 class="media-heading text-center">No Upcoming Appointments</h4>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div><div class="slimScrollBar" style="background: rgb(187, 187, 187); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px; height: 440.555px;"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(221, 221, 221); opacity: 0.2; z-index: 90; right: 1px;"></div></div>

                </div>

            </div>
        </div>
    </div>
    <a href="javascript:;" class="page-quick-sidebar-toggler">
        <i class="icon-login"></i>
    </a>
	<?= $html ?>
</div>
<!-- END CONTAINER -->

<?php foreach ($models as $key => $model){ ?>
    <div class="modal fade" id="myModal_<?= $key ?>" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <?php if($model['footer'] != ''){ ?>
                    <div class="modal-header">
                        <i class="fa fa-close pull-right closeModel" data-dismiss="modal"></i>
                        <?= $model['title'] ?>
                    </div>
                <?php } ?>
                <?php if($model['footer'] != ''){ ?>
                    <div class="modal-body">
                        <?= $model['body'] ?>
                    </div>
                <?php } ?>
                <?php if($model['footer'] != ''){ ?>
                    <div class="modal-footer">
                        <?= $model['footer'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#myModal_<?= $key ?>').modal();
        })
    </script>
<?php } ?>
<div class="modal fade" id="myModal_destruction" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form method="post" action="<?= site_url('Config/Installer/Database/Install/-f') ?>">
            <div class="modal-header">
                <i class="fa fa-close pull-right closeModel" data-dismiss="modal"></i>
                <h4><span class="glyphicon glyphicon-lock"></span> Are You Sure You Want to Reset?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <p class="text-center">All the data will be lost and system will be back to startup data.</p>
                    <div class="text-center">
                        <h3><?= $destructionKey ?></h3>
                        <p>Please re enter the key to start reset.</p>
                        <input type="text" class="form-control btn-default text-center" name="keyToTerminate" id="keyToTerminate">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default btn-danger pull-right"> Reset</button>&nbsp;
                <button type="button" class="btn btn-default btn-primary pull-right" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    function discturctionCall(){
        console.log('Event Logged')
        $('#myModal_destruction').modal();
    }
</script>





<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner pull-right">
        Powered By: <a target="_blank" href="https://www.bsofts.com?ref=<?php $application_app_key ?>">Bsofts</a>, &copy; Copyrights <?= date('y') ?>, all rights reserved.
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<script>
    $(function () {

        var nav = $('.subBar');
        if (nav.length) {
            var fixmeTop = nav.offset().top -46;
            $(window).scroll(function () {
                var currentScroll = $(window).scrollTop();
                if (currentScroll >= fixmeTop) {
                    $('.subBar').css({
                        position: 'fixed',
                        top: '46px',
                        right: '0',
                        width: 'calc(100% - 235px)',
                        'z-index': 1
                    });
                } else {
                    $('.subBar').css({
                        position: 'inherit',
                        width: '100%'
                    });
                }
            });
        }
     setInterval(function(){
         $.ajax({
            url: "<?= site_url('User/UserNotifications') ?>", 
            success: function(result){
                console.log(result);
                var jsonArray = JSON.parse(result);

                $(jsonArray).each(function (index, notification){
                    console.log(notification);
                    infoMsg(notification.content);
                })
            }
         });
     },6000);
        /* The redirect to autoplay page function */
        
	$('form').on('submit',function(){
	    if($('input[type=submit]').hasClass('dont_disable')) {
            $('input[type=submit]').prop('disabled', true);
            $('button[type=submit]').prop('disabled', true);
            $('input[type=submit]').addClass('disabled');
            $('button[type=submit]').addClass('disabled');
        }

    })
    
    $('form').submit(function(){
        $this = $(this);

        /** prevent double posting */
        if ($this.data().isSubmitted) {
            return false;
        }

        /** do some processing */

        /** mark the form as processed, so we will not process it again */
        $this.data().isSubmitted = true;

        return true;
    });

    });
    function updateProfileImage(url){
        $('div.profileImg').css('background-image','url('+url+')');
        $('img.profileImg').attr('src',url);
    }
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    document.onkeydown = function(e) {
  if(event.keyCode == 123) {
     return false;
  }
  if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
     return false;
  }
  if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
     return false;
  }
  if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
     return false;
  }
  if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
     return false;
  }
}
</script>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo base_url('public/plugins/respond.min.js') ?>"></script>
<script src="<?php echo base_url('public/plugins/excanvas.min.js') ?>"></script>
<![endif]-->

<script src="<?php echo base_url('public/plugins/jquery-migrate.min.js') ?>" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js') ?> before bootstrap.min.js') ?> to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo base_url('public/plugins/jquery-ui/jquery-ui.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap/js/bootstrap.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/jquery-slimscroll/jquery.slimscroll.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/jquery.blockui.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/jquery.cokie.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/uniform/jquery.uniform.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/bootstrap-switch/js/bootstrap-switch.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/select2/select2.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/plugins/chart.js/Chart.js') ?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script src="<?php echo base_url('public/scripts/metronic.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/scripts/layout.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/scripts/quick-sidebar.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('public/scripts/demo.js') ?>" type="text/javascript"></script>
<script>
    jQuery(document).ready(function() {
//         initiate layout and plugins
		$('a[title]').tooltip();
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        QuickSidebar.init(); // init quick sidebar
        Demo.init(); // init demo features
        $('.miltiselect').select2();
        $(function(){
            $('.page-quick-sidebar').slimScroll({
                height: ($(window).height() - 50)
            });
        });
		$('#showAppointment').bind('click',function(){
			console.log("yes");
			$('.page-quick-sidebar-wrapper').toggleClass("page-quick-sidebar-open");
		})
    });
    <?php if(isset($counter) && !empty($counter)){ ?>
    var ctx = document.getElementById('receptionchart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Cash (<?= $counter['closing_amount_cash'] ?> /- PKR)', 'Cheque (<?= $counter['closing_amount_atm'] ?> /- PKR)', 'Bank (<?= $counter['closing_amount_card'] ?> /- PKR)','Card (<?= $counter['closing_amount_creditcard'] ?> /- PKR)'],
            datasets: [{
                label: '# of Votes',
                data: [ <?= $counter['closing_amount_cash'] ?>, <?= $counter['closing_amount_atm'] ?>, <?= $counter['closing_amount_card'] ?>, <?= $counter['closing_amount_creditcard'] ?>],
                backgroundColor: [
                    'rgba(187,33,36, 1)',
                    'rgba(34,187,51, 1)',
                    'rgba(240,173,78, 1)'
                    
                ]
            }]
        },
        options: {
            legend: {
                display: false
            },
            aspectRatio:1,
            responsive: true
        }
    });
    var ctx2 = document.getElementById('receptionbarchart').getContext('2d');
    var myChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: [
                <?php if(!empty($counter_transactions)){foreach($counter_transactions as $index=> $ct){
                    echo "\"".$ct['id']."\",";
                }}else{ echo ""; } ?>
            ],
            datasets: [{
                label: "Transactions",
                data: [
                    <?php if(!empty($counter_transactions)){foreach($counter_transactions as $index=> $ct){
                        echo ($ct['income_or_expence'] != "INCOME" ? "-" : "" ).$ct['amount'].",";
                    }}else{ echo 0; } ?>
                ]
            }]
        },
        options: {
            legend: {
                display: false
            },
            aspectRatio:1,
            responsive: true,
            scales: {
                yAxes: [{
                    gridLines: {
                        drawBorder: false,
                        display: true,
                    },
                    ticks: {
                        display: true, //this will remove only the label
                        suggestedMin: 50,
                        suggestedMax: 100
                    }
                }],
                xAxes: [{
                    gridLines: {
                        drawBorder: false,
                        display: true,
                    },
                    ticks: {
                        display: false //this will remove only the label
                    }
                }]
            },
        }
    });
    <?php } ?>
    
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>

