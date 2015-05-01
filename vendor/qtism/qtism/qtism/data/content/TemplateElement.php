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

use qtism\data\QtiIdentifiable;
use qtism\common\utils\Format;
use qtism\data\ShowHide;
use \InvalidArgumentException;
use \SplObjectStorage;
use \SplObserver;

/**
 * The QTI TemplatElement class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class TemplateElement extends BodyElement {
    
    /**
     * From IMS QTI:
     * 
     * The identifier of a template variable that must have a base-type of identifier and be 
     * of either single or multiple cardinality. The visibility of the templateElement is 
     * controlled by the value of the variable.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $templateIdentifier;
    
    /**
     * The showHide attribute.
     *
     * @var integer
     * @qtism-bean-property
     */
    private $showHide = ShowHide::SHOW;
    
    /**
     * The identifier of the templateElement.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $identifier;
    
    /**
     * Create a new TemplateElement object.
     * 
     * @param string $templateIdentifier The identifier of the associated template variable.
     * @param string $identifier The identifier of the templateElement.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct($templateIdentifier, $identifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setIdentifier($identifier);
        $this->setTemplateIdentifier($templateIdentifier);
    }
    
    /**
     * Set the template variable identifier.
     * 
     * @param string $templateIdentifier A QTI identifier.
     * @throws InvalidArgumentException If $templateIdentifier is not a valid QTI identifier.
     */
    public function setTemplateIdentifier($templateIdentifier) {
        if (Format::isIdentifier($templateIdentifier, false) === true) {
            $this->templateIdentifier = $templateIdentifier;
        }
        else {
            $msg = "The 'templateIdentifier' argument must be a valid QTI identifier, '" . $templateIdentifier . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the template variable identifier.
     * 
     * @return string A QTI identifier.
     */
    public function getTemplateIdentifier() {
        return $this->templateIdentifier;
    }
    
    /**
     * Set the value of the showHide attribute.
     * 
     * @param integer $showHide A value from the ShowHide enumeration.
     * @throws InvalidArgumentException If $showHide is not a value from the ShowHide enumeration.
     */
    public function setShowHide($showHide) {
        if (in_array($showHide, ShowHide::asArray()) === true) {
            $this->showHide = $showHide;
        }
        else {
            $msg = "The 'showHide' argument must be a value from the ShowHide enumeration.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value of the showHide attribute.
     * 
     * @return integer A value from the ShowHide enumeration.
     */
    public function getShowHide() {
        return $this->showHide;
    }
    
    /**
     * Whether the templateElement is working on 'show' mode.
     * 
     * @return boolean
     */
    public function mustShow() {
        return $this->showHide === ShowHide::SHOW;
    }
    
    /**
     * Whether the templateElement is working on 'hide' mode.
     * 
     * @return boolean
     */
    public function mustHide() {
        return $this->showHide === ShowHide::HIDE;
    }
    
    /**
     * Set the identifier of the templateElement.
     * 
     * @param unknown_type $identifier A QTI identifier.
     * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
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
    
    public function getIdentifier() {
        return $this->identifier;
    }
}