<section id="<?=get_data('id')?>-container">
	<header>
		<h1><?=get_data('title')?></h1>
	</header>
	<div>
		<div id="<?=get_data('id')?>-tree"></div>
	</div>
	<footer>
		<button id="saver-action-<?=get_data('id')?>" type="button" class="btn-info small"><span class="icon-save"></span> <?=__('Save')?></button>
	</footer>
</section>
<script>
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
