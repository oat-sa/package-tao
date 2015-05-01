<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Internal Server Error</title>
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/errors.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/userError.css" />
</head>
<body>
	<div id="main" class="ui-widget-content ui-corner-all" style="background-image: url(<?= ROOT_URL ?>tao/views/img/errors/user.png);">
		<div id="content">
			<h1>Error</h1>
			<p id="warning_msg">
				<?php if (!empty($message)): ?>
				<?= $message ?>
				<?php endif; ?>
			</p>
			<?php if (isset($returnLink) && $returnLink == true): ?>
			<div id="redirect">
				<a href="<?= ROOT_URL ?>" id="go_to_tao_bt" class="error_button">TAO Home</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</body>

</html>