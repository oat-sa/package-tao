<div id="form-title-history" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:0.5%;">
	<?=__("History")?>
</div>
<div id="form-history" class="ui-widget-content ui-corner-bottom">
	<div id="history-link-container" class="ext-home-container">
		<p>
		<?if(get_data('executionNumber')):?>
			<?=__('There are currently')?>&nbsp;<?=get_data('executionNumber')?>&nbsp;<?=__('delivery executions')?>.
		<?else:?>
			<?=__('There is currently no delivery execution.')?>
		<?endif;?>
		</p>

		<?php /*if(get_data('executionNumber')):?>
		<span>
			<a id='historyLink' href="#">
				<img id='historyLinkImg' src="<?=BASE_WWW?>img/compile_small.png"/>&nbsp;<?=__('View History')?>
			</a>
		</span>
		<?endif;*/?>
	</div>
	<div>
		<table id="history-list"></table>
		<div id="history-list-pager"></div>
	</div>
</div>

<script type="text/javascript">
	var historyGrid = null;
	var actionUrl = '';

function buildHistoryGrid(selector) {
<?if(tao_helpers_Context::check('STANDALONE_MODE')):?>
	actionUrl = "<?=_url('historyData', 'Delivery', 'taoDelivery', array('STANDALONE_MODE' => true))?>";
<?else:?>
	actionUrl = "<?=_url('historyData', 'Delivery', 'taoDelivery')?>";
<?endif;?>
	require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
		historyGrid = $(selector).jqGrid({
			url: actionUrl,
			datatype: "json",
			colNames:[ __('Test Taker'), __('Time'), __('Actions')],
			colModel:[
				{name:'subject',index:'subject'},
				{name:'time',index:'time'},
				{name:'actions',index:'actions', align:"center", sortable: false}
			],
			rowNum:20,
			height:300,
			width:parseInt($(selector).width()) - 2,
			pager: '#history-list-pager',
			sortname: 'subject',
			viewrecords: false,
			sortorder: "asc",
			caption: __("Execution History"),
			postData: {'uri': "<?=get_data('uri')?>", 'classUri': "<?=get_data('classUri')?>"},
			gridComplete: function(){
				$.each(historyGrid.getDataIDs(), function(index, elt){
					historyGrid.setRowData(elt, {
						actions: "<a id='history_deletor_"+elt+"' href='#' class='user_deletor nd' ><img class='icon' src='<?=BASE_WWW?>img/delete.png' alt='<?=__('Delete History')?>' /><?=__('Delete')?></a>"
					});
				});
				$(".user_deletor").click(function(e){
					e.preventDefault();
					removeHistory(this.id.replace('history_deletor_', ''));
				});

				$(window).unbind('resize').bind('resize', function(){
					historyGrid.jqGrid('setGridWidth', (parseInt($(selector).width())-2));
				});
			}
		});
		historyGrid.navGrid('#history-list-pager',{edit:false, add:false, del:false});
	});
}

var removeHistory = function(uri){
	if(confirm("<?=__('Please confirm history deletion')?>")){
		$.ajax({
			url: "<?=_url('deleteHistory', 'Delivery', 'taoDelivery')?>",
			type: "POST",
			data: {
				'historyUri': uri,
				'uri': "<?=get_data('uri')?>",
				'classUri': "<?=get_data('classUri')?>"
			},
			dataType: 'json',
			success: function(r){
				if (r.deleted){
					historyGrid.trigger("reloadGrid");
					helpers.createInfoMessage(r.message);
				}else{
					helpers.createErrorMessage(r.message);
				}
			}
		});
	}
}


$(function(){
	$('#historyLink').click(function(e){
		e.preventDefault();
		$('#history-link-container').hide();
		buildHistoryGrid("#history-list");
	});
});
</script>