<script type="text/javascript">
$(function(){

	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	
	<?if(has_data('message')):?>
		helpers.createMessage("<?=get_data('message')?>");
	<?endif?>

});

</script>