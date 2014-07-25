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

<?endif?>
<script type="text/javascript">
requirejs.config({
    config: {
        'tao/controller/main/trees': {
            'sectionTreesData' : <?=json_encode(get_data('trees'))?>
        }
    }
});
</script>