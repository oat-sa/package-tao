/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt activity variable content

 * @see TaoGridClass
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 *
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridActivityVariableAdapter constructor
 */

function TaoGridActivityVariableAdapter(){}

TaoGridActivityVariableAdapter.formatter = function(cellvalue, options, rowObject){

	var returnValue = '';

	for(var i in cellvalue){
		returnValue += '<span class="activity-variable-value">'+cellvalue[i]+'</span>';
	}

	return returnValue;
};

TaoGridActivityVariableAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{/*
	$(cell).one('dblclick', function(){
		//$(grid.selector).jqGrid('editRow', rowId);
		TaoGridActivityVariableAdapter.edit(grid, cell, rowId, columnId, {'editCode':false});
	});*/
};

TaoGridActivityVariableAdapter.cancelEdit = function(grid, cell, rowId, columnId)
{
	var cellData = grid.getCellData(rowId, columnId);
	var formated = TaoGridActivityVariableAdapter.formatter(cellData);
	$(cell).empty().append(formated);
	TaoGridActivityVariableAdapter.postCellFormat(grid, cell, rowId, columnId);
};

TaoGridActivityVariableAdapter.getEditedValue = function(grid, cell, rowId, columnId)
{
	var values = [];
	$(cell).find('.activity-variable-value input').each(function(){
		values.push($(this).val());
	});
	return values;
};

TaoGridActivityVariableAdapter.editFormatter = function(grid, cell, rowId, columnId)
{
	//TaoGridActivityVariableAdapter.addVariableRow(grid);
	var editHtml = '<div class="activity-variable-edit-container"></div>'
		+'<div class="activity-variable-actions"> \
			<a href="#" class="activity-variable-action activity-variable-addValue"><img src="'+root_url+'tao/views/img/add.png"/> Add</a>\
		</div>';

	//get the variables values
	var cellData = grid.getCellData(rowId, columnId);
	//empty the block
	$(cell).empty().html(editHtml);

	//foreach values add an edit box
	for(var i in cellData){
		TaoGridActivityVariableAdapter.addValue(cell, cellData[i]);
	}

	//bind the delete action
	$(cell).find('.activity-variable-deleteValue').click(function(){
		$(this).parent().remove()
	});
	//bind the add action
	$(cell).find('.activity-variable-addValue').click(function(){
		TaoGridActivityVariableAdapter.addValue(cell, '');
	});
	/*
	//bind the add action
	$(cell).find('.activity-variable-save').click(function(){
		var rowData = grid.getRowData(rowId, columnId);
		var code = rowData['code'];
		var values = TaoGridActivityVariableAdapter.getEditedValue();

		wfApi.Variable.edit(selectedActivityExecutionId, code, values, function(grid, cell, rowId, columnId){
			grid.data[rowId][columnId] = values;
			var formated = TaoGridActivityVariableAdapter.formatter(grid.getCellData(rowId, columnId));
			$(cell).empty().append(formated);
			TaoGridActivityVariableAdapter.postCellFormat(grid, cell, rowId, columnId);
			//currentActivitiesGrid.refresh(selectedActivityExecutionId);
		}, function(){
			alert ('the variable has not been changed')
		});
	});
	//bind the add action
	$(cell).find('.activity-variable-cancel').click(function(){
		TaoGridActivityVariableAdapter.cancelEdit(grid, cell, rowId, columnId);
	});*/
};

TaoGridActivityVariableAdapter.addValue = function(cell, value)
{
	var editCellValueHtml = '<span class="activity-variable-value"> \
			<input type="text" value="'+value+'"/> \
			<img class="activity-variable-deleteValue" src="'+root_url+'tao/views/img/delete.png"/> \
	</span>';
	$(cell).find('.activity-variable-edit-container').append(editCellValueHtml);
};

