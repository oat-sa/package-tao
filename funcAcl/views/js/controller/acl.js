define(['jquery', 'context', 'util/encode', 'i18n'], function($, context, encode, __){
    
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
	$('#aclRoles ul.included-roles').empty();
	if (role === '') return;

	$.ajax({
		type: "POST",
		url: context.root_url + "funcAcl/Admin/getModules",
		data: 'role='+role,
		dataType: 'json',
		success: function(data) {
			for (var r in data['includedRoles']) {
				var $role = $('<li>'+ encode.html(data['includedRoles'][r]) +'</li>');
				$role.appendTo($('#aclRoles ul.included-roles'));
			}
			for (var e in data['extensions']) {
				var ext = data['extensions'][e];
				
				switch (ext['access']) {
					case 'inherited':
						groupCheckboxTitle = __('Inherited access to the extension');
						extra = ' has-inherited';
						break;
					case 'full':
						groupCheckboxTitle = __('Revoke access rights to the entire extension');
						extra = ' has-allaccess';
						break;
					case 'partial':
						extra = ' has-access';
						groupCheckboxTitle = __('Grant access rights to the entire extension');
						break;
					default:
						// no access
						extra = '';
						groupCheckboxTitle = __('Grant access rights to the entire extension');
						break;
				}
				
				var $group = $('<li class="group expendable closed'+extra+'"><div class="group-title"><span class="ui-icon ui-icon-triangle-1-e"/><span class="title">'+ ext['label'] +'</span>'
						+ '<span class="selector all '+(ext['access'] == 'inherited' ? 'has-inherited' : 'checkable')+'" title="' + groupCheckboxTitle + '"></span></div><ul></ul></li>');
				$group.acldata('uri', ext.uri);
				
				switch (ext['access']) {
					case 'full':
						$('.selector', $group).click(function (e) {
							e.stopPropagation();
							Access2None($(this))
						});
						break;
					case 'partial':
						$('.selector', $group).click(function (e) {
							e.stopPropagation();
							Access2All($(this))
						});	
						break;
					case 'none':
						// no access
						$('.selector', $group).click(function (e) {e.stopPropagation();Access2All($(this))});
						break;
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
					switch (mod['access']) {
						case 'inherited':
							modCheckboxTitle = __('Inherited access to the controller');
							extra = ' has-inherited';
							break;
						case 'full':
							modCheckboxTitle = __('Revoke access rights to the entire controller');
							extra = ' has-allaccess';
							break;
						case 'partial':
							extra = ' has-access';
							modCheckboxTitle = __('Grant access rights to the entire controller');
							break;
						default:
							// no access
							extra = '';
							modCheckboxTitle = __('Grant access rights to the entire controller');
							break;
					}
					
					var $el = $('<li class="selectable'+extra+'"><span class="label">'+ mod['label'] +'</span><span class="selector '+(mod['access'] == 'inherited' ? 'has-inherited' : 'checkable') + '" title="'+ modCheckboxTitle +'"></span></li>');
					$el.acldata('uri', mod.uri);
					
					switch (mod['access']) {
						case 'full':
							$('.selector', $el).click(function (e) {e.stopPropagation();Access2None($(this))});
							break;
						case 'partial':
							$('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
							break;
						case 'none':
							// no access
							$('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
							break;
					}
					
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
				for (e in data) {
					var act = data[e];
					
					var extra = '';
					switch (act['access']) {
						case 'inherited':
							actCheckBoxTitle = __('Inherited access to the action');
							break;
						case 'full':
							extra = ' has-allaccess';
							actCheckBoxTitle = __('Revoke access rights to the action');
							break;
						default:
							// no access
							actCheckBoxTitle = __('Grant access rights to the action');
					}
					
					var $el = $('<li class="selectable'+extra+'"><span class="label">'+ e +'</span><span class="selector '+(act['access'] == 'inherited' ? 'has-inherited' : 'checkable')+'" title="'+ actCheckBoxTitle +'"></span></li>');
					$el.acldata('uri', act.uri);

					switch (act['access']) {
						case 'full':
							$('.selector', $el).click(function (e) {e.stopPropagation();Access2None($(this))});
							break;
						case 'none':
							// no access
							$('.selector', $el).click(function (e) {e.stopPropagation();Access2All($(this))});
							break;
					}
					
					//Select action
					//$el.click(function() {
					//	$('#aclActions .selectable').removeClass('selected');
					//	$(this).toggleClass('selected');
					//});
					$el.appendTo($('#aclActions ul.group-list'));
				}
				
				if (typeof successCallback !== 'undefined'){
					successCallback();
				}
			}
		});
    }

		    function Access2All(el) {
				// Act
				var uri = $(el).closest('li').removeClass('has-access')
						.addClass('has-allaccess').acldata('uri');
				actOnUri(uri, 'add', $('#roles').val());
				el.unbind('click').click(function(e) {
					e.stopPropagation();
					Access2None($(this))
				});
			}

			function Access2None(el) {
				// Act
				var uri = $(el).closest('li').removeClass('has-access')
						.removeClass('has-allaccess').acldata('uri');
				actOnUri(uri, 'remove', $('#roles').val());
				el.unbind('click').click(function(e) {
					e.stopPropagation();
					Access2All($(this))
				});
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
		}
		//Do act
		$.ajax({
			type: "POST",
			url: context.root_url + "funcAcl/Admin/" + action,
			data: 'role='+role+'&uri='+uri,
			dataType: 'json',
			success: function(data) {
				
				var open = [];
				$('#aclModules .group.expendable.open').each(function() {open.push($(this).index())});
				
				var $el = $('#aclModules .selected');
				var elidx;
				var selectedUri = null;
	                        
				if ($el.length) {
					selectedUri = $el.acldata('uri');
					elidx = $el.index();
				} else elidx = -1;
				
				// update GUI
				loadModules($('#roles').val(), function() {

					for (var i in open) {
						$('#aclModules .group.expendable:eq('+open[i]+')').removeClass('closed')
						   .addClass('open')
						   .find('.group-title').addClass('open');
					}
					
					if (selectedUri != null) {
						$('#aclModules .open li').each(function() {
							if ($(this).acldata('uri') == selectedUri) {
								$(this).addClass('selected');
							}
						});
						loadActions($('#roles').val(), selectedUri);
						//$('#aclModules .open li:eq('+elidx+')').addClass('selected');
						//loadActions($('#roles').val(), uri);
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

