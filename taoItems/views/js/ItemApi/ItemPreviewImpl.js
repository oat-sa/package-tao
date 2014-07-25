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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
function ItemPreviewImpl() {

	this.responses = {};
	this.scores = {};
	this.events = {};
	
	this.beforeFinishCallbacks = new Array();
}

ItemPreviewImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) === "function") {
		frame.contentWindow.onItemApiReady(this);
	}
}

// Response 

ItemPreviewImpl.prototype.saveResponses = function(valueArray){
	for (var attrname in valueArray) {
		this.responses[attrname] = valueArray[attrname];
	}
}

ItemPreviewImpl.prototype.traceEvents = function(eventArray) {
	for (var attrname in eventArray) {
		this.events[attrname] = eventArray[attrname];
	}
}

ItemPreviewImpl.prototype.beforeFinish = function(callback) {
	this.beforeFinishCallbacks.push(callback);
}

// Scoring
ItemPreviewImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
}

// Variables
ItemPreviewImpl.prototype.setVariable = function(identifier, value) {
	// do nothing in preview
};

ItemPreviewImpl.prototype.getVariable = function(identifier, callback) {
	// always return null in preview
	callback(null);
};

// Flow
ItemPreviewImpl.prototype.finish = function() {

	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};
	
	// submit Results
	this.log('state', 'item is now finished!');
	var strOutcomes = '';
	for (var outcomeKey in this.scores){
		strOutcomes += '[ ' + outcomeKey+ ' = ' + this.scores[outcomeKey] + ' ]';
	}
	window.top.helpers.createInfoMessage('THE OUTCOME VALUES : <br/>'  + strOutcomes);
	this.log('responses', this.responses);
	this.log('outcomes', this.scores);
};

ItemPreviewImpl.prototype.log = function(title, message) {
	if (typeof(message) == 'object') {
		string = '';
		for (var attrname in message) {
			string += ', ' + attrname+ '=' + message[attrname];
		}
		message = '{' + string.substring(2) + '}';
	}
	previewConsole = $('#preview-console');
	if (previewConsole.length > 0){
		//In the preview console
		previewConsole.trigger('updateConsole', [title, message]);
	} else {
		//outside preview container
		util.log(title + ': ' + message);
	}
}