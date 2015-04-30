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
 * A graphic associate interaction is a graphic interaction with a corresponding 
 * set of choices that are defined as areas of the graphic image. The candidate's 
 * task is to associate the areas (hotspots) with each other. The graphic associate 
 * interaction should only be used when the graphical relationship of the choices 
 * with respect to each other (as represented by the graphic image) is important 
 * to the needs of the item. Otherwise, associateInteraction should be used 
 * instead with separate Material for each option.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GraphicAssociateInteraction extends GraphicInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of associations that the candidate is allowed to make. 
     * If maxAssociations is 0 there is no restriction. If maxAssociations is 
     * greater than 1 (or 0) then the interaction must be bound to a response 
     * with multiple cardinality.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxAssociations = 1;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of associations that the candidate is required to make 
     * to form a valid response. If minAssociations is 0 then the candidate is 
     * not required to make any associations. minAssociations must be less than 
     * or equal to the limit imposed by maxAssociations.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minAssociations = 0;
    
    /**
     * From IMS QTI:
     * 
     * The hotspots that define the choices that are to be associated by the 
     * candidate. If the delivery system does not support pointer-based selection 
     * then the order in which the choices are given must be the order in which 
     * they are offered to the candidate for selection. For example, the 
     * 'tab order' in simple keyboard navigation.
     * 
     * @var AssociableHotspotCollection
     * @qtism-bean-property
     */
    private $associableHotspots;
    
    /**
     * Create a new GraphicAssociateInteraction.
     * 
     * @param string $responseIdentifier The identifier of the associated response.
     * @param Object $object The associated image as an Object object.
     * @param AssociableHotspotCollection $associableHotspots The hotspots that define the choices that are to be associated by the candidate.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the argument is invalid.
     */
    public function __construct($responseIdentifier, Object $object, AssociableHotspotCollection $associableHotspots, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $object, $id, $class, $lang, $label);
        $this->setAssociableHotspots($associableHotspots);
    }
    
    /**
     * Set the hotspots that define the gaps that are to be filled by the candidate.
     * 
     * @param AssociableHotspotCollection $associableHotspots A collection of at least 1 AssociableHotspot object.
     * @throws InvalidArgumentException If $associableHotspots is empty.
     */
    public function setAssociableHotspots(AssociableHotspotCollection $associableHotspots) {
        if (count($associableHotspots) > 0) {
            $this->associableHotspots = $associableHotspots;
        }
        else {
            $msg = "A GraphicAssociateInteraction must be composed of at least 1 AssociableHotspot object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the hotspots that define the gaps that are to be filled by the candidate.
     * 
     * @return AssociableHotspotCollection A collection of AssociableHotspot objects.
     */
    public function getAssociableHotspots() {
        return $this->associableHotspots;
    }
    
    /**
     * Set the maximum number of associations that the candidate is allowed
     * to make.
     * 
     * @param integer $maxAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxAssociations is not a positive integer.
     */
    public function setMaxAssociations($maxAssociations) {
        if (is_int($maxAssociations) === true && $maxAssociations >= 0) {
            $this->maxAssociations = $maxAssociations;
        } 
        else {
            $msg = "The 'maxAssociations' argument must be a positive (>= 0) integer, '" . gettype($maxAssociations) . "'.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of associations that the candidate is allowed
     * to make.
     * 
     * @return integer $maxAssociations A positive (>= 0) integer.
     */
    public function getMaxAssociations() {
        return $this->maxAssociations;
    }
    
    /**
     * Set the minimum number of associations that the candidate is required to
     * make.
     * 
     * @param integer $minAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minAssociations is not a positive integer or if $minAssociations is not less than or equal to the limit imposed by maxAssociations.
     */
    public function setMinAssociations($minAssociations) {
        if (is_int($minAssociations) === true && $minAssociations >= 0) {
            
            if ($minAssociations > $this->getMaxAssociations()) {
                $msg = "The 'minAssociations' argument must be less than or equal to the limit imposed by 'maxAssociations'.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->minAssociations = $minAssociations;
        }
        else {
            $msg = "The 'minAssociations' argument must be a positive (>= 0) integer, '" . gettype($minAssociations) . "'.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of associations that the candidate is required
     * to make.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinAssociations() {
        return $this->minAssociations;
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array_merge(array($this->getObject()), $this->getAssociableHotspots()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'graphicAssociateInteraction';
    }
}