/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
function qtiAuthoring(){}

/*
* Format common qti authoring form elements
*/
qtiAuthoring.initFormElements = function($container){
	qtiAuthoring.bindFormElementEventListner($container);
}

qtiAuthoring.bindFormElementEventListner = function($form){
	
	$form.find('select,input').each(function(){
		
		$(this).bind('focus', function(){
			CL('focused', $(this).attr('name'));
		});
		
		$(this).bind('blur', function(){
			CL('blurred', $(this).attr('name'));
		});
		
		$(this).bind('change', function(){
			CL('changed', $(this).attr('name'));
		});
	});
}

qtiAuthoring.initItemForm = function($form, QTIitem){
	$form.find('input[name=title]').keyup(function(){
		qtiEdit.setTitleBar($(this).val());
	}).blur(function(){
		QTIitem.saveAttribute('title', $(this).val());
	});
}

qtiAuthoring.initInteractionForm = function($form, QTIinteraction){

	$form.find('input, select, textarea').each(function(){
		var attribute = $(this).unbind('.qtiAuthoring').attr('name');
		switch(attribute){
			case 'maxChoices':
			case 'maxAssociations':{
				$(this).bind('keyup.qtiAuthoring', function(){
					QTIinteraction.validateAttributeValue(attribute, $(this).val(), function(ok){
						if(ok){
							//check maxChoice
							CL('checking max choice');
						}
					});
				}).bind('blur.qtiAuthoring', function(){
					QTIinteraction.saveAttribute(attribute, $(this).val());
				});
				break;
			}
			case 'shuffle':
			case 'orientation':{
				$(this).bind('change.qtiAuthoring', function(){
					QTIinteraction.saveAttribute(attribute, $(this).val());
				});
				break;
			}
			case 'prompt':{
				var getRelatedInnerDocument = function($elt){
					var iframe = $elt.parent().find('iframe.wysiwyg-interaction').get(0);
					if (iframe && iframe.nodeName.toLowerCase() === "iframe") {
						if (iframe.contentDocument){// Gecko
							return $(iframe.contentDocument);
						} else if (iframe.contentWindow){// IE
							return $(iframe.contentWindow.document);
						}
					}
					return null;
				}
				
				var $editor = $(this);
				var $innerDocument = getRelatedInnerDocument($editor);
				if($innerDocument){
					$innerDocument.unbind('.qtiAuthoring');
					$innerDocument.bind('keyup.qtiAuthoring', function(){
						//reinitialize counter
						}).bind('blur.qtiAuthoring', function(){
						$editor.wysiwyg('saveContent');
						var value = $editor.wysiwyg('getContent');
						CL('value', value);
						//save html data here
						QTIinteraction.saveAttribute(attribute, value);
					});
				}else{
					CL('cannot find the "prompt" iframe');
				}
				
				break;
			}
			case 'object_data':{
				$(this).bind('keyup.qtiAuthoring', function(){
					//check if file exists on server
					QTIinteraction.validateAttributeValue(attribute, $(this).val(), function(ok){
						if(ok){
					//render img
					}
					});
				}).bind('change.qtiAuthoring', function(){
					QTIinteraction.saveAttribute(attribute, $(this).val(),function(){
						//render img
						});
				});
				break;
			}
			case 'object_height':
			case 'object_width':{
				$(this).bind('keyup.qtiAuthoring', function(){
					QTIinteraction.validateAttributeValue(attribute, $(this).val(), function(ok){
						if(ok){
					//update image size
					}
					});
				}).bind('change.qtiAuthoring', function(){
					QTIinteraction.saveAttribute(attribute, $(this).val(),function(){
						//update image size
						});
				});
				break;
			}
		}
		
	});
	
}

qtiAuthoring.initChoiceForm = function($form, QTIchoice){

	$form.find('input, select').each(function(){
		var attribute = $(this).attr('name');
		switch(attribute){
			case 'choiceIdentifier':{
				$(this).bind('keyup.qtiAuthoring', function(){
					QTIchoice.validateAttributeValue(attribute, $(this).val(), function(ok){
						if(ok){
					//update choice identifier in grid cells_
					}
					});
				}).bind('blur.qtiAuthoring', function(){
					QTIchoice.saveAttribute(attribute, $(this).val());
				});
				break;
			}
			case 'fixed':
			case 'matchMax':
			case 'shape'://change shape => reset coords.
			case 'coords':
			case 'objectLabel':{
				$(this).bind('change.qtiAuthoring', function(){
					QTIchoice.saveAttribute(attribute, $(this).val());
				});
				break;
			}
			case 'value':
			case 'hotspotLabel':{
				$(this).bind('blur.qtiAuthoring', function(){
					QTIchoice.saveAttribute(attribute, $(this).val());
				});
				break;
			}
		}
		
	});
	
}

qtiAuthoring.prototype.register = function(event, id, object, callback, data){
	if(!this.events){
		this.events = {};
	}
	if(!this.events[event]){
		this.events[event] = {};
	}
	if(!data) data = {};
	this.events[event][id] = function(){
		callback(id, object, data);
	};
}

qtiAuthoring.prototype.unregister = function(event, id){
	
}

qtiAuthoring.prototype.trigger = function(event, id){
	if(this.events[event]){
		if(id){
			if(this.events[event][id]){
				this.events[event][id].call();
			}
		}else{
			for (var i in this.events[event]) {
				this.events[event][i].call();
			}
		}
	}
}