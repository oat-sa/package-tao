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
			this._super('notempty', options);
			this.message = (typeof this.options.message == 'string')?this.options.message:this.getDefaultOptions().message;
		},
		getDefaultOptions:function(){
			return {
				message:__('This field is required')
			};
		},
		validate:function(value, callback){
			var returnValue = false;
			
			if (typeof value == 'string'){
				value = $.trim(value);
				if(value.length >= 1){
					returnValue = true;
				}
			}
			
			callback(returnValue);
			return returnValue;
		}
		
	}
	
	return ValidatorClass.extend(validatorClassFunctions);
});