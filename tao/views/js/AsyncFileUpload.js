/**
 * AsyncFileUpload class
 * @class
 */
AsyncFileUpload = function(elt, options){

	var self = this;
	var elt = elt;

	this.settings = {
			"script"    : root_url + "tao/File/upload",
			"popupUrl"	: root_url + "tao/File/htmlUpload",
			"uploader"  : taobase_www + "js/jquery.uploadify/uploadify.swf",
			"cancelImg" : taobase_www + "img/cancel.png",
			"buttonImg"	: taobase_www + "img/browse_btn.png",
			"scriptAccess": 'sameDomain',
			"width"		: 71,
			"height"	: 21,
			"auto"      : true,
			"multiple"	: false,
			"buttonText": __('Browse'),
			"folder"    : "/",
			"onCancel"	: function(event, ID, fileObj, data){
				var name = $('#' + $(elt).attr('id') + ID).find('span.fileObj:first').text();
				$.post(root_url + "tao/File/cancelUpload", {filename: name, folder: "/"});
				return true;
			}
	};

	this.settings = $.extend(true, this.settings, options);

	var target = false;
	if(options.target){
		var target = $(options.target);
	}
	(options.starter) ? starter = options.starter : starter = elt + '_starter';

	if(target){
		this.settings.onComplete = function(event, queueID, fileObj, response, data){
			var myResponse = $.parseJSON($.trim(response));
			if(myResponse.uploaded){
				target.val(myResponse.data);
			}
			target.trigger('async_file_uploaded', myResponse);
			return false;
		};

		this.settings.onSelect = function(event, queueID, fileObj){
			target.trigger('async_file_selected', fileObj);
		};
	}

	if(helpers.isFlashPluginEnabled() && typeof(jQuery.fn.uploadify) != 'undefined'){

		$(elt).uploadify(this.settings);

		if(this.settings.auto == true){
			$(starter).parent().hide();
		}
		else{
			$(starter).click(function(){
			 	$(elt).uploadifyUpload();
			 	return false;
			 });
		}
	}
	else{
		//fallback if no flash or if uploadify is not loaded
		var params = {
				target : options.target
		};
		if(this.settings.fileExt){
			params.fileExt = this.settings.fileExt;
		}
		if(this.settings.fileExt){
			params.sizeLimit = this.settings.sizeLimit;
		}

		var opener = $("<span><a href='#'>"+__('Upload File')+"</a></span>");
		opener.click(function(e){

			$(this).attr('disabled', true);

			var url = self.settings.popupUrl + '?' + $.param(params);
			var popupOpts = "width=350px,height=100px,menubar=no,resizable=yes,status=no,toolbar=no,dependent=yes,left="+e.pageX+",top="+e.pageY;

			self.window = window.open(url, 'fileuploader', popupOpts);
			self.window.focus();

			return false;
		});
		$(elt).parents('div.form-elt-container').append(opener);

		$(elt).hide();
		$(starter).hide();
	}
};