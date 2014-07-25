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

use \InvalidArgumentException;
use qtism\common\utils\Format;

/**
 * The TestFeedback class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TestFeedback extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * Test feedback is shown to the candidate either directly following outcome processing 
	 * (during the test) or at the end of the testPart or assessmentTest as appropriate 
	 * (referred to as atEnd). 
	 * 
	 * The value of an outcome variable is used in conjunction with the showHide and 
	 * identifier attributes to determine whether or not the feedback is actually 
	 * shown in a similar way to feedbackElement (Item Model).
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $access = TestFeedbackAccess::DURING;
	
	/**
	 * The QTI Identifier of the outcome variable bound to this feedback.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $outcomeIdentifier;
	
	/**
	 * From IMS QTI:
	 * 
	 * The showHide attribute determines how the visibility of the feedbackElement is controlled.
	 * If set to show then the feedback is hidden by default and shown only if the associated 
	 * outcome variable matches, or contains, the value of the identifier attribute. If set 
	 * to hide then the feedback is shown by default and hidden if the associated outcome 
	 * variable matches, or contains, the value of the identifier attribute.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $showHide = ShowHide::SHOW;
	
	/**
	 * The QTI identifier of the TestFeedback.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * The title of the Feedback. Empty string means there is no title.
	 * 
	 * From IMS QTI:
	 * 
	 * Delivery engines are not required to present the title to the candidate but may do so, 
	 * for example as the title of a modal pop-up window or sub-heading in a combined report.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $title;
	
	/**
	 * The markup content of the feedback to show. In IMS QTI specs, this attribute
	 * is a FlowStatic element. For more convenience, the content is stored as a string
	 * for easier manipulation.
	 * 
	 * From IMS QTI:
	 * 
	 * The content of the testFeedback must not contain any interactions.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $content;
	
	/**
	 * Create a new instance of TestFeedback.
	 * 
	 * Values of attributes 'showHide' and 'access' are respectively ShowHide::SHOW and
	 * TestFeedbackAccess::DURING.
	 * 
	 * @param string $identifier The identifier of the feedback.
	 * @param string $outcomeIdentifier The identifier of the outcome variable bound to the feedback.
	 * @param string $content The content of the feedback as an XML markup binary stream.
	 * @param string $title The title of the feedback. An empty string means that no title is specified.
	 * @throws InvalidArgumentException If one of the arguments has a wrong datatype or incorrect format.
	 */
	public function __construct($identifier, $outcomeIdentifier, $content, $title = '') {		
		$this->setIdentifier($identifier);
		$this->setOutcomeIdentifier($outcomeIdentifier);
		$this->setContent($content);
		$this->setTitle($title);
	}
	
	/**
	 * Get how the feedback is shown to the candidate.
	 * 
	 * * TestFeedbackAccess::DURING = At outcome processing time.
	 * * TestFeedbackAccess::AT_END = At the end of the TestPart or AssessmentTest.
	 * 
	 * @return int A value of the TestFeedbackAccess enumeration.
	 */
	public function getAccess() {
		return $this->access;
	}
	
	/**
	 * Set how the feedback is shown to the candidate.
	 * 
	 * * TestFeedbackAccess::DURING = At outcome processing time.
	 * * TestFeedbackAccess:AT_END = At the end of the TestPart or AssessmentTest.
	 * 
	 * @param int $access A value of the TestFeedbackAccess enumeration.
	 * @throws InvalidArgumentException If $access is not a value from the TestFeedbackAccess enumeration.
	 */
	public function setAccess($access) {
		if (in_array($access, TestFeedbackAccess::asArray())) {
			$this->access = $access;
		}
		else {
			$msg = "'${access}' is not a value from the TestFeedbackAccess enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the QTI Identifier of the outcome variable bound to this TestFeedback.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getOutcomeIdentifier() {
		return $this->outcomeIdentifier;
	}
	
	/**
	 * Set the QTI Identifier of the outcome variable bound to this TestFeedback.
	 * 
	 * @param string $outcomeIdentifier A QTI Identifier.
	 * @throws InvalidArgumentException If $outcomeIdentifier is not a valid QTI Identifier.
	 */
	public function setOutcomeIdentifier($outcomeIdentifier) {
		if (Format::isIdentifier($outcomeIdentifier)) {
			$this->outcomeIdentifier = $outcomeIdentifier;
		}
		else {
			$msg = "'${outcomeIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get how the feedback should be displayed.
	 * 
	 * @return int A value from the ShowHide enumeration.
	 */
	public function getShowHide() {
		return $this->showHide;
	}
	
	/**
	 * Set how the feedback should be displayed.
	 * 
	 * @param unknown_type $showHide A value from the ShowHide enumeration.
	 * @throws InvalidArgumentException If $showHide is not a value from the ShowHide enumeration.
	 */
	public function setShowHide($showHide) {
		if (in_array($showHide, ShowHide::asArray())) {
			$this->showHide = $showHide;
		}
		else {
			$msg = "'${showHide}' is not a value from the ShowHide enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the QTI identifier of this TestFeedback.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Set the QTI identifier of this TestFeedback.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function setIdentifier($identifier) {
		if (Format::isIdentifier($identifier, false)) {
			$this->identifier = $identifier;
		}
		else {
			$msg = "'${identifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the title of this TestFeedback. Empty string means no title specified.
	 * 
	 * @return string A title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Set the title of this TestFeedback. Empty string means no title specified.
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
	 * Get the XML stream of the content of the TestFeedback.
	 * 
	 * @return string XML markup binary stream as a string.
	 */
	public function getContent() {
		// @todo handle flowStatic as a DOMDocumentFragment which easily 'queryable'.
		return $this->content;
	}
	
	/**
	 * Set the XML stream of the content of the TestFeedback.
	 * 
	 * @param string $content XML markup binary stream as a string.
	 * @throws InvalidArgumentException If $content is not a string.
	 */
	public function setContent($content) {
		if (gettype($content) === 'string') {
			$this->content = $content;
		}
		else {
			$msg = "Content must be a string, '" . gettype($content) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'testFeedback';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
