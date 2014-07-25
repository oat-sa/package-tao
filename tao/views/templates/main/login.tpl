<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?=get_data('title')?></title>

    <link rel="stylesheet" type="text/css" media="screen"
          href="<?=Template::css('custom-theme/jquery-ui-1.8.22.custom.css','tao')?>"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?=Template::css('style.css','tao')?>"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?=Template::css('layout.css','tao')?>"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?=Template::css('form.css','tao')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=Template::css('portal.css','tao')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=Template::css('login.css','tao')?>"/>

    <script id='amd-loader'
            type="text/javascript"
            src="<?=Template::js('lib/require.js')?>"
            data-main="<?=TAOBASE_WWW?>js/login"></script>
</head>
<body>

<div class="content-wrap">

    <div id="portal-box" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
        <?php if (has_data('msg')) :?>
		<span class="loginHeader">
		    <span class="hintMsg"><?=get_data('msg')?></span>
		</span>
        <?php endif;?>
		<div class="loginBox">
			<?if(get_data('errorMessage')):?>
            <div class="ui-widget ui-corner-all ui-state-error error-message">
                <?=urldecode(get_data('errorMessage'))?>
            </div>
				<br/>
            <?endif?>
            <div id="login-form">
                <?=get_data('form')?>
            </div>
		</div>
    </div>
    <!-- portal-box -->
</div>
<!-- content-wrap -->
<?php
Template::inc('layout_footer.tpl', 'tao')
?>
</body>
</html>
