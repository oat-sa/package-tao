<div id="campaign-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Add to delivery campaign')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="campaign-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-campaign" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	
	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		new GenerisTreeSelectClass('#campaign-tree', '<?= _url('getCampaigns', 'Campaign', 'taoCampaign')?>', {
			actionId: 'campaign',
			saveUrl : '<?= _url('saveDeliveryCampaigns', 'Campaign', 'taoCampaign')?>',
			checkedNodes : <?=get_data('relatedCampaigns')?>
		});
	});
});
</script>
