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

use qtism\data\content\xhtml\Object;
use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Like hotspotInteraction, a select point interaction is a graphic interaction. 
 * The candidate's task is to select one or more points. The associated response 
 * may have an areaMapping that scores the response on the basis of comparing it 
 * against predefined areas but the delivery engine must not indicate these areas 
 * of the image. Only the actual point(s) selected by the candidate shall be 
 * indicated.
 * 
 * The select point interaction must be bound to a response variable with a 
 * baseType of point and single or multiple cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SelectPointInteraction extends GraphicInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of choices that the candidate is allowed to select. 
     * If maxChoices is 0 there is no restriction. If maxChoices is greater 
     * than 1 (or 0) then the interaction must be bound to a response with 
     * multiple cardinality.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxChoices = 1;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of choices that the candidate is required to select to 
     * form a valid response. If minChoices is 0 then the candidate is not required 
     * to select any choices. minChoices must be less than or equal to the limit 
     * imposed by maxChoices.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minChoices = 0;
    
    /**
     * Create a new SelectPointInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the response associated to the interaction.
     * @param Object $object The associated image as an Object object.
     * @param integer $maxChoices The maximum number of choices that the candidate is allowed to select as a positive (>= 0) integer.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the argument is invalid.
     */
    public function __construct($responseIdentifier, Object $object, $maxChoices, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $object, $id, $class, $lang, $label);
        $this->setMaxChoices($maxChoices);
        $this->setMinChoices(0);
    }
    
    /**
     * Get the maximum number of points that the candidate is allowed to select.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Set the maximum number of points that the candidate is allowed to select.
     * 
     * @param integer $maxChoices A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxChoices is not a positive integer.
     */
    public function setMaxChoices($maxChoices) {
        if (is_int($maxChoices) === true && $maxChoices >= 0) {
            $this->maxChoices = $maxChoices;
        }
        else {
            $msg = "The 'maxChoices' argument must be a positive (>= 0) integer, '" . gettype($maxChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of points that the candidate is allowed to select.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    /**
     * Set the minimum number of points that the candidate is allowed to select.
     * 
     * @param integer $minChoices A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minChoices is not a positive integer.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) === true && $minChoices >= 0) {
            $this->minChoices = $minChoices;
        }
        else {
            $msg = "The 'minChoices' argument must be a positive (>= 0) integer, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getObject()));
    }
    
    public function getQtiClassName() {
        return 'selectPointInteraction';
    }
}