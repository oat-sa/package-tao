/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Download file

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridDownloadAdapter constructor
 */
function TaoGridDownloadFileResourceAdapter(){}

/**
 * format the content to display a download link
 */
TaoGridDownloadFileResourceAdapter.formatter = function(cellvalue, options, rowObject){
	var returnValue = '';
	
	var downloadUrl = root_url+'/tao/File/downloadFile?uri='+encodeURIComponent(cellvalue)+"&amp;";
	returnValue = '<a target="_blank" href="'+downloadUrl+'" disabled="false" style="cursor: pointer;">'
			+'<span class="ui-icon ui-icon-document" style="float:left;"></span>'
			+'<span>'+__('Download')+'</span>'
		+'</a>';
	
	return returnValue;
}
