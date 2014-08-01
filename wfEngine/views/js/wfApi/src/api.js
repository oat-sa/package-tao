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
 * @deprecated
 * @see wfApi.js
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @version 0.1
 */ 


  ////////////////////
 // WF Controls    //
////////////////////


/**  
 * @namespace wfApi
 */
var wfApi = { 
	context : window.parent.document || window.document
};

var root_url = root_url || '';


/**
 * Trigger the forward control:
 * go to the next activity
 * 
 * @function forward
 * 
 */
function forward(){
	var next = wfApi.context.getElementById('next');
	if(next){
		$(next).trigger('click');
	}
}

/**
 * Trigger the backward control:
 * load to the previous activity
 * 
 * @function backward
 */
function backward(){
	var back = wfApi.context.getElementById('back');
	if(back){
		$(back).trigger('click');
	}
}

/**
 * Trigger the pause control:
 * suspend the current activity and go back to the process control panel
 * 
 * @function pause
 */
function pause(){
	var pause = wfApi.context.getElementById('pause');
	if(pause){
		$(pause).trigger('click');
	}
}


  /////////////
 // STATES  //
/////////////

if(typeof(finish) != 'function'){

	/**
	 * Define the item's state as finished.
	 * This state can have some consequences.
	 * 
	 * @function finish
	 */
	function finish(){
		$(window).trigger(wf_STATE.ITEM.PRE_FINISHED);
		$(window).trigger(wf_STATE.ITEM.FINISHED);
		$(window).trigger(wf_STATE.ITEM.POST_FINISHED);
	}
	
	/**
	 * Add a callback that will be executed on finish state.
	 * 
	 * @function
	 * @param {function} callback
	 */
	function onFinish(callback){
		$(window).bind(wf_STATE.ITEM.FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but before the other callbacks  
	 * 
	 * @function
	 * @param {function} callback
	 */
	function beforeFinish(callback){
		$(window).bind(wf_STATE.ITEM.PRE_FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but after the other callbacks  
	 * 
	 * @function
	 * @param {function} callback
	 */
	function afterFinish(callback){
		$(window).bind(wf_STATE.ITEM.POST_FINISHED, callback);
	}

}

/*
 * By default, the variables are pushed when the item is finished
 */
afterFinish(forward);


  ///////////////
 // RECOVERY  //
///////////////

/**
 * instantiate the RecoveryContext
 * 
 * @function
 * @type RecoveryContext
 */
var recoveryCtx = new RecoveryContext();

/**
 * Initialize the interfaces communication for the context recovery.
 * The source service defines where and how we retrieve contexts.
 * The destination service defines where and how we send/save the contexts.
 * 
 * @function
 * 
 * 
 * @param {Object} source 
 * @param {String} [source.type = "sync"] the type of source <b>(sync|async|manual)</b>
 * @param {Object} [source.data] For the <i>manual</i> source type, set direclty the context data structure
 * @param {String} [source.url = "/wfEngine/RecoveryContext/retrieve"] For the <i>sync</i> and <i>async</i> source type, the URL of the remote service
 * @param {Object} [source.params] the parameters to send to the remote service
 * @param {String} [source.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [source.method = "post"] HTTP method of the sync service <b>(get|post)</b>
 * 
 * @param {Object} destination
 * @param {String} [destination.type = "async"] the type of source <b>(sync|async)</b>
 * @param {String} [destination.url = "/wfEngine/RecoveryContext/save"] the URL of the remote service
 * @param {Object} [destination.params] the common parameters to send to the service
 * @param {String} [destination.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [destination.method = "post"] HTTP method of the service <b>(get|post)</b>
 */
function initRecoveryContext(source, destination){
	recoveryCtx.initSourceService(source);
	recoveryCtx.initDestinationService(destination);
}

/**
 * Retrieve the context identified by the key 
 * 
 * @function
 * 
 * @param {String} key
 * @return {Object}
 */
function getRecoveryContext(key){
	return recoveryCtx.getContext(key);
}

/**
 * Set, send and save a context that could be recovered 
 * 
 * @function
 * 
 * @param {String} key
 * @param {Object} data
 */
function setRecoveryContext(key, data){
	recoveryCtx.setContext(key, data);
	recoveryCtx.saveContext();
}

/**
 * Remove a context (once you don't need you to recover it anymore) 
 * 
 * @function
 * 
 * @param {String} key
 */
function deleteRecoveryContext(key){
	setRecoveryContext(key, null);
}
