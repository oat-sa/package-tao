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

use qtism\data\rules\Selection;
use qtism\data\rules\Ordering;
use qtism\data\content\RubricBlockCollection;
use qtism\data\SectionPartCollection;
use qtism\data\AssessmentTest;
use \SplObjectStorage;
use \InvalidArgumentException;
use \RuntimeException;

/**
 * The Assessment Section class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentSection extends SectionPart {
	
	/**
	 * The title of the Assessment Section.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $title;
	
	/**
	 * If the section is visible to the candidate.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $visible = true;
	
	/**
	 * If the items of the section (if invisible) must be kept together or not.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $keepTogether = true;
	
	/**
	 * The rules used to select which children of the section are to be used for
	 * each instance of the test.
	 * 
	 * @var Selection
	 * @qtism-bean-property
	 */
	private $selection = null;
	
	/**
	 * The rules used to determine the order in which the children
	 * of the section are to be arranged for each instance of the test.
	 * 
	 * @var Ordering
	 * @qtism-bean-property
	 */
	private $ordering = null;
	
	/**
	 * Section rubrics are presented to the candidate with each item contained 
	 * (directly or indirectly) by the section.
	 * 
	 * @var RubricBlockCollection
	 * @qtism-bean-property
	 */
	private $rubricBlocks;
	
	/**
	 * Child elements.
	 * 
	 * @var SectionPartCollection
	 * @qtism-bean-property
	 */
	private $sectionParts;
	
	/**
	 * Create a new AssessmentSection object
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @param string $title A Title.
	 * @param boolean $visible If it is visible or not.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier, $title is not a string, or visible is not a boolean.
	 */
	public function __construct($identifier, $title, $visible) {
		parent::__construct($identifier);
		$this->setTitle($title);
		$this->setVisible($visible);
		$this->setRubricBlocks(new RubricBlockCollection());
		$this->setSectionParts(new SectionPartCollection());
	}
	
	/**
	 * Get the title of the Assessment Section.
	 * 
	 * @return string A title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Set the title of the Assessment Section.
	 * 
	 * @param string $title A title.
	 * @throws InvalidArgumentException If $title is not a string.
	 */
	public function setTitle($title) {
		if (gettype($title) === 'string') {
			$this->title = $title;
		}
		else {
			$msg = "Title must be a string, '" . gettype($title) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Wether the section is visible.
	 * 
	 * @return boolean true if the section is visible, false if not.
	 */
	public function isVisible() {
		return $this->visible;
	}
	
	/**
	 * Set the visibility of the section.
	 * 
	 * @param boolean $visible true if it must be visible, false otherwise.
	 * @throws InvalidArgumentException If $visible is not a boolean.
	 */
	public function setVisible($visible) {
		if (is_bool($visible)) {
			$this->visible = $visible;
		}
		else {
			$msg = "Visible must be a boolean, '" . gettype($visible) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Inform you if the items must be kept together if the section is invisible.
	 * 
	 * @return boolean
	 */
	public function mustKeepTogether() {
		return $this->keepTogether;
	}
	
	/**
	 * Set if the items must be kept together if the section is invisible.
	 * 
	 * @param boolean $keepTogether true if the items must be kept together, false otherwise.
	 * @throws InvalidArgumentException If $keepTogether is not a boolean.
	 */
	public function setKeepTogether($keepTogether) {
		if (is_bool($keepTogether)) {
			$this->keepTogether = $keepTogether;
		}
		else {
			$msg = "KeepTogether must be a boolan, '" . gettype($keepTogether) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the selection rule for this section. Returns null
	 * if no selection rule is applied to the section.
	 * 
	 * @return Selection A selection rule.
	 */
	public function getSelection() {
		return $this->selection;
	}
	
	/**
	 * Set the selection rule  for this section.
	 * 
	 * @param Selection $selection A selection rule.
	 */
	public function setSelection(Selection $selection = null) {
		$this->selection = $selection;
	}
	
	/**
	 * Whether the AssessmentSection holds a Selection object.
	 * 
	 * @return boolean
	 */
	public function hasSelection() {
	    return is_null($this->getSelection()) === false;
	}
	
	/**
	 * Get the ordering rule for this section. Returns null
	 * if no ordering is applied to the section.
	 * 
	 * @return Ordering An Ordering object.
	 */
	public function getOrdering() {
		return $this->ordering;
	}
	
	/**
	 * Set the ordering rule for this section.
	 * 
	 * @param Ordering $ordering An Ordering object.
	 */
	public function setOrdering(Ordering $ordering = null) {
		$this->ordering = $ordering;
	}
	
	/**
	 * Whether the AssessmentSection holds an Ordering object.
	 * 
	 * @return boolean
	 */
	public function hasOrdering() {
	    return is_null($this->getOrdering()) === false;
	}
	
	/**
	 * Get the section rubrics to presented to the candidate with each
	 * item contained by the section.
	 * 
	 * @return RubricBlockCollection A collection of RubricBlock objects.
	 */
	public function getRubricBlocks() {
		return $this->rubricBlocks;
	}
	
	/**
	 * Set the section rubrics to presented to the candidate with each
	 * item contained by the section.
	 *
	 * @param RubricBlockCollection A collection of RubricBlock objects.
	 */
	public function setRubricBlocks(RubricBlockCollection $rubricBlocks) {
		$this->rubricBlocks = $rubricBlocks;
	}
	
	/**
	 * Get the child elements.
	 * 
	 * @return SectionPartCollection A collection of SectionPart objects.
	 */
	public function getSectionParts() {
		return $this->sectionParts;
	}
	
	/**
	 * Set the child elements.
	 * 
	 * @param SectionPartCollection $sectionParts A collection of SectionPart objects.
	 */
	public function setSectionParts(SectionPartCollection $sectionParts) {
		$this->sectionParts = $sectionParts;
	}
	
	public function getQtiClassName() {
		return 'assessmentSection';
	}
	
	public function getComponents() {
		$comp = array_merge(parent::getComponents()->getArrayCopy(),
							$this->getRubricBlocks()->getArrayCopy(),
							$this->getSectionParts()->getArrayCopy());
		
		if ($this->getSelection() !== null) {
			$comp[] = $this->getSelection();
		}
		
		if ($this->getOrdering() !== null) {
			$comp[] = $this->getOrdering();
		}
		
		return new QtiComponentCollection($comp);
	}
}
