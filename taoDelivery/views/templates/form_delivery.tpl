<?include('header.tpl')?>

<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_delivery.css" />

<div id="delivery-left-container">
   	<?= get_data('contentForm')?>
	<?=get_data('groupTree')?>
	<?=get_data('groupTesttakers')?>
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
		<?=__("Publishing Status")?>
	</div>
	<div id="form-compile" class="ui-widget-content ui-corner-bottom">
		<div class="ext-home-container <?php if(get_data('hasContent') && !get_data('isCompiled')):?>ui-state-highlight <?php endif;?>ui-state-highlight-delivery">
		<p>
		<?=get_data('deliveryLabel')?>:
		<?if(get_data('isCompiled')):?>
			<?=__('Published on %s', get_data('compiledDate'))?>.
		<?else:?>
			<?=__('Not yet published')?>
		<?endif;?>
		</p>

		<span>
			<?if(get_data('hasContent')):?>
	            <a id='compileLink' class='nav' href="<?=BASE_URL.'Compilation/index?uri='.tao_helpers_Uri::encode(get_data('uri')).'&classUri='.tao_helpers_Uri::encode(get_data('classUri'))?>">
                    <img id='compileLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>
    				<?if(get_data('isCompiled')):?>
    					<?=__('Publish again')?>
    				<?else:?>
    					<?=__('Publish')?>
    				<?endif;?>
                </a>
			<?endif;?>
		</span>

		<br/>

		</div>
	</div>
	<?endif;?>

	<?include('delivery_history.tpl');?>

</div>

<?include('footer.tpl');?>