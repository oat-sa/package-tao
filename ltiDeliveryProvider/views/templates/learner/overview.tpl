<?php
use oat\tao\helpers\Template;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=__('Thank you');?></title>
	<link rel="stylesheet" type="text/css" href="<?= Template::css('reset.css','tao') ?>" />
	<link rel="stylesheet" type="text/css" href="<?= Template::css('custom-theme/jquery-ui-1.8.22.custom.css','tao') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= Template::css('thankyou.css') ?>" />	
</head>

<body>
	<div id="main" class="ui-widget-content ui-corner-all">
    	<h1><?=__('You have already taken this test.');?></h1>
        <div class="continer2">
        <?php if (get_data('allowRepeat')) :?>
        <a href="<?= _url('repeat', 'DeliveryRunner', null, array('delivery' => get_data('delivery')))?>" class="button" title="<?=__('Repeat the test')?>" >
            <?=__('Retake the test');?>
    	</a>
        <?php endif; ?>
        </div>
	</div>
</body>

</html>
