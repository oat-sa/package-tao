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
 *               
 * 
 */
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi
 * 
 * This file provide functions to drive the workflow engine from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  ////////////////////
 // WF Controls    //
////////////////////


/**  
 * @namespace wfApi
 */
if(typeof wfApi == 'undefined'){
	wfApi = {};
}

/**
 * The wfengine controlers name
 */
wfApi.ProcessDefinitionControler 	= 'WfApiProcessDefinition';
wfApi.ProcessExecutionControler 	= 'WfApiProcessExecution';
wfApi.ActivityExecutionControler 	= 'WfApiActivityExecution';
wfApi.VariableControler 			= 'WfApiVariable';
wfApi.RecoveryContextControler 		= 'RecoveryContext';

/**
 * Request the workflow engine API on the server
 * @param {String} controler The controler to request
 * @param {String} action The action to request
 * @param {Array} options The options to send to the action
 */
wfApi.request = function(controler, action, parameters, successCallback, errorCallback, options)
{
	var options = typeof options != ('undefined') ? options : new Array();
	var async = typeof options.async != ('undefined') ? options.async : true;
	var url = root_url+'/wfEngine/'+controler+'/'+action;
	$.ajax({
		'url'			: url
		, 'type' 		: 'GET'
		, 'dataType'	: 'json'
		, 'data'		: parameters
		, 'async'		: async
		, 'success'		: function(response){
			if(response.success){
				if(typeof successCallback != 'undefined'){
					successCallback(response.data);
				}
			}
			else{
				if(typeof errorCallback != 'undefined'){
					errorCallback(response.data);
				}
			}
		}
		, 'error' 		: function(){
			errorCallback();
		}
	});
}

