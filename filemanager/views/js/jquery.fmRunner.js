/**
 * JQuery Plugin Adapter for the FmRunner class
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 * @see fmRunner.js
 */

//include the fmRunner class if not done previously
/*try { FmRunner; } catch(e){
	$("script").each(function(){
		source = $(this).attr('src');
		if(/jquery\.fmRunner\.js$/.test(source)){
			document.write("<script type='text/javascript' src='"+source.replace('jquery.fmRunner.js', 'fmRunner.js')+"'></script>");
			return;
		}
	})
}*/

require(['require', 'jquery', root_url + 'filemanager/views/js/fmRunner.js']);

/**
 * JQuery plugin to bind the fmRunner with any node.
 * The runner is bound to the click event
 * @param {Object} options the list of options usually used with the window.open function (width,height, menubar, toolbar, etc.)
 * @example $("#myId").fmload()
 * @example $("#myId").fmload({width: '1024px', height: '768px'});
 */
$.fn.fmload = function (options, elt, callback) {
	return this.each(function () {
		$(this).addClass('fm-launcher');
		$(this).click(function(){
			options.elt = elt;
			FmRunner.load(options, callback);
		});
	});
};

/**
 * JQuery plugin to bind the fmRunner to an icon inserted after the matching node
 * @param {Object} options
 */
$.fn.fmbind = function(options, callback){
	var imgSrc = root_url + 'filemanager/views/img/folder_page.png';
	if(options.type == 'image'){
		imgSrc = root_url + 'filemanager/views/img/folder_image.png';
	}
	if(options.type == 'audio'){
		imgSrc = root_url + 'filemanager/views/img/folder_audio.png';
	}

	var fmType = 'file';
	if(options.type){
		fmType = options.type;
	}

	return this.each(function () {
		if(!$(this).next().hasClass(fmType)){
			var $imgNode = $("<img src='"+imgSrc+"' />");
			$imgNode.addClass(fmType);
			$imgNode.attr('title', __('Open File Manager')).css('cursor', 'pointer').css('margin', '1px');
			$imgNode.hover(function(){
				$imgNode.css('padding', '1px 0 0 1px').css('opacity', 0.6);
			}, function(){
				$imgNode.css('padding', '0').css('opacity', 1);
			});
			$imgNode.fmload(options, this, callback);
			$(this).after($imgNode);
		}
	});
};
