<?
use oat\tao\helpers\Template;
Template::inc('form_context.tpl', 'tao')
?>
<div class="main-container">
    <div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
    	<?=get_data('formTitle')?>
    </div>
    <?if(get_data('hasContent')):?>
        <div class="feedback-info">
            <span class="icon-info"></span><?= __('This Open Web Item already has content. Go to Preview to see it or import a replacement below.')?>
        </div>
	<?endif?>
    <div id="form-container" class="ui-widget-content ui-corner-bottom">
    
    	<?=get_data('myForm')?>
    	<?if(has_data('report')):?>
    	   <?php echo tao_helpers_report_Rendering::render(get_data('report')); ?>
    	<?endif?>
    	
    </div>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>
