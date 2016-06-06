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

namespace qtism\data\content;

use qtism\data\ShowHide;
use qtism\common\utils\Format;
use qtism\data\QtiComponent;
use \InvalidArgumentException;

class ModalFeedback extends QtiComponent {
    
    /**
     * From IMS QTI:
     * 
     * The identifier of an outcome variable that must have a base-type of identifier 
     * and be of either single or multiple cardinality. The visibility of the feedbackElement 
     * is controlled by assigning a value (or values) to this outcome variable during 
     * responseProcessing.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $outcomeIdentifier;
    
    /**
     * From IMS QTI:
     * 
     * The showHide attribute determines how the visibility of the feedbackElement is 
     * controlled. If set to show then the feedback is hidden by default and shown only if 
     * the associated outcome variable matches, or contains, the value of the identifier 
     * attribute. If set to hide then the feedback is shown by default and hidden if the 
     * associated outcome variable matches, or contains, the value of the identifier attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $showHide = ShowHide::SHOW;
    
    /**
     * From IMS QTI:
     * 
     * The identifier that determines the visibility of the feedback in conjunction with the 
     * showHide attribute.
     *
     * A feedback element that forms part of a Non-adaptive Item must not contain an 
     * interaction object, either directly or indirectly.
     *
     * When an interaction is contained in a hidden feedback element it must also be hidden.
     * The candidate must not be able to set or update the value of the associated response 
     * variables. 
     * 
     * Feedback elements can be embedded inside each other, with one exception: 
     * feedBackInline cannot contain feedbackBlock elements.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * From IMS QTI:
     * 
     * Delivery engines are not required to present the title to the candidate but may do 
     * so, for example as the title of a modal pop-up window.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $title = '';
    
    /**
     * From IMS QTI:
     * 
     * The content of the modalFeedback must not contain any interactions.
     * 
     * @var FlowStaticCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new ModalFeedback object.
     * 
     * @param string $outcomeIdentifier The identifier of the outcome variable controlling the visibilty of the modal feedback.
     * @param string $identifier The identifier to be used in conjunction with the showHide attribute.
     * @param FlowStaticCollection $content The content of the modal feedback.
     * @param string $title The title of the modal feedback.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($outcomeIdentifier, $identifier, FlowStaticCollection $content = null, $title = '') {
        $this->setOutcomeIdentifier($outcomeIdentifier);
        $this->setIdentifier($identifier);
        $this->setContent((is_null($content) === true) ? new FlowStaticCollection() : $content);
        $this->setTitle($title);
    }
    
    /**
     * Set the identifier of the outcome variable that will be used to
     * determine the visibility of the modal feedback.
     * 
     * @param string $outcomeIdentifier A valid QTI identifier.
     * @throws InvalidArgumentException If $outcomeIdentifier is not a valid QTI identifier.
     */
    public function setOutcomeIdentifier($outcomeIdentifier) {
        if (Format::isIdentifier($outcomeIdentifier, false) === true) {
            $this->outcomeIdentifier = $outcomeIdentifier;
        }
        else {
            $msg = "The 'outcomeIdentifier' argument must be a valid QTI identifier, '" . $outcomeIdentifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the identifier of the outcome variable that will be used to
     * determine the visibility of the modal feedback.
     * 
     * @return string A QTI identifier.
     */
    public function getOutcomeIdentifier() {
        return $this->outcomeIdentifier;
    }
    
    /**
     * Set how the visibility of the modal feedback is controlled.
     * 
     * @param integer $showHide A value from the ShowHide enumeration.
     * @throws InvalidArgumentException If $showHide is not a value from the ShowHide enumeration.
     */
    public function setShowHide($showHide) {
        if (in_array($showHide, ShowHide::asArray()) === true) {
            $this->showHide = $showHide;
        }
        else {
            $msg = "The 'showHide' argument must be a value from the ShowHide enumeration, '" . $showHide . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get how the visibility of the modal feedback is controlled.
     * 
     * @return integer A value from the ShowHide enumeration.
     */
    public function getShowHide() {
        return $this->showHide;
    }
    
    /**
     * Set the identifier that determines the visibility of the feedback in conjunction with the 
     * showHide attribute.
     * 
     * @param string $identifier A QTI identifier
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the identifier that determines the visibility of the feedback in conjunction with
     * the showHide attribute.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the title of the modal feedback.
     * 
     * @param string $title A title.
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * Get the title of the modal feedback.
     * 
     * @return string A title.
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Whether or not a value is defined for the title attribute.
     * 
     * @return boolean
     */
    public function hasTitle() {
        return $this->getTitle() !== '';
    }
    
    /**
     * Set the content of the modal feedback that must not contain any interactions.
     * 
     * @param FlowStaticCollection $content A collection of FlowStatic objects.
     */
    public function setContent(FlowStaticCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the content of the modal feedback that does not contain any interactions.
     * 
     * @return FlowStaticCollection A collection of FlowStatic objects.
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'modalFeedback';
    }
}