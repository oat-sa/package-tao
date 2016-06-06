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
/**
 * TAO API core.
 * It provides the tools to set up the environment, 
 * stock the data and push them to the server 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 */

/**
 * The TaoStack class enables you:
 * - to set up the platform to communicate with 
 * (it's by default the TAO plateform but could be any other with the same services provided by the server side) 
 * - to set and get variables created by the user or defined by the platform
 * - to manage the source of data that the item could need
 * - to push the communications with the platform
 *  
 * @class TaoStack
 */
function TaoStack (){
	
	
	/**
	 * This object describes the way the data are accessed 
	 * @fieldOf TaoStack
	 * @type {Object} 
	 */
	this.dataSource = new Object();
	
	//default data source environment
	this.dataSource.environment = {
		'type'		: 'async', 							// (manual|sync|async) 
		'url' 		: root_url + '/taoDelivery/ResultDelivery/initialize',	// the url to the server [NOT for manual type] 
		'params'	: { }								// the key/values to send to the server [NOT for manual type] 
	};
	
	//default data source settings
	this.dataSource.settings = {
		'format'		: 'json',		//only json is supported
		'method' 		: 'post',		//HTTP method (get|post) [NOT for manual type] 
		'load'			: 'onInit' 		// when the source is loaded (ONLY onInit is currently supported]
	};
	
	/**
	 * This object stores the contextual  data (sent by the server on load, or on getting them)
	 * @fieldOf TaoStack   
	 * @type {Object}
	 */
	this.dataStore = new Object();
	
	/**
	 * Initialize and setup the data source.
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {Object} environment 
	 * @see TaoStack.dataSource.environment
	 * 
	 * @param {Object} settings 
	 * @see TaoStack.dataSource.settings
	 * 
	 * @param {Object} source if manual data source
	 */
	this.initDataSource = function(environment, settings, source){
		if($.inArray(environment.type, ['manual','sync','async']) > -1){
			
			this.dataSource.environment.type = environment.type;
			
			if(this.dataSource.environment.type != 'manual'){
				
				//set the source url
				if(environment.url){
					if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url format
						this.dataSource.environment.url = url;		
					}
				}
					
				//and the parameters to add
				if($.isPlainObject(environment.params)){	
					for(key in params){
						if(isScalar(environment.params[key])){
							this.dataSource.environment.params[key] = environment.params[key]+''; 
						}
					}
				}
			}
			
			//set the source settings
			if($.isPlainObject(settings)){	
				if(settings.method){		//only the method is supported now
					if(/^get|post$/i.test(settings.method)){
						this.dataSource.settings.method = settings.method;
					}
				}
			}
			
			//load the source
			if(this.dataSource.settings.load == 'onInit'){
				this.loadData(source);
			}
		}
	};
	
	/**
	 * Load the contextual data 
	 * @methodOf TaoStack
	 * @param {Object} [source] the data ONLY for the manual source
	 */
	this.loadData = function(source){
		
		/** 
		 * Assign the 
		 * @param {Object} data to the 
		 * @param {TaoStack} instance 
		 */
		var populateData = function(data, instance){
			//we filter on what we want
			if($.isPlainObject(data)){
				for(key in data){
					for(uriKey in URI){
						if(URI[uriKey] == key ||  $.inArray(key, ['token', 'localNamespace']) > -1){
							instance.dataStore[key] = data[key];
						}	
					}
				}
			}
		};
		
		if(this.dataSource.environment.type == 'manual' && source != null){		
			
			//manual loading
			populateData(source, this);
		}
		else{		
			
			//sync|async loading, use an ajax request 
			var params = this.dataSource.environment.params;
			var instance = this;
			$.ajax({
				'url'  		: this.dataSource.environment.url,
				'data' 		: params,
				'type' 		: this.dataSource.settings.method,
				'async'		: (this.dataSource.environment.type == 'async'),
				'dataType'  : this.dataSource.settings.format,
				'success' 	: function(data){
					//we load the data sent back by the remote source, in the FORMAT defined
				
					if(data.token){		// the token field is MANDATORY
						populateData(data, instance);
					}
				}
			});
		}
	};
	
	/**
	 * The push data
	 * @fieldOf TaoStack
	 * @type {Object} 
	 */
	this.dataPush = new Object();
	this.dataPush.environment = {
		'url' 		: root_url + '/taoDelivery/ResultDelivery/save',					// the url to the server
		'params'	: {									// the params to send to the server at each communication 
			'token'	: this.dataStore.token				//these parameters comes from the dataStore
		}
	};
	this.dataPush.settings = {
		'format'		: 'json',	//only json is supported
		'method' 		: 'post',	//HTTP method to push the data (get|post)
		'async'			: false,	//if the request is asynchrone 
		'clearAfter'	: true		//if the variables stacks are cleared once pushed
	};

		
	/**
	 * Initialize and setup the push.
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {Object} environment 
	 * @see TaoStack#dataPush#environment
	 * @param {Object} settings 
	 * @see TaoStack#dataPush#settings
	 */
	this.initPush = function(environment, settings){
		
		if($.isPlainObject(environment)){
			if(environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
					this.dataPush.environment.url = environment.url;		//set url
				}
			}
			
			//ADD parameters
			if($.isPlainObject(environment.params)){	
				for(key in environment.params){
					if(isScalar(environment.params[key]) && !this.dataPush.environment.params[key]){	//don't edit the common params
						this.dataPush.environment.params[key] = environment.params[key]; 
					}
				}
			}
		}
		
		//set push settings
		if($.isPlainObject(settings)){	
			if(settings.method){
				if(/^get|post$/i.test(settings.method)){
					this.dataPush.settings.method = settings.method;
				}
			}
			if(settings.async === false){
				this.dataPush.settings.async = false;
			}
			if(settings.clearAfter === false){
				this.dataPush.settings.clearAfter = false;
			}
		}
	};
	
	/**
	 * push all the data in the stack to the server
	 * @methodOf TaoStack
	 */
	this.push = function(){
		
		var params = this.dataPush.environment.params;	//common parameters
		if(params.token == undefined){
			params.token = this.dataStore.token;
		}
		params['taoVars'] 	= new Object();
		params['userVars'] 	= this.userVars;
		
		for (key in this.taoVars){					//tao variables
			if(/^##NAMESPACE#/.test(key) && this.dataStore.localNamespace != undefined){
				newkey = key.replace('##NAMESPACE', this.dataStore.localNamespace);	//replace the localNamespace
				params['taoVars'][newkey]= this.taoVars[key];
			}
			else{
				params['taoVars'][key]= this.taoVars[key];
			}
		}
		
		 
		//push the data to the server
		if(this.dataPush.settings.async === true){
			var _instance = this;
			$.ajax({
				'url'  		: this.dataPush.environment.url,
				'data' 		: params,
				'type' 		: this.dataPush.settings.method,
				'async'		: true,
				'dataType'  : this.dataPush.settings.format,
				'success' 	: function(data){
	
					//the server send back the push status as
					//@example {"saved": true} or {"saved": false} for a json format
					if(data.saved == true){		
						//clear the stack 
						if(_instance.dataPush.settings.clearAfter === true){
							_instance.taoVars  = new Object();
							_instance.userVars = new Object();
						}
					}
				}
			});
		}
		else{
			received = $.parseJSON($.ajax({
				async		: false,
				url  		: this.dataPush.environment.url,
				data 		: params,
				type 		: this.dataPush.settings.method
			}).responseText);
			if(received.saved == true){		
				//clear the stack 
				if(this.dataPush.settings.clearAfter === true){
					this.taoVars  = new Object();
					this.userVars = new Object();
				}
			}
		}
	};
	
