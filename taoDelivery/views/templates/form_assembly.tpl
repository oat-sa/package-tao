<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<link rel="stylesheet" type="text/css" href="<?= Template::css('form_delivery.css')?>" />

<div class="tao-scope grid-container">
    <div class="grid-row">
        <div class="col-12 ui-state-default topbox">
            <span style="font-style: italic">
        	   <?=__('%1s has been published on the %2s', get_data('label'), tao_helpers_Date::displayeDate(get_data('date')))?>
        	</span>
        </div>
    </div>
    
    <div class="grid-row">
        <div class="col-12">
    	   <div id="form-title-history" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
            	<?=__("Attempts")?>
            </div>
            <div id="form-history" class="ui-widget-content ui-corner-bottom">
            	<div id="history-link-container" class="ext-home-container">
            	    <?if(has_data('exec')):?>
                		<p>
                		<?if(get_data('exec') == 0):?>
                			<?=__('No attempt has been started yet.')?>
                		<?elseif(get_data('exec') == 1) :?>
                			<?=__('There is currently 1 attempt')?>.
            			<?else:?>
                            <?=__('There are currently %s attempts', get_data('exec'))?>.
            			<?endif;?>
            		    </p>
        		    <?else:?>
                        <?=__('No informations available')?>.
        			<?endif;?>
            	</div>
            	<div>
            		<table id="history-list"></table>
            		<div id="history-list-pager"></div>
            	</div>
            </div>
        </div>
    </div>
    
    <div class="grid-row">
        <div class="col-6">
            <div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
        		<?=get_data('formTitle')?>
        		<?=tao_helpers_Icon::iconTranslate(array('class' => 'form-translator title-action', 'title' => __('Translate')))?>
        	</div>
        	<div id="form-container" class="ui-widget-content ui-corner-bottom">
        		<?=get_data('myForm')?>
        	</div>
        </div>
        <div class="col-6">
            <?=get_data('groupTree')?>
            <?php Template::inc('widget_exclude.tpl');?>
            <?= has_data('campaign') ? get_data('campaign') : '';?>
        </div>
    </div>
</div>
<?php
Template::inc('footer.tpl');
?>