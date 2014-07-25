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

namespace qtism\data\processing;

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\data\rules\ResponseRuleCollection;
use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * If a template identifier is given it may be used to locate an externally defined 
 * responseProcessing template. The rules obtained from the external template may 
 * be used instead of the rules defined within the item itself, though if both are 
 * given the internal rules are still preferred.
 * 
 * In practice, the template attribute may well contain a URN or the URI of a 
 * template stored on a remote web server, such as the standard response processing 
 * templates defined by this specification. When processing an assessmentItem tools 
 * working offline will not be able to obtain the template from a URN or remote URI. 
 * The templateLocation attribute provides an alternative URI, typically a relative 
 * URI to be resolved relative to the location of the assessmentItem itself, that 
 * can be used to obtain a copy of the response processing template. If a delivery 
 * system is able to determine the correct behaviour from the template identifier 
 * alone the templateLocation should be ignored. For example, a delivery system 
 * may have built-in procedures for handling the standard templates defined above.
 * 
 * The mapping from values assigned to respones variables by the candidate onto 
 * appropriate values for the item's outcome variables is achieved through a 
 * number of rules.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseProcessing extends QtiComponent {
	
	/**
	 * A collection of ResponseRule objects.
	 * 
	 * @var ResponseRuleCollection
	 * @qtism-bean-property
	 */
	private $responseRules;
	
	/**
	 * The optional response processing template.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $template = '';
	
	/**
	 * The optional response processing template location.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $templateLocation = '';
	
	/**
	 * Create a new instance of ResponseProcessing.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 */
	public function __construct(ResponseRuleCollection $responseRules = null) {
		if (empty($responseRules)) {
			$responseRules = new ResponseRuleCollection();
		}
		
		$this->setResponseRules($responseRules);
	}
	
	/**
	 * Get the ResponseRule objects that form the ResponseProcessing.
	 * 
	 * @return ResponseRuleCollection A collection of ResponseRule objects.
	 */
	public function getResponseRules() {
		return $this->responseRules;
	}
	
	/**
	 * Set the ResponseRule objects that form the ResponseProcessing.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 */
	public function setResponseRules(ResponseRuleCollection $responseRules) {
		$this->responseRules = $responseRules;
	}
	
	/**
	 * Get the optional response processing template. An empty string ('') means
	 * there is no template given.
	 * 
	 * @return string The URI of the response processing template.
	 */
	public function getTemplate() {
		return $this->template;
	}
	
	/**
	 * Whether the ResponseProcessing has a response processing template.
	 * 
	 * @return boolean
	 */
	public function hasTemplate() {
		return $this->getTemplate() !== '';
	}
	
	/**
	 * Set the optional response processing template. An empty string ('') indicates
	 * there is no template description.
	 * 
	 * @param string $template The URI of the template.
	 * @throws InvalidArgumentException If $template is not a valid URI nor an empty string.
	 */
	public function setTemplate($template) {
		if (Format::isUri($template) === true || (gettype($template) === 'string' && empty($template) === true)) {
			$this->template = $template;
		}
		else {
			$msg = "The given template '${template}' is not a valid URI.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the optional response processing template location. An empty string ('') means
	 * there is no template location given.
	 *
	 * @return string The URI of the response processing template location.
	 */
	public function getTemplateLocation() {
		return $this->templateLocation;
	}
	
	/**
	 * Whether the ResponseProcessing has a response processing template location.
	 * 
	 * @return boolean
	 */
	public function hasTemplateLocation() {
		return $this->getTemplateLocation() !== '';
	}
	
	/**
	 * Set the optional response processing template location. An empty string ('') indicates
	 * there is no template location description.
	 *
	 * @param string $templateLocaton The URI of the template location.
	 * @throws InvalidArgumentException If $templateLocation is not a valid URI nor an empty string.
	 */
	public function setTemplateLocation($templateLocation) {
		if (Format::isUri($templateLocation) === true || (gettype($templateLocation) === 'string' && empty($templateLocation) === true)) {
			$this->templateLocation = $templateLocation;
		}
		else {
			$msg = "The given templateLocation '${templateLocation}' is not a valid URI.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'responseProcessing';
	}
	
	public function getComponents() {
		return new QtiComponentCollection($this->getResponseRules()->getArrayCopy());
	}
}
