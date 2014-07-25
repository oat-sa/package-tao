<div id="item-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Available Items')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<span class="elt-info" style="margin-right:6px;"><?=__('Select the items composing the test.')?></span>
		<div id="item-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<div id="item-order-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Items sequence')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="item-list">
			<span class="elt-info" <?php if (!count(get_data('itemSequence'))) echo ' style="display:none"' ?>><?=__('Drag and drop the items to order them')?></span>
			<ul id="item-sequence" class="sortable-list">
			<?foreach(get_data('itemSequence') as $index => $item):?>
				<li class="ui-state-default" id="item_<?=$item['uri']?>" >
					<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>
					<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>
					<?=$index?>. <?=$item['label']?>
				</li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item-sequence" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<input type='hidden' name='uri' value="<?=get_data('uri')?>" />

<script type="text/javascript">
//manual require because of  the redirect
require(['taoWfTest/controller/authoring'], function(controller){
    'use strict';

    controller.start({
        sequence    : <?=get_data('relatedItems')?>,
        labels      : <?=get_data('allItems')?>,
        saveurl     : <?=json_encode(get_data('saveUrl'))?>,
        openNodes   : <?=json_encode(get_data('itemOpenNodes'))?>,
        rootNode    : <?=json_encode(get_data('itemRootNode'))?>
    });
});
</script>
