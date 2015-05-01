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
 * A hotspot interaction is a graphical interaction with a corresponding set of 
 * choices that are defined as areas of the graphic image. The candidate's task is 
 * to select one or more of the areas (hotspots). The hotspot interaction should 
 * only be used when the spacial relationship of the choices with respect to each 
 * other (as represented by the graphic image) is important to the needs of the item. 
 * Otherwise, choiceInteraction should be used instead with separate material for 
 * each option.
 * 
 * The delivery engine must clearly indicate the selected area(s) of the image and 
 * may also indicate the unselected areas as well. Interactions with hidden 
 * hotspots are achieved with the selectPointInteraction.
 * 
 * The hotspot interaction must be bound to a response variable with a baseType
 * of identifier and single or multiple cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class HotspotInteraction extends GraphicInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of choices that the candidate is allowed to select. If 
     * maxChoices is 0 there is no restriction. If maxChoices is greater than 
     * 1 (or 0) then the interaction must be bound to a response with multiple 
     * cardinality.
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
     * The hotspotChoice components of the hotspotInteraction.
     *
     * @var HotspotChoiceCollection
     * @qtism-bean-property
     */
    private $hotspotChoices;
    
    /**
     * Create a new HotspotInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the response associated to the interaction.
     * @param Object $object The associated image given as an Object object.
     * @param integer $maxChoices The maximum number of choices the candidate is allowed to select as a positive (>= 0) integer.
     * @param HotspotChoiceCollection $hotspotChoices The collection of HotspotChoice objects composing the HotspotInteraction.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, Object $object, $maxChoices, HotspotChoiceCollection $hotspotChoices, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $object, $id, $class, $lang, $label);
        $this->setMaxChoices($maxChoices);
        $this->setHotspotChoices($hotspotChoices);
    }
    
    /**
     * Set the maximum number of choices that the candidate is required
     * to select.
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
     * Get the maximum number of choices that the candidate is required to
     * select.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Set the minimum number of choices that the candidate is allowed to
     * select.
     * 
     * @param integer $minChoices A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minChoices is not a positive integer.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) === true && $minChoices >= 0) {
            $this->minChoices = $minChoices;
        }
        else {
            $msg = "The 'minChoices' argument must be a positive integer, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of choices that the candidate is allowed to
     * select.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    /**
     * Set the hotspotChoices composing the interaction.
     * 
     * @param HotspotChoiceCollection $hotspotChoices A collection of HotspotChoice objects.
     * @throws InvalidArgumentException If the given collection is empty.
     */
    public function setHotspotChoices(HotspotChoiceCollection $hotspotChoices) {
        if (count($hotspotChoices) > 0) {
            $this->hotspotChoices = $hotspotChoices;
        }
        else {
            $msg = "The 'hostspotChoices' argument must contain at least one hotspotChoice, 0 given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hotspotChoices composing the interaction.
     * 
     * @return HotspotChoiceCollection A collection of HotspotChoice objects.
     */
    public function getHotspotChoices() {
        return $this->hotspotChoices;
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array_merge(array($this->getObject()), $this->getHotspotChoices()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'hotspotInteraction';
    }
}