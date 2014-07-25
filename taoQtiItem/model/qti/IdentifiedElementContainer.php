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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\IdentifiedElementContainer;

/**
 * By implementing the IdentifiedElementContainer interface, the object declares the list of
 * identified QTI elments it contains.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @see oat\taoQtiItem\model\qti\IdentifiedElementContainer
 * @package taoQTI
 
 */
interface IdentifiedElementContainer
{

    /**
	 * Every identified QTI element must declare the list of (string) identifers being used within it
     * This method gets all identified Qti Elements contained in the current Qti Element
     * 
	 * @author Sam, <sam@taotesting.com>
     * @return oat\taoQtiItem\model\qti\IdentifierCollection
	 */
	public function getIdentifiedElements();

}