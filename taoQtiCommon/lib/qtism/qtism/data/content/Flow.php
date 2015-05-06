<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content;

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Elements that can appear inside list items, table cells, etc. 
 * which includes block-type and inline-type elements.
 * 
 * @authorJérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface Flow extends ObjectFlow {
    
    /**
     * Set the URI to change the base for resolving relative
     * URIs for the scope of this object.
     * 
     * @param string $base A URI or an empty string if the there is no base set.
     * @throws InvalidArgumentException If $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($base = '');
    
    /**
     * Get the URI that changes the base for resolving relative URIs
     * for the scope of this object.
     * 
     * @return string A URI or an empty string if no base is set.
     */
    public function getXmlBase();
    
    /**
     * Whether or not a value is defined for the xml:base attribute.
     * 
     * @return boolean
     */
    public function hasXmlBase();
}