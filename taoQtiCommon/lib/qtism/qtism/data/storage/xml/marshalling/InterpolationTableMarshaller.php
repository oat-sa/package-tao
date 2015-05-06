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


namespace qtism\data\storage\xml\marshalling;

use qtism\data\QtiComponent;
use qtism\data\state\InterpolationTable;
use qtism\data\state\InterpolationTableEntryCollection;
use qtism\common\enums\BaseType;
use qtism\data\storage\Utils;
use \InvalidArgumentException;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for interpolationTable.
 * 
 * This marshaller is parametric and thus need to be construct with
 * the baseType of the variableDeclaration where the interpolationTable
 * to marshall is contained.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class InterpolationTableMarshaller extends Marshaller {
	
	private $baseType = -1;
	
	/**
	 * Create a new instance of InterpolationTableMarshaller. Because the InterpolationTableMarshaller
	 * needs to know the baseType of the variableDeclaration that contains the interpolationTable,
	 * a $baseType can be passed as an argument for instantiation.
	 * 
	 * @param integer $baseType A value from the BaseType enumeration or -1.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration nor -1.
	 */
	public function __construct($baseType = -1) {
		$this->setBaseType($baseType);
	}
	
	/**
	 * Set the baseType of the variableDeclaration where the interpolationTable
	 * to marshall is contained. Set to -1 if no baseType found for the related
	 * variableDeclaration.
	 * 
	 * @param integer $baseType A value from the BaseType enumeration.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration nor -1.
	 */
	public function setBaseType($baseType) {
		if (in_array($baseType, BaseType::asArray()) || $baseType == -1) {
			$this->baseType = $baseType;
		}
		else {
			$msg = "The BaseType attribute must be a value from the BaseType enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the baseType of the variableDeclaration where the interpolationTable
	 * to marshall is contained. It returns -1 if no baseType found for the related
	 * variableDeclaration.
	 * 
	 * @return integer A value from the BaseType enumeration or -1 if no baseType found for the related variableDeclaration.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Marshall an InterpolationTable object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An InterpolationTable object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		foreach ($component->getInterpolationTableEntries() as $interpolationTableEntry) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($interpolationTableEntry, array($this->getBaseType()));
			$element->appendChild($marshaller->marshall($interpolationTableEntry));
		}
		
		if ($component->getDefaultValue() !== null) {
			static::setDOMElementAttribute($element, 'defaultValue', $component->getDefaultValue());
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI InterpolationTable element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An InterpolationTable object.
	 * @throws UnmarshallingException If $element does not contain any interpolationTableEntry QTI elements.
	 */
	protected function unmarshall(DOMElement $element) {
		$interpolationTableEntryElements = $element->getElementsByTagName('interpolationTableEntry');
		
		if ($interpolationTableEntryElements->length > 0) {
			$interpolationTableEntryCollection = new InterpolationTableEntryCollection();
			for ($i = 0; $i < $interpolationTableEntryElements->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($interpolationTableEntryElements->item($i), array($this->getBaseType()));
				$interpolationTableEntryCollection[] = $marshaller->unmarshall($interpolationTableEntryElements->item($i));
			}
			
			$object = new InterpolationTable($interpolationTableEntryCollection);
			
			if (($defaultValue = static::getDOMElementAttributeAs($element, 'defaultValue')) !== null) {
				try {
					$object->setDefaultValue(Utils::stringToDatatype($defaultValue, $this->getBaseType()));
				}
				catch (InvalidArgumentException $e) {
					$strType = BaseType::getNameByConstant($this->getBaseType());
					$msg = "Unable to transform '${defaultValue}' into ${strType}.";
					throw new UnmarshallingException($msg, $element, $e);
				}
			}
			
			return $object;
		}
		else {
			$msg = "An 'interpolationTable' element must contain at least one 'interpolationTableEntry' element.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'interpolationTable';
	}
}
