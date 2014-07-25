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


namespace qtism\data;

use qtism\common\collections\IdentifierCollection;
use qtism\data\state\Weight;
use qtism\data\state\VariableMapping;
use qtism\data\state\VariableMappingCollection;
use qtism\data\state\WeightCollection;
use qtism\data\state\TemplateDefaultCollection;
use qtism\data\state\VariableDeclarationCollection;
use \InvalidArgumentException;
use \RuntimeException;

/**
 * From IMS QTI:
 * 
 * Items are incorporated into the test by reference and not by direct aggregation.
 * Note that the identifier of the reference need not have any meaning outside the test.
 * In particular it is not required to be unique in the context of any catalog, or be 
 * represented in the item's metadata. The syntax of this identifier is more restrictive 
 * than that of the identifier attribute of the assessmentItem itself.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemRef extends SectionPart {
	
	/**
	 * A URI to refer the item's file.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $href;
	
	/**
	 * A collection of QTI identifiers representing categories to which the 
	 * Assessment Item is assigned.
	 * 
	 * @var IdentifierCollection
	 * @qtism-bean-property
	 */
	private $categories;
	
	/**
	 * A collection of VariableMapping objects.
	 * 
	 * @var VariableMappingCollection
	 * @qtism-bean-property
	 */
	private $variableMappings;
	
	/**
	 * A collection of Weight objects.
	 * 
	 * @var WeightCollection
	 * @qtism-bean-property
	 */
	private $weights;
	
	/**
	 * A collection of TemplateDefault objects.
	 * 
	 * @var TemplateDefaultCollection
	 * @qtism-bean-property
	 */
	private $templateDefaults;
	
	/**
	 * Create a new instance of AssessmentItemRef.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @param string $href The URI to refer to the item's file.
	 * @param IdentifierCollection $categories The categories to which the item belongs to.
	 * @throws InvalidArgumentException If $href is not a string.
	 */
	public function __construct($identifier, $href, IdentifierCollection $categories = null) {
		parent::__construct($identifier);
		
		$this->setHref($href);
		if (empty($categories)) {
			$this->setCategories(new IdentifierCollection());
		}
		else {
			$this->setCategories($categories);
		}
		
		$this->setVariableMappings(new VariableMappingCollection());
		$this->setWeights(new WeightCollection());
		$this->setTemplateDefaults(new TemplateDefaultCollection());
	}
	
	/**
	 * Get the URI that references the item's file.
	 * 
	 * @return string A URI.
	 */
	public function getHref() {
		return $this->href;
	}
	
	/**
	 * Set the URI that references the item's file.
	 * 
	 * @param string $href A URI.
	 * @throws InvalidArgumentException If $href is not a string.
	 */
	public function setHref($href) {
		if (gettype($href) === 'string') {
			$this->href = $href;
		}
		else {
			$msg = "href must be a string, '" . gettype($href) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the categories to which the item belongs.
	 * 
	 * @return IdentifierCollection A collection of QTI Identifiers.
	 */
	public function getCategories() {
		return $this->categories;
	}
	
	/**
	 * Set the categories to which the item belongs.
	 * 
	 * @param IdentifierCollection $categories A collection of QTI Identifiers.
	 */
	public function setCategories(IdentifierCollection $categories) {
		$this->categories = $categories;
	}
	
	/**
	 * Get the Variable Mappings related to the referenced item.
	 * 
	 * @return VariableMappingCollection A collection of VariableMapping objects.
	 */
	public function getVariableMappings() {
		return $this->variableMappings;
	}
	
	/**
	 * Set the Variable Mappings related to the referenced item.
	 * 
	 * @param VariableMappingCollection $variableMappings A collection of VariableMapping objects.
	 */
	public function setVariableMappings(VariableMappingCollection $variableMappings) {
		$this->variableMappings = $variableMappings;
	}
	
	/**
	 * Get the Weights defined for scaling the referenced item's outcomes.
	 * 
	 * @return WeightCollection A collection of Weight objects.
	 */
	public function getWeights() {
		return $this->weights;
	}
	
	/**
	 * Set the Weights defined for scaling the referenced item's outcomes.
	 * 
	 * @param WeightCollection $weights A collection of Weight objects.
	 */
	public function setWeights(WeightCollection $weights) {
		$this->weights = $weights;
	}
	
	/**
	 * Get the Template Defaults that alter the default value of a template variable
	 * declared by the referenced item.
	 * 
	 * @return TemplateDefaultCollection A collection of TemplateDefault objects.
	 */
	public function getTemplateDefaults() {
		return $this->templateDefaults;
	}
	
	/**
	 * Set the Template Defaults that alter the default value of a template variable
	 * declared by the referenced item.
	 * 
	 * @param TemplateDefaultCollection $templateDefaults A collection of TemplateDefault objects.
	 */
	public function setTemplateDefaults(TemplateDefaultCollection $templateDefaults) {
		$this->templateDefaults = $templateDefaults;
	}
	
	public function getQtiClassName() {
		return 'assessmentItemRef';
	}
	
	public function getComponents() {
		$comp = array_merge(parent::getComponents()->getArrayCopy(),
							$this->getTemplateDefaults()->getArrayCopy(),
							$this->getVariableMappings()->getArrayCopy(),
							$this->getWeights()->getArrayCopy());
		
		return new QtiComponentCollection($comp);
	}
	
	public function __toString() {
	    return $this->getIdentifier();
	}
}
