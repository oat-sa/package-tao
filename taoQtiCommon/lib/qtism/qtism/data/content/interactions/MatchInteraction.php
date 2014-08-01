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
 * A match interaction is a blockInteraction that presents candidates with two sets of choices 
 * and allows them to create associates between pairs of choices in the two sets, but not between 
 * pairs of choices in the same set. Further restrictions can still be placed on the allowable 
 * associations using the matchMax attribute of the choices.
 * 
 * The matchInteraction must be bound to a response variable with base-type directedPair 
 * and either single or multiple cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MatchInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $shuffle = false;
    
    /**
     * From IMS QTI:
     *
     * @var integer
     * @qtism-bean-property
     */
    private $maxAssociations = 1;
    
    /**
     * From IMS QTI:
     *
     * @var integer
     * @qtism-bean-property
     */
    private $minAssociations = 0;
    
    /**
     * From IMS QTI:
     *
     * @var SimpleMatchSetCollection
     * @qtism-bean-property
     */
    private $simpleMatchSets;

    public function __construct($responseIdentifier, SimpleMatchSetCollection $simpleMatchSets, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setSimpleMatchSets($simpleMatchSets);
        $this->setShuffle(false);
        $this->setMaxAssociations(1);
        $this->setMinAssociations(0);
    }
    
    /**
     * Set whether the delivery engine must randomize the choices.
     *
     * @param boolean $shuffle A boolean value.
     * @throws InvalidArgumentException If $shuffle is not a boolean value.
     */
    public function setShuffle($shuffle) {
        if (is_bool($shuffle) === true) {
            $this->shuffle = $shuffle;
        }
        else {
            $msg = "The 'shuffle' argument must be a boolean value, '" . gettype($shuffle) . "given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the delivery engine must randomize the choices.
     *
     * @return boolean
     */
    public function mustShuffle() {
        return $this->shuffle;
    }
    
    /**
     * Set the maximum number of associations that the candidate is allowed to make. If maxAssociations
     * is 0 then there is no restriction.
     * 
     * @param integer $maxAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxAssociations is not a positive integer.
     */
    public function setMaxAssociations($maxAssociations) {
        if (is_int($maxAssociations) === true && $maxAssociations >= 0) {
            $this->maxAssociations = $maxAssociations;
        }
        else {
            $msg = "The 'maxAssociations' argument must be a positive (>= 0) integer, '" . gettype($maxAssociations) . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of associations that the candidate is allowed to make. If maxAssociations
     * is 0 then there is no restriction.
     * 
     * @return integer A positive (>= 0) integer
     */
    public function getMaxAssociations() {
        return $this->maxAssociations;
    }
    
    /**
     * Whether a restriction is imposed on the maximum number of associations a candidate
     * can make.
     * 
     * @return boolean
     */
    public function hasMaxAssociations() {
        return $this->getMaxAssociations() > 0;
    }
    
    /**
     * Set the minimum number of associations that the candidate is required to make. If minAssociations is 0 then
     * there is no restriction.
     * 
     * @param integer $minAssociations A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minAssociations is not a positive integer or does not respect the limit imposed by maxAssociations.
     */
    public function setMinAssociations($minAssociations) {
        if (is_int($minAssociations) === true && $minAssociations >= 0) {
            
            if ($this->hasMaxAssociations() === true && $minAssociations > $this->getMaxAssociations()) {
                $msg = "The 'minAssociations' argument must be less than or equal to the limit imposed by 'maxAssociations'.";
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
     * Get the minimum number of associations that the candidate is required to make. If minAssociations is 0 then
     * there is no restriction.
     * 
     * @return integer A positive (> 0) integer.
     */
    public function getMinAssociations() {
        return $this->minAssociations;
    }
    
    /**
     * Whether there is a minimum number of associations required.
     * 
     * @return boolean
     */
    public function hasMinAssociations() {
        return $this->getMinAssociations() > 0;
    }
    
    /**
     * Set the set of choices. The first defines the source choices and the second one the
     * targets.
     * 
     * @param SimpleMatchSetCollection $simpleMatchSets A collection of exactly two SimpleMatchSet objects.
     * @throws InvalidArgumentException If $simpleMatchSets does not contain exactly two SimpleMatchSet objects.
     */
    public function setSimpleMatchSets(SimpleMatchSetCollection $simpleMatchSets) {
        if (count($simpleMatchSets) === 2) {
            $this->simpleMatchSets = $simpleMatchSets;
        }
        else {
            $msg = "A MatchInteraction object must be composed of exactly two SimpleMatchSet objects.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the set of choices. The first defines the source choices and the secon one the
     * targets.
     * 
     * @return SimpleMatchSetCollection A collection of exactly two SimpleMatchSet objects.
     */
    public function getSimpleMatchSets() {
        return $this->simpleMatchSets;
    }
    
    /**
     * Get the source choices.
     * 
     * @return SimpleMatchSet A SimpleMatchSet object.
     */
    public function getSourceChoices() {
        $matchSets = $this->getSimpleMatchSets();
        return $matchSets[0];
    }
    
    /**
     * Get the target choices.
     * 
     * @return SimpleMatchSet A SimpleMatchSet object.
     */
    public function getTargetChoices() {
        $matchSets = $this->getSimpleMatchSets();
        return $matchSets[1];
    }
    
    public function getComponents() {
        $parentComponents = parent::getComponents();
        return new QtiComponentCollection(array_merge($parentComponents->getArrayCopy(), $this->getSimpleMatchSets()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'matchInteraction';
    }
}