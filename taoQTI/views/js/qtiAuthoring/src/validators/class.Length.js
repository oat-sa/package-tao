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
define(['require', 'jquery', root_url  + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Validator.js'], function(req, $, ValidatorClass){
	
	var validatorClassFunctions = {
		init:function(options){
			this._super('length', options);
			if(this.options.min != null && this.options.max != null){
				this.message = __('Invalid field length')+" (minimum "+this.options.min+", maximum "+this.options.max+")";
			}else if(this.options.min != null && this.options.max == null){
				this.message = __('This field is too short')+" (minimum "+this.options.min+")";
			}else if(this.options.min == null && this.options.max != null){
				this.message = __('This field is too long')+" (maximum "+this.options.max+")";
			}else{
				throw 'validator definition : invalid options';
				CL('invalid options', this.options);
			}
		},
		getDefaultOptions:function(){
			return {
				min:0,
				max:null,
				allowEmpty:false
			};
		},
		validate:function(value, callback){
			var returnValue = true;
			if(this.options.min != null && value.length<this.options.min){
				if(this.options.allowEmpty && value.length == 0){
					//ok
				}else{
					returnValue = false;
				}
			}
			if(this.options.max && value.length>this.options.max){
				returnValue = false;
			}
			callback(returnValue);
			return returnValue;
		}
		
	}
	
	return ValidatorClass.extend(validatorClassFunctions);
});