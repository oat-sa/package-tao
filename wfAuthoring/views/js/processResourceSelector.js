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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
function resourceSelector(identifier, resourceType){

	/*
	 * Open the list editor: a tree in a dialog popup 
	 */
	var dialogId = resourceType + '_dialog';
	var treeId = resourceType + '_tree';
	var closerId = resourceType + '_closer';
	
	//dialog content
	elt = $(identifier).parent("div");
	elt.append("<div id='"+ dialogId +"' style='display:none;' > " +
					"<span class='ui-state-highlight'>" + __('Select a test') + "</span><br /><br />" +
					"<div id='"+treeId+"' ></div> " +
				"</div>");
			
	//init dialog events
	$("#"+dialogId).dialog({
		width: 350,
		height: 400,
		autoOpen: false,
		title: __('Select a test')
	});
	
	$("#"+dialogId).bind('dialogclose', function(event, ui){
		$.tree.reference("#"+treeId).destroy();
		$("#"+dialogId).dialog('destroy');
		$("#"+dialogId).remove();
	});
	$("#"+closerId).click(function(){
		$("#"+dialogId).dialog('close');
	});
	$("#"+dialogId).bind('dialogopen', function(event, ui){
		
		dataUrl = root_url + "taoDelivery/DeliveryAuthoring/getTestData";
		 
		//create tree
		$("#"+treeId).tree({
			data: {
				type: "json",
				async : true,
				opts: {
					method : "POST",
					url: dataUrl
				}
			},
			types: {
			 "default" : {
					renameable	: true,
					deletable	: false,
					creatable	: false,
					draggable	: false
				}
			},
			ui: {
				theme_name : "custom"
			},
			callback: {
				onload: function(TREE_OBJ){
						TREE_OBJ.open_branch($("li.node-class:first"));
				},
				onselect: function(NODE, TREE_OBJ) {
					//select instance node only
					if($(NODE).hasClass('node-instance')){
						//set the value of the selected link in the textbox:
						$(identifier).val($(NODE).attr('val'));
						
						//close the dialog box:
						$("#"+dialogId).dialog('close');
					}
				}
			}
		});
	});
	$("#"+dialogId).dialog('open');
}
