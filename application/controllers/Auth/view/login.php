<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Login</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
    <link href="<?php echo base_url('public/fonts/fa/web-fonts-with-css/css/fontawesome-all.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url('public/fonts/Barlow/css.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/public/plugins/simple-line-icons/simple-line-icons.min.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/public/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url('/public/css/login.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo base_url('/public/css/layout.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/public/css/theme.css') ?>" rel="stylesheet" type="text/css" id="style_color"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?= base_url('assets/favicon.ico') ?>"/>
<meta name="theme-color" content="#ffffff">
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" method="post">
		<div class="col-xs-8 col-xs-offset-2 text-center">
			<img class="img-responsive" style="display:inline-block;max-width: 50%" src="<?= base_url('logo.png') ?>">
			<hr/>
		</div>
        <?php $error = $this->aauth->errors; ?>
		<?php if(!empty($error)){ ?>
			<div class="col-sm-12 alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
                <span>
                    <?= implode(', ', $error) ?>.
                </span>
			</div>
		<?php } ?>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
		</div>
		<div class="form-group">
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
		</div>
		<div class="form-group text-right">
			<button type="submit" class="btn btn-success uppercase" style="border-radius: 0px !important;">Login</button>
<!--            --><?php //if(OPEN_ENTRY_POINT == 1){ ?>
<!--                <a href="--><?//= site_url(OPEN_ENTRY_POINT_LINK) ?><!--" class="btn btn-primary uppercase pull-left" style="border-radius: 0px !important;">--><?//= OPEN_ENTRY_POINT_TITLE ?><!--</a>-->
<!--            --><?php //} ?>
		</div>
	</form>
	<!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo base_url('/public/plugins/jquery.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/public/plugins/jquery-migrate.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/public/plugins/bootstrap/js/bootstrap.min.js') ?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url('/public/scripts/metronic.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/public/scripts/login.js') ?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>