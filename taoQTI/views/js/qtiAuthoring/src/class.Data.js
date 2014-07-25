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
define(['require', 'jquery'], function(req, $){
	
	var QTIdataClassFunctions = {
		init : function(type, serial, options){
			
			this.getSerial = function(){
				return serial;
			}
			this.getType = function(){
				return type;
			}
			
			//for debug only!
			this.type = type;//private
			this.serial = serial;//private
			
			this.options = options;
			this.attributes = [];
			this.attributesCallbacks = {};
			this.attributesFormElements = {};
			
			try{
				this.initAttributesCallbacks(this.getDefaultCallbacks());
			}catch(e){
				console.warn('initAttributesCallbacks', e);
			}
		},
		
		getDefaultCallbacks : function(){
			return {};
		},
		
		initAttributesCallbacks : function(callbacks){
			
			for(var attributeKey in callbacks){
				
				for(var callbackName in callbacks[attributeKey]){
					
					switch(callbackName){
						case 'validators':{
							var validators = callbacks[attributeKey].validators;
							for(var i in validators){
								if(validators[i].type){
									var options = (validators[i].options)?validators[i].options:{};
									this.addAttributeValidator(attributeKey, validators[i].type, options);
								}
							}
							break;
						}
						//other authorized callback:
						case 'onChange':
						case 'beforeSave':
						case 'saveSuccess':
						case 'saveFailed':
						case 'afterValidation':{
							this.addAttributeCallback(attributeKey, callbackName, callbacks[attributeKey][callbackName]);
							break;
						}
					}
					
				}
				
			}
		},
		
		addAttributeCallback : function(attribute, callbackName, callbackFunction){
			if(typeof this.attributesCallbacks[attribute] == 'undefined'){
				this.attributesCallbacks[attribute] = {};
			}
			this.attributesCallbacks[attribute][callbackName] = callbackFunction;
		},
		
		addAttributeValidator : function(attribute, type, options){
			var _this = this;
			type += '';// convert to string
			type = type.toLowerCase();
			if($.inArray(type, ['dummy','length', 'url', 'notempty', 'integer'])){
				var className = type.charAt(0).toUpperCase() + type.substr(1);
				require([root_url  + 'taoQTI/views/js/qtiAuthoring/validators/class.' + className + '.js'], function(validatorClass){
					if(typeof _this.attributesCallbacks[attribute] == 'undefined'){
						_this.attributesCallbacks[attribute] = {};
					}
					if(typeof _this.attributesCallbacks[attribute].validators == 'undefined'){
						_this.attributesCallbacks[attribute].validators = [];
					}
					
					var validator =  new validatorClass(options);
					_this.attributesCallbacks[attribute].validators.push(validator);
				});
			}else{
				throw new QTIauthoringException('validator', 'invalid validator type : '+type);
			}
		},
		
		getAttributeValidators : function(){
			
		},
		
		getAttributeCallback : function(){
			
		},
		
		validateAttributeValue : function(attributeKey, value, afterValidationCallback){
			var returnValue = true;
			var messages = [];
			if(this.attributesCallbacks[attributeKey] && typeof this.attributesCallbacks[attributeKey].validators == 'array'){
				
				var onValidateCallback = this.getCallback(attributeKey, 'onValidate');
				
				for(var i in this.attributesCallbacks[attributeKey].validators){
					var validator = this.attributesCallbacks[attributeKey].validators[i];
					var success = validator.validate(value);
						
					if(onValidateCallback){
						onValidateCallback(validator.getType(), success, (success)?'':validator.getMessage());
					}
						
					if(!success){
						returnValue = false;
						break;
					}
				}
			}
			
			afterValidationCallback = (typeof afterValidationCallback == 'function')?afterValidationCallback:this.getCallback(attributeKey, 'afterValidation');
			if(afterValidationCallback){
				afterValidationCallback(success, messages);
			}
					
			return returnValue;
		},
		
		syncValidateAttributeValue : function(attributeKey, value, afterValidationCallback){
			
			var _this = this;
			var getNextValidator = function(){
				var returnValue = null;
				if(_this.currentValidatorIndex == null || typeof _this.currentValidatorIndex == 'undefined'){
					_this.currentValidatorIndex = 0;
				}
				if(_this.attributesCallbacks[attributeKey] && _this.attributesCallbacks[attributeKey].validators){
					if(typeof _this.attributesCallbacks[attributeKey].validators[_this.currentValidatorIndex]){
						returnValue = _this.attributesCallbacks[attributeKey].validators[_this.currentValidatorIndex];
						_this.currentValidatorIndex ++;
					}
				}
				return returnValue;
			};
			
			var endValidation = function(ok, messages){
				_this.currentValidatorIndex = null;
				afterValidationCallback = (typeof afterValidationCallback == 'function')?afterValidationCallback:_this.getCallback(attributeKey, 'afterValidation');
				if(afterValidationCallback){
					afterValidationCallback(ok, messages);
				}
			};
				
			var validator = getNextValidator();
			
			if(validator && typeof validator.validate =='function'){
				validator.validate(value, function(ok){
					
//					CL('validation '+validator.getType(), ok);
					
					var onValidateCallback = _this.getCallback(attributeKey, 'onValidate');
					if(onValidateCallback){
						onValidateCallback(validator.getType(), ok, (ok)?'':validator.getMessage());
					}
					if(ok){
						_this.syncValidateAttributeValue(attributeKey, value, afterValidationCallback);
					}else{
						endValidation(ok, validator.getMessage());
					}
				});
			}else{
				endValidation(true, []);
			}
				
		},
		getCallback:function(attributeKey, callbackName){
			var returnValue = null;
			if(this.attributes[attributeKey]){
				if(typeof this.attributes[attributeKey][callbackName] == 'function'){
					returnValue = this.attributes[attributeKey][callbackName];
				}
			}
			return returnValue;
		},
		saveAttribute : function(attributeKey, value){
			
			var _this = this;
			
			//validate val: 
			var oldValue = (this.attributes[attributeKey] == null)?null:this.attributes[attributeKey];
			this.syncValidateAttributeValue(attributeKey, value, function(ok, message){
				
				if(ok){
					CL('validated');
				
					//save to local datamodel:
					_this.attributes[attributeKey] = value;
				
					//before save callback
					var beforeSaveCallback = _this.getCallback(attributeKey, 'beforeSave');
					if(beforeSaveCallback){
						beforeSaveCallback(oldValue, value, _this);
					}
				
					//save to server:
					qtiEdit.ajaxRequest({
						type: "POST",
						url: root_url + "taoQTI/QtiAuthoring/saveAttribute",
						data: {
							'type': _this.getType(),
							'serial': _this.getSerial(),
							'attribute':attributeKey,
							'value':value
						},
						dataType: 'json',
						success: function(r){
							if(r.success){
								//call modified attribute callback:
								var saveSuccessCallback = _this.getCallback(attributeKey, 'saveSuccess');
								if(saveSuccessCallback){
									saveSuccessCallback(oldValue, value, _this);
								}
							}else{
								//call modified attribute callback:
								var saveFailCallback = _this.getCallback(attributeKey, 'saveFailed');
								if(saveFailCallback){
									saveFailCallback(oldValue, value, _this);
								}
							}
						}
					});//end of ajax call
				}
				
				
			});//end of this.validateAttributeValue
			
		}
	}
	
	var QTIdataClass = Class.extend(QTIdataClassFunctions);

	return QTIdataClass;
	
});


