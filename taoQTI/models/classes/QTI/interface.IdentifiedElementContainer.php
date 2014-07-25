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
 * By implementing the FlowContainer interface, the object declares the list of
 * identified QTI elments it contains.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @see taoQTI_models_classes_QTI_IdentifiedElementContainer
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
interface taoQTI_models_classes_QTI_IdentifiedElementContainer
{

    /**
	 * Every identified QTI element must declare the list of (string) identifers being used within it

	 * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_IdentifierCollection
	 */
	public function getIdentifiedElements();

}