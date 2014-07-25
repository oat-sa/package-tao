<? include(TAO_TPL_PATH . 'form_context.tpl') ?>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<script type="text/javascript">
function disableInput($input){
	// $input.attr('disabled', 'disabled').hide();
	$input.hide();
}

function enableInput($input){
	// $input.attr('disabled', '').show();
	$input.show();
}

function switchACLmode(){
	var restrictedUserElt = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_PROCESS_INIT_RESTRICTED_USER)?>\']').parent();
	var restrictedRoleElt = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE)?>\']').parent();
	var mode = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_PROCESS_INIT_ACL_MODE)?>\']').val();

	if(mode == '<?=tao_helpers_Uri::encode(INSTANCE_ACL_USER)?>'){//mode "user"
		enableInput(restrictedUserElt);
		disableInput(restrictedRoleElt);
	}else if(mode == ''){
		disableInput(restrictedRoleElt);
		disableInput(restrictedUserElt);
	}else{
		enableInput(restrictedRoleElt);
		disableInput(restrictedUserElt);
	}
}

$(document).ready(function(){
	switchACLmode();
	$('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_PROCESS_INIT_ACL_MODE)?>\']').change(switchACLmode);
});
</script>
<?include('footer.tpl');?>
