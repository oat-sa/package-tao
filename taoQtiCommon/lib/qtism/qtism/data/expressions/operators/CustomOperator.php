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

namespace qtism\data\expressions\operators;

use qtism\data\ExternalQtiComponent;
use qtism\data\IExternal;
use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

class CustomOperator extends Operator implements IExternal {
    
    /**
     * @var string
     * @qtism-bean-property
     */
    private $xmlString = '';
    
    /**
     * From IMS QTI:
     * 
     * The class attribute allows simple sub-classes to be named. The definition of a sub-class is tool
     * specific and may be inferred from toolName and toolVersion.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $class = '';
    
    /**
     * From IMS QTI:
     * 
     * A URI that identifies the definition of the custom operator in the global namespace.
     * 
     * In addition to the class and definition attributes, sub-classes may add any number of
     * attributes of their own.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $definition = '';
    
    /**
     * @var ExternalQtiComponent
     */
    private $externalComponent = null;
    
    public function __construct(ExpressionCollection $expressions, $xmlString) {
        parent::__construct($expressions, 0, -1, array(OperatorCardinality::ANY), array(OperatorBaseType::ANY));
        $this->setXmlString($xmlString);
        $this->setExternalComponent(new ExternalQtiComponent($xmlString));
    }
    
    /**
     * Set the class attribute. An empty value means there is no class attribute specified.
     * 
     * @param string $class A class name which is tool specific.
     * @throws InvalidArgumentException If $class is not a string.
     */
    public function setClass($class) {
        if (is_string($class) === true) {
            $this->class = $class;
        }
        else {
            $msg = "The 'class' argument must be a string, '" . gettype($class) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the class attribute.
     * 
     * @return string A class name which is tool specific.
     */
    public function getClass() {
        return $this->class;
    }
    
    /**
     * Whether or not a value is defined for the class attribute.
     * 
     * @return boolean
     */
    public function hasClass() {
        return $this->getClass() !== '';
    }
    
    /**
     * Set the URI that identifies the definition of the custom operator
     * in the global namespace. An empty value means there is no value set for the definition attribute.
     * 
     * @param string $definition A URI or an empty string.
     * @throws InvalidArgumentException If $definition is not a string.
     */
    public function setDefinition($definition) {
        if (is_string($definition) === true) {
            $this->definition = $definition;
        }
        else {
            $msg = "The 'definition' argument must be a string, '" . gettype($class) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the URI that identifies the definition of the custom operator
     * in the global namespace. An empty value means there is no value set for the definition attribute.
     * 
     * @return string A URI or an empty string.
     */
    public function getDefinition() {
        return $this->definition;
    }
    
    /**
     * Whether or not a value is defined for the definition attribute.
     * 
     * @return boolean
     */
    public function hasDefinition() {
        return $this->getDefinition() !== '';
    }
    
    /**
     * Get the XML content of the custom operator itself and its content.
     * 
     * @return DOMDocument A DOMDocument object representing the custom operator itself.
     * @throws RuntimeException If the XML content of the custom operator and/or its content cannot be transformed into a valid DOMDocument.
     */
    public function getXml() {
        return $this->getExternalComponent()->getXml();
    }
    
    public function setXmlString($xmlString) {
        $this->xmlString = $xmlString;
        
        if ($this->externalComponent !== null) {
            $this->getExternalComponent()->setXmlString($xmlString);
        }
    }
    
    public function getXmlString() {
        return $this->xmlString;
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
    
    public function getQtiClassName() {
        return 'customOperator';
    }
}