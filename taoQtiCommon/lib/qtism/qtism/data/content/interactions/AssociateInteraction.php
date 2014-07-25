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
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * An associate interaction is a blockInteraction that presents candidates
 * with a number of choices and allows them to create associations between 
 * them.
 * 
 * The associateInteraction must be bound to a response variable with 
 * base-type pair and either single or multiple cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssociateInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * If the shuffle attribute is true then the delivery engine must randomize the order in which the choices are 
     * initially presented, subject to the value of the fixed attribute of each choice.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $shuffle = false;
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of associations that the candidate is allowed to make. If maxAssociations 
     * is 0 then there is no restriction. If maxAssociations is greater than 1 (or 0) then the interaction 
     * must be bound to a response with multiple cardinality.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxAssociations = 1;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of associations that the candidate is required to make to form a valid response. 
     * If minAssociations is 0 then the candidate is not required to make any associations. minAssociations 
     * must be less than or equal to the limit imposed by maxAssociations.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minAssociations = 0;
    
    /**
     * From IMS QTI:
     * 
     * An ordered set of choices.
     * 
     * @var SimpleAssociableChoiceCollection
     * @qtism-bean-property
     */
    private $simpleAssociableChoices;
    
    /**
     * Create a new AssociateInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param SimpleAssociableChoiceCollection $simpleAssociableChoices A collection of at lease one SimpleAssociableChoice object.
     * @param string $id The identifier of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($responseIdentifier, SimpleAssociableChoiceCollection $simpleAssociableChoices, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setSimpleAssociableChoices($simpleAssociableChoices);
        $this->setShuffle(false);
        $this->setMaxAssociations(1);
        $this->setMinAssociations(0);
    }
    
    /**
     * Set whether the delivery engine must randomize the order in which
     * the choices are displayed.
     * 
     * @param boolean $shuffle
     * @throws InvalidArgumentException If $shuffle is not a boolean value.
     */
    public function setShuffle($shuffle) {
        if (is_bool($shuffle) === true) {
            $this->shuffle = $shuffle;
        }
        else {
            $msg = "The 'shuffle' argument must be a boolean value, '" . gettype($shuffle) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the delivery engine must randomize the order in which the 
     * choices are displayed.
     * 
     * @return boolean
     */
    public function mustShuffle() {
        return $this->shuffle;
    }
    
    /**
     * Set the maximum number of associations the candidate is required to make. If $maxAssociations
     * is 0, there is no maximum number of associations.
     * 
     * @param integer $maxAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxAssociations is not a positive integer.
     */
    public function setMaxAssociations($maxAssociations) {
        if (is_int($maxAssociations) === true && $maxAssociations >= 0) {
            $this->maxAssociations = $maxAssociations;
        }
        else {
            $msg = "The 'maxAssociations' argument must be a positive (>= 0) integer, '" . gettype($maxAssociations) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of associations the candidate is required to make. If $maxAssociations
     * is 0, there is no maximum number of associations.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMaxAssociations() {
        return $this->maxAssociations;
    }
    
    /**
     * Whether there is a maximum number of associations required.
     * 
     * @return boolean
     */
    public function hasMaxAssociations() {
        return $this->maxAssociations > 0;
    }
    
    /**
     * Set the minimum number of associations that the candidate is required to make. If
     * $minAssociations is 0, there is no minimum number of associations required.
     * 
     * @param integer $minAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minAssociations is not a positive integer or if $minAssociations is not less than or equal to the limit imposed by 'maxAssociations'.
     */
    public function setMinAssociations($minAssociations) {
        if (is_int($minAssociations) === true && $minAssociations >= 0) {
            
            if ($this->hasMaxAssociations() === true && $minAssociations > $this->getMaxAssociations()) {
                $msg = "The 'maxAssociations' argument must be less than or equal to the limit imposed by 'maxAssociations'.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->minAssociations = $minAssociations;
        }
        else {
            $msg = "The 'minAssociations' argument must be a positive (>= 0) integer, '" . gettype($minAssociations) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of associations that the candidate is required to make. If 0 is returned,
     * it means that there is no minimum number of associations required.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinAssociations() {
        return $this->minAssociations;
    }
    
    /**
     * Whether the candidate is required to respect a minimum number of associations.
     * 
     * @return boolean
     */
    public function hasMinAssociations() {
        return $this->getMinAssociations() > 0;
    }
    
    /**
     * Set the choices.
     * 
     * @param SimpleAssociableChoiceCollection $simpleAssociableChoices A collection of at least one SimpleAssociableChoice object.
     * @throws InvalidArgumentException If $simpleAssociableChoices is empty.
     */
    public function setSimpleAssociableChoices(SimpleAssociableChoiceCollection $simpleAssociableChoices) {
        if (count($simpleAssociableChoices) > 0) {
            $this->simpleAssociableChoices = $simpleAssociableChoices;
        }
        else {
            $msg = "An AssociateInteraction object must be composed of at lease one SimpleAssociableChoice object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the choices.
     * 
     * @return SimpleAssociableChoiceCollection A collection of at least one SimpleAssociableChoice.
     */
    public function getSimpleAssociableChoices() {
        return $this->simpleAssociableChoices;
    }
    
    public function getComponents() {
        $parentComponents = parent::getComponents();
        return new QtiComponentCollection(array_merge($parentComponents->getArrayCopy(), $this->getSimpleAssociableChoices()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'associateInteraction';
    }
}