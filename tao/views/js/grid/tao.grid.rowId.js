/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Preformat the content with the value row id

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridRowIdAdapter constructor
 */

function TaoGridRowIdAdapter(){}

TaoGridRowIdAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = '';
	returnValue = rowId;
	return returnValue;
}

TaoGridRowIdAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = cellvalue;
	return returnValue;
}

TaoGridRowIdAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
}
