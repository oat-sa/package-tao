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

namespace qtism\data\content\interactions;

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The upload interaction allows the candidate to upload a pre-prepared file representing
 * their response. It must be bound to a response variable with base-type file and single 
 * cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class UploadInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The upload interaction allows the candidate to upload a pre-prepared
     * file representing their response. It must be bound to a response 
     * variable with base-type file and single cardinality.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $type = '';
    
    /**
     * Create a new UploadInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param string $id The identifier of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct($responseIdentifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
    }
    
    /**
     * Set the expected mime-type of the uploaded file.
     * 
     * @param string $type A mime-type.
     * @throws InvalidArgumentException If $type is not a string value.
     */
    public function setType($type) {
        if (is_string($type) === true) {
            $this->type = $type;
        }
        else {
            $msg = "The 'type' argument must be a string value, '" . gettype($type) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the expected mime-type of the uploaded file.
     * 
     * @return string A mime-type.
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Whether or not a value is defined for the 'type'
     * attribute.
     * 
     * @return boolean
     */
    public function hasType() {
        return $this->getType() !== '';
    }
    
    public function getComponents() {
        return parent::getComponents();
    }
    
    public function getQtiClassName() {
        return 'uploadInteraction';
    }
}