/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt activity variables content

 * @see TaoGridClass
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 *
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridActivityVariablesAdapter constructor
 */

function TaoGridActivityVariablesAdapter(){}

TaoGridActivityVariablesAdapter.formatter = function(cellvalue, options, rowObject) {

	var returnValue = '';

	returnValue = '<a href="#" disabled="false" tyle="cursor: pointer;">'
			+'<span class="ui-icon ui-icon-flag" style="float:left; cursor:pointer;"></span><span>'+__('Edit')+'</span>'
		+'</a>';

	return returnValue;
}

TaoGridActivityVariablesAdapter.postCellFormat = function(grid, cell, rowId, columnId) {
	/**
	 * Instantiate the variables grid
	 */
	$(cell).find('a').click(function(){
		var processId = selectedProcessId;
		var activityId = rowId;
		selectedActivityExecutionId = rowId;

		var dialogHtml = '<div id="activity-variables-popup" style="margin-top:5px;"> \
			<div style="height:30px;"> \
				<a href="#" class="activity-variable-action activity-variable-add"><img src="'+root_url+'tao/views/img/add.png"/> '+__('Add a variable')+'</a> \
			</div> \
			<div style="height:405px;"> \
				<table id="activity-variables-grid"></table> \
			</div> \
			<div style="height:30px; margin-top:10px;"> \
				<a href="#" class="activity-variable-action activity-variable-add"><img src="'+root_url+'tao/views/img/add.png"/> '+__('Add a variable')+'</a> \
			</div> \
		</div>';

		//instantiate the dialog box
		$(dialogHtml).dialog({
			'height'	: 500
			, 'width'	: 400
			//, 'modal'	: true
			, 'close': function(event, ui) {
				$('#activity-variables-grid').jqGrid('GridUnload');
				$('#activity-variables-popup').dialog('destroy').remove();
				delete activityVariablesGrid;
			}
			, editurl: "server.php"
			, viewrecords: true
		});

		//the grid model
		//var activityVariablesModel = model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['subgrids']['variables']['subgrids'];
		var activityVariablesModel = [
			{'id' : 'code' , 'name' : 'code', 'widget':'ActivityVariableLabel'}
			, {'id' : 'value' , 'name' : 'value', 'weight':3, 'widget':'ActivityVariable' }
		];
		//the current activities grid options
		var activityVariablesGridOptions = {
			'title'  : __('Activity Variables'),
			'callback' : {
				'saveNewRow' : function(rowId, rowData){
					var code = rowData['code'];
					var value = rowData['value'];

					//delete the temp row
					activityVariablesGrid.deleteRow(rowId);

					wfApi.Variable.edit(selectedActivityExecutionId, code, value, function(){
						var varUri = null;
						var rowToAdd = [];

						//find uri of the variable type
						for (var crtVarUri in wfVariables) {
							if (wfVariables['code'] == code) {
								varUri = crtVarUri;
							}
						}
						//format variable for the grid
						rowToAdd[varUri] = {
							'code' :	code
							,'value' :	value
						};
						//add the variable to the grid
						activityVariablesGrid.add(rowToAdd);
					}, function(){
						alert ('the variable has not been modified')
					});
				}
				, 'saveEditedRow' : function(rowId, rowData){
					var code = rowData['code'];
					var value = rowData['value'];

					wfApi.Variable.edit(selectedActivityExecutionId, code, value, function(){
						var varUri = rowId;
						//format variable for the grid
						var rowToEdit = [];
						rowToEdit[varUri] = {
							'code' :	code
							,'value' :	value
						};
						//add the variable to the grid
						activityVariablesGrid.refresh(rowToEdit);
					}, function(){
						alert ('the variable has not been modified')
					});
				}
			}
		};
		//instantiate the grid widget
		var activityVariablesGrid = new TaoGridClass('#activity-variables-grid', activityVariablesModel, '', activityVariablesGridOptions);
		//display the data
		var rowData = grid.getRowData(activityId);
		activityVariablesGrid.add(rowData['variables']);

		//bind actions
		$('#activity-variables-popup').find('.activity-variable-add').click(function(){
			activityVariablesGrid.newRow();
		});
	});
}
