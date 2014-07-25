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
define(['require', 'jquery', root_url  + 'taoQTI/views/js/qtiAuthoring/class.Attribute.js'], function(req, $, QTIattributeClass){
	
	var QTIattributeClassFunctions = {
		init:function(value, serial, options){
			
			this._super('title', value, options);
		},
		
		defaultValidatorsDefinitions:function(){
			return [
				{type:'notempty'},
				{type:'length', options:{max:150}}
			];
		}
		
	}
	
	return QTIattributeClass.extend(QTIattributeClassFunctions);
	
});