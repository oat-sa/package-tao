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

use qtism\data\QtiComponentCollection;
use qtism\data\content\xhtml\Object;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * A graphic order interaction is a graphic interaction with a corresponding set 
 * of choices that are defined as areas of the graphic image. The candidate's 
 * task is to impose an ordering on the areas (hotspots). The order hotspot 
 * interaction should only be used when the spacial relationship of the choices
 * with respect to each other (as represented by the graphic image) is important 
 * to the needs of the item. Otherwise, orderInteraction should be used instead 
 * with separate material for each option.
 * 
 * The delivery engine must clearly indicate all defined area(s) of the image.
 * 
 * The order hotspot interaction must be bound to a response variable with a 
 * baseType of identifier and ordered cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GraphicOrderInteraction extends GraphicInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The hotspots that define the choices that are to be ordered by the candidate.
     * If the delivery system does not support pointer-based selection then the 
     * order in which the choices are given must be the order in which they are 
     * offered to the candidate for selection. For example, the 'tab order' in 
     * simple keyboard navigation.
     * 
     * @var HotspotChoiceCollection
     * @qtism-bean-property
     */
    private $hotspotChoices;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of choices that the candidate must select and order to form a 
     * valid response to the interaction. If specified, minChoices must be 1 or greater but 
     * must not exceed the number of choices available. If unspecified, all of the choices 
     * must be ordered and maxChoices is ignored.
     * 
     * If $minChoices is a negative value, it means it was not specified.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minChoices = -1;
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of choices that the candidate may select and order when responding to the interaction.
     * Used in conjunction with minChoices, if specified, maxChoices must be greater than or equal to minChoices 
     * and must not exceed the number of choices available. If unspecified, all of the choices may be ordered.
     * 
     * 
     * If $maxChoices is a negative value, it means it was not specified.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxChoices = -1;
    
    /**
     * Create a new GraphicOrderInteraction object.
     * 
     * @param string $responseIdentifier The response identifier associated to the interaction.
     * @param Object $object The image associated with the interaction as an object.
     * @param HotspotChoiceCollection $hotspotChoices A collection of HotspotChoice objects that define the choices that are to be ordered.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, Object $object, HotspotChoiceCollection $hotspotChoices, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $object, $id, $class, $lang, $label);    
        $this->setHotspotChoices($hotspotChoices);
    }
    
    /**
     * Set the hotspots that define the choices that are to be ordered by the candidate.
     * 
     * @param HotspotChoiceCollection $hotspotChoices A collection of HotspotChoice objects.
     * @throws InvalidArgumentException If the given $hotspotChoices is empty.
     */
    public function setHotspotChoices(HotspotChoiceCollection $hotspotChoices) {
        if (count($hotspotChoices) > 0) {
            $this->hotspotChoices = $hotspotChoices;
        }
        else {
            $msg = "A GraphicOrderInteraction must contain at least 1 hotspotChoice object. None given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hotspots that define the choices that are to be ordered by the candidate.
     * 
     * @return HotspotChoiceCollection A collection of HotspotChoice objects.
     */
    public function getHotspotChoices() {
        return $this->hotspotChoices;
    }
    
    /**
     * Set the minimum number of choices that the candidate must select and order to form a valid response. A negative
     * value indicates that no minChoice is indicated.
     * 
     * @param integer $minChoices A strictly negative or positive integer.
     * @throws InvalidArgumentException If $minChoice is not a strictly negative or positive integer.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) && $minChoices !== 0) {
            
            if ($minChoices > count($this->getHotspotChoices())) {
                $msg = "The 'minChoices' argument must not exceed the number of choices available.";
                throw new InvalidArgumentException($msg);    
            }
            
            $this->minChoices = $minChoices;
        }
        else {
            $msg = "The 'minChoices' argument must be a strictly negative or positive integer, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of choices that the candidate must select and order to form a valid response. A negative
     * value indicates that no minChoice is indicated.
     * 
     * @return A strictly negative or positive integer.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    /**
     * Whether or not a value is defined for the minChoices attribute.
     * 
     * @return boolean
     */
    public function hasMinChoices() {
        return $this->getMinChoices() > -1;
    }
    
    /**
     * Set the maximum number of choices that the candidate must select and order to form a valid response. A negative
     * value indicates that no maxChoice is indicated.
     * 
     * @param integer $maxChoices A strictly negative or positive integer.
     * @throws InvalidArgumentException If $maxChoices is not a strictly negative or positive integer.
     */
    public function setMaxChoices($maxChoices) {
        if (is_int($maxChoices) && $maxChoices !== 0) {
            
            if ($this->getMinChoices() > 0 && $maxChoices < $this->getMinChoices()) {
                $msg = "The 'maxChoices' argument must be greater than or equal to the 'minChoices' attribute.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->maxChoices = $maxChoices;
        }
        else {
            $msg = "The 'maxChoices' argument must be a strictly negative or positive integer, '" . gettype($maxChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of choices that the candidate must select and order to form a valid response. A negative
     * value means that no maxChoice is indicated.
     * 
     * @return integer A strictly negative or positive integer.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Whether or not a value is defined for the maxChoice attribute.
     * 
     * @return boolean
     */
    public function hasMaxChoices() {
        return $this->getMaxChoices() > -1;
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array_merge(array($this->getObject()), $this->getHotspotChoices()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'graphicOrderInteraction';
    }
}