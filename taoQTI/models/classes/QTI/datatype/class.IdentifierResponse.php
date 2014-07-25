<?php
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

/**
 * The Response Identifier datatype holds the reference to a Response Declaration
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_datatype_IdentifierResponse extends taoQTI_models_classes_QTI_datatype_Identifier
{
	
	/**
	 * Define the array of authorized QTI element classes
	 * 
	 * @return array
	 */
	public static function getAllowedClasses(){
		return array(
			'taoQTI_models_classes_QTI_ResponseDeclaration'
		);
	}
	
} /* end of class taoQTI_models_classes_QTI_datatype_IdentifierResponse */