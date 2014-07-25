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
 * In an order interaction the candidate's task is to reorder the choices, the order 
 * in which the choices are displayed initially is significant. By default the candidate's 
 * task is to order all of the choices but a subset of the choices can be requested using 
 * the maxChoices and minChoices attributes. When specified the candidate must select a 
 * subset of the choices and impose an ordering on them.
 * 
 * If a default value is specified for the response variable associated with an order 
 * interaction then its value should be used to override the order of the choices 
 * specified here.
 * 
 * By its nature, an order interaction may be difficult to render in an unanswered state, 
 * especially in the default case where all choices are to be ordered. Implementors should 
 * be aware of the issues concerning the use of default values described in the section 
 * on Response Variables.
 * 
 * The orderInteraction must be bound to a response variable with a baseType of identifier 
 * and ordered cardinality only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OrderInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * An ordered list of the choices that are displayed to the user. The order 
     * is the initial order of the choices presented to the user unless shuffle 
     * is true.
     * 
     * @var SimpleChoiceCollection
     * @qtism-bean-property
     */
    private $simpleChoices;
    
    /**
     * From IMS QTI:
     * 
     * If the shuffle attribute is true then the delivery engine must randomize the order 
     * in which the choices are initially presented, subject to the value of the fixed 
     * attribute of each choice.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $shuffle = false;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of choices that the candidate must select and order to form a valid 
     * response to the interaction. If specified, minChoices must be 1 or greater but must not 
     * exceed the number of choices available. If unspecified, all of the choices must be ordered 
     * and maxChoices is ignored.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minChoices = -1;
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of choices that the candidate may select and order when responding to the 
     * interaction. Used in conjunction with minChoices, if specified, maxChoices must be greater 
     * than or equal to minChoices and must not exceed the number of choices available. If unspecified, 
     * all of the choices may be ordered.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxChoices = -1;
    
    /**
     * From IMS QTI:
     * 
     * The orientation attribute provides a hint to rendering systems that 
     * the ordering has an inherent vertical or horizontal interpretation.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $orientation = Orientation::VERTICAL;
    
    /**
     * Create a new OrderInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param SimpleChoiceCollection $simpleChoices A collection of at least one SimpleChoice object.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($responseIdentifier, SimpleChoiceCollection $simpleChoices, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setSimpleChoices($simpleChoices);
        $this->setShuffle(false);
        $this->setMinChoices(-1);
        $this->setMaxChoices(-1);
        $this->setOrientation(Orientation::VERTICAL);
    }
    
    /**
     * Set the ordered list of choices that are displayed to the user.
     * 
     * @param SimpleChoiceCollection $simpleChoices A list of at lease one SimpleChoice object.
     * @throws InvalidArgumentException If $simpleChoices is empty.
     */
    public function setSimpleChoices(SimpleChoiceCollection $simpleChoices) {
        if (count($simpleChoices) > 0) {
            $this->simpleChoices = $simpleChoices;
        }
        else {
            $msg = "An OrderInteraction object must be composed of at lease one SimpleChoice object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the ordered list of choices that are displayed to the user.
     * 
     * @return SimpleChoiceCollection A list of at lease one SimpleChoice object.
     */
    public function getSimpleChoices() {
        return $this->simpleChoices;
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
     * Set the minimum number of choices that the candidate may select.
     * 
     * @param integer $minChoices A strictly (> 0) positive integer or -1 if no value is specified for the attribute.
     * @throws InvalidArgumentException If $minChoices is not a strictly positive integer nor -1.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) === true && ($minChoices > 0 || $minChoices === -1)) {
            
            if ($minChoices > count($this->getSimpleChoices())) {
                $msg = "The value of 'minChoices' cannot exceed the number of available choices.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->minChoices = $minChoices;
        }
        else {
            $msg = "The 'minChoices' argument must be a strictly positive (> 0) integer or -1, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of choices that the candidate may select.
     * 
     * @return integer A strictly positive (> 0) integer.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    /**
     * Wether a value is defined for the minChoices attribute.
     * 
     * @return boolean
     */
    public function hasMinChoices() {
        return $this->getMinChoices() !== -1;
    }
    
    /**
     * Set the maximum number of choices that the candidate may select and order when responding to the interaction.
     * 
     * @param integer $maxChoices A strictly positive (> 0) integer or -1 if no value is specified for the attribuite.
     * @throws InvalidArgumentException If $maxChoices is not a strictly positive integer nor -1.
     */
    public function setMaxChoices($maxChoices) {
        if (is_int($maxChoices) === true && $maxChoices > 0 || $maxChoices === -1) {
            
            if ($this->hasMinChoices() === true && $maxChoices > count($this->getSimpleChoices())) {
                $msg = "The 'maxChoices' argument cannot exceed the number of available choices.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->maxChoices = $maxChoices;
        }
        else {
            $msg = "The 'maxChoices' argument must be a strictly positive (> 0) integer or -1, '" . gettype($maxChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of choices that the candidate may select and order when responding to the interaction.
     * 
     * @return integer A strictly positive (> 0) integer or -1 if no value is defined for the maxChoices attribute.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Whether a value is defined for the maxChoices attribute.
     * 
     * @return boolean
     */
    public function hasMaxChoices() {
        return $this->getMaxChoices() !== -1;
    }
    
    /**
     * Set the orientation of the choices.
     * 
     * @param integer $orientation A value from the Orientation enumeration.
     * @throws InvalidArgumentException If $orientation is not a value from the Orientation enumeration.
     */
    public function setOrientation($orientation) {
        if (in_array($orientation, Orientation::asArray()) === true) {
            $this->orientation = $orientation;
        }
        else {
            $msg = "The 'orientation' argument must be a value from the Orientation enumeration, '" . gettype($orientation) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the orientation of the choices.
     * 
     * @return A value from the Orientation enumeration.
     */
    public function getOrientation() {
        return $this->orientation;
    }
    
    public function getComponents() {
        $parentComponents = parent::getComponents();
        return new QtiComponentCollection(array_merge($parentComponents->getArrayCopy(), $this->getSimpleChoices()->getArrayCopy()));
    }
    
    public function getQtiClassName() {
        return 'orderInteraction';
    }
}