<?php
use oat\tao\helpers\Template;
?>
<?if(get_data('message')):?>
	<div id="info-box" class="ui-widget-header ui-corner-all auto-slide">
		<span><?=get_data('message')?></span>
	</div>
<?endif?>
<div class="main-container">
	<div style='padding: 20px'>
	<span class="ui-state-default ui-corner-all">
		<a href="#" id="getScoreButton">
			<img src="<?=Template::img('add.png', 'tao')?>" alt="add" /> <?=__('Add grades')?>
		</a>
	</span>
	</div>
	<table id="result-table-grid"></table>
	<div id="result-table-pager"></div>
</div>
<script type="text/javascript">
require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
	function setColumns(columns) {
		var models = [];
		for (key in columns) {
			models.push({'name': columns[key], index:'index'+models.length});
		}
		$('#result-table-grid').jqGrid('GridUnload');
		var myGrid = $("#result-table-grid").jqGrid({
			url: "<?=_url('data')?>",
			postData: {'filter': <?=tao_helpers_Javascript::buildObject($filter)?>, 'columns':Object.keys(columns)},
			mtype: "post",
			datatype: "json",
			colNames: columns.values,
			colModel: models,
			rowNum:20,
			height:300,
			width: (parseInt($("#result-table-grid").width()) - 2),
			pager: '#user-list-pager',
			sortname: 'login',
			viewrecords: false,
			sortorder: "asc",
			caption: __("Delivery results"),
			gridComplete: function(){

				$(".user_deletor").click(function(){
					removeUser(this.id.replace('user_deletor_', ''));
				});

				$(window).unbind('resize').bind('resize', function(){
					myGrid.jqGrid('setGridWidth', (parseInt($("#user-list").width()) - 2));
				});
			}
		});
		myGrid.navGrid('#result-table-grid',{edit:false, add:false, del:false});

		helpers._autoFx();
	}

	$('#getScoreButton').click(function(e) {
		e.preventDefault();
		$.getJSON( "<?=_url('getGradeColumns')?>"
			, <?=tao_helpers_Javascript::buildObject(array('filter' => $filter))?>
			, function (data) {
				setColumns(data.columns)
			}
		);
	});

	$(function(){
		setColumns([]);
	});
});
</script>
<?php
Template::inc('footer.tpl', 'tao')
?>