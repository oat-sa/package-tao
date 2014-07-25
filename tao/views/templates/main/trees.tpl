<?
	if(has_data('trees')):
		foreach(get_data('trees') as $i => $tree):
?>
		<div class="tree-block">
			<div class="ui-widget-header ui-corner-top ui-state-default container-title"><?=__((string)$tree['name'])?></div>
			<div id="tree-actions-<?=$i?>" class="tree-actions">
				<input type="text"   id="filter-content-<?=$i?>" value="*"  autocomplete='off'  size="10" title="<?=__('Use the * character to replace any string')?>" />
				<input type='button' id="filter-action-<?=$i?>"  value="<?=__("Filter")?>" />
				<input type='button' id="filter-cancel-<?=$i?>"  value="<?=__("Finish")?>" class="ui-helper-hidden ui-state-error"/>
			</div>
			<div class="ui-widget ui-widget-content ui-corner-bottom">
				<div id="tree-<?=$i?>"></div>
			</div>
		</div>
<?endforeach?>

<script type="text/javascript">
	$(function(){
		require(['require', 'jquery', 'generis.tree.browser'], function(req, $, GenerisTreeBrowserClass) {
		
<?foreach(get_data('trees') as $i => $tree):?>
			var tree = new GenerisTreeBrowserClass('#tree-<?=$i?>', "<?=$tree['dataUrl']?>", {
				formContainer: helpers.getMainContainerSelector(uiBootstrap.tabs),
				actionId: "<?=$i?>",
<?if (isset($tree['editClassUrl'])):?>editClassAction: "<?=$tree['editClassUrl']?>",<?endif;?>
<?if (isset($tree['editInstanceUrl'])):?>editInstanceAction: "<?=$tree['editInstanceUrl']?>",<?endif;?>
<?if (isset($tree['addInstanceUrl'])):?>createInstanceAction: "<?=$tree['addInstanceUrl']?>",<?endif;?>
<?if (isset($tree['moveInstanceUrl'])):?>moveInstanceAction: "<?=$tree['moveInstanceUrl']?>",<?endif;?>
<?if (isset($tree['addSubClassUrl'])):?>subClassAction: "<?=$tree['addSubClassUrl']?>",<?endif;?>
<?if (isset($tree['deleteUrl'])):?>deleteAction: "<?=$tree['deleteUrl']?>",<?endif;?>
<?if (isset($tree['duplicateUrl'])):?>duplicateAction: "<?=$tree['duplicateUrl']?>",<?endif;?>
<?if (isset($tree['className'])):?>
				instanceClass: "node-<?=str_replace(' ', '-', strtolower($tree['className']))?>",
				instanceName: "<?=mb_strtolower(__($tree['className']), TAO_DEFAULT_ENCODING)?>",
<?endif;?>
				paginate: 30
<?if(get_data('openUri')):?>
				,selectNode: "<?=get_data('openUri')?>"
<?endif?>
			});

			generisActions.setMainTree(tree);
<?endforeach?>
		});
	});
</script>
<?endif?>