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
// alert("gtw loaded");
var GatewayProcessAuthoring = {name: 'process authoring ontology gateway'};

GatewayProcessAuthoring.addActivity = function(url, processUri){
	$.ajax({
		url: url,
		type: "POST",
		data: {processUri: processUri, type: 'activity'},
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				$(document).trigger('activityAdded', response);
			}else{
				$.error('error in adding an activity');
			}
		}
	});

}

GatewayProcessAuthoring.addInteractiveService = function(url, activityUri, serviceDefinitionUri){
	// console.log('url', url);
	// console.log('processUri', processUri);
	data = {activityUri: activityUri, type: 'interactive-service'};
	if(serviceDefinitionUri){
		data.serviceDefinitionUri = serviceDefinitionUri;
	}

	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				$(document).trigger('interactiveServiceAdded', response);
			}
		}
	});

}

GatewayProcessAuthoring.addConnector = function(url, prevActivityUri,typeOfConnector){

	// prevActivityUri of either a connector or an activity

	$.ajax({
		url: url,
		type: "POST",
		data: {"uri": prevActivityUri, "type":typeOfConnector},
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				$(document).trigger('connectorAdded', response);
			}else{
				throw 'error in adding a connector';
			}
		}
	});

}

GatewayProcessAuthoring.saveActivityProperties = function(url, activityUri, propertiesValues){

	var data = propertiesValues;
	data.activityUri = activityUri;

	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved) {
				$(document).trigger('activityPropertiesSaved', response);
			}else{
				throw 'error in saving activity properties';
			}
		}
	});

}

GatewayProcessAuthoring.deleteActivity = function(url, activityUri){
	if(confirm(__("Please confirm the deletion of the activity"))){
		$.ajax({
			url: url,
			type: "POST",
			data: {"activityUri": activityUri},
			dataType: 'json',
			success: function(response){
				if(response.deleted){
					$(document).trigger('activityDeleted', response);
				}else{
					throw 'error in deleteing the activity';
				}
			}
		});
	}
}

GatewayProcessAuthoring.deleteConnector = function(url, connectorUri){

	$.ajax({
		url: url,
		type: "POST",
		data: {"connectorUri": connectorUri},
		dataType: 'json',
		success: function(response){
			if(response.deleted){
				$(document).trigger('connectorDeleted', response);
			}else{
				throw 'error in deleteing the connector';
			}
		}
	});

}

GatewayProcessAuthoring.saveConnector = function(url, connectorUri, prevActivityUri, propertiesValues){

	var data = propertiesValues;
	data.connectorUri = connectorUri;
	data.activityUri = prevActivityUri;

	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved){
				$(document).trigger('connectorSaved', response);
			}else{
				throw 'error in saving connector';
			}
		}
	});

}

GatewayProcessAuthoring.selectElement = function(elementUri){
	$(document).trigger('elementSelected', response);
}