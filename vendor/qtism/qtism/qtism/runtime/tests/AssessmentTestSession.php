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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\runtime\tests;

use qtism\data\processing\ResponseProcessing;
use qtism\data\IAssessmentItem;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\expressions\ExpressionEngine;
use qtism\data\AssessmentSectionCollection;
use qtism\data\TimeLimits;
use qtism\common\datatypes\Duration;
use qtism\runtime\processing\ResponseProcessingEngine;
use qtism\data\SubmissionMode;
use qtism\runtime\common\ProcessingException;
use qtism\runtime\processing\OutcomeProcessingEngine;
use qtism\common\collections\IdentifierCollection;
use qtism\data\NavigationMode;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\runtime\tests\Route;
use qtism\runtime\tests\RouteItem;
use qtism\runtime\common\OutcomeVariable;
use qtism\data\AssessmentTest;
use qtism\data\TestPart;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentItemRef;
use qtism\data\AssessmentItemRefCollection;
use qtism\runtime\common\State;
use qtism\runtime\common\VariableIdentifier;
use qtism\runtime\common\Variable;
use \SplObjectStorage;
use \InvalidArgumentException;
use \OutOfRangeException;
use \OutOfBoundsException;
use \LogicException;
use \UnexpectedValueException;
use \Exception;

