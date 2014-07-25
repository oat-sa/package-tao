<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= BASE_WWW ?>css/linkManagement.css" />	
<script type="text/javascript">
$("#copyPasteBox").select();
</script>

<div class="main-container">
    <div id="form-title"
        class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('%s Tool Provider', get_data('deliveryLabel'))?>
	</div>
    <div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?php if (has_data('warning')) :?>
            <div class='warning'>
                <?= get_data('warning')?>
            </div>
        <?php endif;?>    
		<?php if (has_data('launchUrl')) :?>
        <div class='description'>
            <?=__('Copy and paste the following URL into your LTI compatible tool consumer.')?>
        </div>
        <div class="row">
            <label><?= __('Launch URL')?></label>
            <div>
                <textarea rows="3" id="copyPasteBox"><?= get_data('launchUrl')?></textarea>
                </div>
        </div>
        <?php endif;?>
        <div class="row">
            <label><?= __('Tool consumer(s)')?></label>
            <div>
    		<?php if (count(get_data('consumers')) > 0) :?>
            <ul class="consumerList">
                <?foreach (get_data('consumers') as $consumer) :?>
    			    <li><?= $consumer->getLabel()?></li>
    	        <?endforeach;?>
    	    </ul>
    		<?php else:?>
    	      <span class="emptyList"><?= __('No LTI consumers defined')?></span>
    	    <?php endif;?>
    	    </div>
	    </div>
    </div>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>