/* TAO Variables */
	
	/**
	 * The stack container
	 * @fieldOf TaoStack
	 * @type {Object} 
	 */
	this.taoVars = new Object();
	
	/**
	 * Get the value of a TAO varaiable identified by the key
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {String} key
	 * @param {boolean} [label] if you want to retrieve the label instead of the complete Object
	 * @returns {mixed} value (false if the key is not found)
	 */
	this.getTaoVar = function(key, label){
		
		//we check if the data are 
		var value =  false;
		if(this.taoVars[key]){			//set by the taoVar
			value =  this.taoVars[key];
		}
		else if (this.dataStore[key]){	//or comes from the dataStore
			value =  this.dataStore[key];
		}
		
		if($.isPlainObject(value)){
			if( (value['uri'] != undefined && value[URI.LABEL] != undefined && value.length == 2) || label){
				return value[URI.LABEL];
			}
		}
		return value;
	};
	
	/**
	 * The set method is restricted to scalar,
	 * but could be used to reference a property node
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {String} key
	 * @param {String|number|boolean} value
	 * @param {String} [property] the property uri 
	 */
	this.setTaoVar = function(key, value, property){
		
		if(isScalar(value)){
		
			var currentValue =  this.getTaoVar(key);
			if($.isPlainObject(currentValue)){
				if(property){
					this.taoVars[key][property] = value;
				}
				else if( value.indexOf('uri') > -1 && value.indexOf(URI.LABEL) > -1){
					this.taoVars[key][URI.LABEL] = value;
				}
			}
			else{
				this.taoVars[key] = value;
			}
		}
	};
	
/* Custom Variables */
	
	/**
	 * The user custom variables container 
	 * @fieldOf TaoStack
	 * @type {Object} 
	 */
	this.userVars = new Object();
	
	/**
	 * Get the value of a previously defined user's custom variable, identified by it's key
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {String} key
	 * @returns {String|number|boolean} value (false if the key is not found)
	 */
	this.getUserVar = function(key){
		return (this.userVars[key]) ? this.userVars[key] : false;
	};
	
	/**
	 * The item author can define it's own variables in order to keep them in the stack
	 * and to send them to the plateform. It's usefull to record cutom field and values
	 * that have not been taken in consideration but  have a real interest in the item.
	 * 
	 * @methodOf TaoStack
	 * 
	 * @param {String} key
	 * @param {String|number|boolean} value
	 */
	this.setUserVar = function(key, value){
		if(isScalar(value)){
			this.userVars[key] = value;
		}
	};
}


/**
 * Utility function to check if a value is a scalar:
 * (string, integer, float and boolean) 
 * 
 * @param {mixed} value
 * @returns {bool} true if it's a scalar
 */
function isScalar(value){
	return ($.inArray((typeof value).toLowerCase(), ['string', 'number', 'int', 'float', 'boolean']) > -1);
}
