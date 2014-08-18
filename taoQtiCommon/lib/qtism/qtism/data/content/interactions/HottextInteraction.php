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

use qtism\data\content\BlockStaticCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The hottext interaction presents a set of choices to the candidate represented 
 * as selectable runs of text embedded within a surrounding context, such as a 
 * simple passage of text. Like choiceInteraction, the candidate's task is to 
 * select one or more of the choices, up to a maximum of maxChoices. The 
 * interaction is initialized from the defaultValue of the associated response 
 * variable, a NULL value indicating that no choices are selected (the usual case).
 * 
 * The hottextInteraction must be bound to a response variable with a baseType of 
 * identifier and single or multiple cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class HottextInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of choices that can be selected by the candidate. If 
     * matchChoices is 0 there is no restriction. If maxChoices is greater than 
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
     * The minimum number of choices that the candidate is required to select 
     * to form a valid response. If minChoices is 0 then the candidate is not 
     * required to select any choices. minChoices must be less than or equal 
     * to the limit imposed by maxChoices.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minChoices = 0;
    
    /**
     * From IMS QTI:
     * 
     * The content of the interaction is simply a piece of content, such as a 
     * simple passage of text, that contains the hottext areas.
     * 
     * @var BlockStaticCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new HottextInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param BlockStaticCollection $content A collection of BlockStatic objects as the content of the interaction.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, BlockStaticCollection $content, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setContent($content);
        $this->setMaxChoices(1);
        $this->setMinChoices(0);
    }
    
    /**
     * Set the maximum number of choices that can be selected by the candidate. If the returned value
     * is 0, it means that the candidate is not requitred to select any choice.
     * 
     * @param integer $maxChoices A positive  integer.
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
     * Get the maximum number of choices that can be selected by the candidate. If the returned
     * value is 0, it means that the candidate is not required to select any choice.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Set the minimum number of choices that the candidate is required to select to form a valid response.
     * 
     * @param integer $minChoices A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minChoices is not a positive integer or does not respect the limits imposed by maxChoices.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) && $minChoices > 0) {
            
            if ($minChoices <= $this->getMaxChoices()) {
                $this->minChoices = $minChoices;
            }
            else {
                $msg = "The 'minChoices' argument must respect the limits imposed by maxChoice.";
                throw new InvalidArgumentException($msg);
            }
        }
        else if (is_int($minChoices) && $minChoices === 0) {
            $this->minChoices = $minChoices;
        }
        else {
            $msg = "The 'minChoices' argument must be a positive (>= 0) integer value, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of choices that the candidate is required to select to form a valid response.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    /**
     * Set the content of the interaction, containing the hottext areas.
     * 
     * @param BlockStaticCollection $content A collection of at least one BlockStatic object.
     * @throws InvalidArgumentException If $content is empty.
     */
    public function setContent(BlockStaticCollection $content) {
        if (count($content) > 0) {
            $this->content = $content;
        }
        else {
            $msg = "A HottextInteraction object must be composed of at least one BlockStatic object, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the content of the interaction, containing the hottext areas.
     * 
     * @return BlockStaticCollection A collection of at least one BlockStatic object.
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'hottextInteraction';
    }
}