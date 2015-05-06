<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?if(count(get_data('importErrors')) > 0):?>
			<fieldset class='ui-state-error'>
				<legend><strong><?=__('Validation has failed')?></strong></legend>
				<ul id='error-details'>
				<?foreach(get_data('importErrors') as $ierror):?>
					<li><?=$ierror['message']?></li>
				<?endforeach?>
				</ul>
			</fieldset>
		<?endif?>
	
		<?=get_data('myForm')?>
	</div>
</div>
<?php
Template::inc('footer.tpl');
?>