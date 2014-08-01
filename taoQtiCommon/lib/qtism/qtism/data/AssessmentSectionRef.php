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

use qtism\data\QtiComponent;

/**
 * From IMS QTI:
 * 
 * Sections can be included into testParts or other assessmentSections by aggregation or by 
 * reference. The assessmentSectionRef element enables the inclusion by reference. The only 
 * documents that can be refered to by assessmentSectionRef are XML documents that contain 
 * a single assessmentSection as a single root. There are no other restrictions on the 
 * referenced assessmentSection document.
 * 
 * The assessmentSectionRef element functions as a facade for the assessmentSection it 
 * refers to. That means that, at runtime, the XML tree of the document that contains 
 * the reference — with the refered-to section merged in — should behave exactly the same 
 * as a document that has all the same sections aggregated in one document.
 * 
 * Adaptive test branch rules can only refer to included or directly referenced sections, 
 * they can not refer to sections that are in their turn included or referenced within the 
 * referenced section. That is to say, branching rules should treat referred sections as 
 * leaf nodes, that have no children that are amenable to branching separately from their 
 * immediate parent.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentSectionRef extends SectionPart {
	
	/**
	 * From IMS QTI:
	 * 
	 * The uri is used to refer to the assessmentSection document file (e.g., elsewhere 
	 * in the same content package). There is no requirement that this be unique. A test may 
	 * refer to the same item multiple times within a test. Note however that each reference 
	 * must have a unique identifier.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $href;
	
	/**
	 * Create a new instance of AssessmentSectionRef.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @param string $href A URI.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function __construct($identifier, $href) {
		parent::__construct($identifier);
		$this->setHref($href);
	}
	
	/**
	 * Set the hyper-text reference of the section.
	 * 
	 * @param string $href A URI.
	 */
	public function setHref($href) {
		$this->href = $href;
	}
	
	/**
	 * Get the hyper-text reference of the section.
	 * 
	 * @return string A URI.
	 */
	public function getHref() {
		return $this->href;
	}
	
	public function getQtiClassName() {
		return 'assessmentSectionRef';
	}
}
