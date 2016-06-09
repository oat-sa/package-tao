<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html>

<html>

<head>
	<title><?=__('Thank you');?></title>
	
    <link rel="stylesheet" href="<?= Template::css('reset.css','tao') ?>" />
	<link rel="stylesheet" href="<?= Template::css('custom-theme/jquery-ui-1.8.22.custom.css','tao') ?>" />
    <link rel="stylesheet" href="<?= Template::css('thankyou.css') ?>" />	
</head>

<body>
	<div id="main" class="ui-widget-content ui-corner-all">
			<h1><?=has_data('message')? get_data('message') : __('You have finished the test!');?></h1>
<?php if (has_data('returnUrl')) :?>
			<div class='message'>
    		<?=has_data('consumerLabel')
    		  ? __('Click on the back button to return to %s.', get_data('consumerLabel'))
    		  : __('Click on the back button to return.');?>
		  </div>
<?php endif; ?>
        		  <div class="continer2">
<?php if (has_data('returnUrl')) :?>
        		  <a href="<?=get_data('returnUrl')?>" class="button" <?php if (has_data('consumerLabel')):?>title="<?=__('Return to %s.',get_data('consumerLabel'))?>"<?php endif;?>>
        			<?=__('Back');?>
        			</a>
<?php endif; ?>
<?php if (get_data('allowRepeat')) :?>
        		  <a href="<?=get_data('returnUrl')?>" class="button" title="<?=__('Repeat the test')?>">
        			<?=__('Repeat');?>
        			</a>
<?php endif; ?>
    		</div>
		</div>
</body>

</html>
