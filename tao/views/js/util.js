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

util = new Object();

util.log = function(arg1, arg2){
	if(console && typeof(console)!='undefined'){
		if(console.log){
			if(arguments && !util.msie()){
				console.log.apply(console, arguments);
			}else if(arg1){
				if(arg2){
					console.log(arg1, arg2);
				}else{
					console.log(arg1);
				}
			}
		}
	}
}

util.dir = function(object, desc){
	if(console && typeof(console)!='undefined'){
		if(console.log && console.dir && !util.msie()){
			if(desc){
				console.log(desc+':');
			}
			console.dir(object);
		}
	}
}

/**
 * Special config and functions to debug in ie8, where only console.log is available in console
 */
util.tab = '    ';
util.deep = 2;
util.printFunction = false;
util.childMax = 50;
util.dump = function(obj, keyValue, layer){
	
	if(!layer) layer = 1;
	
	var tab = function(){
		var tabstring = '';
		for(var i =0; i<layer; i++){
			tabstring += util.tab;
		}
		return tabstring;
	}
	
	var val = function(value){
		var returnValue = '';
		
		if(!value){
			if(value == null){
				returnValue = '(null) null';
			}else{
				switch(typeof(value)){
					case 'undefined':{
						returnValue = '(undefined) null';
						break;
					}
					case 'number':{	
						returnValue = '(number) 0';
						break;
					}
					case 'boolean':{
						returnValue = '(boolean) false';
						break;
					}
					case 'string':{
						returnValue = '(string) ""';
						break;
					}
					default:{
						returnValue = '(array or object) empty';
					}
				}
			}	
		}else{
			returnValue = '('+typeof(value)+') '+value;
		}
		
		return returnValue;
	}
	
	if(typeof(keyValue)!='undefined'){
		util.log(tab()+keyValue+':');
		layer++;
		if(layer>util.deep){
			util.log(tab()+'limit of deep '+util.deep+' reached');
			return false;
		}
	}else{
		util.log('___________________________ obj dump ___________________________');
	}
		
	if(typeof(obj)=='object'){
		
		var childCount = 0;		
		for(var key in obj){
		
			var value = obj[key];
			if(typeof(value)=='object'){
				util.dump(value, key, layer);
				childCount ++;
			}else{
				if(typeof(value)!='function'){
					util.log(tab()+key+': ', val(value));
					childCount ++;
				}else{	
					if(util.printFunction){
						util.log(tab()+key+': ', 'function(...)');
						childCount ++;
					}
				}					
			}
			
			if(childCount > util.childMax){
				util.log(tab()+'limit of '+util.childMax+' children reached');
				return false;
			}
		}
		if(!childCount){
			util.log(tab()+'nullObj('+typeof(obj)+'): ', val(obj));
		}
		 
	}else{
		if(typeof(obj)!='function'){
			util.log(tab()+'noObj('+typeof(obj)+'): ', val(obj));
		}else{
			if(util.printFunction){
				util.log(tab()+'noObj('+typeof(obj)+'): ', 'function(...)');
			}
		}
			
	}
	
}

/**
 * Handy shortcuts to debugging functions
 */
CL = util.log;
CD = util.dir;
_dump = util.dump;

/**
 * Special encoding of ouput html generated from ie8
 */
util.htmlEncode = function(encodedStr){
	
	var returnValue = '';
	
	if(encodedStr){
		//<br...> are replaced by <br... />
		returnValue = encodedStr;
		returnValue = returnValue.replace(/<br([^>]*)?>/ig, '<br />');
		returnValue = returnValue.replace(/<hr([^>]*)?>/ig, '<hr />');
		 
		//<img...> are replaced by <img... />
		returnValue = returnValue.replace(/(<img([^>]*)?\s?[^\/]>)+/ig,
			function($0, $1){
				return $0.replace('>', ' />');
			});
	}
	
	
	return returnValue;
}

util.getMediaResource = function(backgroundImagePath, qtiItem){
	
	if(backgroundImagePath && backgroundImagePath.substring(0,4) != 'http'){
		if(!qtiItem && qtiEdit.instances) qtiItem = qtiEdit.instances[0];
		backgroundImagePath = root_url + '/taoItems/Items/getMediaResource?path='+encodeURIComponent(backgroundImagePath);
		backgroundImagePath += '&classUri=' + qtiItem.itemClassUri + '&uri=' + qtiItem.itemUri;
	}
	
	return backgroundImagePath;
}

/**
 * Display a confirm modal box
 */
util.confirmBox = function(title, message, userDefinedButtons){
	
	var $dialog = $('#dialog-confirm');
	if($dialog && $dialog.length){
		var defaultButtons = {
			'Cancel': function(){
				$dialog.dialog('close');
			}
		}
		var buttons = $.extend(defaultButtons, userDefinedButtons);
		$dialog.attr('title', title);
		$dialog.find('span#dialog-confirm-message').html(message);
		$dialog.dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons:buttons
		});
	}
	
}

/**
 * Detect msie < 8, where opacity attribute is not supperted
 */
util.msie = function(){
	return (!$.support.opacity);
}

/**
 * Generates a facade that implements all functions
 * of the provided class
 * 
 * @param {Object} classInstance
 */
util.generateFacade = function(classInstance){
	
	function DelegationSkeleton() {
		this.implementation = null;
		this.pendingCalls = new Array();
	}

	DelegationSkeleton.prototype.setImplementation = function(implementation) {
		this.implementation = implementation;
		for (var i = 0; i < this.pendingCalls.length; i++) {
			this.pendingCalls[i](implementation);
		};
		this.pendingCalls = new Array();
	};

	DelegationSkeleton.prototype.__delegate = function(call) {
		if (this.implementation != null) {
			return call(this.implementation);
		} else {
			this.pendingCalls.push(function(implementation) {
				return call(implementation);
			});
		}
	};
	
	var Facade = function() {};
	Facade.prototype = new DelegationSkeleton();
	Facade.prototype.constructor = Facade;
	Facade.prototype.parent = DelegationSkeleton.prototype;
	
	for (var member in classInstance.prototype) {
		Facade.prototype[member] = function(member) {
			return function() {
				this.__delegate((function(argArray) {return function(implementation) {
					implementation[member].apply(implementation, argArray);
				}})(Array.slice(arguments)));
			};
		}(member);
	}
	
	return new Facade;
}