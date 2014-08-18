<?php
use oat\tao\helpers\Template;

if(get_data('message')):?>
	<div id="info-box" class="ui-corner-all auto-highlight auto-hide">
		<?=get_data('message')?>
	</div>
<?endif?>

<div id="versioning-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=get_data('formTitle')?>
</div>
<div id="versioning-container" class="ui-widget-content ui-corner-bottom">
	<?=get_data('myForm')?>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>

<script type="text/javascript">
$(function(){
	<?if(get_data('reload')):?>
		window.location.reload();
	<?endif?>
});
</script>
