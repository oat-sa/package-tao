<?if(get_data('errorMessage')):?>
	<script type='text/javascript'>
		callbackMeWhenReady.loginError = function() {
				helpers.createErrorMessage("<?=get_data('errorMessage')?>");
			};
	</script>
<?endif?>