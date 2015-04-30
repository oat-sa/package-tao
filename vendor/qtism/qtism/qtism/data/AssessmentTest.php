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
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\processing\OutcomeProcessing;
use qtism\data\state\VariableDeclarationCollection;
use qtism\data\state\Weight;
use qtism\common\utils\Format;
use \SplObjectStorage;
use \ReflectionClass;
use \ReflectionException;
use \RuntimeException;
use \InvalidArgumentException;
use \SplObserver;

/**
 * From IMS QTI:
 * 
 * A test is a group of assessmentItems with an associated set of rules that determine 
 * which of the items the candidate sees, in what order, and in what way the candidate 
 * interacts with them. The rules describe the valid paths through the test, when responses 
 * are submitted for response processing and when (if at all) feedback is to be given.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTest extends QtiComponent implements QtiIdentifiable {
	
	/**
	 * From IMS QTI:
	 * 
	 * The principle identifier of the test. This identifier must have a corresponding 
	 * entry in the test's metadata. See Metadata and Usage Data for more information.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * From IMS QTI:
	 * 
	 * The title of an assessmentTest is intended to enable the test to be selected outside 
	 * of any test session. Therefore, delivery engines may reveal the title to candidates 
	 * at any time, but are not required to do so.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $title;
	
	/**
	 * From IMS QTI:
	 * 
	 * The tool name attribute allows the tool creating the test to identify itself. 
	 * Other processing systems may use this information to interpret the content of 
	 * application specific data, such as labels on the elements of the test rubric.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $toolName = '';
	
	/**
	 * From IMS QTI:
	 * 
	 * The tool version attribute allows the tool creating the test to identify its version. This value must only be interpreted in the context of the toolName.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $toolVersion = '';
	
	/**
	 * From IMS QTI:
	 * 
	 * Each test has an associated set of outcomes. The values of these outcomes are set by the 
	 * test's outcomeProcessing rules.
	 * 
	 * @var OutcomeDeclarationCollection
	 * @qtism-bean-property
	 */
	private $outcomeDeclarations;
	
	/**
	 * From IMS QTI:
	 * 
	 * Optionally controls the amount of time a candidate is allowed for the entire test.
	 * 
	 * @var TimeLimits
	 * @qtism-bean-property
	 */
	private $timeLimits = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * Each test is divided into one or more parts which may in turn be divided into sections, 
	 * sub-sections and so on. A testPart represents a major division of the test and is used 
	 * to control the basic mode parameters that apply to all sections and sub-sections within 
	 * that part.
	 * 
	 * @var TestPartCollection
	 * @qtism-bean-property
	 */
	private $testParts;
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of rules used for calculating the values of the test outcomes.
	 * 
	 * @var OutcomeProcessing
	 * @qtism-bean-property
	 */
	private $outcomeProcessing = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * Contains the test-level feedback controlled by the test outcomes.
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
	private $observers = null;
	
	public function __construct($identifier, $title, TestPartCollection $testParts = null) {
		$this->setObservers(new SplObjectStorage());
		
		$this->setIdentifier($identifier);
		$this->setTitle($title);
		$this->setOutcomeDeclarations(new OutcomeDeclarationCollection());
		$this->setTestParts((empty($testParts)) ? new TestPartCollection() : $testParts);
		$this->setTestFeedbacks(new TestFeedbackCollection());
	}
	
	/**
	 * Get the identifier of the AssessmentTest.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Set the identifier of the AssessmentTest.
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
	 * Get the title of the AssessmentTest.
	 * 
	 * @return string A title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Set the title of the AssessmentTest.
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
	 * Get the name of the tool that was used to author the AssessmentTest. Returns an
	 * empty string if not specfied.
	 * 
	 * @return string A tool name or empty string if not specified.
	 */
	public function getToolName() {
		return $this->toolName;
	}
	
	/**
	 * Set the name of the tool that was used to author the AssessmentTest.
	 * 
	 * @param string $toolName A tool name.
	 * @throws InvalidArgumentException If $toolName is not a string.
	 */
	public function setToolName($toolName) {
		if (gettype($toolName) === 'string') {
			$this->toolName = $toolName;
		}
		else {
			$msg = "Toolname must be a string, '" . gettype($toolName) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the version of the tool that was used to author the AssessmentTest. Returns an
	 * empty string if it was not specified.
	 * 
	 * @return string A tool version.
	 */
	public function getToolVersion() {
		return $this->toolVersion;
	}
	
	/**
	 * Set the version of the tool that was used to author the AssessmentTest. Returns an
	 * empty string if it was not specified.
	 * 
	 * @param string $toolVersion A tool version.
	 * @throws InvalidArgumentException If $toolVersion is not a string.
	 */
	public function setToolVersion($toolVersion) {
		if (gettype($toolVersion) === 'string') {
			$this->toolVersion = $toolVersion;
		}
		else {
			$msg = "ToolVersion must be a string, '" . gettype($toolVersion) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get a collection of OutcomeDeclaration objects bound to the AssessmentTest.
	 * 
	 * @return OutcomeDeclarationCollection A collection of OutcomeDeclaration objects.
	 */
	public function getOutcomeDeclarations() {
		return $this->outcomeDeclarations;
	}
	
	/**
	 * Set a collection of OutcomeDeclaration objects bound to the AssessmentTest.
	 * 
	 * @param OutcomeDeclarationCollection $outcomeDeclarations A collection of OutcomeDeclaration objects.
	 */
	public function setOutcomeDeclarations(OutcomeDeclarationCollection $outcomeDeclarations) {
		$this->outcomeDeclarations = $outcomeDeclarations;
	}
	
	/**
	 * Get the time limits of this AssessmentTest. Returns null if not specified.
	 * 
	 * @return TimeLimits A TimeLimits object or null value if not specified.
	 */
	public function getTimeLimits() {
		return $this->timeLimits;
	}
	
	/**
	 * Set the time limits of this AssessmentTest.
	 * 
	 * @param TimeLimits $timeLimits A TimeLimits object.
	 */
	public function setTimeLimits(TimeLimits $timeLimits = null) {
		$this->timeLimits = $timeLimits;
	}
	
	/**
	 * Get the test parts that form the AssessmentTest.
	 * 
	 * @return TestPartCollection A collection of TestPart objects.
	 */
	public function getTestParts() {
		return $this->testParts;
	}
	
	/**
	 * Set the test parts that form the AssessmentTest.
	 * 
	 * @param TestPartCollection $testParts A collection of TestPart objects.
	 */
	public function setTestParts(TestPartCollection $testParts) {
		$this->testParts = $testParts;
	}
	
	/**
	 * Get the OutcomeProcessing of the AssessmentTest. Returns null if it was not
	 * specified.
	 * 
	 * @return OutcomeProcessing An OutcomeProcessing object or null if not specified.
	 */
	public function getOutcomeProcessing() {
		return $this->outcomeProcessing;
	}
	
	/**
	 * Set the OutcomeProcessing of the AssessmentTest.
	 * 
	 * @param OutcomeProcessing $outcomeProcessing An OutcomeProcessing object.
	 */
	public function setOutcomeProcessing(OutcomeProcessing $outcomeProcessing = null) {
		$this->outcomeProcessing = $outcomeProcessing;
	}
	
	/**
	 * Whether the AssessmentTest holds an OutcomeProcessing object.
	 * 
	 * @return boolean
	 */
	public function hasOutcomeProcessing() {
	    return is_null($this->getOutcomeProcessing()) !== true;
	}
	
	/**
	 * Get the feedbacks associated to the AssessmentTest.
	 * 
	 * @return TestFeedbackCollection A collection of TestFeedback objects.
	 */
	public function getTestFeedbacks() {
		return $this->testFeedbacks;
	}
	
	/**
	 * Set the feedbacks associated to the AssessmentTest.
	 * 
	 * @param TestFeedbackCollection A collection of TestFeedback objects.
	 */
	public function setTestFeedbacks(TestFeedbackCollection $testFeedbacks) {
		$this->testFeedbacks = $testFeedbacks;
	}
	
	public function getQtiClassName() {
		return 'assessmentTest';
	}
	
	public function getComponents() {
		$comp = array_merge($this->getOutcomeDeclarations()->getArrayCopy(),
							$this->getTestFeedbacks()->getArrayCopy(),
							$this->getTestParts()->getArrayCopy());
		
		if ($this->getOutcomeProcessing() !== null) {
			$comp[] = $this->getOutcomeProcessing();
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
	
	/**
	 * Whether the AssessmentTest is exclusively linear. Be carefull, if the test has no test part,
	 * the result will be false.
	 * 
	 * @return boolean
	 */
	public function isExclusivelyLinear() {
	    $testParts = $this->getTestParts();
	    if (count($testParts) === 0) {
	        return false;
	    }
	    
	    $result = true;
	    
	    foreach ($testParts as $testPart) {
	        if ($testPart->getNavigationMode() !== NavigationMode::LINEAR) {
	            $result = false;
	            $testParts->rewind();
	            break;
	        }
	    }
	    
	    return $result;
	}
	
	/**
	 * Whether the AssessmentTest as a TimeLimits component bound to it.
	 * 
	 * @return boolean
	 */
	public function hasTimeLimits() {
	    return $this->getTimeLimits() !== null;
	}
}
