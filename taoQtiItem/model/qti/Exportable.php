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

use oat\taoQtiItem\model\qti\Exportable;

/**
 * By implementing the exportable interface, the object must export it's data to
 * formats defined here.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoQTI
 
 */
interface Exportable
{

    /**
     * Export the data to QTI XML format
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI();

    /**
     * Get the array representation of the Qti Element.
     * Particularly helpful for data transformation, e.g. json
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function toArray($filterVariableContent = false, &$filtered = array());

}