<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<div class="main-container">
    <div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
    	<?=get_data('formTitle')?>
    </div>
    	<?php if(get_data('importErrors')):?>
		<fieldset class='ui-state-error'>
			<legend><strong><?=(get_data('importErrorTitle'))?get_data('importErrorTitle'):__('Error during file import')?></strong></legend>
			<ul id='error-details'>
			<?php foreach(get_data('importErrors') as $ierror):?>
				<li><?=$ierror->__toString()?></li>
			<?php endforeach?>
			</ul>
		</fieldset>
	<?php endif?>
    <div id="form-container" class="ui-widget-content ui-corner-bottom">
    
    	<?php if(has_data('errorMessage')):?>
    		<fieldset class='ui-state-error'>
    			<legend><strong><?=__('Error')?></strong></legend>
    			<?=get_data('errorMessage')?>
    		</fieldset>
    	<?php endif?>
    
    	<?=get_data('myForm')?>
    </div>
</div>
<?php
Template::inc('footer.tpl', 'tao');
?>