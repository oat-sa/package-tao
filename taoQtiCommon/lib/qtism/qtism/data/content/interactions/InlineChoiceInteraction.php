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
 * A inline choice is an inlineInteraction that presents the user with a 
 * set of choices, each of which is a simple piece of text. The candidate's 
 * task is to select one of the choices. Unlike the choiceInteraction, the 
 * delivery engine must allow the candidate to review their choice within 
 * the context of the surrounding text.
 * 
 * The inlineChoiceInteraction must be bound to a response variable with a 
 * baseType of identifier and single cardinality only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class InlineChoiceInteraction extends InlineInteraction {
    
    /**
     * From IMS QTI:
     * 
     * If the shuffle attribute is true then the delivery engine must randomize 
     * the order in which the choices are initially presented, subject to the 
     * value of the fixed attribute of each choice.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $shuffle = false;
    
    /**
     * From IMS QTI:
     * 
     * If true then a choice must be selected by the candidate inorder to form a 
     * valid response to the interaction.
     * 
     * Note that the effect intended by required=false can also be achieved by 
     * including a blank value among the choices. Where 'required=false' is set 
     * the rendering system must ensure it is possible to select a blank response.
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $required = false;
    
    /**
     * From IMS QTI:
     * 
     * An ordered list of the choices that are displayed to the user. The order 
     * is the order of the choices presented to the user unless shuffle is true.
     * 
     * @var InlineChoiceCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new InlineChoiceInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param InlineChoiceCollection $content The InlineChoice objects composing the interaction.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($responseIdentifier, InlineChoiceCollection $content, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setContent($content);
        $this->setShuffle(false);
        $this->setRequired(false);
    }
    
    /**
     * Set whether the delivery engine must shuffle the choices.
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
     * Whether the delivery engine must shuffle the choices.
     * 
     * @return boolean
     */
    public function mustShuffle() {
        return $this->shuffle;
    }
    
    /**
     * Set whether a choice is required to be selected by the candidate.
     * 
     * @param boolean $required
     * @throws InvalidArgumentException If $required is not a boolean value.
     */
    public function setRequired($required) {
        if (is_bool($required) === true) {
            $this->required = $required;
        }
        else {
            $msg = "The 'required' argument must be a boolean value, '" . gettype($required) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether a choice is required to be selected by the candidate.
     * 
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }
    
    /**
     * Set the InlineChoice objects composing the interaction.
     * 
     * @param InlineChoiceCollection $content A collection of at least one InlineChoice object.
     * @throws InvalidArgumentException If $content is empty.
     */
    public function setContent(InlineChoiceCollection $content) {
        if (count($content) > 0) {
            $this->content = $content;
        }
        else {
            $msg = "An InlineChoiceInteraction must be composed by at lease one InlineChoice object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the InlineChoice objects composing the interaction.
     * 
     * @return InlineChoiceCollection A collection of at least one InlineChoice object.
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'inlineChoiceInteraction';
    }
}