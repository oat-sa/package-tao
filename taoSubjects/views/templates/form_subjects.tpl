<? include(TAO_TPL_PATH . 'form_context.tpl') ?>
<script type="text/javascript" src='<?=TAOBASE_WWW?>js/users.js'></script>

<?=get_data('groupForm')?>

<div class="main-container large">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<?if(get_data('checkLogin')):?>
	<script type="text/javascript">
		$(document).ready(function(){
			checkLogin("<?=get_data('loginUri')?>", "<?=_url('checkLogin', 'Users', 'tao')?>");
		});
	</script>
<?endif?>

<?include(TAO_TPL_PATH . 'footer.tpl');?>
