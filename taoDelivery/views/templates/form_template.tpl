<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_delivery.css" />

<div id="delivery-left-container">
   	<?= get_data('contentForm')?>
	<?= has_data('campaign') ? get_data('campaign') : '';?>
	<div class="breaker"></div>
</div>

<div class="main-container medium" id="delivery-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
	<!-- compile box not available in standalone mode-->
	<?if(!tao_helpers_Context::check('STANDALONE_MODE')):?>
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
		<?=__("Publishing")?>
	</div>
	<div id="form-compile" class="ui-widget-content ui-corner-bottom">
		<div class="ext-home-container ui-state-highlight ui-state-highlight-delivery">

		<span>
			<?if(get_data('hasContent')):?>
	            <a id='compileLink' class='nav' href="<?=BASE_URL.'Compilation/index?uri='.tao_helpers_Uri::encode(get_data('uri')).'&classUri='.tao_helpers_Uri::encode(get_data('classUri'))?>">
                    <img id='compileLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>
    					<?=__('Create Delivery')?>
                </a>
			<?endif;?>
		</span>

		<br/>

		</div>
	</div>
	<?endif;?>
	
</div>

<script type="text/javascript">
$(function(){
	require(['jquery'], function($) {
		$('.compilationButton').click(function(){
    		$.ajax({
    			url: "<?=get_data('exportUrl')?>",
    			type: "POST",
    			data: {'uri': $(this).data('uri')},
    			dataType: 'json',
    			success: function(response) {
    				if (response.success) {
    					window.location.href = response.download; 
    				}
			    }
    		});			
		});
	});
		
});
</script>
<?php
Template::inc('footer.tpl');
?>