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

use qtism\common\utils\Format;
use qtism\data\ShowHide;
use \InvalidArgumentException;

/**
 * The FeedbackBlock QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class FeedbackBlock extends BodyElement implements FlowStatic, BlockStatic, FeedbackElement {
    
    /**
     * The base URI.
     *
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The components composing the FeedbackBlock content.
     *
     * @var FlowCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * From IMS QTI:
     * 
     * The identifier of an outcome variable that must have a base-type of 
     * identifier and be of either single or multiple cardinality. The visibility 
     * of the feedbackElement is controlled by assigning a value (or values) to 
     * this outcome variable during responseProcessing.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $outcomeIdentifier;
    
    /**
     * From IMS QTI:
     * 
     * The showHide attribute determines how the visibility of the feedbackElement is 
     * controlled. If set to show then the feedback is hidden by default and shown 
     * only if the associated outcome variable matches, or contains, the value of 
     * the identifier attribute. If set to hide then the feedback is shown by 
     * default and hidden if the associated outcome variable matches, or contains, 
     * the value of the identifier attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $showHide = ShowHide::SHOW;
    
    /**
     * From IMS QTI:
     * 
     * The identifier that determines the visibility of the feedback in conjunction 
     * with the showHide attribute.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * Create a new FeedbackBlock object.
     * 
     * @param string $outcomeIdentifier The identifier of an outcome variable controlling the visibility of the feedbackElement.
     * @param string $identifier The identifier value that determines the visibility of the feedbackElement in conjunction with $showHide.
     * @param integer $showHide A values of the ShowHide enumeration that determines hot the visibility of the feedbackElement is controlled.
     * @param string $id The identifier of the bodyElement.
     * @param string $class The class(es) of the bodyElement. If multiple classes, separate them with whitespaces (' ').
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any arguments of the constructor is invalid.
     */
    public function __construct($outcomeIdentifier, $identifier, $showHide = ShowHide::SHOW, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setOutcomeIdentifier($outcomeIdentifier);
        $this->setIdentifier($identifier);
        $this->setShowHide($showHide);
        $this->setContent(new FlowCollection());
    }
    
    public function setOutcomeIdentifier($outcomeIdentifier) {
        if (Format::isIdentifier($outcomeIdentifier, false) === true) {
            $this->outcomeIdentifier = $outcomeIdentifier;
        }
        else {
            $msg = "The 'outcomeIdentifier' argument must be a valid QTI identifier, '" . $outcomeIdentifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    public function getOutcomeIdentifier() {
        return $this->outcomeIdentifier;
    }
    
    public function setShowHide($showHide) {
        if (in_array($showHide, ShowHide::asArray()) === true) {
            $this->showHide = $showHide;
        }
        else {
            $msg = "The 'showHide' argument must be a value from the ShowHide enumeration, '" . $showHide . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    public function getShowHide() {
        return $this->showHide;
    }
    
    public function setIdentifier($identifier) {
        if (Format::isIdentifier($identifier, false) === true) {
            $this->identifier = $identifier;
        }
        else {
            $msg = "The 'identifier' argument must be a valid QTI identifier, '" . $identifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    public function getIdentifier() {
        return $this->identifier;
    }
    
    /**
     * Set the collection of objects composing the FeedbackBlock.
     *
     * @param FlowCollection $content A collection of FlowStatic objects.
     */
    public function setContent(FlowCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the collection of objects composing the FeedbackBlock.
     *
     * @return FlowCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'feedbackBlock';
    }
    
    /**
     * Set the base URI of the TemplateBlock.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'base' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the TemplateBlock.
     *
     * @return string An empty string or a URI.
     */
    public function getXmlBase() {
        return $this->xmlBase;
    }
    
    /**
     * Whether a value is defined for the base URI of the TemplateBlock.
     *
     * @return boolean
     */
    public function hasXmlBase() {
        return $this->getXmlBase() !== '';
    }
}