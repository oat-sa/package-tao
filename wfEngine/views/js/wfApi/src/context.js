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
 * WF API
 * It provides a tool to manage a recoverable context.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 * 
 */

/**
 *  The RecoveryContext enables you to initialize, 
 *  send and retrieve a data structure (a context).
 *  It can be used to recover a context in case of crash.
 *  
 * @class RecoveryContext
 */
function RecoveryContext (){
	
	//keep the current instance
	var _recoveryCtx = this;
	
	/**
	 * The registry store the contexts 
	 * @fieldOf RecoveryContext
	 * @type {Object}
	 */
	this.registry = null;
	
	/**
	 * @fieldOf RecoveryContext
	 * @type {bool}
	 */
	this.enabled = true;
	
	/**
	 * The parameters defining how and where to retrieve a context
	 * @fieldOf RecoveryContext
	 * @type {Object}
	 */
	this.sourceService = {
			type:	'sync',										// (async | sync | manual)
			data:	null,										//if type is manual, contains the data in JSON, else it should be null
			url:	root_url + 'wfEngine/RecoveryContext/retrieve',		//the url where we retrieve the context
			params: {},	 										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json'										//the response format, now ONLY JSON is supported
	};
	
	/**
	 * The parameters defining how and where to send a context
	 * @fieldOf RecoveryContext
	 * @type {Object}
	 */
	this.destinationService = {
			type:	'sync',									// (async | sync)
			url:	root_url + 'wfEngine/RecoveryContext/save',			//the url where we send the context
			params:  {},										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json',										//the response format, now ONLY JSON is supported
			flush:  false										//clear the context registry once the context is saved
	};
	
	/**
	 * Initialize the service interface for the source service: 
	 * how and where we retrieve a context
	 * @methodOf RecoveryContext
	 * @param {Object} environment
	 */
	this.initSourceService = function(environment){
		
		//define the source service
		if($.isPlainObject(environment)){
			
			if(environment.type){
				if($.inArray(environment.type, ['manual','sync', 'async']) > -1){
					this.sourceService.type = environment.type;
				}
			}
			//manual behaviour
			if(this.sourceService.type == 'manual' && $.isPlainObject(environment.data)){
				this.sourceService.data = environment.data;
			}
			else{ 	//remote behaviour
		
				if(environment.url){
					if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
						this.sourceService.url = environment.url;		//set url
					}
				}
				//ADD parameters
				if($.isPlainObject(environment.params)){	
					for(key in environment.params){
						if($.inArray((typeof environment.params[key]).toLowerCase(), ['string', 'number', 'int', 'float', 'boolean']) > -1){
							this.sourceService.params[key] = environment.params[key]; 
						}
					}
				}
				
				if(environment.method){
					if(/^get|post$/i.test(environment.method)){
						this.sourceService.method = environment.method;
					}
				}
			}
		}
	};
	
	/**
	 * Retrieve a context and populate the registry
	 * @methodOf RecoveryContext
	 */
	this.retrieveContext = function(){
			
		if(this.sourceService.type == 'manual'){
			this.registry = this.sourceService.data;
		}
		else{
			var ctxResponse = $.ajax({
				async		: false,
				url			: this.sourceService.url,
				data		: this.sourceService.params,
				type		: this.sourceService.method,
				dataType	: this.sourceService.format
			}).responseText;
			try{
				this.registry = $.parseJSON(ctxResponse);
			}
			catch(jsonException){ console.log(ctxResponse); }
			
		}
	};
	
	/**
	 * Initialize the service interface forthe destination service: 
	 * how and where we send the contexts
	 * @methodOf RecoveryContext
	 * @param {Object} environment
	 */
	this.initDestinationService = function(environment){
		
		if($.isPlainObject(environment)){
			
			if(environment.type){
				if($.inArray(environment.type, ['sync', 'async']) > -1){
					this.destinationService.type = environment.type;
				}
			}
			if(environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
					this.destinationService.url = environment.url;		//set url
				}
			}
			//ADD parameters
			if($.isPlainObject(environment.params)){	
				for(key in environment.params){
					if($.inArray((typeof environment.params[key]).toLowerCase(), ['string', 'number', 'int', 'float', 'boolean']) > -1){
						this.destinationService.params[key] = environment.params[key]; 
					}
				}
			}
			if(environment.method){
				if(/^get|post$/i.test(environment.method)){
					this.destinationService.method = environment.method;
				}
			}
			if(environment.flush){
				this.destinationService.flush = (environment.flush === true);
			}
		}
	};
	
	/**
	 * Save the contexts by sending them to the destination service 
	 * @methodOf RecoveryContext
	 */
	this.saveContext = function(){
		
		var registryParams = this.destinationService.params;
		registryParams['context'] = new Object();
		for(key in this.registry){
			registryParams['context'][key] = this.registry[key];
		}
		
		$.ajax({
				async		: (this.destinationService.type == 'async'),
				url  		: this.destinationService.url,
				data 		: registryParams,
				type 		: this.destinationService.method,
				dataType	: this.destinationService.format,
				success		: function(data){
			 		if(data.saved){
			 			if(_recoveryCtx.destinationService.flush){
			 				_recoveryCtx.registry = new Object();	//clear it but don't set it to null, to prevent retrieving
			 			}
			 		}
		 		}
			});
	};
	
	/**
	 * Get a context defined by the key. 
	 * If not loaded, we retrieve it⋅
	 * @methodOf RecoveryContext
	 * @param {String} key
	 * @returns {Object} the context
	 */
	this.getContext = function(key){
		if(this.enabled){
			if(this.registry == null){
				this.retrieveContext();
			}
			if(this.registry != null){
				return (this.registry[key]) ? this.registry[key] : {};
			}
		}
		return  {};
	};
	
	/**
	 * Create/edit a context
	 * @methodOf RecoveryContext
	 * @param {String} key
	 * @param {Object} value
	 */
	this.setContext = function(key, value){
		if(this.enabled){
			if(this.registry == null){
				this.registry = new Object();
			}
			if(key != ''){
				this.registry[key] = value;
			}
		}
	};
}
