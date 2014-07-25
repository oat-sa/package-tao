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
	
	var QTIattributeClassFunctions = {
		init:function(attributeName, value, validators, callbacks){
			
			this.value = value;
			this.history = [];
			this.formElements = []
			this.validators = [];
			
			if(!validators){
				validators = this.defaultValidatorsDefinitions();
			}
			
			for(var i in validators){
				if(validators[i].type){
					var options = (validators[i].options)?validators[i].options:{};
					this.addValidator(validators[i].type, options);
				}
			}
			
			this.getAttributeName = function(){
				return attributeName;
			}
			this.getCallbacks = function(){
				return callbacks;
			}
		},
		setValue:function(value){
			this.history.push(this.value);
			this.value = value;
		},
		getValue:function(){
			return this.value;
		},
		saveAttribute : function(value){
			
			var _this = this;
			
			//validate val: 
			var oldValue = (this.value == null)?null:this.value ;
			this.validateValue(value, function(ok, message){
				
				if(ok){
					CL('validated');
				
					//save to local datamodel:
					_this.setValue(value);
				
					//before save callback
					var beforeSaveCallback = _this.getCallback('beforeSave');
					if(beforeSaveCallback){
						beforeSaveCallback(oldValue, value, _this);
					}
				
					//save to server:
					qtiEdit.ajaxRequest({
						type: "POST",
						url: root_url + "taoQTI/QtiAuthoring/saveAttribute",
						data: {
							'type': _this.getType(),//??
							'serial': _this.getSerial(),//???
							'attribute':_this.getAttributeName(),
							'value':value
						},
						dataType: 'json',
						success: function(r){
							if(r.success){
								//call modified attribute callback:
								var saveSuccessCallback = _this.getCallback('saveSuccess');
								if(saveSuccessCallback){
									saveSuccessCallback(oldValue, value, _this);
								}
							}else{
								//call modified attribute callback:
								var saveFailCallback = _this.getCallback('saveFailed');
								if(saveFailCallback){
									saveFailCallback(oldValue, value, _this);
								}
							}
						}
					});//end of ajax call
				}
				
				
			});//end of this.validateAttributeValue
			
		},
		addValidator : function(type, options){
			var _this = this;
			type += '';// convert to string
			type = type.toLowerCase();
			if($.inArray(type, ['dummy','length', 'url', 'notempty', 'integer', 'uniqueidentifier'])){
				var className = type.charAt(0).toUpperCase() + type.substr(1);
				require([root_url  + 'taoQTI/views/js/qtiAuthoring/validators/class.' + className + '.js'], function(validatorClass){
					_this.validators.push(new validatorClass(options));
				});
			}else{
				throw new QTIauthoringException('validator', 'invalid validator type : '+type);
			}
		},
		//sunchronous validation only, stop on first fail
		validateValue : function(value, afterValidationCallback){
			var _this = this;
			
			var getNextValidator = function(){
				
				var returnValue = null;
				if(_this.currentValidatorIndex == null || typeof _this.currentValidatorIndex == 'undefined'){
					_this.currentValidatorIndex = 0;
				}
				
				if(_this.validators[_this.currentValidatorIndex]){
					returnValue = _this.validators[_this.currentValidatorIndex];
					_this.currentValidatorIndex ++;
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
					
					var onValidateCallback = _this.getCallback('onValidate');
					if(onValidateCallback){
						onValidateCallback(validator.getType(), ok, (ok)?'':validator.getMessage());
					}
					if(ok){
						_this.validateValue(value, afterValidationCallback);
					}else{
						endValidation(ok, validator.getMessage());
					}
				});
			}else{
				endValidation(true, []);
			}
		},
		//listen to a form element for changing:
		registerFormElement:function($elt, event, actionCallback, readFunction, writeFunction){
			
			var _this = this;
			
			$elt.bind(event+'.qtiAuthoring', actionCallback);//bind a function to a custom event
			
			$elt.bind('update.qtiAuthoring', function(){
				writeFunction($elt, _this.getValue());
			});
			
			$elt.bind('changing.qtiAuthoring', function(){
				validateValue(readFunction($elt), actionCallback);
			});
			
			$elt.bind('change.qtiAuthoring', function(){
				_this.saveValue(readFunction($elt));
			});
		}
	}
	
	var QTIattributeClass = Class.extend(QTIattributeClassFunctions);

	return QTIattributeClass;
});


