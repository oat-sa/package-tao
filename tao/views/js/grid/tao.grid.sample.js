/**
 * Here you can find a tao grid adapter sample.
 * The class name has to respect a rule
 * -> TaoGrid + TheNameYouChoose + Adapter

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridSampleAdapter constructor
 */
function TaoGridSampleAdapter(){}

/**
 * format the content to display a download link
 */
TaoGridDownloadFileResourceAdapter.formatter = function(cellvalue, options, rowObject){
	var returnValue = 'Put here the formated content of the cell';	
	return returnValue;
}

/**
 * The post cell format function will be called after cell rendering
 */
TaoGridDownloadFileResourceAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	alert ('The cell is rendered');
}
