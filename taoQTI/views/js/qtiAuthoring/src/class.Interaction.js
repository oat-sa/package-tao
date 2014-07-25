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
define(['require', 'jquery', root_url  + 'taoQTI/views/js/qtiAuthoring/src/class.Data.js'], function(req, $, QTIdataClass){
	
	var QTIinteractionClassFunctions = {
		init:function(type, serial, options){
			this.getInteractionType = function(){
				return type;
			}
			
			this._super('interaction', serial, options);
			this.choices = [];
		},
		addChoices:function(count){
			//append choices to the end of data, then reload response?
		},
		addChoicesFromData:function(data){
			/*
			 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do 
			 * eiusmod tempor {{choice:newInteraction}} ut labore et dolore magna aliqua. Ut enim
			 * ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut 
			 * aliquip ex ea commodo consequat. {{newInteraction:graphicAssociate}} Duis aute irure dolor in 
			 * reprehenderit in voluptate velit esse cillum dolore eu fugiat 
			 * nulla pariatur. {{choice:serial_123456}} sint occaecat cupidatat non proident, 
			 * sunt in culpa qui officia deserunt mollit anim id est laborum.
			 */
		},
		initChoice:function(type, serial){
			var _this = this;
			require([root_url  + 'taoQTI/views/js/qtiAuthoring/src/class.Choice.js'], function(ChoiceClass){
				_this.choices[serial] = new ChoiceClass(type, serial);
			});
		},
		getChoice:function(serial){
			var returnValue = null;
			if(this.choices[serial]){
				returnValue = this.choices[serial];
			}
			return returnValue;
		}
		
	}
	
	return QTIdataClass.extend(QTIinteractionClassFunctions);
	
});