/**
 * The AssessmentTestSession class represents a candidate session
 * for a given AssessmentTest.
 * 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSession extends State {
	
    const ROUTECOUNT_ALL = 0;
    const ROUTECOUNT_EXCLUDENORESPONSE = 1;
    
    /**
     * A unique ID for this AssessmentTestSession.
     * 
     * @var string
     */
    private $sessionId;
    
    /**
     * The AssessmentItemSession store.
     * 
     * @var AssessmentItemSessionStore
     */
	private $assessmentItemSessionStore;
	
	/**
	 * The route to be taken by this AssessmentTestSession.
	 * 
	 * @var Route
	 */
	private $route;
	
	/**
	 * The state of the AssessmentTestSession.
	 * 
	 * @var integer
	 */
	private $state;
	
	/**
	 * The AssessmentTest the AssessmentTestSession is an instance of.
	 * 
	 * @var AssessmentTest
	 */
	private $assessmentTest;
	
	/**
	 * A map (indexed by AssessmentItemRef objects) to store
	 * the last occurence that has one of its variable updated.
	 * 
	 * @var SplObjectStorage
	 */
	private $lastOccurenceUpdate;
	
	/**
	 * A Store of PendingResponse objects that are used to postpone
	 * response processing in SIMULTANEOUS submission mode.
	 * 
	 * @var PendingResponseStore
	 */
	private $pendingResponseStore;
	
	/**
	 * Whether the Assessment Test Session must move automatically
	 * to the next RouteItem after ending an attempt.
	 * 
	 * @var boolean
	 */
	private $autoForward = true;
	
	/**
	 * How/When test results must be submitted.
	 * 
	 * @var integer
	 * @see TestResultsSubmission The TestResultsSubmission enumeration.
	 */
	private $testResultsSubmission = TestResultsSubmission::OUTCOME_PROCESSING;
	
	/**
	 * A state dedicated to store assessment test level durations.
	 * 
	 * @var DurationStore
	 */
	private $durationStore;
	
	/**
	 * The manager to be used to create new AssessmentItemSession objects.
	 * 
	 * @var AbstractSessionManager
	 */
	private $sessionManager;
	
	/**
	 * An array of identifiers representing outcome variable that must not be applied their default value at outcome processing time.
	 * 
	 * @var array
	 */
	private $preservedOutcomeVariables;
	
	/**
	 * Create a new AssessmentTestSession object.
	 *
	 * @param AssessmentTest $assessmentTest The AssessmentTest object which represents the assessmenTest the context belongs to.
	 * @param AbstractSessionManager $sessionManager The manager to be used to create new AssessmentItemSession objects.
	 * @param Route $route The sequence of items that has to be taken for the session.
	 */
	public function __construct(AssessmentTest $assessmentTest, AbstractSessionManager $sessionManager, Route $route) {
		
		parent::__construct();
		$this->setAssessmentTest($assessmentTest);
		$this->setSessionManager($sessionManager);
		$this->setRoute($route);
		$this->setAssessmentItemSessionStore(new AssessmentItemSessionStore());
		$this->setLastOccurenceUpdate(new SplObjectStorage());
		$this->setPendingResponseStore(new PendingResponseStore());
		$durationStore = new DurationStore();
		$this->setDurationStore($durationStore);
		$this->setPreservedOutcomeVariables(array());

		// Take the outcomeDeclaration objects of the global scope.
		// Instantiate them with their defaults.
		foreach ($this->getAssessmentTest()->getOutcomeDeclarations() as $globalOutcome) {
		    $variable = OutcomeVariable::createFromDataModel($globalOutcome);
			$variable->applyDefaultValue();
		    $this->setVariable($variable);
		}
		
		$this->setSessionId('no_session_id');
		$this->setState(AssessmentTestSessionState::INITIAL);
	}
	
	/**
	 * Set the unique session ID for this AssessmentTestSession.
	 * 
	 * @param string $sessionId A unique ID.
	 * @throws InvalidArgumentException If $sessionId is not a string or is empty.
	 */
	public function setSessionId($sessionId) {
	    
	    if (gettype($sessionId) === 'string') {
	        
	        if (empty($sessionId) === false) {
	            $this->sessionId = $sessionId;
	        }
	        else {
	            $msg = "The 'sessionId' argument must be a non-empty string.";
	            throw new InvalidArgumentException($msg);
	        }
	    }
	    else {
	        $msg = "The 'sessionId' argument must be a string, '" . gettype($sessionId) . "' given.";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Get the unique session ID for this AssessmentTestSession.
	 * 
	 * @return string A unique ID.
	 */
	public function getSessionId() {
	    return $this->sessionId;
	}
	
	/**
	 * Get the AssessmentTest object the AssessmentTestSession is an instance of.
	 * 
	 * @return AssessmentTest An AssessmentTest object.
	 */
	public function getAssessmentTest() {
	    return $this->assessmentTest;
	}
	
	/**
	 * Set the AssessmentTest object the AssessmentTestSession is an instance of.
	 * 
	 * @param AssessmentTest $assessmentTest
	 */
	protected function setAssessmentTest(AssessmentTest $assessmentTest) {
	    $this->assessmentTest = $assessmentTest;
	}
	
	/**
	 * Get the assessmentItemRef objects involved in the context.
	 * 
	 * @return AssessmentItemRefCollection A Collection of AssessmentItemRef objects.
	 */
	protected function getAssessmentItemRefs() {
		return $this->getRoute()->getAssessmentItemRefs();
	}
	
	/**
	 * Get the Route object describing the succession of items to be possibly taken.
	 * 
	 * @return Route A Route object.
	 */
	public function getRoute() {
	    return $this->route;
	}
	
	/**
	 * Set the Route object describing the succession of items to be possibly taken.
	 * 
	 * @param Route $route A route object.
	 */
	public function setRoute(Route $route) {
	    $this->route = $route;
	}
	
	/**
	 * Get the current status of the AssessmentTestSession.
	 * 
	 * @return integer A value from the AssessmentTestSessionState enumeration.
	 */
	public function getState() {
	    return $this->state;
	}
	
	/**
	 * Set the current status of the AssessmentTestSession.
	 * 
	 * @param integer $state A value from the AssessmentTestSessionState enumeration.
	 */
	public function setState($state) {
	    if (in_array($state, AssessmentTestSessionState::asArray()) === true) {
	        $this->state = $state;
	    }
	    else {
	        $msg = "The state argument must be a value from the AssessmentTestSessionState enumeration";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Get the AssessmentItemSessionStore.
	 * 
	 * @return AssessmentItemSessionStore
	 */
	public function getAssessmentItemSessionStore() {
	    return $this->assessmentItemSessionStore;
	}
	
	/**
	 * Set the AssessmentItemSessionStore.
	 * 
	 * @param AssessmentItemSessionStore $assessmentItemSessionStore
	 */
	public function setAssessmentItemSessionStore(AssessmentItemSessionStore $assessmentItemSessionStore) {
	    $this->assessmentItemSessionStore = $assessmentItemSessionStore;
	    
	    foreach ($this->assessmentItemSessionStore->getAllAssessmentItemSessions() as $itemSession) {
	        $itemSession->onDurationUpdate(array($this, 'onItemSessionDurationUpdate'));
	    }
	}
	
	/**
	 * Get the pending responses that are waiting for response processing
	 * when the simultaneous sumbission mode is in force.
	 * 
	 * @return PendingResponsesCollection A collection of PendingResponses objects.
	 */
	public function getPendingResponses() {
	    return $this->getPendingResponseStore()->getAllPendingResponses();
	}
	
	/**
	 * Get the PendingResponses objects store used to postpone
	 * response processing in SIMULTANEOUS submission mode.
	 * 
	 * @return PendingResponseStore A PendingResponseStore object.
	 */
	public function getPendingResponseStore() {
	    return $this->pendingResponseStore;
	}
	
	/**
	 * Set the PendingResponses objects store used to postpone
	 * response processing in SIMULTANEOUS submission mode.
	 * 
	 * @param PendingResponseStore $pendingResponseStore
	 */
	public function setPendingResponseStore(PendingResponseStore $pendingResponseStore) {
	    $this->pendingResponseStore = $pendingResponseStore;
	}
	
	/**
	 * Add a set of responses for which the response processing is postponed.
	 * 
	 * @param PendingResponses $pendingResponses
	 * @throws AssessmentTestSessionException If the current submission mode is not simultaneous.
	 */
	protected function addPendingResponses(PendingResponses $pendingResponses) { 
	    if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {   
	        $this->getPendingResponseStore()->addPendingResponses($pendingResponses);
	    }
	    else {
	        $msg = "Cannot add pending responses while the current submission mode is not SIMULTANEOUS";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	}
	
	/**
	 * Get the Test Results Submission configuration value.
	 * 
	 * @param integer $testResultsSubmission
	 * @see TestResultsSubmission The TestResultsSubmission enumeration.
	 */
	public function setTestResultsSubmission($testResultsSubmission) {
	    $this->testResultsSubmission = $testResultsSubmission;
	}
	
	/**
	 * Get the Test Results Submission configuration value.
	 * 
	 * @return integer
	 * @see TestResultsSubmission The TestResultsSubmission enumeration.
	 */
	public function getTestResultsSubmission() {
	    return $this->testResultsSubmission;
	}
	
	/**
	 * Set the state dedicated to store assessment test level durations.
	 * 
	 * @param DurationStore $durationStore
	 */
	public function setDurationStore(DurationStore $durationStore) {
	    $this->durationStore = $durationStore;
	}
	
	/**
	 * Get the state dedicated to store assessment test level durations.
	 * 
	 * @return DurationStore
	 */
	public function getDurationStore() {
	    return $this->durationStore;
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
	 * Set the manager to be used to create new AssessmentItemSession objects.
	 * 
	 * @param AbstractSessionManager $sessionManager
	 */
	public function setSessionManager(AbstractSessionManager $sessionManager) {
	    $this->sessionManager = $sessionManager;
	}
	
	/**
	 * Get the manager to be used to create new AssessmentItemSession objects.
	 * 
	 * @return AbstractSessionManager
	 */
	public function getSessionManager() {
	    return $this->sessionManager;
	}
	
	/**
	 * Set the identifiers of the outcome variables that must not be applied their default value at outcome processing time.
	 * 
	 * @param array $identifiers
	 */
	public function setPreservedOutcomeVariables(array $identifiers) {
	    $this->preservedOutcomeVariables = $identifiers;
	}
	
	/**
	 * Get the identifiers of the outcome variables that must not be applied their default value at outcome processing time.
	 * 
	 * @return array
	 */
	public function getPreservedOutcomeVariables() {
	    return $this->preservedOutcomeVariables;
	}

	/**
	 * Get a weight by using a prefixed identifier e.g. 'Q01.weight1'
	 * where 'Q01' is the item the requested weight belongs to, and 'weight1' is the
	 * actual identifier of the weight.
	 * 
	 * @param string|VariableIdentifier $identifier A prefixed string identifier or a PrefixedVariableName object.
	 * @return false|Weight The weight corresponding to $identifier or false if such a weight do not exist.
	 * @throws InvalidArgumentException If $identifier is malformed string, not a VariableIdentifier object, or if the VariableIdentifier object has no prefix.
	 */
	public function getWeight($identifier) {
		if (gettype($identifier) === 'string') {
			try {
				$identifier = new VariableIdentifier($identifier);
				if ($identifier->hasSequenceNumber() === true) {
				    $msg = "The identifier ('${identifier}') cannot contain a sequence number.";
				    throw new InvalidArgumentException($msg);
				}
			}
			catch (InvalidArgumentException $e) {
				$msg = "'${identifier}' is not a valid variable identifier.";
				throw new InvalidArgumentException($msg, 0, $e);
			}
		}
		else if (!$identifier instanceof VariableIdentifier) {
			$msg = "The given identifier argument is not a string, nor a VariableIdentifier object.";
			throw new InvalidArgumentException($msg);
		}
		
		// identifier with prefix or not, no sequence number.
		if ($identifier->hasPrefix() === false) {
			$itemRefs = $this->getAssessmentItemRefs();
		    foreach ($itemRefs->getKeys() as $itemKey) {
		        $itemRef = $itemRefs[$itemKey];
		        $weights = $itemRef->getWeights();
		        
		        foreach ($weights->getKeys() as $weightKey) {
		            if ($weightKey === $identifier->__toString()) {
		                return $weights[$weightKey];
		            }
		        }
		    }
		}
		else {
		    // get the item the weight should belong to.
		    $assessmentItemRefs = $this->getAssessmentItemRefs();
		    $expectedItemId = $identifier->getPrefix();
		    if (isset($assessmentItemRefs[$expectedItemId])) {
		        $weights = $assessmentItemRefs[$expectedItemId]->getWeights();
		        	
		        if (isset($weights[$identifier->getVariableName()])) {
		            return $weights[$identifier->getVariableName()];
		        }
		    }
		}
		
		return false;
	}
	
	/**
	 * Add a variable (Variable object) to the current context. Variables that can be set using this method
	 * must have simple variable identifiers, in order to target the global AssessmentTestSession scope only.
	 * 
	 * @param Variable $variable A Variable object to add to the current context.
	 * @throws OutOfRangeException If the identifier of the given $variable is not a simple variable identifier (no prefix, no sequence number).
	 */
	public function setVariable(Variable $variable) {
	    
	    try {
	        $v = new VariableIdentifier($variable->getIdentifier());
	        
	        if ($v->hasPrefix() === true) {
	            $msg = "The variables set to the AssessmentTestSession global scope must have simple variable identifiers. ";
	            $msg.= "'" . $v->__toString() . "' given.";
	            throw new OutOfRangeException($msg);
	        }
	    }
	    catch (InvalidArgumentException $e) {
	        $variableIdentifier = $variable->getIdentifier();
	        $msg = "The identifier '${variableIdentifier}' of the variable to set is invalid.";
	        throw new OutOfRangeException($msg, 0, $e);
	    }
		
		$data = &$this->getDataPlaceHolder();
		$data[$v->__toString()] = $variable;
	}
	
	/**
	 * Get a variable from any scope of the AssessmentTestSession.
	 * 
	 * @return Variable A Variable object or null if no Variable object could be found for $variableIdentifier.
	 */
	public function getVariable($variableIdentifier) {
	    $v = new VariableIdentifier($variableIdentifier);
	    
	    if ($v->hasPrefix() === false) {
	        $data = &$this->getDataPlaceHolder();
	        if (isset($data[$v->getVariableName()])) {
	            return $data[$v->getVariableName()];
	        }
	    }
	    else {
	        // given with prefix.
	        $store = $this->getAssessmentItemSessionStore();
	        $items = $this->getAssessmentItemRefs();
	        $sequence = ($v->hasSequenceNumber() === true) ? $v->getSequenceNumber() - 1 : 0;
	        if ($store->hasAssessmentItemSession($items[$v->getPrefix()], $sequence)) {
	            $session = $store->getAssessmentItemSession($items[$v->getPrefix()], $sequence);
	            return $session->getVariable($v->getVariableName());
	        }
	    }
	    
	    return null;
	}
	
	/**
	 * Get a variable value from the current session using the bracket ([]) notation.
	 * 
	 * The value can be retrieved for any variables in the scope of the AssessmentTestSession. In other words,
	 * 
	 * * Outcome variables in the global scope of the AssessmentTestSession,
	 * * Outcome and Response variables in TestPart/AssessmentSection scopes.
	 * 
	 * Please note that if the requested variable is a duration, the durationUpdate() method
	 * will be called to return an accurate result.
	 * 
	 * @return mixed A QTI Runtime compliant value or NULL if no such value can be retrieved for $offset.
	 * @throws OutOfRangeException If $offset is not a string or $offset is not a valid variable identifier.
	 * @qtism-test-duration-update
	 */
	public function offsetGet($offset) {
		
		try {
			$v = new VariableIdentifier($offset);
			
			if ($v->hasPrefix() === false) {
			    
				// Simple variable name.
				// -> This means the requested variable is in the global test scope.
				
				if ($v->getVariableName() === 'duration') {
				    // Duration of the whole assessmentTest requested.
				    $this->updateDuration();
				    $durationStore = $this->getDurationStore();
				    return $durationStore[$this->getCurrentRouteItem()->getAssessmentTest()->getIdentifier()];
				}
				else {
				    $data = &$this->getDataPlaceHolder();
				     
				    $varName = $v->getVariableName();
				    if (isset($data[$varName]) === false) {
				        return null;
				    }
				    
				    return $data[$offset]->getValue();
				}
			}
			else {
				
				// prefix given.
				// - prefix targets an item?
				$store = $this->getAssessmentItemSessionStore();
				$items = $this->getAssessmentItemRefs();
				
				if (isset($items[$v->getPrefix()]) === true) {
				    
				    $itemRef = $items[$v->getPrefix()];
				    
				    // This item is known to be in the route.
				    if ($v->hasSequenceNumber() === true) {
				        $sequence = $v->getSequenceNumber() - 1;
				    }
				    else if ($this->getAssessmentItemSessionStore()->hasMultipleOccurences($itemRef) === true) {
				        // No sequence number provided + multiple occurence of this item in the route.
				        $sequence = $this->whichLastOccurenceUpdate($itemRef);
				        
				        // As per QTI 2.1 specs, The value of an item variable taken from an item instantiated multiple times from the 
				        // same assessmentItemRef (through the use of selection withReplacement) is taken from the last instance submitted
				        // if submission is simultaneous, otherwise it is undefined.
				        if ($sequence === false || $this->getCurrentSubmissionMode() === SubmissionMode::INDIVIDUAL) {
				            return null;
				        }
				    }
				    else {
				        // No sequence number provided + single occurence of this item in the route.
				        $sequence = 0;
				    }
				    
				    try {
				        $session = $store->getAssessmentItemSession($items[$v->getPrefix()], $sequence);
				        
				        // If duration, update!
				        if ($v->getVariableName() === 'duration') {
				            $session->updateDuration();
				        }
				        
				        return $session[$v->getVariableName()];
				    }
				    catch (OutOfBoundsException $e) {
				        // No such session referenced in the session store.
				        return null;
				    }
				}
				else if ($v->getVariableName() === 'duration') {
				    $this->updateDuration();
				    $durationStore = $this->getDurationStore();
				    return $durationStore[$v->getPrefix()];
				}
			    
			    return null;
			}
		}
		catch (InvalidArgumentException $e) {
			$msg = "AssessmentTestSession object addressed with an invalid identifier '${offset}'.";
			throw new OutOfRangeException($msg, 0, $e);
		}
	}
	
	/**
	 * Set the value of a variable with identifier $offset.
	 * 
	 * @throws OutOfRangeException If $offset is not a string or an invalid variable identifier.
	 * @throws OutOfBoundsException If the variable with identifier $offset cannot be found.
	 */
	public function offsetSet($offset, $value) {
		
		if (gettype($offset) !== 'string') {
			$msg = "An AssessmentTestSession object must be addressed by string.";
			throw new OutOfRangeException($msg);
		}
		
		try {
			$v = new VariableIdentifier($offset);
			
			if ($v->hasPrefix() === false) {
				// global scope request.
				$data = &$this->getDataPlaceHolder();
				$varName = $v->getVariableName();
				if (isset($data[$varName]) === false) {
					$msg = "The variable '${varName}' to be set does not exist in the current context.";
					throw new OutOfBoundsException($msg);
				}
				
				$data[$offset]->setValue($value);
				return;
			}
			else {
				// prefix given.
				
				// - prefix targets an item ?
				$store = $this->getAssessmentItemSessionStore();
				$items = $this->getAssessmentItemRefs();
				$sequence = ($v->hasSequenceNumber() === true) ? $v->getSequenceNumber() - 1 : 0;
				$prefix = $v->getPrefix();
				
				try {
				    if (isset($items[$prefix]) && ($session = $this->getItemSession($items[$prefix], $sequence)) !== false) {
				        $session[$v->getVariableName()] = $value;
				        return;
				    }
				}
				catch (OutOfBoundsException $e) {
				    // The session could be retrieved, but no such variable into it.
				}
				
				$msg = "The variable '" . $v->__toString() . "' does not exist in the current context.";
				throw new OutOfBoundsException($msg);
			}
		}
		catch (InvalidArgumentException $e) {
			// Invalid variable identifier.
			$msg = "AssessmentTestSession object addressed with an invalid identifier '${offset}'.";
			throw new OutOfRangeException($msg, 0, $e);
		}
	}
	
	/**
	 * Unset a given variable's value identified by $offset from the global scope of the AssessmentTestSession.
	 * Please not that unsetting a variable's value keep the variable still instantiated
	 * in the context with its value replaced by NULL.
	 * 
	 * 
	 * @param string $offset A simple variable identifier (no prefix, no sequence number).
	 * @throws OutOfRangeException If $offset is not a simple variable identifier.
	 * @throws OutOfBoundsException If $offset does not refer to an existing variable in the global scope.
	 */
	public function offsetUnset($offset) {
		$data = &$this->getDataPlaceHolder();
		
		// Valid identifier?
		try {
			$v = new VariableIdentifier($offset);
			
			if ($v->hasPrefix() === true) {
			    $msg = "Only variables in the global scope of an AssessmentTestSession may be unset. '${offset}' is not in the global scope.";
			    throw new OutOfBoundsException($msg);
			}
			
			if (isset($data[$offset]) === true) {
			    $data[$offset]->setValue(null);
			}
			else {
			    $msg = "The variable '${offset}' does not exist in the AssessmentTestSession's global scope.";
			    throw new OutOfBoundsException($msg); 
			}
		}
		catch (InvalidArgumentException $e) {
			$msg = "The variable identifier '${offset}' is not a valid variable identifier.";
			throw new OutOfRangeException($msg, 0, $e);
		}
	}
	
	/**
	 * Check if a given variable identified by $offset exists in the global scope
	 * of the AssessmentTestSession.
	 * 
	 * @return boolean Whether the variable identified by $offset exists in the current context.
	 * @throws OutOfRangeException If $offset is not a simple variable identifier (no prefix, no sequence number).
	 */
	public function offsetExists($offset) {
	    try {
	        $v = new VariableIdentifier($offset);
	        
	        if ($v->hasPrefix() === true) {
	            $msg = "Test existence of a variable in an AssessmentTestSession may only be addressed with simple variable ";
	            $msg = "identifiers (no prefix, no sequence number). '" . $v->__toString() . "' given.";
	            throw new OutOfRangeException($msg, 0);
	        }
	        
	        $data = &$this->getDataPlaceHolder();
	        return isset($data[$offset]);
	    }
	    catch (InvalidArgumentException $e) {
	       $msg = "'${offset}' is not a valid variable identifier.";
	       throw new OutOfRangeException($msg);
	    }
	}
	
	/**
	 * Initialize the AssessmentItemSession for the whole route.
	 * 
	 */
	protected function initializeItemSessions() {
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    
	    foreach ($this->getRoute() as $routeItem) {
	        $itemRef = $routeItem->getAssessmentItemRef();
	        $testPart = $routeItem->getTestPart();
	        
	        $navigationMode = $routeItem->getTestPart()->getNavigationMode();
	        $submissionMode = $routeItem->getTestPart()->getSubmissionMode();
	        
	        $session = $this->createAssessmentItemSession($itemRef, $navigationMode, $submissionMode);
	        $session->onDurationUpdate(array($this, 'onItemSessionDurationUpdate'));
	        
	        // Determine the item session control.
	        if (($control = $routeItem->getItemSessionControl()) !== null) {
	            $session->setItemSessionControl($control->getItemSessionControl());
	        }
	        
	        // Determine the time limits.
	        if ($itemRef->hasTimeLimits() === true) {
	            $session->setTimeLimits($itemRef->getTimeLimits());
	        }
	        // else ... No time limits !
	        
	        $this->addItemSession($session, $routeItem->getOccurence());
	    }
	    
	    $route->setPosition($oldPosition);
	}
	
	/**
	 * This protected method contains the logic of instantiating a new AssessmentItemSession object.
	 * 
	 * @param IAssessmentItem $assessmentItem
	 * @param integer $navigationMode
	 * @param integer $submissionMode
	 * @param boolean $mustConsiderMinTime
	 * @return AssessmentItemSession
	 */
	protected function createAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode, $submissionMode) {
	    return $this->getSessionManager()->createAssessmentItemSession($assessmentItem, $navigationMode, $submissionMode);
	}
	
	/**
	 * Callback to invoked when an item session duration is updated.
	 * 
	 * @param AssessmentItemSession $assessmentItemSession
	 * @param Duration $diff
	 */
	public function onItemSessionDurationUpdate(AssessmentItemSession $assessmentItemSession, Duration $diff) {
	    $routeItem = $this->getCurrentRouteItem();
	    $durationStore = $this->getDurationStore();
	    
	    $assessmentTestDurationId = $routeItem->getAssessmentTest()->getIdentifier();
	    $testPartDurationId = $routeItem->getTestPart()->getIdentifier();
	    $assessmentSectionDurationIds = $routeItem->getAssessmentSections()->getKeys();
	    
	    
	    foreach (array_merge(array($assessmentTestDurationId), array($testPartDurationId), $assessmentSectionDurationIds) as $id) {
	        $durationStore[$id]->add($diff);
	    }
	}
	
	/**
	 * Initialize test-level durations.
	 */
	protected function initializeTestDurations() {
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    $route->setPosition(0);
	    $durationStore = $this->getDurationStore();

	    // This might be rude but actually, it's fast ;)!
	    foreach ($route as $routeItem) {
	        $assessmentTestId = $routeItem->getAssessmentTest()->getIdentifier();
	        $testPartId = $routeItem->getTestPart()->getIdentifier();
	        $assessmentSectionIds = $routeItem->getAssessmentSections()->getKeys();
	         
	        $ids = array_merge(array($assessmentTestId), array($testPartId), $assessmentSectionIds);
	        foreach ($ids as $id) {
	            if (isset($durationStore[$id]) === false) {
	                $durationStore->setVariable(new OutcomeVariable($id, Cardinality::SINGLE, BaseType::DURATION, new Duration('PT0S')));
	            }
	        }
	    }
	     
	    $route->setPosition($oldPosition);
	}
	
	/**
	 * Begins the test session. Calling this method will make the state
	 * change into AssessmentTestSessionState::INTERACTING.
	 * 
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function beginTestSession() {
	    // Initialize test-level durations.
	    $this->initializeTestDurations();
	    
	    // Initialize item sessions.
	    $this->initializeItemSessions();
	    
	    // Select the eligible items for the candidate.
	    $this->selectEligibleItems();
	    
	    // The test session has now begun.
	    $this->setState(AssessmentTestSessionState::INTERACTING);
	}
	
	/**
	 * Select the eligible items from the current one to the last
	 * following item in the route which is in linear navigation mode.
	 * 
	 * AssessmentItemSession objects related to the eligible items
	 * will be instantiated.
	 * 
	 */
	protected function selectEligibleItems() {
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    
	    // In this loop, we select at least the first routeItem we find as eligible.
	    while ($route->valid() === true) {
	        $routeItem = $route->current();
	        $session = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	        
	        if ($session->getState() === AssessmentItemSessionState::NOT_SELECTED) {
	            $session->beginItemSession();
	        }
	        
	        if ($route->isNavigationLinear() === true) {
	            // We cannot foresee more items to be selected for presentation
	            // because the rest of the sequence is linear.
	            break;
	        }
	        else {
	            // We continue to search for route items that are selectable for
	            // presentation to the candidate.
	            $route->next();
	        }
	    }
	    
	    $route->setPosition($oldPosition);
	}
	
	/**
	 * Add an item session to the current assessment test session.
	 * 
	 * @param AssessmentItemSession $session
	 * @throws LogicException If the AssessmentItemRef object bound to $session is unknown by the AssessmentTestSession.
	 */
	protected function addItemSession(AssessmentItemSession $session, $occurence = 0) {
	    
	    $assessmentItemRefs = $this->getAssessmentItemRefs();
	    $sessionAssessmentItemRefIdentifier = $session->getAssessmentItem()->getIdentifier();
	    
	    if ($this->getAssessmentItemRefs()->contains($session->getAssessmentItem()) === false) {
	        // The session that is requested to be set is bound to an item
	        // which is not referenced in the test. This is a pure logic error.
	        $msg = "The item session to set is bound to an unknown AssessmentItemRef.";
	        throw new LogicException($msg);
	    }
	    
	    $this->getAssessmentItemSessionStore()->addAssessmentItemSession($session, $occurence);
	}
	
	
	/**
	 * Get an assessment item session.
	 * 
	 * @param AssessmentItemRef $assessmentItemRef
	 * @param integer $occurence
	 * @return AssessmentItemSession|false
	 */
	protected function getItemSession(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
	    
	    $store = $this->getAssessmentItemSessionStore();
	    if ($store->hasAssessmentItemSession($assessmentItemRef, $occurence) === true) {
	        return $store->getAssessmentItemSession($assessmentItemRef, $occurence);
	    }
        
        // No such item session found.
        return false;
	}
	
	/**
	 * Get the current Route Item.
	 * 
	 * @return RouteItem|false A RouteItem object or false if the test session is not running.
	 */
	protected function getCurrentRouteItem() {
	    if ($this->isRunning() === true) {
	        return $this->getRoute()->current();
	    }
	    
	    return false;
	}
	
	/**
	 * Get the current AssessmentItemRef.
	 * 
	 * @return AssessmentItemRef|false An AssessmentItemRef object or false if the test session is not running.
	 */
	public function getCurrentAssessmentItemRef() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentRouteItem()->getAssessmentItemRef();
	    }

	    return false;
	}
	
	/**
	 * Get the current AssessmentItemRef occurence number. In other words
	 * 
	 *  * if the current item of the selection is Q23, the return value is 0.
	 *  * if the current item of the selection is Q01.3, the return value is 2.
	 *  
	 * @return integer| the occurence number of the current AssessmentItemRef in the route or false if the test session is not running.
	 */
	public function getCurrentAssessmentItemRefOccurence() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentRouteItem()->getOccurence();
	    }
	    
	    return false;
	}
	
	/**
	 * Get the current AssessmentSection.
	 * 
	 * @return AssessmentSection|false An AssessmentSection object or false if the test session is not running.
	 */
	public function getCurrentAssessmentSection() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentRouteItem()->getAssessmentSection();
	    }
	   
	    return false;
	}
	
	/**
	 * Get the current TestPart.
	 * 
	 * @return TestPart A TestPart object or false if the test session is not running.
	 */
	public function getCurrentTestPart() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentRouteItem()->getTestPart();
	    }
	    
	    return false;
	}
	
	/**
	 * Get the current navigation mode.
	 * 
	 * @return integer|false A value from the NavigationMode enumeration or false if the test session is not running.
	 */
	public function getCurrentNavigationMode() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentTestPart()->getNavigationMode();
	    }
	    
	    return false;
	}
	
	/**
	 * Get the current submission mode.
	 * 
	 * @return integer|false A value from the SubmissionMode enumeration or false if the test session is not running.
	 */
	public function getCurrentSubmissionMode() {
	    if ($this->isRunning() === true) {
	        return $this->getCurrentTestPart()->getSubmissionMode();
	    }
	    
	    return false;
	}
	
	/**
	 * Get the number of remaining items for the current item in the route.
	 * 
	 * @return integer|false -1 if the item is adaptive but not completed, otherwise the number of remaining attempts. If the assessment test session is not running, false is returned.
	 */
	public function getCurrentRemainingAttempts() {
	    if ($this->isRunning() === true) {
	        $routeItem = $this->getCurrentRouteItem();
	        $session = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	        return $session->getRemainingAttempts();
	    }
	    
	    return false;
	}
	
	/**
	 * Whether the current item is adaptive.
	 * 
	 * @return boolean
	 * @throws AssessmentTestSessionException If the test session is not running.
	 */
	public function isCurrentAssessmentItemAdaptive() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot know if the current item is adaptive while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    return $this->getCurrentAssessmentItemRef()->isAdaptive();
	}
	
	/**
	 * Whether the current item is in INTERACTIVE mode.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running.
	 */
	public function isCurrentAssessmentItemInteracting() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot know if the current item is in INTERACTING state while the state of the test session INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $store = $this->getAssessmentItemSessionStore();
	    $currentItem = $this->getCurrentAssessmentItemRef();
	    $currentOccurence = $this->getCurrentAssessmentItemRefOccurence();
	    return $store->getAssessmentItemSession($currentItem, $currentOccurence)->getState() === AssessmentItemSessionState::INTERACTING;
	}
	
	/**
	 * Get the Previous RouteItem object in the route.
	 * 
	 * @throws AssessmentTestSessionException If the AssessmentTestSession is not running.
	 * @throws OutOfBoundsException If the current position in the route is 0.
	 * @return RouteItem A RouteItem object.
	 */
	public function getPreviousRouteItem() {
	     if ($this->isRunning() === false) {
	         $msg = "Cannot know what is the previous route item while the state of the test session is INITIAL or CLOSED";
	         throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	     }
	     
	     try {
	         return $this->getRoute()->getPrevious();
	     }
	     catch (OutOfBoundsException $e) {
	         $msg = "There is no previous route item because the current position in the route sequence is 0";
	         throw new OutOfBoundsException($msg, 0, $e);
	     }
	}
	
	/**
	 * Get the Next RouteItem object in the route.
	 * 
	 * @throws AssessmentTestSessionException
	 * @throws OutOfBoundsException
	 * @return RouteItem A RouteItem object.
	 */
	public function getNextRouteItem() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot know what is the next route item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    try {
	        return $this->getRoute()->getNext();
	    }
	    catch (OutOfBoundsException $e) {
	        $msg = "There is not next route item because the current position in the route sequence is the last one.";
	        throw new OutOfBoundsException($msg, 0, $e);
	    }
	}
	
	/**
	 * Skip the current item.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running or it is the last route item of the testPart but the SIMULTANEOUS submission mode is in force and not all responses were provided.
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function skip() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot skip the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }

        $item = $this->getCurrentAssessmentItemRef();
        $occurence = $this->getCurrentAssessmentItemRefOccurence();
        $session = $this->getItemSession($item, $occurence);
        
        try {
            // Might throw an AssessmentItemSessionException.
            $session->skip();
             
            if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
                // Store the responses for a later processing at the end of the test part.
                $pendingResponses = new PendingResponses($session->getResponseVariables(false), $item, $occurence);
                $this->addPendingResponses($pendingResponses);
            }
            else {
                $this->submitItemResults($session, $occurence);
                $this->outcomeProcessing();
            }
        }
        catch (Exception $e) {
            throw $this->transformException($e);
        }
	}
	
	/**
	 * Begin an attempt for the current item in the route.
	 * 
	 * @throws AssessmentTestSessionException If the time limits at the test level (testPart, assessmentSection) are already exceeded or the session is closed or time limits are not respected.
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function beginAttempt() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot begin an attempt for the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    // Are the time limits in force (at the test level) respected?
        $this->checkTimeLimits();
         
        // Time limits are OK! Let's try to begin the attempt.
        $routeItem = $this->getCurrentRouteItem();
        $session = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
        
        try {
            $session->beginAttempt();
        }
        catch (Exception $e) {
            throw $this->transformException($e);
        }
	}
	
	/**
	 * End an attempt for the current item in the route. If the current navigation mode
	 * is LINEAR, the TestSession moves automatically to the next step in the route or
	 * the end of the session if the responded item is the last one.
	 * 
	 * @param State $responses The responses for the curent item in the sequence.
	 * @param boolean $allowLateSubmission If set to true, maximum time limits will not be taken into account.
	 * @throws AssessmentTestSessionException
	 * @throws AssessmentItemSessionException
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function endAttempt(State $responses, $allowLateSubmission = false) {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot end an attempt for the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $routeItem = $this->getCurrentRouteItem();
	    $currentItem = $routeItem->getAssessmentItemRef();
	    $currentOccurence = $routeItem->getOccurence();
	    $session = $this->getItemSession($currentItem, $currentOccurence);
	    $session->updateDuration();
	    
	    // -- Are time limits in force respected?
	    if ($allowLateSubmission === false) {
	        $this->checkTimeLimits(true);
	    }

	    // -- Time limits in force respected, try to end the item attempt.
        if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
        
            // Store the responses for a later processing.
            $this->addPendingResponses(new PendingResponses($responses, $currentItem, $currentOccurence));
            
            try {
                $session->endAttempt($responses, false, $allowLateSubmission);
            }
            catch (Exception $e) {
                throw $this->transformException($e);
            }
        }
        else {
            try {
                $session->endAttempt($responses, true, $allowLateSubmission);
            }
            catch (Exception $e) {
                throw $this->transformException($e);
            }
            
            // Update the lastly updated item occurence.
            $this->notifyLastOccurenceUpdate($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
        
            // Item Results submission.
            try {
                $this->submitItemResults($this->getAssessmentItemSessionStore()->getAssessmentItemSession($currentItem, $currentOccurence), $currentOccurence);
            }
            catch (AssessmentTestSessionException $e) {
                $msg = "An error occurred while transmitting item results to the appropriate data source at deferred responses processing time.";
                throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::RESULT_SUBMISSION_ERROR, $e);
            }
            
            // Outcome processing.
            $this->outcomeProcessing();
        }
	}
	
	/**
	 * Perform a 'jump' to a given position in the Route sequence. The current navigation
	 * mode must be LINEAR to be able to jump.
	 * 
	 * @param integer $position The position in the route the jump has to be made.
	 * @param boolean $allowTimeout Whether or not it is allowed to jump if the timeLimits in force of the jump target are not respected.
	 * @throws AssessmentTestSessionException If $position is out of the Route bounds or the jump is not allowed because of time constraints.
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function jumpTo($position, $allowTimeout = false) {
	    
	    // Can we jump?
	    if ($this->getCurrentNavigationMode() !== NavigationMode::NONLINEAR) {
	        $msg = "Jumps are not allowed in LINEAR navigation mode.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP);
	    }
	    
	    $this->suspendItemSession();
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    
	    try {
            $route->setPosition($position);
            $this->selectEligibleItems();
            
            // Check the time limits after the jump is trully performed.
            if ($allowTimeout === false) {
                $this->checkTimeLimits(false, true);
            }
            
            // No exception thrown, interact!
            $this->interactWithItemSession();
	    }
	    catch (AssessmentTestSessionException $e) {
	        // Rollback to previous position.
	        $route->setPosition($oldPosition);
	        throw $e;
	    }
	    catch (OutOfBoundsException $e) {
	        $msg = "Position '${position}' is out of the Route bounds.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP, $e);
	    }
	}
    
    /**
     * Suspend the current test session if it is running.
     */
    public function suspend() {
        if ($this->isRunning() === true) {
            $this->suspendItemSession();
            $this->setState(AssessmentTestSessionState::SUSPENDED);
        }
    }
    
    /**
     * Resume the current test session if it is suspended.
     */
    public function resume() {
        if ($this->getState() === AssessmentTestSessionState::SUSPENDED) {
            $this->interactWithItemSession();
            $this->setState(AssessmentTestSessionState::INTERACTING);
        }
    }
	
	/**
	 * AssessmentTestSession implementations must override this method in order
	 * to submit item results from a given $assessmentItemSession to the appropriate
	 * data source.
	 * 
	 * This method is triggered each time response processing takes place.
	 * 
	 * @param AssessmentItemSession $assessmentItemSession The lastly updated AssessmentItemSession.
	 * @param integer $occurence The occurence number of the item bound to $assessmentItemSession.
	 * @throws AssessmentTestSessionException With error code RESULT_SUBMISSION_ERROR if an error occurs while transmitting results.
	 */
	protected function submitItemResults(AssessmentItemSession $assessmentItemSession, $occurence = 0) {
	    return;
	}
	
	/**
	 * AssessmentTestSession implementations must override this method in order to submit test results
	 * from the current AssessmentTestSession to the appropriate data source.
	 * 
	 * This method is triggered once at the end of the AssessmentTestSession. 
	 * 
	 * * @throws AssessmentTestSessionException With error code RESULT_SUBMISSION_ERROR if an error occurs while transmitting results.
	 */
	protected function submitTestResults() {
	    return;
	}
	
	/**
	 * Apply the response processing on pending responses due to
	 * the simultaneous submission mode in force.
	 * 
	 * @return PendingResponsesCollection The collection of PendingResponses objects that were processed.
	 * @throws AssessmentTestSessionException If an error occurs while processing the pending responses or sending results.
	 */
	protected function defferedResponseProcessing() {
	    $itemSessionStore = $this->getAssessmentItemSessionStore();
	    $pendingResponses = $this->getPendingResponses();
	    $pendingResponsesProcessed = 0;
	    
	    foreach ($pendingResponses as $pendingResponses) {
	        
	        $item = $pendingResponses->getAssessmentItemRef();
	        $occurence = $pendingResponses->getOccurence();
	        $itemSession = $itemSessionStore->getAssessmentItemSession($item, $occurence);
	        $responseProcessing = $item->getResponseProcessing();
	        
	        // If the item has a processable response processing...
	        if (is_null($responseProcessing) === false && ($responseProcessing->hasTemplate() === true || $responseProcessing->hasTemplateLocation() === true || count($responseProcessing->getResponseRules()) > 0)) {
	            try {
	                $engine = $this->createResponseProcessingEngine($responseProcessing, $itemSession);
	                $engine->process();
	                $pendingResponsesProcessed++;
	                $this->submitItemResults($itemSession, $occurence);
	            }
	            catch (ProcessingException $e) {
	                $msg = "An error occured during postponed response processing.";
	                throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::RESPONSE_PROCESSING_ERROR, $e);
	            }
	            catch (AssessmentTestSessionException $e) {
	                // An error occured while transmitting the results.
	                $msg = "An error occured while transmitting item results to the appropriate data source.";
	                throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::RESULT_SUBMISSION_ERROR, $e);
	            }
	        }
	    }
	    
	    $result = $pendingResponses;
	    
	    // Reset the pending responses, they are now processed.
	    $this->setPendingResponseStore(new PendingResponseStore());
	    
	    // OutcomeProcessing can now take place (only makes sense if pending response
	    // processing were performed.
	    if ($pendingResponsesProcessed > 0) {
	        $this->outcomeProcessing();
	    }
	    
	    return $result;
	}
	
	/**
	 * This protected method contains the logic of creating a new ResponseProcessingEngine.
	 * 
	 * @param ResponseProcessing $responseProcessing
	 * @param AssessmentItemSession $assessmentItemSession
	 * @return ResponseProcessingEngine
	 */
	protected function createResponseProcessingEngine(ResponseProcessing $responseProcessing, AssessmentItemSession $assessmentItemSession) {
	    return new ResponseProcessingEngine($responseProcessing, $assessmentItemSession);
	}
	
	/**
	 * Ask the test session to move to next RouteItem in the Route sequence.
	 * 
	 * If $allowTimeout is set to true, the very next RouteItem in the Route sequence will bet set
	 * as the current RouteItem, whether or not it is timed out or not.
	 * 
	 * On the other hand, if $allowTimeout is set to false, the next RouteItem in the Route sequence
	 * which is not timed out will be set as the current RouteItem. If there is no more following RouteItems
	 * that are not timed out in the Route sequence, the test session ends gracefully.
	 * 
	 * @param boolean $allowTimeout If set to true, the next RouteItem in the Route sequence does not have to respect the timeLimits in force. Default value is false.
	 * @throws AssessmentTestSessionException If the test session is not running or an issue occurs during the transition (e.g. branching, preConditions, ...).
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function moveNext($allowTimeout = false) {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next item while the test session state is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $this->suspendItemSession();
	    $this->nextRouteItem();
	    
	    while ($this->isRunning() === true) {
	        
	        if ($allowTimeout === false) {
	            try {
	                // Do not check min times, include item timings.
	                $this->checkTimeLimits(false, true, false);
	                
	                // No exception thrown, we found a non timed-out item to be
	                // presented to the candidate.
	                $this->interactWithItemSession();
	                return;
	            }
	            catch (AssessmentTestSessionException $e) {
	                // The item is timed out, let's try the next route item.
	                $this->nextRouteItem();
	            }
	        }
	        else {
	            // $allowTimeout === true, only 1 iteration
	            // is necessary, the following item is presented
	            // whatever happens.
	            $this->interactWithItemSession();
	            return;
	        }
	    }
	    
	    // If the execution comes here, this is the end of the test...
	}
	
	/**
	 * Ask the test session to move to the previous RouteItem in the Route sequence.
	 * 
	 * If $allowTimeout is set to true, the previous RouteItem in the Route sequence will bet set
	 * as the current RouteItem, whether or not it is timed out.
	 * 
	 * On the other hand, if $allowTimeout is set to false, the previous RouteItem in the Route sequence
	 * which is not timed out will be set as the current RouteItem. If there is no more previous RouteItems
	 * that are not timed out in the Route sequence, the current RouteItem remains the same and an
	 * AssessmentTestSessionException with the appropriate timing error code is thrown.
	 * 
	 * @param boolean $allowTimeout If set to true, the next RouteItem in the sequence does not have to respect timeLimits in force. Default value is false.
	 * @throws AssessmentTestSessionException If the test session is not running or an issue occurs during the transition (e.g. branching, preConditions, ...) or if $allowTimeout = false and there absolutely no possibility to move backward (even the first RouteItem is timed out).
	 * @qtism-test-interaction
	 * @qtism-test-duration-update
	 */
	public function moveBack($allowTimeout = false) {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the previous item while the test session state is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $this->suspendItemSession();
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    $this->previousRouteItem();
	    
	    while ($route->isFirst() === false) {
	        
	        if ($allowTimeout === false) {
	            try {
	                // Do not check min times, include item timings.
	                $this->checkTimeLimits(false, true, false);
	                
	                // No exception thrown, return.
	                $this->interactWithItemSession();
	                return;
	            }
	            catch (AssessmentTestSessionException $e) {
	                // The item is timed out, let's try the previous route item.
	                $this->previousRouteItem();
	            }
	        }
	        else {
	            $this->interactWithItemSession();
	            return;
	        }
	    }
	    
	    // If we are here, it means we got moved back to the very first RouteItem.
	    // Maybe we are not allowed to be here because of the hypothetic timeout
	    // of the first RouteItem of the test...
	    if ($allowTimeout === false) {
	        try {
	            $this->checkTimeLimits(false, true, false);
	            $this->interactWithItemSession();
	        }
	        catch (AssessmentTestSessionException $e) {
	            $route->setPosition($oldPosition);
	            $msg = "Cannot move backward. All the previous items in the route sequence are timed out.";
	            throw new AssessmentTestSessionException($msg, $e->getCode(), $e);
	        }
	    }
	}
	
	/**
	 * Move to the next item in the route. 
	 * 
	 * * If there is no more item in the route to be explored the session ends gracefully.
	 * * If there the end of a test part is reached, pending responses are submitted. 
	 * 
	 * @param boolean $ignoreBranchings Whether or not to ignore branching.
	 * @param boolean $ignorePreConditions Whether or not to ignore preConditions.
	 * @throws AssessmentTestSessionException If the test session is not running or something wrong happens during deffered outcome processing or branching.
	 */
	protected function nextRouteItem($ignoreBranchings = false, $ignorePreConditions = false) {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next position while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    // If the submitted responses are the one of the last
	    // item of the test part, apply deffered response processing.
	    if ($this->getRoute()->isLastOfTestPart() === true && $this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {

            // The testPart is complete so deffered response processing must take place.
            $this->defferedResponseProcessing();
	    }
	    
	    $route = $this->getRoute();
	    $stop = false;
	    
	    while ($route->valid() === true && $stop === false) {
	        
	        // Branchings?
	        if ($ignoreBranchings === false && $this->getCurrentNavigationMode() === NavigationMode::LINEAR && count($route->current()->getBranchRules()) > 0) {
	            
	            $branchRules = $route->current()->getBranchRules();
	            for ($i = 0; $i < count($branchRules); $i++) {
	                $engine = new ExpressionEngine($branchRules[$i]->getExpression(), $this);
	                $condition = $engine->process();
	                if ($condition !== null && $condition->getValue() === true) {
	                    
	                    $target = $branchRules[$i]->getTarget();
	                    
	                    if ($target === 'EXIT_TEST') {
	                        $this->endTestSession();
	                    }
	                    else if ($target === 'EXIT_TESTPART') {
                            $route->next();
	                        $this->moveNextTestPart();
	                    }
	                    else if ($target === 'EXIT_SECTION') {
                            $route->next();
	                        $this->moveNextAssessmentSection();
	                    }
	                    else {
	                        $route->branch($branchRules[$i]->getTarget());
	                    }
	                    
	                    break;
	                }
	            }
	            
	            if ($i >= count($branchRules)) {
	                // No branch rule returned true. Simple move next.
	                $route->next();
	            }
	        }
	        else {
	            $route->next();
	        }
	        
	        // Preconditions on target?
	        if ($ignorePreConditions === false && $route->valid() === true) {
	            $preConditions = $route->current()->getPreConditions();
	            
	            if (count($preConditions) > 0) {
	                for ($i = 0; $i < count($preConditions); $i++) {
	                    $engine = new ExpressionEngine($preConditions[$i]->getExpression(), $this);
	                    $condition = $engine->process();
	                    
	                    if ($condition !== null && $condition->getValue() === true) {
	                        // The item must be presented.
	                        $stop = true;
	                        break;
	                    }
	                }    
	            }
	            else {
	                $stop = true;
	            }
	        }
	    }
	    
	    $this->selectEligibleItems();
	    
	    if ($route->valid() === false && $this->isRunning() === true) {
	        $this->endTestSession();
	    }
	}
	
	/**
	 * Set the position in the Route at the very next TestPart in the Route sequence or, if the current
	 * testPart is the last one of the test session, the test session ends gracefully. If the submission mode
	 * is simultaneous, the pending responses are processed.
	 * 
	 * @throws AssessmentTestSessionException If the test is currently not running.
	 */
	public function moveNextTestPart() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next testPart while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $route = $this->getRoute();
	    $from = $route->current();

        while ($route->valid() === true && $route->current()->getTestPart() === $from->getTestPart()) {
            $this->nextRouteItem();
        }

        if ($this->isRunning() === true) {
            $this->interactWithItemSession();
        }
	}

	/**
	 * Set the position in the Route at the very next assessmentSection in the route sequence.
	 *
	 * * If there is no assessmentSection left in the flow, the test session ends gracefully.
	 * * If there are still pending responses, they are processed.
	 *
	 * @throws AssessmentTestSessionException If the test is not running.
	 */
	public function moveNextAssessmentSection() {

	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next assessmentSection while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }

	    $route = $this->getRoute();
	    $from = $route->current();

        while ($route->valid() === true && $route->current()->getAssessmentSection() === $from->getAssessmentSection()) {
            $this->nextRouteItem();
        }

        if ($this->isRunning() === true) {
            $this->interactWithItemSession();
        }
	}
	
	/**
	 * Set the position in the Route at the very next assessmentItem in the route sequence.
	 * 
	 * * If there is no item left in the flow, the test session ends gracefully.
	 * * If there are still pending responses, they are processed.
	 * 
	 * @throws AssessmentTestSessionException If the test is not running.
	 */
	public function moveNextAssessmentItem() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next testPart while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $this->nextRouteItem();
	    
	    if ($this->isRunning() === true) {
	        $this->interactWithItemSession();
	    }
	}
	
	/**
	 * Move to the previous item in the route.
	 * 
	 * @throws AssessmentTestSessionException If the test is not running or if trying to go to the previous route item in LINEAR navigation mode or if the current route item is the very first one in the route sequence.
	 */
	protected function previousRouteItem() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move backward in the route item sequence while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    else if ($this->getCurrentNavigationMode() === NavigationMode::LINEAR) {
	        $msg = "Cannot move backward in the route item sequence while the LINEAR navigation mode is in force.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::NAVIGATION_MODE_VIOLATION);
	    }
	    else if ($this->getRoute()->getPosition() === 0) {
	         $msg = "Cannot move backward in the route item sequence while the current position is the very first one of the AssessmentTestSession.";
	         throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::LOGIC_ERROR);   
	    }
	    
	    $this->getRoute()->previous();
	    $this->selectEligibleItems();
	}
	
	/**
	 * Apply outcome processing at test-level.
	 * 
	 * @throws AssessmentTestSessionException If an error occurs at OutcomeProcessing time or at result submission time.
	 */
	protected function outcomeProcessing() {
	    
	    if ($this->getAssessmentTest()->hasOutcomeProcessing() === true) {
	        // As per QTI Spec:
	        // The values of the test's outcome variables are always reset to their defaults prior
	        // to carrying out the instructions described by the outcomeRules.
	        $this->resetOutcomeVariables(true, $this->getPreservedOutcomeVariables());
	         
	        $outcomeProcessing = $this->getAssessmentTest()->getOutcomeProcessing();
	        
	        try {
	            $outcomeProcessingEngine = new OutcomeProcessingEngine($outcomeProcessing, $this);
	            $outcomeProcessingEngine->process();
	            
	            if ($this->getTestResultsSubmission() === TestResultsSubmission::OUTCOME_PROCESSING) {
	                $this->submitTestResults();
	            }
	        }
	        catch (ProcessingException $e) {
	            $msg = "An error occured while processing OutcomeProcessing.";
	            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::OUTCOME_PROCESSING_ERROR, $e);
	        }
	    }
	}
	
	/**
	 * Whether the test session is running. In other words, if the test session is not in
	 * state INITIAL nor CLOSED.
	 * 
	 * @return boolean Whether the test session is running.
	 */
	public function isRunning() {
	    return $this->getState() !== AssessmentTestSessionState::INITIAL && $this->getState() !== AssessmentTestSessionState::CLOSED;
	}
	
	/**
	 * End the test session.
	 * 
	 * @throws AssessmentTestSessionException If the test session is already CLOSED or is in INITIAL state.
	 * @qtism-test-interaction
	 * @qtism-test-update-duration
	 */
	public function endTestSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot end the test session while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    // If there are still pending responses to be sent, apply a deffered response processing + outcomeProcessing.
	    $this->defferedResponseProcessing();
	    
	    if ($this->getTestResultsSubmission() === TestResultsSubmission::END) {
	        $this->submitTestResults();
	    }
	    
	    $this->setState(AssessmentTestSessionState::CLOSED);
	}
	
	/**
	 * Get the item sessions held by the test session by item reference $identifier.
	 * 
	 * @param string $identifier An item reference $identifier e.g. Q04. Prefixed or sequenced identifiers e.g. Q04.1.X are considered to be malformed.
	 * @return AssessmentItemSessionCollection|false A collection of AssessmentItemSession objects or false if no item session could be found for $identifier.
	 * @throws InvalidArgumentException If the given $identifier is malformed.
	 */
	public function getAssessmentItemSessions($identifier) {
	    try {
	        $v = new VariableIdentifier($identifier);
	        
	        if ($v->hasPrefix() === true || $v->hasSequenceNumber() === true) {
	            $msg = "'${identifier}' is not a valid item reference identifier.";
	            throw new InvalidArgumentException($msg, 0);
	        }
	        
	        $itemRefs = $this->getAssessmentItemRefs();
	        if (isset($itemRefs[$identifier]) === false) {
	            return false;
	        }
	        
	        try {
	            return $this->getAssessmentItemSessionStore()->getAssessmentItemSessions($itemRefs[$identifier]);
	        }
	        catch (OutOfBoundsException $e) {
	            return false;
	        }
	    }
	    catch (InvalidArgumentException $e) {
	        $msg = "'${identifier}' is not a valid item reference identifier.";
	        throw new InvalidArgumentException($msg, 0, $e);
	    }
	}
	
	/**
	 * Get a subset of AssessmentItemRef objects involved in the test session.
	 * 
	 * @param string $sectionIdentifier An optional section identifier.
	 * @param IdentifierCollection $includeCategories The optional item categories to be included in the subset.
	 * @param IdentifierCollection $excludeCategories The optional item categories to be excluded from the subset.
	 * @return AssessmentItemRefCollection A collection of AssessmentItemRef objects that match all the given criteria.
	 */
    public function getItemSubset($sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
        return $this->getRoute()->getAssessmentItemRefsSubset($sectionIdentifier, $includeCategories, $excludeCategories);
    }
	
    /**
     * Get the number of items in the current Route. In other words, the total number
     * of item occurences the candidate can take during the test.
     * 
     * The $mode parameter can take two values:
     * 
     * * AssessmentTestSession::ROUTECOUNT_ALL: consider all item occurences of the test
     * * AssessmentTestSession::ROUTECOUNT_EXCLUDENORESPONSE: consider only item occurences containing at least one response declaration.
     *
     * @param integer $mode AssessmentTestSession::ROUTECOUNT_ALL | AssessmentTestSession::ROUTECOUNT_EXCLUDENORESPONSE
     * @return integer
     */
    public function getRouteCount($mode = self::ROUTECOUNT_ALL)
    {
        if ($mode === self::ROUTECOUNT_ALL) {
            
            return $this->getRoute()->count();
        } else {
            $i = 0;
            
            foreach ($this->getRoute()->getAssessmentItemRefs() as $assessmentItemRef) {
                if (count($assessmentItemRef->getResponseDeclarations()) > 0) {
                    $i++;
                }
            }
            
            return $i;
        }
    }
	
	/**
	 * Get the map of last occurence updates.
	 * 
	 * @return SplObjectStorage A map.
	 */
	protected function getLastOccurenceUpdate() {
		return $this->lastOccurenceUpdate;
	}
	
	/**
	 * Set the map of last occurence updates.
	 * 
	 * @param SplObjectStorage $lastOccurenceUpdate A map.
	 */
	public function setLastOccurenceUpdate(SplObjectStorage $lastOccurenceUpdate) {
		$this->lastOccurenceUpdate = $lastOccurenceUpdate;
	}
	
	/**
	 * Whether a given item occurence is the last updated.
	 * 
	 * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
	 * @param integer $occurence An occurence number
	 * @return boolean
	 */
	public function isLastOccurenceUpdate(AssessmentItemRef $assessmentItemRef, $occurence) {
	    if (($lastUpdate = $this->whichLastOccurenceUpdate($assessmentItemRef)) !== false) {
	        if ($occurence === $lastUpdate) {
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * Notify which $occurence of $assessmentItemRef was the last updated.
	 * 
	 * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
	 * @param integer $occurence An occurence number for $assessmentItemRef.
	 */
	protected function notifyLastOccurenceUpdate(AssessmentItemRef $assessmentItemRef, $occurence) {
		$lastOccurenceUpdate = $this->getLastOccurenceUpdate();
		$lastOccurenceUpdate[$assessmentItemRef] = $occurence;
	}
	
	/**
	 * Returns which occurence of item was lastly updated.
	 * 
	 * @param AssessmentItemRef|string $assessmentItemRef An AssessmentItemRef object.
	 * @return int|false The occurence number of the lastly updated item session for the given $assessmentItemRef or false if no occurence was updated yet.
	 */
	public function whichLastOccurenceUpdate($assessmentItemRef) {
		if (gettype($assessmentItemRef) === 'string') {
			$assessmentItemRefs = $this->getAssessmentItemRefs();
			if (isset($assessmentItemRefs[$assessmentItemRef]) === true) {
				$assessmentItemRef = $assessmentItemRefs[$assessmentItemRef];
			}
		}
		else if (!$assessmentItemRef instanceof AssessmentItemRef) {
			$msg = "The 'assessmentItemRef' argument must be a string or an AssessmentItemRef object.";
			throw new InvalidArgumentException($msg);
		}
		
		$lastOccurenceUpdate = $this->getLastOccurenceUpdate();
		if (isset($lastOccurenceUpdate[$assessmentItemRef]) === true) {
			return $lastOccurenceUpdate[$assessmentItemRef];
		}
		else {
			return false;
		}
	}
	
	/**
	 * Whether the candidate is authorized to move backward depending on the current context
	 * of the test session.
	 * 
	 * * If the current navigation mode is LINEAR, false is returned.
	 * * Otherwise, it depends on the position in the Route. If the candidate is at first position in the route, false is returned.
	 * 
	 * @return boolean
	 */
	public function canMoveBackward() {
	    
	    if ($this->getRoute()->getPosition() === 0) {
	        return false;
	    }
	    else {
	        // We are sure there is a previous route item.
	        $previousRouteItem = $this->getPreviousRouteItem();
	        if ($previousRouteItem->getTestPart()->getNavigationMode() === NavigationMode::LINEAR) {
	            return false;
	        }
	        else if ($this->getCurrentNavigationMode() === NavigationMode::NONLINEAR) {
	            return true;
	        }
	        else {
	            return false;
	        }
	    }
	}
	
	/**
	 * Get the Jump description objects describing to which RouteItem the candidate
	 * is able to "jump" to when the NONLINEAR navigation mode is in force.
	 * 
	 * If the LINEAR navigation mode is in force, an empty JumpCollection is returned.
	 * 
	 * @param integer $place A value from the the AssessmentTestPlace enumeration determining the scope of possible jumps to be gathered.
	 * @return JumpCollection A collection of Jump objects.
	 */
	public function getPossibleJumps($place = AssessmentTestPlace::ASSESSMENT_TEST, $identifier = '') {
	    $jumps = new JumpCollection();
	    
	    if ($this->isRunning() === false || $this->getCurrentNavigationMode() === NavigationMode::LINEAR) {
	        // No possible jumps.
	        return $jumps;
	    }
	    else {
	        $route = $this->getRoute();
	        
	        switch ($place) {
	            case AssessmentTestPlace::ASSESSMENT_TEST:
	                $jumpables = $route->getAllRouteItems();
	            break;
	            
	            case AssessmentTestPlace::TEST_PART:
	                $jumpables = $route->getRouteItemsByTestPart((empty($identifier) === true) ? $this->getCurrentTestPart() : $identifier);
	            break;
	            
	            case AssessmentTestPlace::ASSESSMENT_SECTION:
	                $jumpables = $route->getRouteItemsByAssessmentSection((empty($identifier) === true) ? $this->getCurrentAssessmentSection() : $identifier);
	            break;
	            
	            case AssessmentTestPlace::ASSESSMENT_ITEM:
	                $jumpables = $this->getRouteItemsByAssessmentItemRef((empty($identifier) === true) ? $this->getCurrentAssessmentItemRef() : $identifier);
	            break;
	        }
	        
	        $offset = $this->getRoute()->getRouteItemPosition($jumpables[0]);
	        
	        // Scan the route for "jumpable" items.
	        foreach ($jumpables as $routeItem) {
	            $itemRef = $routeItem->getAssessmentItemRef();
	            $occurence = $routeItem->getOccurence();
	            
	            // get the session related to this route item.
	            $store = $this->getAssessmentItemSessionStore();
	            $itemSession = $store->getAssessmentItemSession($itemRef, $occurence);
	            $jumps[] = new Jump($offset, $routeItem, $itemSession);
	            $offset++;
	        }
	        
	        return $jumps;
	    }
	}
	
	/**
	 * Whether the current TestPart is complete. In other words, if all the items of the current TestPart were 'responded'.
	 * 
	 * @throws AssessmentTestSessionException If the current navigation mode is not SIMULTANEOUS. The error code will be LOGIC_ERROR.
	 */
	public function isCurrentTestPartComplete() {
	    if ($this->getCurrentSubmissionMode() !== SubmissionMode::SIMULTANEOUS) {
	        $msg = "It makes no sense to check if the current TestPart is complete when the current navigation mode is not SIMULTANEOUS.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::LOGIC_ERROR);
	    }
	    
	    return count($this->getRoute()->getCurrentTestPartRouteItems()) === count($this->getPendingResponses());
	}
	
	/**
	 * Get the acceptable latency time for the AssessmentTestSession.
	 * 
	 * @return Duration A Duration object.
	 */
	public function getAcceptableLatency() {
	    return $this->getSessionManager()->getAcceptableLatency();
	}
	
	/**
	 * Get the time constraints running for the current testPart or/and current assessmentSection
	 * or/and assessmentItem.
	 * 
	 * @param integer $places A composition of values (use | operator) from the AssessmentTestPlace enumeration. If the null value is given, all places will be taken into account.
	 * @return TimeConstraintCollection A collection of TimeConstraint objects.
	 * @qtism-test-duration-update
	 */
	public function getTimeConstraints($places = null) {
	    	    
	    if ($places === null) {
	        // Get the constraints from all places in the Assessment Test.
	        $places = (AssessmentTestPlace::ASSESSMENT_TEST | AssessmentTestPlace::TEST_PART | AssessmentTestPlace::ASSESSMENT_SECTION | AssessmentTestPlace::ASSESSMENT_ITEM);
	    }
	    
	    $route = $this->getRoute();
	    $navigationMode = $this->getCurrentNavigationMode();
	    $routeItem = $this->getCurrentRouteItem();
	    $durationStore = $this->getDurationStore();
	    $considerMinTime = $this->mustConsiderMinTime();
	    
	    if ($places & AssessmentTestPlace::ASSESSMENT_TEST) {
	        $source = $routeItem->getAssessmentTest();
	        $duration = $durationStore[$source->getIdentifier()];
	        $constraints[] = new TimeConstraint($source, $duration, $navigationMode, $considerMinTime);
	    }
	    
	    if ($places & AssessmentTestPlace::TEST_PART) {
	        $source = $this->getCurrentTestPart();
	        $duration = $durationStore[$source->getIdentifier()];
	        $constraints[] = new TimeConstraint($source, $duration, $navigationMode, $considerMinTime);
	    }
	    
	    if ($places & AssessmentTestPlace::ASSESSMENT_SECTION) {
	        $source = $this->getCurrentAssessmentSection();
	        $duration = $durationStore[$source->getIdentifier()];
	        $constraints[] = new TimeConstraint($source, $duration, $navigationMode, $considerMinTime);
	    }
	    
	    if ($places & AssessmentTestPlace::ASSESSMENT_ITEM) {
	        $source = $routeItem->getAssessmentItemRef();
	        $session = $this->getCurrentAssessmentItemSession();
	        $duration = $session['duration'];
	        $constraints[] = new TimeConstraint($source, $duration, $navigationMode, $considerMinTime);
	    }
	    
	    return $constraints;
	}
	
	/**
	 * Checks if the timeLimits in force, at the testPart/assessmentSection/assessmentItem level, are respected.
	 * If this is not the case, an AssessmentTestSessionException will be raised with the appropriate error code.
	 * 
	 * In case of error, the error code shipped with the AssessmentTestSessionException might be:
	 * 
	 * * AssessmentTestSession::ASSESSMENT_TEST_DURATION_OVERFLOW
	 * * AssessmentTestSession::ASSESSMENT_TEST_DURATION_UNDERFLOW
	 * * AssessmentTestSession::TEST_PART_DURATION_OVERFLOW
	 * * AssessmentTestSession::TEST_PART_DURATION_UNDERFLOW
	 * * AssessmentTestSession::ASSESSMENT_SECTION_DURATION_OVERFLOW
	 * * AssessmentTestSession::ASSESSMENT_SECTION_DURATION_UNDERFLOW
	 * * AssessmentTestSession::ASSESSMENT_ITEM_DURATION_OVERFLOW
	 * * AssessmentTestSession::ASSESSMENT_ITEM_DURATION_UNDERFLOW
	 * 
	 * @param boolean $includeMinTime Whether or not to check minimum times. If this argument is true, minimum times on assessmentSections and assessmentItems will be checked only if the current navigation mode is LINEAR.
	 * @param boolean $includeAssessmentItem If set to true, the time constraints in force at the item level will also be checked.
	 * @param boolean $acceptableLatency Whether or not consider the acceptable latency.
	 * @throws AssessmentTestSessionException If one or more time limits in force are not respected.
	 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10535 IMS QTI about TimeLimits.
	 */
	public function checkTimeLimits($includeMinTime = false, $includeAssessmentItem = false, $acceptableLatency = true) {
	    
	    $this->updateDuration();
	    
	    $places = AssessmentTestPlace::TEST_PART | AssessmentTestPlace::ASSESSMENT_TEST | AssessmentTestPlace::ASSESSMENT_SECTION;
	    // Include assessmentItem only if formally asked by client-code.
	    if ($includeAssessmentItem === true) {
	        $places = $places | AssessmentTestPlace::ASSESSMENT_ITEM;
	    }
	    
	    $constraints = $this->getTimeConstraints($places);
	    foreach ($constraints as $constraint) {

	        $maxTimeRespected = true;
	        $minTimeRespected = true;
	        $includeMinTime = $includeMinTime && $constraint->minTimeInForce();
	        $includeMaxTime = $constraint->maxTimeInForce() && $constraint->allowLateSubmission() === false;
	        $spentTime = $constraint->getDuration();
	        
	        if ($includeMinTime === true) {
	            $minRemainingTime = $constraint->getMinimumRemainingTime();

	            if ($acceptableLatency === true) {
	                $minRemainingTime->add($this->getAcceptableLatency());
	            }
	        }
	        
	        if ($includeMaxTime === true) {
	            $maxRemainingTime = $constraint->getMaximumRemainingTime();

	            if ($acceptableLatency === true) {
	                $maxRemainingTime->add($this->getAcceptableLatency());
	            }
	        }
	        
	        $minTimeRespected = !($includeMinTime === true && $minRemainingTime->round()->getSeconds(true) > 0);
	        $maxTimeRespected = !($includeMaxTime === true && $maxRemainingTime->round()->getSeconds(true) === 0);
	        
	        if ($minTimeRespected === false || $maxTimeRespected === false) {
	            
	            $sourceNature = ucfirst($constraint->getSource()->getQtiClassName());
	            $identifier = $constraint->getSource()->getIdentifier();
	            $source = $constraint->getSource();
	            
	            if ($minTimeRespected === false) {
	                $msg = "Minimum duration of ${sourceNature} '${identifier}' not reached.";
	                
	                if ($source instanceof AssessmentTest) {
	                    $code = AssessmentTestSessionException::ASSESSMENT_TEST_DURATION_UNDERFLOW;
	                }
	                else if ($source instanceof TestPart) {
	                    $code = AssessmentTestSessionException::TEST_PART_DURATION_UNDERFLOW;
	                }
	                else if ($source instanceof AssessmentSection) {
	                    $code = AssessmentTestSessionException::ASSESSMENT_SECTION_DURATION_UNDERFLOW;
	                }
	                else {
	                    $code = AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_UNDERFLOW;
	                }
	                
	                throw new AssessmentTestSessionException($msg, $code);
	            }
	            else if ($maxTimeRespected === false) {
	                $msg = "Maximum duration of ${sourceNature} '${identifier}' not respected.";
	                
	                if ($source instanceof AssessmentTest) {
	                    $code = AssessmentTestSessionException::ASSESSMENT_TEST_DURATION_OVERFLOW;
	                }
	                else if ($source instanceof TestPart) {
	                    $code = AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW;
	                }
	                else if ($source instanceof AssessmentSection) {
	                    $code = AssessmentTestSessionException::ASSESSMENT_SECTION_DURATION_OVERFLOW;
	                }
	                else {
	                    $code = AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_OVERFLOW;
	                }
	                
	                throw new AssessmentTestSessionException($msg, $code);
	            }
	        }
	    }
	}
	
	/**
	 * Get the current AssessmentItemSession object.
	 * 
	 * @return AssessmentItemSession|false The current AssessmentItemSession object or false if no assessmentItemSession is running.
	 */
	public function getCurrentAssessmentItemSession() {
	    
	    $session = false;
	    
	    if ($this->isRunning() === true) {
	        
	        $itemRef = $this->getCurrentAssessmentItemRef();
	        $occurence = $this->getCurrentAssessmentItemRefOccurence();
	        
	        $session = $this->getAssessmentItemSessionStore()->getAssessmentItemSession($itemRef, $occurence);
	    }
	    
	    return $session;
	}
	
	/**
	 * Update the durations involved in the AssessmentTestSession to mirror the durations at the current time.
	 * This method can be useful for stateless systems that make use of QtiSm.
	 */
	public function updateDuration() {
	    
	    if (($itemSession = $this->getCurrentAssessmentItemSession()) !== false) {
	        $itemSession->updateDuration();
	    }
	}
	
	/**
	 * Put the current item session in SUSPENDED state.
	 * 
	 * @throws AssessmentItemSessionException With code STATE_VIOLATION if the current item session cannot switch to the SUSPENDED state.
	 * @throws AssessmentTestSessionException With code STATE_VIOLATION if the test session is not running.
	 * @throws UnexpectedValueException If the current item session cannot be retrieved.
	 */
	protected function suspendItemSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot suspend the item session if the test session is not running.";
	        $code = AssessmentTestSessionException::STATE_VIOLATION;
	        throw new AssessmentTestSessionException($msg, $code);
	    }
	    else if (($itemSession = $this->getCurrentAssessmentItemSession()) !== false) {
	        if ($itemSession->getState() === AssessmentItemSessionState::INTERACTING) {
	            $itemSession->suspend();
	        }
	    }
	    else {
	        $msg = "Cannot retrieve the current item session.";
	        throw new UnexpectedValueException($msg);
	    }
	}
	
	/**
	 * Put the current item session in INTERACTING mode.
	 * 
	 * @throws AssessmentItemSessionException With code STATE_VIOLATION if the current item session cannot switch to the INTERACTING state.
	 * @throws AssessmentTestSessionException With code STATE_VIOLATION if the test session is not running.
	 * @throws UnexpectedValueException If the current item session cannot be retrieved.
	 */
	protected function interactWithItemSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot set the item session in interacting state if test session is not running.";
	        $code = AssessmentTestSessionException::STATE_VIOLATION;
	        throw new AssessmentTestSessionException($msg, $code);
	    }
	    else if (($itemSession = $this->getCurrentAssessmentItemSession()) !== false) {
	        if ($itemSession->getState() === AssessmentItemSessionState::SUSPENDED && $itemSession->isAttempting()) {
	            $itemSession->interact();
	        }
	    }
	    else {
	        $msg = "Cannot retrieve the current item session.";
	        throw new UnexpectedValueException($msg);
	    }
	}
	
	/**
	 * Get the number of responded items.
	 * 
	 * @param string $identifier An optional assessmentSection identifier.
	 * @return integer
	 */
	public function numberResponded($identifier = '') {
	    $numberResponded = 0;

        foreach ($this->getItemSubset($identifier) as $itemRef) {
            $itemSessions = $this->getAssessmentItemSessions($itemRef->getIdentifier());
            
            if ($itemSessions !== false) {
                foreach ($itemSessions as $itemSession) {
                    if ($itemSession->isResponded() === true) {
                        $numberResponded++;
                    }
                }
            } 
        }
	    
	    return $numberResponded;
	}
	
	/**
	 * Get the number of correctly answered items.
	 *
	 * @param string $identifier An optional assessmentSection identifier.
	 * @return integer
	 */
	public function numberCorrect($identifier = '') {
	    $numberCorrect = 0;
	    
	    foreach ($this->getItemSubset($identifier) as $itemRef) {
	        $itemSessions = $this->getAssessmentItemSessions($itemRef->getIdentifier());
	        
	        if ($itemSessions !== false) {
	            foreach ($itemSessions as $itemSession) {
	                if ($itemSession->isCorrect() === true) {
	                    $numberCorrect++;
	                }
	            }
	        }
	    }
	    
	    return $numberCorrect;
	}
	
	/**
	 * Get the number of incorrectly answered items.
	 *
	 * @param string $identifier An optional assessmentSection identifier.
	 * @return integer
	 */
	public function numberIncorrect($identifier = '') {
	    $numberIncorrect = 0;
	     
	    foreach ($this->getItemSubset($identifier) as $itemRef) {
	        $itemSessions = $this->getAssessmentItemSessions($itemRef->getIdentifier());
	         
	        if ($itemSessions !== false) {
	            foreach ($itemSessions as $itemSession) {
	                if ($itemSession->isAttempted() === true && $itemSession->isCorrect() === false) {
	                    $numberIncorrect++;
	                }
	            }
	        }
	    }
	     
	    return $numberIncorrect;
	}
	
	/**
	 * Get the number of presented items.
	 *
	 * @param string $identifier An optional assessmentSection identifier.
	 * @return integer
	 */
	public function numberPresented($identifier = '') {
	    $numberPresented = 0;
	
	    foreach ($this->getItemSubset($identifier) as $itemRef) {
	        $itemSessions = $this->getAssessmentItemSessions($itemRef->getIdentifier());
	
	        if ($itemSessions !== false) {
	            foreach ($itemSessions as $itemSession) {
	                if ($itemSession->isPresented() === true) {
	                    $numberPresented++;
	                }
	            }
	        }
	    }
	
	    return $numberPresented;
	}
	
	/**
	 * Get the number of selected items.
	 *
	 * @param string $identifier An optional assessmentSection identifier.
	 * @return integer
	 */
	public function numberSelected($identifier = '') {
	    $numberSelected = 0;
	
	    foreach ($this->getItemSubset($identifier) as $itemRef) {
	        $itemSessions = $this->getAssessmentItemSessions($itemRef->getIdentifier());
	
	        if ($itemSessions !== false) {
	            foreach ($itemSessions as $itemSession) {
	                if ($itemSession->isSelected() === true) {
	                    $numberSelected++;
	                }
	            }
	        }
	    }
	
	    return $numberSelected;
	}
	
	/**
	 * Obtain the number of items considered to be completed during the AssessmentTestSession.
	 *
	 * An item involved in a candidate test session is considered complete if:
	 *
	 * * The navigation mode in force for the item is non-linear, and its completion status is 'complete'.
	 * * The navigation mode in force for the item is linear, and it was presented at least one time.
	 *
	 * @return integer The number of completed items.
	 */
	public function numberCompleted() {
	
	    $numberCompleted = 0;
	    $route = $this->getRoute();
	    $oldPosition = $route->getPosition();
	    
	    foreach ($this->getRoute() as $routeItem) {
	
	        $itemSession = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	
	        if ($routeItem->getTestPart()->getNavigationMode() === NavigationMode::LINEAR) {
	            // In linear mode, we consider the item completed if it was presented.
	            if ($itemSession->isPresented() === true) {
	                $numberCompleted++;
	            }
	        }
	        else {
	            // In nonlinear mode we consider: 
	            // - an adaptive item completed if it's completion status is 'completed'.
	            // - a non-adaptive item to be completed if it is responded.
	            $isAdaptive = $itemSession->getAssessmentItem()->isAdaptive();
	            
	            if ($isAdaptive === true && $itemSession['completionStatus']->getValue() === AssessmentItemSession::COMPLETION_STATUS_COMPLETED) {
	                $numberCompleted++;
	            }
	            else if ($isAdaptive === false && $itemSession->isResponded() === true) {
	                $numberCompleted++;
	            }
	        }
	    }
	    
	    $route->setPosition($oldPosition);
	    
	    return $numberCompleted;
	}
	
	/**
	 * Transforms any exception to a suitable AssessmentTestSessionException object.
	 * 
	 * This method takes car to return matching AssessmentTestSessionException objects
	 * when $e are AssessmentItemSessionException objects.
	 * 
	 * In case of other Exception types, an AssessmentTestSession object
	 * with code UNKNOWN is returned.
	 * 
	 * @param Exception $e
	 * @return AssessmentTestSessionException
	 */
	protected function transformException(Exception $e) {
	    
	    if ($e instanceof AssessmentItemSessionException) {
	        switch ($e->getCode()) {
	            case AssessmentItemSessionException::UNKNOWN:
	                $msg = "An unknown error occured at the AssessmentItemSession level.";
	                $code = AssessmentTestSessionException::UNKNOWN;
	            break;
	                 
	            case AssessmentItemSessionException::DURATION_OVERFLOW:
	                $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
	                $msg = "Maximum duration of Item Session '${sessionIdentifier}' is reached.";
	                $code = AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_OVERFLOW;
	            break;
	                 
    	        case AssessmentItemSessionException::DURATION_UNDERFLOW:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "Minimum duration of Item Session '${sessionIdentifier}' not reached.";
    	            $code = AssessmentTestSessionException::ASSESSMENT_ITEM_DURATION_UNDERFLOW;
    	        break;
    	        
    	        case AssessmentItemSessionException::ATTEMPTS_OVERFLOW:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "Maximum number of attempts of Item Session '${sessionIdentifier}' reached.";
    	            $code = AssessmentTestSessionException::ASSESSMENT_ITEM_ATTEMPTS_OVERFLOW;
    	        break;
    	        
    	        case AssessmentItemSessionException::RUNTIME_ERROR:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "A runtime error occured at the AssessmentItemSession level.";
    	            $code = AssessmentTestSessionException::UNKNOWN;
    	        break;
    	        
    	        case AssessmentItemSessionException::INVALID_RESPONSE:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "An invalid response was given for Item Session '${sessionIdentifier}' while 'itemSessionControl->validateResponses' is in force.";
    	            $code = AssessmentTestSessionException::ASSESSMENT_ITEM_INVALID_RESPONSE;
    	        break;
    	        
    	        case AssessmentItemSessionException::SKIPPING_FORBIDDEN:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "The Item Session '${sessionIdentifier}' is not allowed to be skipped.";
    	            $code = AssessmentTestSessionException::ASSESSMENT_ITEM_SKIPPING_FORBIDDEN;
    	        break;
    	        
    	        case AssessmentItemSessionException::STATE_VIOLATION:
    	            $sessionIdentifier = $this->buildCurrentItemSessionIdentifier();
    	            $msg = "The Item Session '${sessionIdentifier}' entered an invalid state.";
    	            $code = AssessmentTestSessionException::STATE_VIOLATION;
    	        break;
	        }
	        
	        return new AssessmentTestSessionException($msg, $code, $e);
	    }
	    else {
	        // Generic exception...
	        $msg = "An unexpected error occured at the level of the Test Session.";
	        return new AssessmentTestSessionException($msg, AssessmentTestSessionException::UNKNOWN, $e);
	    }
	}
	
	protected function buildCurrentItemSessionIdentifier() {
	    $itemIdentifier = $this->getCurrentAssessmentItemRef()->getIdentifier();
	    $itemOccurence = $this->getCurrentAssessmentItemRefOccurence();
	    return "${itemIdentifier}.${itemOccurence}";
	}
	
	/**
	 * Whether or not time limits are in force for the current route item.
	 * 
	 * @param boolean $excludeItem Whether or not include item time limits.
	 * @return boolean
	 */
	protected function timeLimitsInForce($excludeItem = false) {
	    return count($this->getCurrentRouteItem()->getTimeLimits($excludeItem)) !== 0;
	}
}
