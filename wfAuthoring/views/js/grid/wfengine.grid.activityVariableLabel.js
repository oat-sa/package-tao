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
 * The TaoGridActivityVariableLabelAdapter constructor
 */

function TaoGridActivityVariableLabelAdapter(){}

TaoGridActivityVariableLabelAdapter.formatter = function(cellvalue, options, rowObject){
	return cellvalue;
};

TaoGridActivityVariableLabelAdapter.editFormatter = function(grid, cell, rowId, columnId)
{
	if(rowId != TaoGridClass.__NEW_ROW__) return;
	var editHtml = '<select>';
	for(var i in wfVariables){
		if(typeof grid.data[i] == 'undefined'){
			editHtml += '<option value="'+wfVariables[i]['code']+'">'+wfVariables[i]['code']+'</option>';
		}
	}
	editHtml += '</select>';
	
	//get the variables values
	//var cellData = grid.getCellData(rowId, columnId);
	//empty the block
	$(cell).empty().html(editHtml);
};

TaoGridActivityVariableLabelAdapter.getEditedValue = function(grid, cell, rowId, columnId)
{
	var returnValue = null;
	if(rowId == TaoGridClass.__NEW_ROW__){
		returnValue = $(cell).find('select').val();
	}else{
		returnValue = grid.getCellData(rowId, columnId);
	}
	return returnValue;
};
