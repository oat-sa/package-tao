<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Currenly Under Maintenance</title>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-1.8.0.min.js "></script>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/jquery-ui-1.8.23.custom.min.js"></script>

	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/errors.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/maintenance.css" />
</head>

<body>
	<div id="main" class="ui-widget-content ui-corner-all" style="background-image: url(<?= ROOT_URL ?>tao/views/img/errors/maintenance.png);">
		<div id="content">
			<h1>Currently Under Maintenance</h1>
			<p id="warning_msg">
				<img src="<?= ROOT_URL ?>tao/views/img/warning_error_tpl.png" alt="warning" class="embedWarning" />
				This TAO Platform is currently <strong>under maintenance</strong> and should be available in a few moments.
				We apologize for any inconvenience.
			</p>
		</div>
	</div>
</body>

</html>