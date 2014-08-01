<?php
/**
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\runtime\common\Variable;

/**
 * Implementation of AbstractStateOutput for IMS PCI JSON Representation based output.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_PciStateOutput extends taoQtiCommon_helpers_AbstractStateOutput {
    
    /**
     * Create a new taoQtiCommon_helpers_PciStateOutput.
     * 
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a variable to be transformed into its IMS PCI JSON Representation
     * for a later output.
     * 
     * @param Variable $variable
     */
    public function addVariable(Variable $variable) {
        $output = &$this->getOutput();
        $varName = $variable->getIdentifier();
        $marshaller = new taoQtiCommon_helpers_PciJsonMarshaller();
        $output[$varName] = $marshaller->marshall($variable->getValue(), taoQtiCommon_helpers_PciJsonMarshaller::MARSHALL_ARRAY);
    }
}