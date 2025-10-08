<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Installation</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/assets/plugins/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/assets/plugins/simple-line-icons/simple-line-icons.min.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('/assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url('/assets/pages/css/login.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo base_url('/assets/css/theme.css') ?>" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo base_url('/assets/css/layout.css') ?>" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?php base_url('favicon.ico')?>"/>
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
<div class="content text-center">
    <form class="login-form" method="post" action="<?php site_url('Config/LicenceKey') ?>">
    	<div class="col-xs-12 text-center">
    		<img class="img-responsive logo margin-0" style="display: inline-block;" src="<?= base_url('/assets/logo.png') ?>">
    		<hr/>
    	</div>
    	<?php if(!empty($error)){ ?>
			<div class="col-sm-12 alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
                <span>
                    <?= implode(', ', $error) ?>.
                </span>
			</div>
		<?php } ?>
    	<h2 class="text-center">Please Provide Licence Key</h2>
    	<div class="form-group">
    		<label for="licenceKey">Please Enter Your Licence Key</label>
    		<textarea name="licenceKey" rows="12" class="form-control form-control-solid placeholder-no-fix"></textarea>	
    	</div>
    	<div class="form-group">
    		<h6>Dont have licence key?</h6>
    		<p><a href="http://bsofts.com/getyourlicencekey" title="FREE LICENCE KEY">GET Your Licence Key For Free</a></p>	
    	</div>
    	<div class="form-group text-right">
    		<button type="submit" class="btn btn-success">Continue</button>	
    	</div>
	</form>
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo base_url('/assets/plugins/jquery.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/assets/plugins/jquery-migrate.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/assets/plugins/bootstrap/js/bootstrap.min.js') ?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url('/assets/scripts/metronic.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('/assets/pages/scripts/login.js') ?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {     
$('a[title]').tooltip();
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>