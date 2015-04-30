<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Internal Server Error</title>
        <link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/errors.css" />
	<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/error500.css" />
        
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/lib/jquery-1.8.0.min.js "></script>
	<script type="text/javascript" src="<?= ROOT_URL ?>tao/views/js/lib/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript">
    	$(document).ready(function(){
    	    $('#go_back').click(function(){
    	        parent.history.back();
    	        return false;
    	    });
    	});
	</script>	
</head>
<body>
	<div id="main" class="ui-widget-content ui-corner-all" style="background-image: url(<?= ROOT_URL ?>tao/views/img/errors/500.png);">
		<div id="content">
			<h1>Internal Server Error</h1>
			<div id="warning_msg">
 				<p>The page you requested generated an unexpected error on this server.</p>
				<ul>
				    <li><strong>Verify the address</strong> you entered in your web browser is valid.</li>
				    <li>If you are sure that the address is correct but this page is still displayed  <strong>contact your TAO administrator</strong>.</li>
			    </ul>
			</div>
			
			<?php if (defined('DEBUG_MODE') && DEBUG_MODE == true): ?>
				<?php if (!empty($message)): ?>
				<p>
					<strong>Debug Message:</strong>
					
					<p>
						<?= nl2br($message) ?>
					</p>
				</p>
				<?php endif; ?>
				
				<?php if (!empty($trace)): ?>
				<p>
					<strong>Stack Trace:</strong>
					
					<p class="trace">
						<?= nl2br($trace) ?>
					</p>
				</p>
				<?php endif; ?>
			<?php endif; ?>
			<div id="redirect">
				<a href="#" id="go_back" class='error_button'><?=__('Go Back')?></a> |
				<a href="<?= ROOT_URL ?>" id="go_to_tao_bt" class='error_button'><?=__('TAO Home')?></a>
			</div>
		</div>
	</div>
</body>

</html>