<?if(get_data('exit')):?>
	<script type="text/javascript">
		window.location = "<?=_url('index', 'Main', 'tao', array('structure' => 'users', 'ext' => 'tao', 'message' => get_data('message')))?>";
	</script>
<?else:?>
	<script type="text/javascript" src='<?=TAOBASE_WWW?>js/users.js'></script>

	<?if(get_data('message')):?>
		<div id="info-box" class="ui-widget-header ui-corner-all auto-slide">
			<span><?=get_data('message')?></span>
		</div>
	<?endif?>
	
	<div class="main-container">
		<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?=get_data('formTitle')?>
		</div>
		<div id="form-container" class="ui-widget-content ui-corner-bottom">
			<?=get_data('myForm')?>
		</div>
	</div>

	<script type="text/javascript">
		var ctx_extension 	= "<?=get_data('extension')?>";
		var ctx_module 		= "<?=get_data('module')?>";
		var ctx_action 		= "<?=get_data('action')?>";

		$(document).ready(function(){
			if(ctx_action == 'add'){
				uiBootstrap.tabs.tabs('disable', helpers.getTabIndexByName('edit_user'));
				checkLogin("<?=get_data('loginUri')?>", "<?=_url('checkLogin', 'Users', 'tao')?>");
			}
		});
	</script>
<?endif?>