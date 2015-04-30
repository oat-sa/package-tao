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

use qtism\data\rules\BranchRuleCollection;
use qtism\data\rules\PreConditionCollection;
use qtism\common\utils\Format;
use \InvalidArgumentException;
use \SplObserver;
use \SplObjectStorage;

/**
 * 
 * The TestPart class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TestPart extends QtiComponent implements QtiIdentifiable {
	
	/**
	 * From IMS QTI:
	 * 
	 * The identifier of the test part must be unique within the test and must not be 
	 * the identifier of any assessmentSection or assessmentItemRef.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * The navigation mode, a value of the NavigationMode enumeration.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $navigationMode = NavigationMode::LINEAR;
	
	/**
	 * The submission mode, a value of the SubmissionMode enumeration.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $submissionMode = SubmissionMode::INDIVIDUAL;
	
	/**
	 * From IMS QTI:
	 * 
	 * A set of conditions evaluated during the test, that determine 
	 * if this part is to be skipped.
	 * 
	 * @var PreConditionCollection
	 * @qtism-bean-property
	 */
	private $preConditions;
	
	/**
	 * From IMS QTI:
	 * 
	 * A set of rules, evaluated during the test, for setting an alternative
	 * target as the next part of the test.
	 * 
	 * @var BranchRuleCollection
	 * @qtism-bean-property
	 */
	private $branchRules;
	
	/**
	 * From IMS QTI:
	 * 
	 * Parameters used to control the allowable states of each item session in this part. 
	 * These values may be overridden at section and item level.
	 * 
	 * @var ItemSessionControl
	 * @qtism-bean-property
	 */
	private $itemSessionControl = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * Optionally controls the amount of time a candidate is allowed for this part of the test.
	 * 
	 * @var TimeLimits
	 * @qtism-bean-property
	 */
	private $timeLimits = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * The items contained in each testPart are arranged into sections and sub-sections.
	 * 
	 * @var AssessmentSectionCollection
	 * @qtism-bean-property
	 */
	private $assessmentSections;
	
	/**
	 * From IMS QTI:
	 * 
	 * Test-level feedback specific to this part of the test.
	 * 
	 * @var TestFeedbackCollection
	 * @qtism-bean-property
	 */
	private $testFeedbacks;
	
	/**
	 * The observers of this object.
	 * 
	 * @var SplObjectStorage
	 */
	private $observers;
	
	/**
	 * Create a new instance of TestPart.
	 * 
	 * @param string $identifier A QTI Identifier;
	 * @param AssessmentSectionCollection $assessmentSections A collection of AssessmentSection objects.
	 * @param int $navigationMode A value of the NavigationMode enumeration.
	 * @param int $submissionMode A value of the SubmissionMode enumeration.
	 * @throws InvalidArgumentException If an argument has the wrong type or format.
	 */
	public function __construct($identifier, AssessmentSectionCollection $assessmentSections, $navigationMode = NavigationMode::LINEAR, $submissionMode = SubmissionMode::INDIVIDUAL) {
		$this->setObservers(new SplObjectStorage());
		
		$this->setIdentifier($identifier);
		$this->setAssessmentSections($assessmentSections);
		$this->setNavigationMode($navigationMode);
		$this->setSubmissionMode($submissionMode);
		$this->setPreConditions(new PreConditionCollection());
		$this->setBranchRules(new BranchRuleCollection());
		$this->setTestFeedbacks(new TestFeedbackCollection());
	}
	
	/**
	 * Get the identifier of the Test Part.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Set the identifier of the Test Part.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function setIdentifier($identifier) {
		if (Format::isIdentifier($identifier, false)) {
			
			$this->identifier = $identifier;
			$this->notify();
		}
		else {
			$msg = "'${identifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the navigation mode of the Test Part.
	 * 
	 * @return int A value of the Navigation enumeration.
	 */
	public function getNavigationMode() {
		return $this->navigationMode;
	}
	
	/**
	 * Set the navigation mode of the Test Part.
	 * 
	 * @param int $navigationMode A value of the Navigation enumaration.
	 * @throws InvalidArgumentException If $navigation mode is not a value from the Navigation enumeration.
	 */
	public function setNavigationMode($navigationMode) {
		if (in_array($navigationMode, NavigationMode::asArray())) {
			$this->navigationMode = $navigationMode;
		}
		else {
			$msg = "'${navigationMode}' is not a valid value for NavigationMode.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the submission mode of the Test Part.
	 * 
	 * @return int A value of the SubmissionMode enumeration.
	 */
	public function getSubmissionMode() {
		return $this->submissionMode;
	}
	
	/**
	 * Set the submission mode of the Test Part.
	 * 
	 * @param int $submissionMode A value of the SubmissionMode enumeration.
	 * @throws InvalidArgumentException If $submissionMode is not a value from the SubmissionMode enumeration.
	 */
	public function setSubmissionMode($submissionMode) {
		if (in_array($submissionMode, SubmissionMode::asArray())) {
			$this->submissionMode = $submissionMode;
		}
		else {
			$msg = "'${submissionMode}' is not a valid value for SubmissionMode.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the PreConditions that must be applied to this Test Part.
	 * 
	 * @return PreConditionCollection A collection of PreCondition objects.
	 */
	public function getPreConditions() {
		return $this->preConditions;
	}
	
	/**
	 * Set the PreConditions that must be applied to this Test Part.
	 * 
	 * @param PreConditionCollection $preConditions A collection of PreCondition objects.
	 */
	public function setPreConditions(PreConditionCollection $preConditions) {
		$this->preConditions = $preConditions;
	}
	
	/**
	 * Get the BranchRules that must be applied to this Test Part.
	 * 
	 * @return BranchRuleCollection A collection of BranchRule objects.
	 */
	public function getBranchRules() {
		return $this->branchRules;
	}
	
	/**
	 * Set the BranchRules that must be applied to this Test Part.
	 * 
	 * @param BranchRuleCollection $branchRules A collection of BranchRule objects.
	 */
	public function setBranchRules(BranchRuleCollection $branchRules) {
		$this->branchRules = $branchRules;
	}
	
	/**
	 * Get the ItemSessionControl applied to this Test Part. Returns null if there
	 * is no ItemSessionControl to apply.
	 * 
	 * @return ItemSessionControl An ItemSessionControl object.
	 */
	public function getItemSessionControl() {
		return $this->itemSessionControl;
	}
	
	/**
	 * Set the ItemSessionControl applied to this Test Part.
	 * 
	 * @param ItemSessionControl $itemSessionControl An ItemSessionControl object.
	 */
	public function setItemSessionControl(ItemSessionControl $itemSessionControl = null) {
		$this->itemSessionControl = $itemSessionControl;
	}
	
	/**
	 * Whether the TestPart holds an ItemSessionControl object.
	 * 
	 * @return boolean
	 */
	public function hasItemSessionControl() {
	    return is_null($this->getItemSessionControl()) === false;
	}
	
	/**
	 * Get the TimeLimits applied to this Test Part. Returns null if there is no
	 * TimeLimits to apply.
	 * 
	 * @return TimeLimits A TimeLimits object.
	 */
	public function getTimeLimits() {
		return $this->timeLimits;
	}
	
	/**
	 * Set the TimeLimits applied to this Test Part. Returns null if there is no
	 * TimeLimits to apply.
	 * 
	 * @param TimeLimits $timeLimits A TimeLimits object.
	 */
	public function setTimeLimits(TimeLimits $timeLimits = null) {
		$this->timeLimits = $timeLimits;
	}
	
	/**
	 * Whether the TestPart holds a TimeLimits object.
	 * 
	 * @return boolean
	 */
	public function hasTimeLimits() {
	    return is_null($this->getTimeLimits()) === false;
	}
	
	/**
	 * Set the AssessmentSection that are part of this Test Part.
	 * 
	 * @return AssessmentSectionCollection  A collection of AssessmentSection object.
	 */
	public function getAssessmentSections() {
		return $this->assessmentSections;
	}
	
	/**
	 * Set the AssessmentSection that are part of this Test Part.
	 * 
	 * @param AssessmentSectionCollection $assessmentSections A collection of AssessmentSection objects.
	 * @throws InvalidArgumentException If $assessmentSections is an empty collection.
	 */
	public function setAssessmentSections(AssessmentSectionCollection $assessmentSections) {
		if (count($assessmentSections) > 0) {
			$this->assessmentSections = $assessmentSections;
		}
		else {
			$msg = 'A TestPart must contain at least one AssessmentSection.';
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the feedbacks that are part of this Test Part.
	 * 
	 * @return TestFeedbackCollection A collection of TestFeedback objects.
	 */
	public function getTestFeedbacks() {
		return $this->testFeedbacks;
	}
	
	/**
	 * Set the feedbacks that are part of this Test Part.
	 * 
	 * @param TestFeedbackCollection $testFeedbacks A collection of TestFeedback objects.
	 */
	public function setTestFeedbacks(TestFeedbackCollection $testFeedbacks) {
		$this->testFeedbacks = $testFeedbacks;
	}
	
	public function getQtiClassName() {
		return 'testPart';
	}
	
	public function getComponents() {
		$comp = array_merge($this->getAssessmentSections()->getArrayCopy(),
							$this->getBranchRules()->getArrayCopy(),
							$this->getPreConditions()->getArrayCopy(),
							$this->getTestFeedbacks()->getArrayCopy());
		
		if ($this->getItemSessionControl() !== null) {
			$comp[] = $this->getItemSessionControl();
		}
		
		if ($this->getTimeLimits() !== null) {
			$comp[] = $this->getTimeLimits();
		}
		
		return new QtiComponentCollection($comp);
	}
	
	/**
	 * Get the observers of the object.
	 *
	 * @return SplObjectStorage An SplObjectStorage object.
	 */
	protected function getObservers() {
		return $this->observers;
	}
	
	/**
	 * Set the observers of the object.
	 *
	 * @param SplObjectStorage $observers An SplObjectStorage object.
	 */
	protected function setObservers(SplObjectStorage $observers) {
		$this->observers = $observers;
	}
	
	/**
	 * SplSubject::attach implementation.
	 *
	 * @param SplObserver An SplObserver object.
	 */
	public function attach(SplObserver $observer) {
		$this->getObservers()->attach($observer);
	}
	
	/**
	 * SplSubject::detach implementation.
	 *
	 * @param SplObserver $observer An SplObserver object.
	 */
	public function detach(SplObserver $observer) {
		$this->getObservers()->detach($observer);
	}
	
	/**
	 * SplSubject::notify implementation.
	 */
	public function notify() {
		foreach ($this->getObservers() as $observer) {
			$observer->update($this);
		}
	}
}
