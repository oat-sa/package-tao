<div id="<?=get_data('id')?>-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=get_data('title')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="<?=get_data('id')?>-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-<?=get_data('id')?>" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<script type="text/javascript">
require(['jquery', 'generis.tree.select'], function($, GenerisTreeSelectClass) {

        new GenerisTreeSelectClass('#<?=get_data('id')?>-tree', '<?=get_data('dataUrl')?>', {
                actionId: '<?=get_data('id')?>',
                saveUrl: '<?=get_data('saveUrl')?>',
                saveData: {
                        resourceUri: '<?=get_data('resourceUri')?>',
                        propertyUri: '<?=get_data('propertyUri')?>'
                },
                checkedNodes: <?=json_encode(tao_helpers_Uri::encodeArray(get_data('values')))?>,
                serverParameters: {
                        openNodes: <?=json_encode(get_data('openNodes'))?>,
                        rootNode: <?=json_encode(get_data('rootNode'))?>
                },
                paginate: 10
        });
});
</script>