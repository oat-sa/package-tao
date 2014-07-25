<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<script type="text/javascript" src="<?= TAOBASE_WWW ?>js/jquery-1.8.0.min.js"/></script>
		<script type='text/javascript' src='<?=get_data('webFolder');?>js/runtime/ItemServiceImpl.js'></script>
		<script type='text/javascript' src='<?=get_data('webFolder');?>js/runtime/item_runner.js'></script>
		<script type='text/javascript' src='<?=ROOT_URL?><?=get_data('resultJsApiPath');?>'></script>
		<script type='text/javascript'>
		    var resultApi = <?=get_data('resultJsApi');?>;
			var itemId = <?=json_encode(get_data('itemId'));?>;
			var itemPath = '<?=get_data('itemPath')?>';
		</script>
	</head>
	<body>
		<iframe id='item-container' class="toolframe" frameborder="0" style="width:100%;overflow:hidden" scrolling="no"></iframe>
	</body>
</html>