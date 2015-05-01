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
use qtism\data\state\MatchTable;
use qtism\data\state\MatchTableEntryCollection;
use qtism\common\enums\BaseType;
use qtism\data\storage\Utils;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for matchTable.
 * This marshaller is parametric, because it needs the baseType
 * of the variableDeclaration the MatchTable belongs to.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MatchTableMarshaller extends Marshaller {
	
	/**
	 * The baseType of the variableDeclaration the MatchTable belongs to.
	 * 
	 * @var int
	 */
	private $baseType = -1;
	
	/**
	 * Get the baseType of the variableDeclaration the MatchTable belongs to.
	 * 
	 * @return int
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Set the baseType of the variableDeclaration the MatchTable belongs to.
	 * Pass -1 to set there is no particular baseType (record case).
	 * 
	 * @param integer $baseType A value from the BaseType enumeration or -1 to state there is no particular baseType.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration nor -1.
	 */
	public function setBaseType($baseType) {
		if (in_array($baseType, BaseType::asArray()) || $baseType == -1) {
			$this->baseType = $baseType;
		}
		else {
			$msg = "The baseType attribute must be a value from the BaseType enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Create a new instance of MatchTableMarshaller.
	 * 
	 * @param integer $baseType The baseType of the variableDeclaration the MatchTable belongs to.
	 * @throws InvalidArgumentException If $baseType is an invalid value.
	 */
	public function __construct($baseType = -1) {
		$this->setBaseType($baseType);
	}
	
	/**
	 * Marshall a MatchTable object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A MatchTable object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		foreach ($component->getMatchTableEntries() as $matchTableEntry) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($matchTableEntry, array($this->getBaseType()));
			$element->appendChild($marshaller->marshall($matchTableEntry));
		}
		
		if ($component->getDefaultValue() !== null) {
			static::setDOMElementAttribute($element, 'defaultValue', $component->getDefaultValue());
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI MatchTable element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A MatchTable object.
	 * @throws UnmarshallingException If the $element to unmarshall has no matchTableEntry children.
	 */
	protected function unmarshall(DOMElement $element) {
		$matchTableEntryElements = $element->getElementsByTagName('matchTableEntry');
		if ($matchTableEntryElements->length > 0) {
			$matchTableEntries = new MatchTableEntryCollection();
			
			for ($i = 0; $i < $matchTableEntryElements->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($matchTableEntryElements->item($i), array($this->getBaseType()));
				$matchTableEntries[] = $marshaller->unmarshall($matchTableEntryElements->item($i));
			}
			
			$object = new MatchTable($matchTableEntries);
			
			if (($defaultValue = static::getDOMElementAttributeAs($element, 'defaultValue')) !== null) {
				try {
					$defaultValue = Utils::stringToDatatype($defaultValue, $this->getBaseType());
					$object->setDefaultValue($defaultValue);
				}
				catch (InvalidArgumentException $e) {
					$strType = BaseType::getNameByConstant($this->getBaseType());
					$msg = "Unable to transform '$defaultValue' in a ${strType}.";
					throw new UnmarshallingException($msg, $element, $e);
				}
			}
			
			return $object;
		}
		else {
			$msg = "A QTI matchTable element must contain at least one matchTableEntry element.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'matchTable';
	}
}
