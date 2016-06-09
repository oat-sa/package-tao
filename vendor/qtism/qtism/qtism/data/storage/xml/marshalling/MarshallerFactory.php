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

use qtism\common\utils\Reflection;
use qtism\data\QtiComponent;
use qtism\data\storage\xml\Utils;
use \DOMElement;
use \InvalidArgumentException;
use \RuntimeException;
use \ReflectionClass;
use \ReflectionException;

/**
 * The MarshallerFactory aims at giving the client code the ability to
 * create appropriate marshallers regarding a specific QtiComponent
 * or DOMElement.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MarshallerFactory {
	
	
	/**
	 * An associative array where keys are QTI class names
	 * and values are fully qualified marshaller PHP class names.
	 * 
	 * @var array
	 */
	private $mapping = array();
	
	/**
	 * Get the associative array which represents the current QTI class <-> Marshaller
	 * mapping.
	 * 
	 * @return array An associative array where keys are QTI class names and values are fully qualified PHP class names.
	 */
	public function &getMapping() {
		return $this->mapping;
	}
	
	/**
	 * Create a new instance of MarshallerFactory.
	 * 
	 */
	public function __construct() {
		$this->addMappingEntry('default', 'qtism\\data\\storage\\xml\\marshalling\\DefaultValMarshaller');
		$this->addMappingEntry('null', 'qtism\\data\\storage\\xml\\marshalling\\NullValueMarshaller');
		$this->addMappingEntry('max', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('min', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('gcd', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('lcm', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('multiple', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('ordered', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('containerSize', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('isNull', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('random', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('member', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('delete', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('contains', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('not', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('and', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('or', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('match', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('lt', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('gt', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('lte', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('gte', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('durationLT', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('durationGTE', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('sum', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('product', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('subtract', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('divide', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('power', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('integerDivide', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('integerModulus', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('truncate', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('round', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('integerToFloat', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('customOperator', 'qtism\\data\\storage\\xml\\marshalling\\OperatorMarshaller');
		$this->addMappingEntry('outcomeIf', 'qtism\\data\\storage\\xml\\marshalling\\OutcomeControlMarshaller');
		$this->addMappingEntry('outcomeElseIf', 'qtism\\data\\storage\\xml\\marshalling\\OutcomeControlMarshaller');
		$this->addMappingEntry('outcomeElse', 'qtism\\data\\storage\\xml\\marshalling\\OutcomeControlMarshaller');
		$this->addMappingEntry('responseIf', 'qtism\\data\\storage\\xml\\marshalling\\ResponseControlMarshaller');
		$this->addMappingEntry('responseElseIf', 'qtism\\data\\storage\\xml\\marshalling\\ResponseControlMarshaller');
		$this->addMappingEntry('responseElse', 'qtism\\data\\storage\\xml\\marshalling\\ResponseControlMarshaller');
		$this->addMappingEntry('templateIf', 'qtism\\data\\storage\\xml\\marshalling\\TemplateControlMarshaller');
		$this->addMappingEntry('templateElseIf', 'qtism\\data\\storage\\xml\\marshalling\\TemplateControlMarshaller');
		$this->addMappingEntry('templateElse', 'qtism\\data\\storage\\xml\\marshalling\\TemplateControlMarshaller');
		$this->addMappingEntry('em', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('strong', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('abbr', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('acronym', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('b', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('big', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('cite', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('code', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('dfn', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('i', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('kbd', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('samp', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('small', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('span', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('sub', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('sup', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('tt', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('var', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('a', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('q', 'qtism\\data\\storage\\xml\\marshalling\\SimpleInlineMarshaller');
		$this->addMappingEntry('thead', 'qtism\\data\\storage\\xml\\marshalling\\TablePartMarshaller');
		$this->addMappingEntry('tbody', 'qtism\\data\\storage\\xml\\marshalling\\TablePartMarshaller');
		$this->addMappingEntry('tfoot', 'qtism\\data\\storage\\xml\\marshalling\\TablePartMarshaller');
		$this->addMappingEntry('td', 'qtism\\data\\storage\\xml\\marshalling\\TableCellMarshaller');
		$this->addMappingEntry('th', 'qtism\\data\\storage\\xml\\marshalling\\TableCellMarshaller');
		$this->addMappingEntry('address', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h1', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h2', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h3', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h4', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h5', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('h6', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('p', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('pre', 'qtism\\data\\storage\\xml\\marshalling\\AtomicBlockMarshaller');
		$this->addMappingEntry('ul', 'qtism\\data\\storage\\xml\\marshalling\\ListMarshaller');
		$this->addMappingEntry('ol', 'qtism\\data\\storage\\xml\\marshalling\\ListMarshaller');
		$this->addMappingEntry('dd', 'qtism\\data\\storage\\xml\\marshalling\\DlElementMarshaller');
		$this->addMappingEntry('dt', 'qtism\\data\\storage\\xml\\marshalling\\DlElementMarshaller');
		$this->addMappingEntry('orderInteraction', 'qtism\\data\\storage\\xml\\marshalling\\ChoiceInteractionMarshaller');
		$this->addMappingEntry('gapText', 'qtism\\data\\storage\\xml\\marshalling\\GapChoiceMarshaller');
		$this->addMappingEntry('gapImg', 'qtism\\data\\storage\\xml\\marshalling\\GapChoiceMarshaller');
		$this->addMappingEntry('textEntryInteraction', 'qtism\\data\\storage\\xml\\marshalling\\TextInteractionMarshaller');
		$this->addMappingEntry('extendedTextInteraction', 'qtism\\data\\storage\\xml\\marshalling\\TextInteractionMarshaller');
		$this->addMappingEntry('feedbackInline', 'qtism\\data\\storage\\xml\\marshalling\\FeedbackElementMarshaller');
		$this->addMappingEntry('feedbackBlock', 'qtism\\data\\storage\\xml\\marshalling\\FeedbackElementMarshaller');
		$this->addMappingEntry('templateInline', 'qtism\\data\\storage\\xml\\marshalling\\TemplateElementMarshaller');
		$this->addMappingEntry('templateBlock', 'qtism\\data\\storage\\xml\\marshalling\\TemplateElementMarshaller');
		$this->addMappingEntry('hotspotChoice', 'qtism\\data\\storage\\xml\\marshalling\\HotspotMarshaller');
		$this->addMappingEntry('associableHotspot', 'qtism\\data\\storage\\xml\\marshalling\\HotspotMarshaller');
		$this->addMappingEntry('include', 'qtism\\data\\storage\\xml\\marshalling\\XIncludeMarshaller');
	}
	
	/**
	 * Set the associative array which represents the current QTI class <-> Marshaller class mapping.
	 * 
	 * @param array $mapping An associative array where keys are QTI class names and values are fully qualified PHP class names.
	 */
	protected function setMapping(array &$mapping) {
		$this->mapping = $mapping;
	}
	
	/**
	 * Add a mapping entry for a given tuple $qtiClassName <-> $marshallerClassName.
	 * 
	 * @param string $qtiClassName A QTI class name.
	 * @param string $marshallerClassName A PHP marshaller class name (fully qualified).
	 */
	public function addMappingEntry($qtiClassName, $marshallerClassName) {
		$mapping = &$this->getMapping();
		$mapping[$qtiClassName] = $marshallerClassName;
	}
	
	/**
	 * Whether a mapping entry is defined for a given $qtiClassName.
	 * 
	 * @param string $qtiClassName A QTI class name.
	 * @return boolean Whether a mapping entry is defined.
	 */
	public function hasMappingEntry($qtiClassName) {
		$mapping = &$this->getMapping();
		return isset($mapping[$qtiClassName]);
	}
	
	/**
	 * Get the mapping entry.
	 * 
	 * @param string $qtiClassName A QTI class name.
	 * @return false|string False if does not exist, otherwise a fully qualified class name.
	 */
	public function getMappingEntry($qtiClassName) {
		$mapping = &$this->getMapping();
		return $mapping[$qtiClassName];
	}
	
	/**
	 * Remove a mapping for $qtiClassName.
	 * 
	 * @param string $qtiClassName A QTI class name.
	 */
	public function removeMappingEntry($qtiClassName) {
		$mapping = &$this->getMapping();
		
		if ($this->hasMappingEntry($qtiClassName)) {
			unset($mapping[$qtiClassName]);
		}
	}
	
	/**
	 * Create a marshaller for a given QtiComponent or DOMElement object, depending on the current mapping
	 * of the MarshallerFactory. If no mapping entry can be found, the factory will perform a ultimate
	 * trial in the qtism\\data\\storage\\xml\\marshalling namespace to find the relevant Marshaller object.
	 * 
	 * The newly created marshaller will be set up with the MarshallerFactory itself as its MarshallerFactory
	 * object (yes, we know, this is highly recursive but necessary x)).
	 * 
	 * @param DOMElement|QtiComponent $object A QtiComponent or DOMElement object you want to get the corresponding Marshaller object.
	 * @param array $args An optional array of arguments to be passed to the Marshaller constructor.
	 * @throws InvalidArgumentException If $object is not a QtiComponent nor a DOMElement object.
	 * @throws RuntimeException If no Marshaller object can be created for the given $object.
	 * @return Marshaller The corresponding Marshaller object.
	 */
	public function createMarshaller($object, array $args = array()) {
		
		if ($object instanceof QtiComponent) {
			$qtiClassName = $object->getQtiClassName();
		}
		else if ($object instanceof DOMElement) {
			$qtiClassName = $object->localName;
		}
		
		if (isset($qtiClassName)) {
		    
		    try {
		        // Look for a mapping entry.
		        if ($this->hasMappingEntry($qtiClassName)) {
		            $class = new ReflectionClass($this->getMappingEntry($qtiClassName));
		        }
		        else {
		            // Look for default.
		            $className = 'qtism\\data\\storage\\xml\\marshalling\\' . ucfirst($qtiClassName) . 'Marshaller';
		            $class = new ReflectionClass($className);
		        }
		    }
			catch (ReflectionException $e) {
			    $msg = "No marshaller implementation could be found for component '${qtiClassName}'.";
			    throw new RuntimeException($msg, 0, $e);
			}
			
		
			$marshaller = Reflection::newInstance($class, $args);
			$marshaller->setMarshallerFactory($this);
			return $marshaller;
		}
		else {
			$msg = "The object argument must be a QtiComponent or a DOMElementObject, '" . gettype($object) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
}
