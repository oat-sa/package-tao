<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\runtime\tests;

use qtism\data\processing\ResponseProcessing;
use qtism\runtime\common\Container;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Integer;
use qtism\data\IAssessmentItem;
use qtism\data\expressions\Correct;
use qtism\runtime\expressions\CorrectProcessor;
use qtism\runtime\expressions\ExpressionProcessingException;
use qtism\data\NavigationMode;
use qtism\data\SubmissionMode;
use qtism\data\TimeLimits;
use qtism\runtime\processing\ResponseProcessingEngine;
use qtism\runtime\common\OutcomeVariable;
use qtism\data\ItemSessionControl;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use oat\dtms\DateTime;
use \DateTimeZone;
use \InvalidArgumentException;

/**
 * The AssessmentItemSession class implements the lifecycle of an AssessmentItem session.
 * 
 * When instantiated, the resulting AssessmentItemSession object is initialized in
 * the in the AssessmentItemSessionState::INITIAL state, and the session begins (a call
 * to AssessmentItemSession::beginItemSession is performed).
 * 
 * FROM IMS QTI:
 * 
 * An item session is the accumulation of all the attempts at a particular instance of an 
 * item made by a candidate. In some types of test, the same item may be presented to the 
 * candidate multiple times (e.g., during 'drill and practice'). Each occurrence or 
 * instance of the item is associated with its own item session.
 * 
 * The following diagram illustrates the user-perceived states of the item session. Not all 
 * states will apply to every scenario, for example feedback may not be provided for an 
 * item or it may not be allowed in the context in which the item is being used. Similarly, 
 * the candidate may not be permitted to review their responses and/or examine a model 
 * solution. In practice, systems may support only a limited number of the indicated state 
 * transitions and/or support other state transitions not shown here.
 * 
 * For system developers, an important first step in determining which requirements apply 
 * to their system is to identify which of the user-perceived states are supported in their 
 * system and to match the state transitions indicated in the diagram to their own event 
 * model.
 * 
 * The discussion that follows forms part of this specification's requirements on Delivery 
 * Engines.
 * 
 * The session starts when the associated item first becomes eligible for delivery to the 
 * candidate. The item session's state is then maintained and updated in response to the 
 * actions of the candidate until the session is over. At any time the state of the session 
 * may be turned into an itemResult. A delivery system may also allow an itemResult to be 
 * used as the basis for a new session in order to allow a candidate's responses to be seen 
 * in the context of the item itself (and possibly compared to a solution) or even to allow 
 * a candidate to resume an interrupted session at a later time.
 * 
 * The initial state of an item session represents the state after it has been determined 
 * that the item will be delivered to the candidate but before the delivery has taken 
 * place.
 * 
 * In a typical non-Adaptive Test the items are selected in advance and the candidate's 
 * interaction with all items is reported at the end of the test session, regardless of 
 * whether or not the candidate actually attempted all the items. In effect, item sessions 
 * are created in the initial state for all items at the start of the test and are 
 * maintained in parallel. In an Adaptive Test the items that are to be presented are 
 * selected during the session based on the responses and outcomes associated with the 
 * items presented so far. Items are selected from a large pool and the delivery engine 
 * only reports the candidate's interaction with items that have actually been selected.
 * 
 * A candidate's interaction with an item is broken into 0 or more attempts. During each 
 * attempt the candidate interacts with the item through one or more candidate sessions. 
 * At the end of a candidate session the item may be placed into the suspended state ready 
 * for the next candidate session. During a candidate session the item session is in the 
 * interacting state. Once an attempt has ended response processing takes place, after 
 * response processing a new attempt may be started.
 * 
 * For non-adaptive items, response processing typically takes place a limited number of 
 * times, usually only once. For adaptive items, no such limit is required because the 
 * response processing adapts the values it assigns to the outcome variables based on the 
 * path through the item. In both cases, each invocation of response processing occurrs at 
 * the end of each attempt. The appearance of the item's body, and whether any modal 
 * feedback is shown, is determined by the values of the outcome variables.
 * 
 * When no more attempts are allowed the item session passes into the closed state. Once in 
 * the closed state the values of the response variables are fixed. A delivery system or 
 * reporting tool may still allow the item to be presented after it has reached the closed 
 * state. This type of presentation takes place in the review state, summary feedback may 
 * also be visible at this point if response processing has taken place and set a suitable 
 * outcome variable.
 * 
 * Finally, for systems that support the display of solutions, the item session may pass 
 * into the solution state. In this state, the candidate's responses are temporarily 
 * replaced by the correct values supplied in the corresponding responseDeclarations 
 * (or NULL if none was declared).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemSession extends State {
	
    /**
     * The item completion status 'incomplete'.
     * 
     * @var string
     */
    const COMPLETION_STATUS_INCOMPLETE = 'incomplete';
    
    /**
     * The item completion status 'not_attempted'.
     * 
     * @var string
     */
    const COMPLETION_STATUS_NOT_ATTEMPTED = 'not_attempted';
    
    /**
     * The item completion status 'unknown'.
     * 
     * @var string
     */
    const COMPLETION_STATUS_UNKNOWN = 'unknown';
    
    /**
     * The item completion status 'completed'.
     * 
     * @var string
     */
    const COMPLETION_STATUS_COMPLETED = 'completed';
    
    /**
     * A timing reference used to compute the duration of 
     * the session.
     * 
     * @var DateTime
     */
    private $timeReference;
    
	/**
	 * The state of the Item Session as described
	 * by the AssessmentItemSessionState enumeration.
	 * 
	 * @var integer
	 */
	private $state = AssessmentItemSessionState::NOT_SELECTED;
	
	/**
	 * The ItemSessionControl object giving information about how to control
	 * the session.
	 * 
	 * @var ItemSessionControl
	 */
	private $itemSessionControl;
	
	/**
	 * The time limits to be applied on the session if
	 * needed.
	 * 
	 * @var TimeLimits
	 */
	private $timeLimits = null;
	
	/**
	 * The navigation mode in use during the item session.
	 * Default is NavigationMode::LINEAR.
	 * 
	 * @var integer
	 */
	private $navigationMode = NavigationMode::LINEAR;
	
	/**
	 * The submission mode in use during the item session.
	 * Default is SubmissionMode::INDIVIDUAL.
	 * 
	 * @var integer
	 */
	private $submissionMode = SubmissionMode::INDIVIDUAL;
	
	/**
	 * The ExtendedAssessmentItemRef describing the item the session
	 * handles.
	 * 
	 * @var IAssessmentItem
	 */
	private $assessmentItem;
	
	/**
	 * Whether or not the session (SUSPENDED or INTERACTING) is 
	 * currently attempting an attempt. In other words, a candidate
	 * begun an attempt and did not ended it yet.
	 * 
	 * @var boolean
	 */
	private $attempting = false;
	
	/**
	 * An array of callbacks to be executed on duration update.
	 * 
	 * @var array
	 */
	private $onDurationUpdate = array();
	
	/**
	 * 
	 * @var AbstractSessionManager
	 */
	private $sessionManager;
    
	/**
	 * List of callback functions
	 * @var array
	 */
	private $callbacks = array();
	
	/**
	 * Create a new AssessmentItemSession object.
	 * 
	 * * Unless provided in the $variables array, the built-in response/outcome variables 'numAttempts', 'duration' and
	 * 'completionStatus' will be created and set to an appropriate default value automatically.
	 * 
	 * * The AssessmentItemSession object is set up with a default ItemSessionControl object. If you want a specific ItemSessionControl object to rule the session, use the setItemSessionControl() method.
	 * 
	 * @param IAssessmentItem $assessmentItem The description of the item that the session handles.
	 * @param AbstractSessionManager $sessionManager
	 * @param integer $navigationMode The current navigation mode. Default is LINEAR.
	 * @param integer $submissionMode The current submission mode. Default is INDIVIDUAL.
	 * @throws InvalidArgumentException If $navigationMode is not a value from the NavigationMode enumeration.
	 */
	public function __construct(IAssessmentItem $assessmentItem, AbstractSessionManager $sessionManager, $navigationMode = NavigationMode::LINEAR, $submissionMode = SubmissionMode::INDIVIDUAL) {
		parent::__construct();
		
		$this->setAssessmentItem($assessmentItem);
		$this->setNavigationMode($navigationMode);
		$this->setSubmissionMode($submissionMode);
		$this->setItemSessionControl(new ItemSessionControl());
		$this->setSessionManager($sessionManager);
		
		// -- Create the built-in response variables.
		$this->setVariable(new ResponseVariable('numAttempts', Cardinality::SINGLE, BaseType::INTEGER, new Integer(0)));
		$this->setVariable(new ResponseVariable('duration', Cardinality::SINGLE, BaseType::DURATION, new Duration('PT0S')));
			
		// -- Create the built-in outcome variables.
		$this->setVariable(new OutcomeVariable('completionStatus', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier(self::COMPLETION_STATUS_NOT_ATTEMPTED)));
	}
	
	/**
	 * Set the state of the current AssessmentItemSession.
	 * 
	 * @param integer $state A value from the AssessmentItemSessionState enumeration.
	 */
	public function setState($state) {
		$this->state = $state;
	}
	
	/**
	 * Get the state of the current AssessmentItemSession.
	 * 
	 * @return integer A value from the AssessmentItemSessionState enumeration.
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * Set the ItemSessionControl object which describes the way to control
	 * the item session. If the current session is in a SIMULTANEOUS submission
	 * mode context, the maxAttempt attribute of $itemSessionControl is
	 * automatically set to 1.
	 * 
	 * @param ItemSessionControl $itemSessionControl An ItemSessionControl object.
	 */
	public function setItemSessionControl(ItemSessionControl $itemSessionControl) {
		$this->itemSessionControl = $itemSessionControl;
	}
	
	/**
	 * Get the ItemSessionControl object which describes the way to control the item session.
	 * 
	 * @return ItemSessionControl An ItemSessionControl object.
	 */
	public function getItemSessionControl() {
		return $this->itemSessionControl;
	}
	
	/**
	 * Set the TimeLimits to be applied to the session.
	 * 
	 * @param TimeLimits $timeLimits A TimeLimits object or null if no time limits must be applied.
	 */
	public function setTimeLimits(TimeLimits $timeLimits = null) {
	    $this->timeLimits = $timeLimits;
	}
	
	/**
	 * Get the TimeLimits to be applied to the session.
	 * 
	 * @return TimeLimits A TimLimits object or null if no time limits must be applied.
	 */
	public function getTimeLimits() {
	    return $this->timeLimits;
	}
	
	/**
	 * Set the timing reference.
	 * 
	 * @param DateTime $timeReference A DateTime object.
	 */
	public function setTimeReference(\DateTime $timeReference) {
	    $this->timeReference = $timeReference;
	}
	
	/**
	 * Get the timing reference.
	 * 
	 * @return DateTime A DateTime object.
	 */
	public function getTimeReference() {
	    return $this->timeReference;
	}
	
	/**
	 * Get the acceptable latency time to be applied when timelimits
	 * are in force.
	 * 
	 * @return Duration A Duration object.
	 */
	public function getAcceptableLatency() {
	    return $this->getSessionManager()->getAcceptableLatency();
	}
	
	/**
	 * Whether or not minimum time limits must be taken into account.
	 * 
	 * @return boolean
	 */
	public function mustConsiderMinTime() {
	    return $this->getSessionManager()->mustConsiderMinTime();
	}
	
	/**
	 * Whether the session is driven by a TimeLimits object
	 * or not.
	 * 
	 * @return boolean
	 */
	public function hasTimeLimits() {
	    return $this->getTimeLimits() !== null;
	}
	
	/**
	 * Set the navigation mode in use during the item session.
	 * 
	 * @param integer $navigationMode A value from the NavigationMode enumeration.
	 */
	public function setNavigationMode($navigationMode) {
	    $this->navigationMode = $navigationMode;
	}
	
	/**
	 * Get the navigation mode in use during the item session.
	 * 
	 * @return integer A value from the NavigationMode enumeration.
	 */
	public function getNavigationMode() {
	    return $this->navigationMode;
	}
	
	/**
	 * Set the submission mode in use during the item session.
	 * 
	 * @param integer $submissionMode A value from the SubmissionMode enumeration.
	 */
	public function setSubmissionMode($submissionMode) {
        $this->submissionMode = $submissionMode;
	}
	
	/**
	 * Get the submission mode in use during the item session.
	 * 
	 * @return integer A value from the SubmissionMode enumeration.
	 */
	public function getSubmissionMode() {
	    return $this->submissionMode;
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the navigation mode in use for the item session is LINEAR.
	 * 
	 * @return boolean
	 */
	public function isNavigationLinear() {
	    return $this->getNavigationMode() === NavigationMode::LINEAR;
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the navigation mode in use for the item session is NON_LINEAR.
	 * 
	 * @return boolean
	 */
	public function isNavigationNonLinear() {
	    return $this->getNavigationMode() === NavigationMode::NONLINEAR;
	}
	
	/**
	 * Set the IAssessmentItem object which describes the item to be handled
	 * by the session.
	 * 
	 * @param IAssessmentItem $assessmentItem An IAssessmentItem object.
	 */
	public function setAssessmentItem(IAssessmentItem $assessmentItem) {
	    $this->assessmentItem = $assessmentItem;
	}
	
	/**
	 * Get the IAssessmentItem object which describes the item to be handled by the
	 * session.
	 * 
	 * @return IAssessmentItem An IAssessmentItem object.
	 */
	public function getAssessmentItem() {
	    return $this->assessmentItem;
	}
	
	/**
	 * Set whether a candidate is currently performing an attempt.
	 * 
	 * @param boolean $attempting
	 * @throws InvalidArgumentException If $attempting is not a boolean value.
	 */
	public function setAttempting($attempting) {
        $this->attempting = $attempting;
	}
	
	/**
	 * Whether the candidate is currently performing an attempt.
	 * 
	 * @return boolean
	 */
	public function isAttempting() {
	    return $this->attempting;
	}
	
	/**
	 * Get the session manager.
	 * 
	 * @return AbstractSessionManager
	 */
	protected function getSessionManager() {
	    return $this->sessionManager;
	}
	
	/**
	 * Set the session manager.
	 * 
	 * @param AbstractSessionManager $sessionManager
	 */
	protected function setSessionManager(AbstractSessionManager $sessionManager) {
	    $this->sessionManager = $sessionManager;
	}
	
	/**
	 * Start the item session. The item session is started when the related item
	 * becomes eligible for the candidate.
	 * 
	 * * ResponseVariable objects involved in the session will be set a value of NULL.
	 * * OutcomeVariable objects involved in the session will be set their default value if any. Otherwise, they are set to NULL unless their baseType is integer or float. In this case, the value is 0 or 0.0.
	 * * The state of the session is set to INITIAL.
	 * 
	 */
	public function beginItemSession() {
	    
		// We initialize the item session and its variables.
		foreach ($this->getAssessmentItem()->getOutcomeDeclarations() as $outcomeDeclaration) {
		    // Outcome variables are instantiantiated as part of the item session.
		    // Their values may be initialized with a default value if they have one.
		    $outcomeVariable = OutcomeVariable::createFromDataModel($outcomeDeclaration);
		    $outcomeVariable->initialize();
		    $outcomeVariable->applyDefaultValue();
		    $this->setVariable($outcomeVariable);
		}
		
		foreach ($this->getAssessmentItem()->getResponseDeclarations() as $responseDeclaration) {
		    // Response variables are instantiated as part of the item session.
		    // Their values are always initialized to NULL.
		    $responseVariable = ResponseVariable::createFromDataModel($responseDeclaration);
		    $responseVariable->initialize();
		    $this->setVariable($responseVariable);
		}
		
		// The session gets the INITIAL state, ready for a first attempt.
		$this->setState(AssessmentItemSessionState::INITIAL);
		$this['duration'] = new Duration('PT0S');
		$this['numAttempts']->setValue(0);
		$this['completionStatus']->setValue(self::COMPLETION_STATUS_NOT_ATTEMPTED);
	}
	
	/**
	 * begin an attempt for this item session.
	 * 
	 * If the attempt to begin is the first one of the session, response variables are 
	 * applied their default value.
	 *
	 * @throws AssessmentItemSessionException
	 */
	public function beginAttempt() {
	    
	    // -- Are we allowed to begin a new attempt?
	    
	    /* If the current submission mode is SIMULTANEOUS, only 1 attempt is allowed per item.
	     *
	     * From IMS QTI:
	     *
	     * In simultaneous mode, response processing cannot take place until the testPart is
	     * complete so each item session passes between the interacting and suspended states only.
	     * By definition the candidate can take one and only one attempt at each item and feedback
	     * cannot be seen during the test. Whether or not the candidate can return to review
	     * their responses and/or any item-level feedback after the test, is outside the scope
	     * of this specification. Simultaneous mode is typical of paper-based tests.
	     *
	     */
	    $maxAttempts = $this->itemSessionControl->getMaxAttempts();
	    if ($this->submissionMode === SubmissionMode::SIMULTANEOUS) {
	        $maxAttempts = 1;
	    }
	    
	    $isClosed = $this->getState() === AssessmentItemSessionState::CLOSED;
	    
	    if ($isClosed === true) {
	        $identifier = $this->assessmentItem->getIdentifier();
	        $msg = "A new attempt for item '${identifier}' is not allowed. The item session is CLOSED.";
	        throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::STATE_VIOLATION);
	    }
	    
		$data = &$this->getDataPlaceHolder();
		
		// Response variables' default values are set at the beginning
		// of the first attempt.
		if ($this['numAttempts']->getValue() === 0) {
		    
		    // At the start of the first attempt, the completionStatus
		    // goes to 'unknown' and response variables get their
		    // default values if any.
		    
			foreach (array_keys($data) as $k) {
				if ($data[$k] instanceof ResponseVariable) {
					$data[$k]->applyDefaultValue();
				}
			}
			
			$this['duration'] = new Duration('PT0S');
			$this['numAttempts'] = new Integer(0);
		}
		
		$this['numAttempts']->setValue($this['numAttempts']->getValue() + 1);
		
		// The session get the INTERACTING STATE.
		$this->state = AssessmentItemSessionState::INTERACTING;
		$this->attempting = true;
		
		// Register a time reference that will be used later on to compute the duration built-in variable.
		$this->timeReference = new DateTime('now', new DateTimeZone('UTC'));
        $this->runCallback('beginAttempt');
	}
	
	/**
	 * End the attempt by providing responses or by another action. If $responses
	 * is provided, the values found into it will be merged to the current state
	 * before ResponseProcessing is executed.
	 * 
	 * * If the item is adaptive and the completionStatus is indicated to be 'completed', the item session ends.
	 * * If the item is non-adaptive, and the number of attempts is exceeded, the item session ends and the completionStatus is set to 'completed'.
	 * * Otherwise, the item session goes to the SUSPENDED state, waiting for a next attempt.
	 * 
	 * @param State $responses (optional) A State composed by the candidate's responses to the item.
	 * @param boolean $responseProcessing (optional) Whether to execute the responseProcessing or not.
	 * @param boolean $allowLateSubmission If set to true, maximum time limits will not be taken into account, even if the a maximum time limit is in force.
	 * @throws AssessmentItemSessionException
	 */
	public function endAttempt(State $responses = null, $responseProcessing = true, $allowLateSubmission = false) {
	    
	    // End of attempt, go in SUSPEND state.
	    $this->suspend();

	    // Flag to indicate if time is exceed or not.
	    $maxTimeExceeded = false;
	    
	    // Is timeLimits in force.
	    if ($this->hasTimeLimits() === true) {

	        // As per QTI 2.1 Spec, Minimum times are only applicable to assessmentSections and
	        // assessmentItems only when linear navigation mode is in effect.
	        if ($this->isNavigationLinear() === true && $this->timeLimits->hasMinTime() === true) {
	            if ($this->mustConsiderMinTime() === true && $this['duration']->getSeconds(true) <= $this->timeLimits->getMinTime()->getSeconds(true)) {
	                // An exception is thrown to prevent the numAttempts to be incremented.
	                // Suspend and wait for a next attempt.
	                $this->suspend();
	                $msg = "The minimal duration is not yet reached.";
	                throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::DURATION_UNDERFLOW);
	            }
	        }
	        
	        // Check if the maxTime constraint is respected.
	        // If late submission is allowed but time exceeded, the item session will be considered 'completed'.
	        // Otherwise, if late submission is not allowed but time exceeded, the session goes to 'incomplete'.
	        if ($this->isMaxTimeReached() === true) {
	            
	            $maxTimeExceeded = true;
	            
	            if ($this->timeLimits->doesAllowLateSubmission() === false && $allowLateSubmission === false) {
	                $this['completionStatus']->setValue(self::COMPLETION_STATUS_INCOMPLETE);
	                $msg = "The maximal duration is exceeded.";
	                $this->endItemSession();
	                throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::DURATION_OVERFLOW);
	            }
	            else {
	                $this['completionStatus']->setValue(self::COMPLETION_STATUS_COMPLETED);
	                $this->endItemSession();
	            }
	        }
	    }
	    
	    // As per specs, when validateResponses is turned on (true) then the candidates are not
	    // allowed to submit the item until they have provided valid responses for all
	    // interactions. When turned off (false) invalid responses may be accepted by the
	    // system.
	    if ($this->submissionMode === SubmissionMode::INDIVIDUAL && $this->itemSessionControl->mustValidateResponses() === true) {
	        // Use the correct expression to control if the responses
	        // are correct.
	        foreach ($responses as $response) {
	            // @todo Reconsider the 'valid' concept.
	            $correctExpression = new Correct($response->getIdentifier());
	            $correctProcessor = new CorrectProcessor($correctExpression);
	            $correctProcessor->setState($responses);
	    
	            try {
	                if ($correctProcessor->process() !== true) {
	                    $responseIdentifier = $response->getIdentifier();
	                    $msg = "The current itemSessionControl.validateResponses attribute is set to true but ";
	                    $msg.= "response '${responseIdentifier}' is incorrect.";
	                    throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::INVALID_RESPONSE);
	                }
	            }
	            catch (ExpressionProcessingException $e) {
	                $responseIdentifier = $response->getIdentifier();
	                $msg = "The current itemSessionControl.validResponses attribute is set to true but an error ";
	                $msg.= "occured while trying to detect if response '${responseIdentifier}' was correct.";
	                throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::RUNTIME_ERROR, $e);
	            }
	        }
	    }
	    
	    // Apply the responses (if provided) to the current state and deal with the responseProcessing.
	    if ($responses !== null) {
	        foreach ($responses as $identifier => $value) {
	            $this[$identifier] = $value->getValue();
	        }
	    }
	    
	    // Apply response processing.
	    // As per QTI 2.1 specs, For Non-adaptive Items, the values of the outcome variables are reset to their 
	    // default values prior to each invocation of responseProcessing. For Adaptive Items the outcome variables 
	    // retain the values that were assigned to them during the previous invocation of response processing. 
	    // For more information, see Response Processing.
	    //
	    // The responseProcessing can be skipped by given a false value to $responseProcessing. Why?
	    // Because when the SubmissionMode is SubmissionMode::SIMULTANEOUS, the responseProcessing must
	    // deffered to the end of the current testPart.
	    if ($responseProcessing === true) {
	    	
	    	if ($this->assessmentItem->isAdaptive() === false) {
	    		$this->resetOutcomeVariables();
	    	}
	    	
	        $responseProcessing = $this->assessmentItem->getResponseProcessing();
	        
	        // Some items (especially to collect information) have no response processing!
	        if ($responseProcessing !== null && ($responseProcessing->hasTemplate() === true || $responseProcessing->hasTemplateLocation() === true || count($responseProcessing->getResponseRules()) > 0)) {
	            $engine = $this->createResponseProcessingEngine($responseProcessing);
	            $engine->process();
	        }
	    }
	    
	    
	    $maxAttempts = $this->itemSessionControl->getMaxAttempts();
	    if ($this->submissionMode === SubmissionMode::SIMULTANEOUS) {
	        $maxAttempts = 1;
	    }
	    
	    // -- Adaptive or non-adaptive item, maximum time limit reached but late submission allowed.
	    if ($maxTimeExceeded === true) {
	        $this->endItemSession();
	        $this['completionStatus']->setValue(self::COMPLETION_STATUS_COMPLETED);   
	    }
	    // -- Adaptive item.
		else if ($this->assessmentItem->isAdaptive() === true && $this->submissionMode === SubmissionMode::INDIVIDUAL && $this['completionStatus']->getValue() === self::COMPLETION_STATUS_COMPLETED) {
		    $this->endItemSession();
		}
		// -- Non-adaptive item + maxAttempts reached.
		else if ($this->assessmentItem->isAdaptive() === false && $this['numAttempts']->getValue() >= $maxAttempts) {
		    
		    // Close only if $maxAttempts !== 0 because 0 means no limit!
		    if ($maxAttempts !== 0) {
		        $this->endItemSession();
		    }
		    
		    // Even if there is no limit of attempts, we consider the item completed.
		    $this['completionStatus']->setValue(self::COMPLETION_STATUS_COMPLETED);
		}
		// -- Non-adaptive - remaining attempts.
		else if ($this->assessmentItem->isAdaptive() === false && $this['numAttempts']->getValue() < $maxAttempts) {
		    $this['completionStatus']->setValue(self::COMPLETION_STATUS_COMPLETED);
		}
		// else...
		// Wait for the next attempt.
		
		$this->attempting = false;
        $this->runCallback('endAttempt');
	}
	
	/**
	 * Suspend the item session. The state will switch to SUSPENDED and the
	 * 'duration' built-in variable is updated.
	 * 
	 * @throws AssessmentItemSessionException With code STATE_VIOLATION if the state of the session is not INTERACTING nor MODAL_FEEDBACK prior to suspension.
	 */
	public function suspend() {
	    
	    if ($this->state !== AssessmentItemSessionState::SUSPENDED) {
	        if ($this->state !== AssessmentItemSessionState::INTERACTING && $this->state !== AssessmentItemSessionState::MODAL_FEEDBACK) {
	            $msg = "Cannot switch from state '" . AssessmentItemSessionState::getNameByConstant($this->state) . "' to state 'suspended'.";
	            $code = AssessmentItemSessionException::STATE_VIOLATION;
	            throw new AssessmentItemSessionException($msg, $this, $code);
	        }
	         
	        $this->updateDuration();
	        $this->state = AssessmentItemSessionState::SUSPENDED;
            $this->runCallback('suspend');
	    }
	}
	
	/**
	 * Set the item session in INTERACTING state.
	 * 
	 * @throws AssessmentItemSessionException With code STATE_VIOLATION if the state of the session is not INITIAL nor SUSPENDED nor MODAL_FEEDBACK nor INTERACTING.
	 */
	public function interact() {
	    
	    $state = $this->getState();
	    
	    if ($state !== AssessmentItemSessionState::INTERACTING) {
	        if ($state !== AssessmentItemSessionState::INITIAL && $state !== AssessmentItemSessionState::SUSPENDED && $state !== AssessmentItemSessionState::MODAL_FEEDBACK) {
	            $msg = "Cannot switch from state '" . AssessmentItemSessionState::getNameByConstant($state) . "' to state 'interacting'.";
	            $code = AssessmentItemSessionException::STATE_VIOLATION;
	            throw new AssessmentItemSessionException($msg, $this, $code);
	        } 
	        
	        $this->setState(AssessmentItemSessionState::INTERACTING);
	        
	        // Reset the time reference. If not, the time spent in SUSPENDED mode will be taken into account!
	        $this->setTimeReference(new \DateTime('now', new DateTimeZone('UTC')));
            $this->runCallback('interact');
	    }
	}
	
	/**
	 * Update the duration built-in variable. The update will only take
	 * place if the current state of the item session is INTERACTING.
	 * 
	 */
	public function updateDuration() {
	    // If the current state is INTERACTING update duration built-in variable.
	    if ($this->getState() === AssessmentItemSessionState::INTERACTING) {
	        $timeRef = $this->getTimeReference();
	        $now = new DateTime('now', new DateTimeZone('UTC'));

	        $data = &$this->getDataPlaceHolder();
			$diff = $now->diff($timeRef);
	        $data['duration']->getValue()->add($diff);
	        
	        $this->setTimeReference($now);
	        
	        foreach ($this->onDurationUpdate as $callBack) {
	            call_user_func_array($callBack, array($this, Duration::createFromDateInterval($diff)));
	        }
	    }
	}
	
	/**
	 * Get the time that remains to the candidate to submit its responses.
	 * 
	 * @return false|Duration A Duration object or false if there is no time limit.
	 */
	public function getRemainingTime() {
	    $this->updateDuration();
	    
	    // false = unlimited
	    $remainingTime = false;
	    
	    if ($this->hasTimeLimits() === true && $this->getTimeLimits()->hasMaxTime() === true) {
	        $remainingTime = clone $this->getTimeLimits()->getMaxTime();
	        $remainingTime->sub($this['duration']);
	    }
	    
	    return $remainingTime;
	}
	
	/**
	 * Close the item session because no more attempts are allowed. The 'completionStatus' built-in outcome variable
	 * becomes 'completed', and the state of the item session becomes 'closed'.
	 * 
	 * The item session ends if:
	 * 
	 * * the candidate ends an attempt, the item is non-adaptive and the maximum amount of attempts is reached.
	 * * the candidate ends an attempt, the is is adaptive and the completionStatus is 'complete'.
	 * 
	 * If the current state is INTERACTING, the state is set to SUSPEND (to get 'duration' computed) and
	 * then the state goes to CLOSED.
	 */
	public function endItemSession() {
	    
	    // If the candidate was interacting, suspend before
	    // to get a correct state flow.
	    if ($this->getState() === AssessmentItemSessionState::INTERACTING) {
	        $this->suspend();
	    }
	   
		$this->setState(AssessmentItemSessionState::CLOSED);
		$this->setAttempting(false);
	}
	
	/**
	 * Skip the item of the current item session. All response variables involved in the item session
	 * will be set to their default value or NULL and submitted.
	 * 
	 * @throws AssessmentItemSessionException If skipping is not allowed with respect with the current itemSessionControl.
	 */
	public function skip() {
	    // allowSkipping is taken into account only if submission mode is INDIVIDUAL.
	    if ($this->getItemSessionControl()->doesAllowSkipping() === false && $this->getSubmissionMode() === SubmissionMode::INDIVIDUAL) {
	        // Skipping not allowed.
	        $itemIdentifier = $this->getAssessmentItem()->getIdentifier();
	        $msg = "Skipping item '${itemIdentifier}' is not allowed.";
	        throw new AssessmentItemSessionException($msg, $this, AssessmentItemSessionException::SKIPPING_FORBIDDEN);
	    }
	    else {
	        // Detect all response variables and submit them with their default value or NULL.
	        foreach ($this->getAssessmentItem()->getResponseDeclarations() as $responseDeclaration) {
	            $this->getVariable($responseDeclaration->getIdentifier())->applyDefaultValue();
	        }

	        // End the attempt with a null state.
	        $this->endAttempt(null, $this->getSubmissionMode() === SubmissionMode::INDIVIDUAL);
	    }
	}
	
	/**
	 * Get the number of remaining attempts possible for the item session.
	 * Be careful! If the item of the session is adaptive but not yet completed or if the maxAttempts is unlimited, -1 is returned
	 * because there is no way to determine how much remaining attempts are available.
	 * 
	 * @return integer The number of remaining items. -1 means unlimited.
	 */
	public function getRemainingAttempts() {
	    $itemRef = $this->getAssessmentItem();
	    
	    $maxAttempts = $this->getItemSessionControl()->getMaxAttempts();
	    if ($this->getSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
	        $maxAttempts = 1;
	    }
	    
	    if ($itemRef->isAdaptive() === false) {
	        if ($maxAttempts === 0) {
	            // 0 means unlimited.
	            return -1;
	        }
	        else if ($itemRef->isAdaptive() === false && $this->isMaxTimeReached() === false) {
	            // The item is non-adaptative and is not completed nor time exceeded.
	            return $maxAttempts - $this['numAttempts']->getValue();
	        }
	        else {
	            return 0;
	        }
	    }
	    else if ($itemRef->isAdaptive() === true && $this['completionStatus']->getValue() === self::COMPLETION_STATUS_COMPLETED) {
	        // The item is adaptive and completed.
	        return 0;
	    }
	    
	    // The item is adaptive, and is not completed yet.
	    return -1;
	}
	
	/**
	 * Whether all non built-in response variables held by the session match their
	 * associated correct response.
	 * 
	 * If the item session has the NOT_SELECTED state, false is directly returned because
	 * it is certain that there is no correct response yet in the session.
	 * 
	 * @return boolean
	 * @throws AssessmentItemSessionException With error code = RUNTIME_ERROR if an error occurs while processing the 'correct' QTI expression on a response variable held by the session.
	 */
	public function isCorrect() {
	    
	    if ($this->getState() === AssessmentItemSessionState::NOT_SELECTED) {
	        // The session cannot be considered as correct if not yet selected
	        // for presentation to the candidate.    
	        return false;
	    }
	    
	    $data = &$this->getDataPlaceHolder();
	    $excludedVariableIdentifiers = array('numAttempts', 'duration');
	    
	    foreach (array_keys($data) as $identifier) {
	        $var = $data[$identifier];
	        
	        if ($var instanceof ResponseVariable && in_array($var->getIdentifier(), $excludedVariableIdentifiers) === false) {
	            $isCorrect = $var->isCorrect();
	             
	            if ($isCorrect === false) {
	                return false;
	            }
	        }
	    }
	    
	    // All responses are correct.
	    return true;
	}
	
	/**
	 * Whether the item of the session has been attempted (at least once). In other words, items which the user has interacted,
	 * whether or not they provided a response.
	 * 
	 * @return boolean
	 */
	public function isPresented() {
	    return $this['numAttempts']->getValue() > 0;
	}
	
	/**
	 * Whether the item of the session has been selected for presentation to the candidate, regardless of whether the candidate
	 * has attempted them or not.
	 * 
	 * @return boolean
	 */
	public function isSelected() {
	    return $this->getState() !== AssessmentItemSessionState::NOT_SELECTED;
	}
	
	/**
	 * Whether the item of the session has been attempted (at least once) and for which at least one response was given.
	 * 
	 * @return boolean
	 */
	public function isResponded() {
	    
	    if ($this->isPresented() === false) {
	        return false;
	    }
	    
	    $excludedResponseVariables = array('numAttempts', 'duration');
	    foreach ($this->getKeys() as $k) {
	        $var = $this->getVariable($k);
	        
	        if ($var instanceof ResponseVariable && in_array($k, $excludedResponseVariables) === false) {
	            
	            $currentValue = $var->getValue();
	            $currentDefaultValue = $var->getDefaultValue();
	            
	            if ($currentValue === null) {
	                if ($currentValue !== $currentDefaultValue) {
	                    return true;
	                }
	            }
	            else if ($currentValue instanceof Container && $currentValue->isNull() === true) {
	                if ($currentDefaultValue !== null && $currentDefaultValue->isNull() === false) {
	                    return true;
	                }
	            }
	            else {
	                if ($currentValue->equals($currentDefaultValue) === false) {
	                    return true;
	                }
	            }
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * Whether a new attempt is possible for this AssessmentItemSession.
	 * 
	 * @return boolean
	 */
	public function isAttemptable() {
	    return $this->getRemainingAttempts() !== 0;
	}
	
	/**
	 * Whether or not the item has been attempted at least one.
	 * 
	 * @return boolean
	 */
	public function isAttempted() {
	    return $this['numAttempts']->getValue() > 0;
	}
	
	/**
	 * Get a cloned $duration with the acceptable latency of the item
	 * session added.
	 * 
	 * @return Duration $duration + acceptable latency.
	 */
	protected function getDurationWithLatency(Duration $duration) {
	    $duration = clone $duration;
	    $duration->add($this->getAcceptableLatency());
	    return $duration;
	}
	
	/**
	 * Whether or not the maximum time limits in force are reached. If there is
	 * no time limits in force, this method systematically returns false.
	 * 
	 * @return boolean
	 */
	protected function isMaxTimeReached() {
	    $reached = false;
	    
	    if ($this->hasTimeLimits() && $this->timeLimits->hasMaxTime() === true) {
	        if ($this['duration']->getSeconds(true) > $this->getDurationWithLatency($this->timeLimits->getMaxTime())->getSeconds(true)) {
	            $reached = true;
	        }
	    }
	    
	    return $reached;
	}
	
	/**
	 * Get the ResponseVariable objects contained in the AssessmentItemSession.
	 * 
	 * @param boolean $builtIn Whether to include the built-in ResponseVariables ('duration' and 'numAttempts').
	 * @return State A State object composed exclusively with ResponseVariable objects.
	 */
	public function getResponseVariables($builtIn = true) {
	    
	    $state = new State();
	    $data = $this->getDataPlaceHolder();
	    
	    foreach ($data as $id => $var) {
	        if ($var instanceof ResponseVariable && ($builtIn === true || in_array($id, array('duration', 'numAttempts')) === false)) {
	            $state->setVariable($var);
	        }
	    }
	    
	    return $state;
	}
	
	/**
	 * Get the OutcomeVariable objects contained in the AssessmentItemSession.
	 * 
	 * @param boolean $builtIn Whether to include the built-in OutcomeVariable 'completionStatus'.
	 * @return State A State object composed exclusively with OutcomeVariable objects.
	 */
	public function getOutcomeVariables($builtIn = true) {
	    
	    $state = new State();
	    $data = $this->getDataPlaceHolder();
	    
	    foreach ($data as $id => $var) {
	        if ($var instanceof OutcomeVariable && ($builtIn === true || $id !== 'completionStatus')) {
	            $state->setVariable($var);
	        }
	    }
	    
	    return $state;
	}
	
	/**
	 * This protected method contains the logic of creating a new ResponseProcessingEngine object.
	 * 
	 * @param ResponseProcessing $responseProcessing
	 * @return ResponseProcessingEngine
	 */
	protected function createResponseProcessingEngine(ResponseProcessing $responseProcessing) {
	    return new ResponseProcessingEngine($responseProcessing, $this);
	}
	
	public function onDurationUpdate(array $callback) {
	    $this->onDurationUpdate[] = $callback;
	}
	
    /**
     * Register callback function which will be invoked after method 
     * specified in the <i>$eventName</i> parameter is called.
     * Events available for callback registration: 
     * <ul>
     *   <li>beginAttempt</li>
     *   <li>endAttempt</li>
     *   <li>suspend</li>
     *   <li>interact</li>
     * </ul>
     * 
     * Note that first parameter passed to the callback function always will be instance of current class,
     * and the remaining parameters will be taken from the <i>$params</i> array. 
     * 
     * @param string $eventName name of method of current class after which callback function will be invoked.
     * @param array $callback The function or method to be called. 
     * This parameter may be an array, with the name of the class, and the method, or a string, with a function name.
     * @param array $params Parameters to be passed to the callback, as an indexed array. 
     */
    public function registerCallback($eventName, $callback, $params = array())
    {
        $this->callbacks[$eventName][] = array(
            'callback' => $callback,
            'params' => $params
        );
    }
    
    /**
     * Call callback functions registered for method specified in <i>$eventName</i> parameter.
     * $this variable will be passed to the callback function as first parameter.
     * 
     * @param string $eventName
     */
    protected function runCallback($eventName)
    {
        if (isset($this->callbacks[$eventName])) {
            foreach($this->callbacks[$eventName] as $callback) {
                array_unshift($callback['params'], $this);
                call_user_func_array($callback['callback'], $callback['params']);
            }
        }
    }
    
	public function __clone() {
	    $newData = array();
	    $oldData = $this->getDataPlaceHolder();
	    
	    foreach ($oldData as $k => $v) {
	        $newData[$k] = clone $v;
	    }
	    
	    $this->setDataPlaceHolder($newData);
	}
}
