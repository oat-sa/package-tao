<br />
<div class="ui-widget-header ui-state-default ui-corner-top">
	<b><?=get_data('treeName')?></b>
</div>
<div class="ui-widget-content ui-corner-bottom">
	<div id="tree-chooser" ></div>
	<div id="tree-action" class="tree-actions"></div>
</div>
<div id="form-container"></div>

<script type="text/javascript">
require(['<?=get_data('client_config_url')?>'], function(){
    require(['jquery', 'generis.tree.browser'], function($, GenerisTreeBrowserClass) {
            new GenerisTreeBrowserClass('#tree-chooser', "<?=get_data('dataUrl')?>", {
                formContainer: "#form-container",
                actionId: "chooser",
                hideInstances: <?=(get_data('editClassUrl'))?'true':'false'?>,
                editClassAction: "<?=get_data('editClassUrl')?>",
                editInstanceAction: "<?=get_data('editInstanceUrl')?>",
                createInstanceAction: false,
                moveInstanceAction: false,
                subClassAction: false,
                deleteAction: false,
                duplicateAction: false,
                instanceClass: "node-<?=str_replace(' ', '-',strtolower(get_data('classLabel')))?>",
                instanceName: "<?=get_data('classLabel')?>"
            });
    });
});
</script>
