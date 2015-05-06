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
use qtism\data\content\Flow;
use qtism\data\content\Block;
use qtism\data\ExternalQtiComponent;
use qtism\data\IExternal;

/**
 * From IMS QTI:
 * 
 * The custom interaction provides an opportunity for extensibility of this 
 * specification to include support for interactions not currently documented.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class CustomInteraction extends Interaction implements IExternal, Block, Flow {
    
    /**
     * The base URI of the CustomInteraction.
     *
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The xml string content of the custom interaction.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $xmlString;
    
    /**
     * 
     * @var ExternalQtiComponent
     */
    private $externalComponent = null;
    
    /**
     * Create a new CustomInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the Response Variable bound to the interaction.
     * @param string $xmlString The xml data representing the whole customInteraction component and its content.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, $xmlString, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setXmlString($xmlString);
        $this->setExternalComponent(new ExternalQtiComponent($xmlString));
    }
    
    public function getQtiClassName() {
        return 'customInteraction';
    }
    
    public function getXmlString() {
        return $this->xmlString;
    }
    
    public function setXmlString($xmlString) {
        $this->xmlString = $xmlString;
        if ($this->externalComponent !== null) {
            $this->getExternalComponent()->setXmlString($xmlString);
        }
    }
    
    /**
     * Get the XML content of the custom interaction itself and its content.
     *
     * @return DOMDocument A DOMDocument object representing the custom interaction.
     * @throws RuntimeException If the XML content of the custom interaction and/or its content cannot be transformed into a valid DOMDocument.
     */
    public function getXml() {
        return $this->getExternalComponent()->getXml();
    }
    
    /**
     * Set the encapsulated external component.
     *
     * @param ExternalQtiComponent $externalComponent
     */
    private function setExternalComponent(ExternalQtiComponent $externalComponent) {
        $this->externalComponent = $externalComponent;
    }
    
    /**
     * Get the encapsulated external component.
     *
     * @return ExternalQtiComponent
     */
    private function getExternalComponent() {
        return $this->externalComponent;
    }
    
    /**
     * Set the base URI of the CustomInteraction.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'xmlBase' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the CustomInteraction.
     *
     * @return string An empty string or a URI.
     */
    public function getXmlBase() {
        return $this->xmlBase;
    }
    
    /**
     * Whether or not a base URI is defined for the CustomInteraction.
     * 
     * @return boolean
     */
    public function hasXmlBase() {
        return $this->getXmlBase() !== '';
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
}