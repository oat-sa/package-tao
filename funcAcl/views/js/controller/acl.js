define(['jquery', 'context', 'i18n'], function($, context, __){
    
    $.fn.acldata = function(key, val){
            if (typeof val !== 'undefined'){
                    $.expr.attrHandle[key] = function(elem){
                            return $(elem).attr(key) || $(elem).data(key);
                    };
            }
            return $.fn.data.apply(this, arguments);
    };
    
    function loadModules(role, successCallback) {
	$('#aclModules ul.group-list').empty();
	$('#aclActions ul.group-list').empty();
	if (role === '') return;

	$.ajax({
		type: "POST",
		url: context.root_url + "funcAcl/Admin/getModules",
		data: 'role='+role,
		dataType: 'json',
		success: function(data) {
			for (var e in data) {
				var ext = data[e];
				var extra = '';
				if (ext['has-access']) {
					extra = ' has-access';
				}
				else if (ext['has-allaccess']) {
					extra = ' has-allaccess';
				}
				
				var groupCheckboxTitle = (ext['has-access'] || ext['has-allaccess']) ? __('Revoke access rights to the entire extension') : __('Grant access rights to the entire extension');
				
				var $group = $('<li class="group expendable closed'+extra+'"><div class="group-title"><span class="ui-icon ui-icon-triangle-1-e"/><span class="title">'+ e +'</span><span class="selector all checkable" title="' + groupCheckboxTitle + '"></span></div><ul></ul></li>');
				$group.acldata('uri', ext.uri);
				if (ext['has-access'] == true){
					$('.selector', $group).click(function (e) {
						e.stopPropagation();
						Access2None($(this))
					});	
				}
				else if (ext['has-allaccess'] == true){
					$('.selector', $group).click(function (e) {
						e.stopPropagation();
						Access2None($(this))
					});
				} 
				else {
					$('.selector', $group).click(function (e) {e.stopPropagation();Access2All($(this))});	
				}
				//Open/close group
				$('.group-title', $group).click(function(e) {
					if ($(this).parent().hasClass('open')){
						$(this).removeClass('open');
						$(this).parent().removeClass('open').addClass('closed');
						$(this).find('.ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
						$(this).parent().find('.selected').removeClass('selected');
					}
					else {
						$(this).addClass('open');
						$(this).parent().removeClass('closed').addClass('open');
						$(this).find('.ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
					}
				});
				for (var m in ext.modules) {
					var mod = ext.modules[m];
					var extra = '';
					if (mod['has-access']){
						extra = ' has-access';
					}
					else if (mod['has-allaccess']){
						extra = ' has-allaccess';
					}
					
					var modCheckboxTitle = (mod['has-access'] || mod['has-allaccess']) ? __('Revoke access rights to the entire module') : __('Grant access rights to the entire module');
					
					var $el = $('<li class="selectable'+extra+'"><span class="label">'+ m +'</span><span class="selector checkable" title="'+ modCheckboxTitle +'"></span></li>');
					$el.acldata('uri', mod.uri);
					if (mod['has-access']) $('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
					else if (mod['has-allaccess']) $('.selector', $el).click(function (e) {e.stopPropagation();Access2None($(this))});
					else $('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
					//Select module
					$el.click(function() {
						$('#aclModules .selectable').removeClass('selected');
						$(this).addClass('selected');
						loadActions($('#roles').val(), $(this).acldata('uri'));
					});
					$el.appendTo($('ul', $group));
				}
				$group.appendTo($('#aclModules ul.group-list'));
			}
			
			if (typeof successCallback != 'undefined'){
				successCallback();
			}
		}
	});
    }

    function loadActions(role, module, successCallback) {
	$.ajax({
		type: "POST",
		url: context.root_url + "funcAcl/Admin/getActions",
		data: 'role='+role+'&module='+module,
		dataType: 'json',
		success: function(data) {
			$('#aclActions ul.group-list').empty();
			var nballaccess = 0;
			for (e in data) {
				var act = data[e];
				var extra = '';

				if (act['has-allaccess'] || act['has-access']) {
					extra = ' has-allaccess';
					nballaccess++;
				}
				
				var actCheckBoxTitle = (extra === ' has-allaccess') ? __('Revoke access rights to the action') : __('Grant access rights to the action');
				
				var $el = $('<li class="selectable'+extra+'"><span class="label">'+ e +'</span><span class="selector checkable" title="'+ actCheckBoxTitle +'"></span></li>');
				$el.acldata('uri', act.uri);
				if ($el.hasClass('has-allaccess')) $('.selector', $el).click(function (e) {e.stopPropagation();Access2None($(this))});
				else $('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
				//Select action
				$el.click(function() {
					$('#aclActions .selectable').removeClass('selected');
					$(this).toggleClass('selected');
				});
				$el.appendTo($('#aclActions ul.group-list'));
			}
			
			if (typeof successCallback !== 'undefined'){
				successCallback();
			}
		}
	});
}

    function Access2All(el) {
	//Act
	var uri = $(el).closest('li').removeClass('has-access').addClass('has-allaccess').acldata('uri');
	actOnUri(uri, 'add', $('#roles').val());
	el.unbind('click').click(function (e) {e.stopPropagation();Access2None($(this))});
    }

    function Access2None(el) {
	//Act
	var uri = $(el).closest('li').removeClass('has-access').removeClass('has-allaccess').acldata('uri');
	actOnUri(uri, 'remove', $('#roles').val());
	el.unbind('click').click(function (e) {e.stopPropagation();Access2All($(this))});
    }

function actOnUri(uri, act, role) {
  var type = uri.split('#')[1].split('_')[0];
	var action = '';
	switch (type) {
		case 'e':
			action = 'Extension';
			break;

		case 'm':
			action = 'Module';
			break;

		case 'a':
			action = 'Action';
			break;
	}
	switch (act) {
		case 'add':
			action = "add"+action+"Access";
			break;

		case 'remove':
			action = "remove"+action+"Access";
			break;

		case 'mod2act':
			action = "moduleTo"+action+"Access";
			break;

		case 'mod2acts':
			action = "moduleToActionsAccess";
			break;

		case 'acts2mod':
			action = "actionsToModuleAccess";
			break;
	}
	//Do act
	$.ajax({
		type: "POST",
		url: context.root_url + "funcAcl/Admin/" + action,
		data: 'role='+role+'&uri='+uri,
		dataType: 'json',
		success: function(data) {
			
			var open = $('#aclModules .group.expendable.open').index();
			var $el = $('#aclModules .selected');
			var elidx;
                        
			if ($el.length) {
				uri = $el.acldata('uri');
				elidx = $el.index();
			} else elidx = -1;
			
			// update GUI
			loadModules($('#roles').val(), function() {
				
				if (open >= 0){
					$('#aclModules .group.expendable:eq('+open+')').removeClass('closed')
																   .addClass('open')
																   .find('.group-title').addClass('open');
				}
				
				if (elidx >= 0) {
					$('#aclModules .open li:eq('+elidx+')').addClass('selected');
					loadActions($('#roles').val(), uri);
				}
			});
		}
	});
    }
    
    return {
        start : function(){
            //Change role
            $('#roles').change(function() {
                    if ($('#roles').val() === ''){
                        $('#roleactions').hide();
                    } else {
                        $('#roleactions').show();
                    }
                    loadModules($('#roles').val());
            }).change();
        }
    };
});

