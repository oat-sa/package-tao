<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<head>
	<title>TAO</title>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/custom-theme/jquery-ui-1.8.22.custom.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/style.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/layout.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=BASE_WWW?>css/portal.css"/>
	<script src="<?=BASE_WWW?>js/lib/jquery-1.8.0.min.js"></script>
        <script type="text/javascript">
            $( document ).ready(function(){
                $('.tile').mouseover(function() {
                    $(this).addClass("tileSelected");
                    jQuery(".tileLabel", this).addClass("tileLabelSelected");
                    jQuery(".Title", this).addClass("TitleSelected");
                });
                $('.tile').mouseleave(function() {
                    $(this).removeClass("tileSelected");
                    jQuery(".tileLabel", this).removeClass("tileLabelSelected");
                    jQuery(".Title", this).removeClass("TitleSelected");
                });
            });
        </script>
</head>
<body>
  <div class="content-wrap">
    <ul id="control">
	    <?php if (has_data('user')) :?>
        <li><span id="connecteduser" class="icon">
    	       <?php echo __("Logged in as:"); ?> <span id="username"><?php echo get_data('user'); ?></span>
            </span>
            <span class="separator"></span>
        </li>
        <li>
            <a id="logout" class="icon action" href="<?=_url('logout')?>"><?php echo __("Log out"); ?></a>
        </li>
    	    <?php else :?>
        <li><span id="connecteduser" class="icon">
    	    <?php echo __("You are not logged in."); ?>
            </span>
            <span class="separator"></span>
        </li>
        <li><a id="login" class="icon action"
            href="<?=_url('login')?>"><?php echo __("Log in"); ?></a>
        </li>
	    <?php endif;?>
        </ul>
    <div id="content">
        <div id="portal-box"
            class="ui-tabs ui-widget ui-widget-content ui-corner-all">
            <!--<span class="portalInfo"><h1><?=__('Welcome to TAO!')?></h1></span>!-->

	    
		<?php foreach (get_data('entries') as $entry) :?>
		<a href="<?=$entry->getUrl()?>"> <span
                class="tile"> <span class="Title"><?=$entry->getTitle()?></span>
                    <span class="hintMsg">
				<?=$entry->getDescription()?>
			    </span> <span class="tileLabel">
				<?=$entry->getLabel()?>
			    </span>

            </span>
            </a>
		<?php endforeach;?>

		</div>
    </div>
    </div>
<!-- /content-wrap -->
<?php
Template::inc('layout_footer.tpl', 'tao')
?>
</body>
</html>
