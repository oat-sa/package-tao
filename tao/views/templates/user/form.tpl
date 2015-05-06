<?if(get_data('exit')):?>
	<script type="text/javascript">
		window.location = "<?=_url('index', 'Main', 'tao', array('structure' => 'users', 'ext' => 'tao', 'message' => get_data('message')))?>";
	</script>
<?else:?>
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
		
                require(['jquery', 'helpers', 'users'], function($, helpers, user){
                    var ctx_action  = "<?=get_data('action')?>";
                    var loginId     = "<?=get_data('loginUri')?>";
                    var url         = "<?=_url('checkLogin', 'Users', 'tao')?>";
                    if(ctx_action === 'add'){
                        $('#tabs').tabs('disable', helpers.getTabIndexByName('edit_user'));
                        user.checkLogin(loginId, url);
                    }
		});
	</script>
<?endif?>