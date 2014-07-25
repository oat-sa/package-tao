<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PRODUCT_NAME?> <?=TAO_VERSION?> Service</title>
	<link rel="shortcut icon" href="<?=BASE_WWW?>img/favicon.ico" type="image/x-icon" />

	<script type='text/javascript'>
		var jsPath 	= '<?=BASE_WWW?>js/';
		var imgPath = '<?=BASE_WWW?>img/';
	</script>

	<script src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>

	<?=tao_helpers_Scriptloader::render()?>

	<script src="<?=TAOBASE_WWW?>js/main.js"></script>

	<script type='text/javascript'>
		var ctx_extension = '<?=get_data("extension")?>';
		var ctx_module = '<?=get_data("module")?>';
		var ctx_action = '<?=get_data("action")?>';

		callbackMeWhenReady.sasReady = function() {
			if(/edit|Edit|add/.test(ctx_action)){
				uiForm.initElements();
				uiForm.initOntoForms();
			} else if(/translate/.test(ctx_action)){
				uiForm.initElements();
				uiForm.initTranslationForm();
			} else {
				uiForm.initElements();
			}
			helpers._autoFx();
		};
	</script>
<?if(get_data('errorMessage')):?>
	<script type='text/javascript'>
		$(function(){
			helpers.createErrorMessage("<?=get_data('errorMessage')?>");
		});
	</script>
<?endif?>
</head>
<body>
<?if(get_data('message')):?>
	<div id="info-box" class="ui-widget-header ui-corner-all auto-slide">
		<span><?=get_data('message')?></span>
	</div>

<?endif?>
	
	<? include(get_data('includedView')); ?>
	
</body>
</html>