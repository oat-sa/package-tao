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

namespace oat\taoQtiItem\model\qti\container;

use oat\taoQtiItem\model\qti\container\FlowContainer;

/**
 * By implementing the FlowContainer interface, the object's content must mainly be represented
 * by a QTI element container. It must therefore provide a method to return its content as
 * a oat\taoQtiItem\model\qti\container\Container
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @see oat\taoQtiItem\model\qti\container\Container
 * @package taoQTI
 
 */
interface FlowContainer
{

    /**
     * Get the flow content
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return oat\taoQtiItem\model\qti\container\Container
     */
    public function getBody();

}
?>