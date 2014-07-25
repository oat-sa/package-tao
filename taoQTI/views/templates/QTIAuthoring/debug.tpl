<link rel="stylesheet" href="<?=BASE_WWW?>css/qtiAuthoringDebugWindow.css" type="text/css" />

<div id="debugWindow">
	<fieldset>
		<legend>QTI item dump:</legend>
		<pre>
			<?print_r(get_data('itemObject'));?>
		</pre>
	</fieldset>
	<fieldset>
		<legend>QTI item session data:</legend>
		<pre>
			<?print_r(get_data('sessionData'));?>
		</pre>
	</fieldset>
</div>