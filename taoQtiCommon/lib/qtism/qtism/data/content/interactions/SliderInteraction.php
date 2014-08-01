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
 * The slider interaction presents the candidate with a control for 
 * selecting a numerical value between a lower and upper bound. It must be 
 * bound to a response variable with single cardinality with a base-type of 
 * either integer or float.
 * 
 * Note that a slider interaction does not have a default or initial position 
 * except where specified by a default value for the associated response variable.
 * The currently selected value, if any, must be clearly indicated to the 
 * candidate.
 * 
 * Because a slider interaction does not have a default or initial 
 * position — except where specified by a default value for the associated 
 * response variable — it is difficult to distinguish between an intentional 
 * response that corresponds to the slider's initial position and a NULL 
 * response. As a workaround, sliderInteraction items have to either a) 
 * not count NULL responses (i.e. count all responses as intentional) or 
 * b) include a 'skip' button and count its activation combined with a 
 * RESPONSE variable that is equal to the slider's initial position as a 
 * NULL response.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SliderInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * If the associated response variable is of type integer then the 
     * lowerBound must be rounded down to the greatest integer less than 
     * or equal to the value given.
     * 
     * @var float
     * @qtism-bean-property
     */
    private $lowerBound;
    
    /**
     * From IMS QTI:
     * 
     * If the associated response variable is of type integer then the 
     * upperBound must be rounded up to the least integer greater than 
     * or equal to the value given.
     * 
     * @var float
     * @qtism-bean-property
     */
    private $upperBound;
    
    /**
     * From IMS QTI:
     * 
     * The steps that the control moves in. For example, if the lowerBound 
     * and upperBound are [0,10] and step is 2 then the response would be 
     * constrained to the set of values {0,2,4,6,8,10}. If the response 
     * variable is bound to an integer, and the step attribute is not 
     * declared, the default step is 1. If the response variable is bound 
     * to a float, and the step attribute is not declared, the slider is 
     * assumed to operate on an approximately continuous scale.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $step = 0;
    
    /**
     * From IMS QTI:
     * 
     * By default, sliders are labelled only at their ends. The stepLabel 
     * attribute controls whether or not each step on the slider should 
     * also be labelled. It is unlikely that delivery engines will be 
     * able to guarantee to label steps so this attribute should be 
     * treated only as request.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $stepLabel = false;
    
    /**
     * From IMS QTI:
     * 
     * The orientation attribute provides a hint to rendering systems that 
     * the slider is being used to indicate the value of a quantity with 
     * an inherent vertical or horizontal interpretation. For example, an 
     * interaction that is used to indicate the value of height might set 
     * the orientation to vertical to indicate that rendering it horizontally 
     * could spuriously increase the difficulty of the item.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $orientation = Orientation::HORIZONTAL;
    
    /**
     * From IMS QTI:
     * 
     * The reverse attribute provides a hint to rendering systems that the 
     * slider is being used to indicate the value of a quantity for which 
     * the normal sense of the upper and lower bounds is reversed. For 
     * example, an interaction that is used to indicate a depth below sea 
     * level might specify both a vertical orientation and set reverse.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $reverse = false;
    
    /**
     * Create a new SliderInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param float $lowerBound A lower bound.
     * @param float $upperBound An upper bound.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, $lowerBound, $upperBound, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setLowerBound($lowerBound);
        $this->setUpperBound($upperBound);
    }
    
    /**
     * Get the value of the lowerBound attribute.
     * 
     * @param float $lowerBound A float value.
     * @throws InvalidArgumentException If $lowerBound is not a float value.
     */
    public function setLowerBound($lowerBound) {
        if (is_float($lowerBound) === true) {
            $this->lowerBound = $lowerBound;
        }
        else {
            $msg = "The 'lowerBound' argument must be a float value, '" . gettype($lowerBound) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Set the value of the lowerBound attribute.
     * 
     * @return float A float value.
     */
    public function getLowerBound() {
        return $this->lowerBound;
    }
    
    /**
     * Set the value of the upperBound attribute.
     *  
     * @param float $upperBound A float value.
     * @throws InvalidArgumentException If $upperBound is not a float value.
     */
    public function setUpperBound($upperBound) {
        if (is_float($upperBound) === true) {
            $this->upperBound = $upperBound;
        }
        else {
            $msg = "The 'upperBound' argument must be a float value, '" . gettype($upperBound) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value of the upperBound attribute.
     * 
     * @return float A float value.
     */
    public function getUpperBound() {
        return $this->upperBound;
    }
    
    /**
     * Set the step that controls the move of the slider. If $step is 0, it means
     * that no value is actually defined for the step attribute.
     * 
     * @param integer $step A positive (>= 0) integer.
     * @throws InvalidArgumentException If $step is not a positive integer.
     */
    public function setStep($step) {
        if (is_int($step) && $step >= 0) {
            $this->step = $step;
        }
        else {
            $msg = "The 'step' argument must be a positive (>= 0) integer, '" . gettype($step) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the step that controls the move of the slider.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getStep() {
        return $this->step;
    }
    
    /**
     * Whether or not a value is defined for the step attribute.
     * 
     * @return boolean
     */
    public function hasStep() {
        return $this->getStep() > 0;
    }
    
    /**
     * Set whether or not each step on the slider has to be labelled.
     * 
     * @param boolean $stepLabel
     * @throws InvalidArgumentException If $stepLabel is not a boolean value.
     */
    public function setStepLabel($stepLabel) {
        if (is_bool($stepLabel) === true) {
            $this->stepLabel = $stepLabel;
        }
        else {
            $msg = "The 'stepLabel' argument must be a boolean value, '" . gettype($stepLabel) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether each step on the slider has to be labelled.
     * 
     * @return boolean
     */
    public function mustStepLabel() {
        return $this->stepLabel;
    }
    
    /**
     * Set the orientation of the slider (horizontal or vertical).
     * 
     * @param integer $orientation A value from the Orientation enumeration.
     * @throws InvalidArgumentException If $orientation is not a value from the Orientation enumeration.
     */
    public function setOrientation($orientation) {
        if (in_array($orientation, Orientation::asArray()) === true) {
            $this->orientation = $orientation;
        }
        else {
            $msg = "The 'orientation' argument must be a value from the Orientation enumeration.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the orientation of the slider (horizontal or vertical).
     * 
     * @return integer A value from the Orientation enumeration.
     */
    public function getOrientation() {
        return $this->orientation;
    }
    
    /**
     * Set whether or not the upper and lower bounds are reversed.
     * 
     * @param boolean $reverse
     * @throws InvalidArgumentException If $reverse is not a boolean value.
     */
    public function setReverse($reverse) {
        if (is_bool($reverse) === true) {
            $this->reverse = $reverse;
        }
        else {
            $msg = "The 'reverse' argument must be a boolean value, '" . gettype($reverse) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the upper and lower bounds are reversed.
     * 
     * @return boolean
     */
    public function mustReverse() {
        return $this->reverse;
    }
    
    public function getComponents() {
        return parent::getComponents();
    }
    
    public function getQtiClassName() {
        return 'sliderInteraction';
    }
}