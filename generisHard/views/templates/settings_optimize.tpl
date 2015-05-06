<?
use oat\tao\helpers\Template;

if(get_data('message')):?>
	<div id="info-box" class="ui-corner-all auto-highlight auto-hide">
		<?=get_data('message')?>
	</div>
<?endif?>

<?
if (get_data('optimizable')) {
    Template::inc('optimize.tpl');
}
?>

<?php
Template::inc('footer.tpl', 'tao');
?>

<script type="text/javascript">
$(function(){
	$("#section-meta").empty();
	<?if(get_data('reload')):?>
		window.location.reload();
	<?endif?>
});
</script>