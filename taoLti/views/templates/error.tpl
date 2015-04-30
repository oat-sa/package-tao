<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?= __('Server Error')?></title>
	<link rel="stylesheet" type="text/css" href="<?= Template::css('reset.css','tao') ?>" />
	<link rel="stylesheet" type="text/css" href="<?= Template::css('custom-theme/jquery-ui-1.8.22.custom.css','tao') ?>" />
	<link rel="stylesheet" type="text/css" href="<?= Template::css('errors.css','tao') ?>" />
	<link rel="stylesheet" type="text/css" href="<?= Template::css('userError.css','tao') ?>" />
</head>
<body>
	<div id="main" class="ui-widget-content ui-corner-all" style="background-image: url(<?= Template::img('errors/user.png', 'tao') ?>);">
		<div id="content">
			<h1><?= __('Error')?></h1>
			<p id="warning_msg">
				<?php if (has_data('message')): ?>
				    <?= get_data('message') ?>
				<?php endif; ?>
			</p>
            <?php if (has_data('returnUrl')) :?>
            <a href="<?=get_data('returnUrl')?>" class="error_button" <?php if (has_data('consumerLabel')):?>title="<?=__('Return to %s.',get_data('consumerLabel'))?>"<?php endif;?>>
            <?=__('Back');?>
            </a>
            <?php endif; ?>

            <?php if (has_data('returnLink')): ?>
			<div id="redirect">
				<a href="<?= get_data('returnLink') ?>" id="go_to_tao_bt" class="error_button">TAO Home</a>
			</div>
			<?php endif; ?>
		</div>
    </div>
</body>

</html>