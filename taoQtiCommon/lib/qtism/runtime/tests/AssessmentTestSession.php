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
 * @subpackage 
 *
 */
namespace qtism\runtime\tests;

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

/**
 * The AssessmentTestSession class represents a candidate session
 * for a given AssessmentTest.
 * 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSession extends State {
	
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
	private $autoForward;
	
	/**
	 * The acceptable latencty time for the AssessmentTestSession.
	 * 
	 * @var Duration
	 */
	private $acceptableLatency;
	
	/**
	 * Create a new AssessmentTestSession object.
	 *
	 * @param AssessmentTest $assessmentTest The AssessmentTest object which represents the assessmenTest the context belongs to.
	 * @param Route $route The sequence of items that has to be taken for the session.
	 */
	public function __construct(AssessmentTest $assessmentTest, Route $route) {
		
		parent::__construct();
		$this->setAssessmentTest($assessmentTest);
		$this->setRoute($route);
		$this->setAssessmentItemSessionStore(new AssessmentItemSessionStore());
		$this->setLastOccurenceUpdate(new SplObjectStorage());
		$this->setPendingResponseStore(new PendingResponseStore());
		$this->setAutoForward(true);
		$this->setAcceptableLatency(new Duration('PT0S'));
		
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
	 * Set whether the test session must move automatically
	 * to the next RouteItem when ending an attempt.
	 * 
	 * @param boolean $autoForward
	 * @throws InvalidArgumentException If $autoForward is not a boolean.
	 */
	public function setAutoForward($autoForward) {
	    $type = gettype($autoForward);
	    if ($type === 'boolean') {
	        $this->autoForward = $autoForward;
	    }
	    else {
	        $msg = "The 'autoForward' argument must be a boolean, '${type}' given.";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Know whether the test session must move automatically to
	 * the next RouteItem when ending an attempt.
	 * 
	 * @return boolean
	 */
	public function mustAutoForward() {
	    return $this->autoForward;
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
	 * @return mixed A QTI Runtime compliant value or NULL if no such value can be retrieved for $offset.
	 * @throws OutOfRangeException If $offset is not a string or $offset is not a valid variable identifier.
	 */
	public function offsetGet($offset) {
		
		try {
			$v = new VariableIdentifier($offset);
			
			if ($v->hasPrefix() === false) {
				// Simple variable name.
				// -> This means the requested variable is in the global test scope.
			    $data = &$this->getDataPlaceHolder();
			    
				$varName = $v->getVariableName();
				if (isset($data[$varName]) === false) {
					return null;
				}
				
				return $data[$offset]->getValue();
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
				        if ($sequence === false || $this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
				            return null;
				        }
				    }
				    else {
				        // No sequence number provided + single occurence of this item in the route.
				        $sequence = 0;
				    }
				    
				    try {
				        $session = $store->getAssessmentItemSession($items[$v->getPrefix()], $sequence);
				        return $session[$v->getVariableName()];
				    }
				    catch (OutOfBoundsException $e) {
				        // No such session referenced in the session store.
				        return null;
				    }
				}
				else if ($v->getVariableName() === 'duration') {
				    // Try to get a testPart duration.
				    try {
				        return $this->getTestPartDuration($v->getPrefix());
				    }
				    catch (OutOfBoundsException $e) {
				        // No such TestPart referenced in the AssessmentTestSession.
				        return null;
				    }
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
	            throw new OutOfRangeException($msg, 0, $e);
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
	 * Begin an item session for a given AssessmentItemRef.
	 * 
	 * @param AssessmentItemRef $assessmentItemRef The AssessmentItemRef you want to session to begin.
	 * @throws OutOfBoundsException If no such AssessmentItemRef is referenced in the route to be taken.
	 */
	protected function beginItemSession(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
	    $assessmentItemRefs = $this->getAssessmentItemRefs();
	    if (isset($assessmentItemRefs[$assessmentItemRef->getIdentifier()]) === true) {
	        $itemSession = new AssessmentItemSession($assessmentItemRef);
	        
	        $currentItemSessions = &$this->getAssessmentItemSessions();
	        if (isset($currentItemSessions[$identifier]) === false) {
	            // No item session registered for item $identifier.
	            $currentItemSessions[$identifier] = array();
	        }

	        $currentItemSessions[$identifier][] = $itemSession;
	    }
	    else {
	        $msg = "No assessmentItemRef with identifier '${identifier}' found in the current assessmentTest.";
	        throw new OutOfBoundsException($msg);
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
	        $assessmentSection = $routeItem->getAssessmentSection();
	        $testPart = $routeItem->getTestPart();
	        
	        $navigationMode = $routeItem->getTestPart()->getNavigationMode();
	        $submissionMode = $routeItem->getTestPart()->getSubmissionMode();
	        
	        $session = new AssessmentItemSession($itemRef, $navigationMode, $submissionMode);
	        
	        // Determine the item session control.
	        if ($itemRef->hasItemSessionControl() === true) {
	            $session->setItemSessionControl($itemRef->getItemSessionControl());
	        }
	        else if ($assessmentSection->hasItemSessionControl() === true) {
	            $session->setItemSessionControl($assessmentSection->getItemSessionControl());
	        }
	        else if ($testPart->hasItemSessionControl() === true) {
	            $session->setItemSessionControl($testPart->getItemSessionControl());
	        }
	        // else ... It will be a default one.
	        
	        // Determine the time limits.
	        if ($itemRef->hasTimeLimits() === true) {
	            $session->setTimeLimits($itemRef->getTimeLimits());
	        }
	        // else ... No time limits !
	        
	        $session->setAcceptableLatency($this->getAcceptableLatency());
	        $this->addItemSession($session, $routeItem->getOccurence());
	    }
	    
	    $route->setPosition($oldPosition);
	}
	
	public function beginTestSession() {
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
	    while($route->valid() === true ) {
	        $routeItem = $route->current();
	        $session = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	        
	        if ($session->getState() === AssessmentItemSessionState::NOT_SELECTED) {
	            $session->beginItemSession();
	        }
	        
	        if ($route->isNavigationLinear() === false) {
	            // We cannot foresee more items to be selected for presentation
	            // because the rest of the sequence is non-linear.
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
	 * @throws AssessmentItemSessionException If the current item cannot be skipped or if timings are not respected.
	 * @throws AssessmentTestSessionException If the test session is not running or it is the last route item of the testPart but the SIMULTANEOUS submission mode is in force and not all responses were provided.
	 */
	public function skip() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot skip the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException(msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $this->checkTimeLimits();
	    
	    $item = $this->getCurrentAssessmentItemRef();
	    $occurence = $this->getCurrentAssessmentItemRefOccurence();
	    $session = $this->getItemSession($item, $occurence);
	    $session->skip();
	    
	    if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
	        // Store the responses for a later processing at the end of the test part.
	        $pendingResponses = new PendingResponses($session->getResponseVariables(false), $item, $occurence);
	        $this->addPendingResponses($pendingResponses);
	    }
	    
	    if ($this->mustAutoForward() === true) {
	        // Go automatically to the next step in the route.
	        $this->moveNext();
	    }
	}
	
	/**
	 * Begin an attempt for the current item in the route.
	 * 
	 * @throws AssessmentTestSessionException If the time limits are already exceeded or if there are no more attempts allowed.
	 */
	public function beginAttempt() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot begin an attempt for the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $this->checkTimeLimits();
	    
	    $routeItem = $this->getCurrentRouteItem();
	    $session = $this->getItemSession($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	    $session->beginAttempt();
	}
	
	/**
	 * End an attempt for the current item in the route. If the current navigation mode
	 * is LINEAR, the TestSession moves automatically to the next step in the route or
	 * the end of the session if the responded item is the last one.
	 * 
	 * @param State $responses The responses for the curent item in the sequence.
	 * @throws AssessmentTestSessionException
	 * @throws AssessmentItemSessionException
	 */
	public function endAttempt(State $responses) {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot end an attempt for the current item while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $routeItem = $this->getCurrentRouteItem();
	    $currentItem = $routeItem->getAssessmentItemRef();
	    $currentOccurence = $routeItem->getOccurence();
	    $session = $this->getItemSession($currentItem, $currentOccurence);
	    $session->updateDuration();
	    
	    try {
	        $this->checkTimeLimits();
	        
	        if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS) {
	             
	            // Store the responses for a later processing.
	            $this->addPendingResponses(new PendingResponses($responses, $currentItem, $currentOccurence));
	            $session->endAttempt($responses, false);
	        }
	        else {
	            $session->endAttempt($responses);
	            // Update the lastly updated item occurence.
	            $this->notifyLastOccurenceUpdate($routeItem->getAssessmentItemRef(), $routeItem->getOccurence());
	             
	            // Item Results submission.
	            try {
	                $this->submitItemResults($this->getAssessmentItemSessionStore()->getAssessmentItemSession($currentItem, $currentOccurence), $currentOccurence);
	            }
	            catch (AssessmentTestSessionException $e) {
	                $msg = "An error occured while transmitting item results to the appropriate data source at deffered responses processing time.";
	                throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::RESULT_SUBMISSION_ERROR, $e);
	            }
	        }
	         
	        if ($this->mustAutoForward() === true) {
	            // Go automatically to the next step in the route.
	            $this->moveNext();
	        }
	    }
	    catch (AssessmentTestSessionException $e) {
	        if ($e->getCode() === AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW) {
	            
	            // Move to the next testPart if AutoForward is true.
	            if ($this->mustAutoForward() === true) {
	                $this->moveNextTestPart();
	            }
	        }
	        
	        // Rethrow the error.
	        throw $e;
	    }
	}
	
	/**
	 * Perform a 'jump' to a given position in the Route sequence. The current navigation
	 * mode must be LINEAR to be able to jump.
	 * 
	 * @throws AssessmentTestSessionException If $position is out of the Route bounds. The error code will be FORBIDDEN_JUMP.
	 */
	public function jumpTo($position) {
	    
	    // Can we jump?
	    if ($this->getCurrentNavigationMode() !== NavigationMode::NONLINEAR) {
	        $msg = "Jumps are not allowed in LINEAR navigation mode.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP);
	    }
	    
	    $route = $this->getRoute();
	    
	    try {
	        $currentTestPart = $this->getCurrentTestPart();
	        
	        if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS && $route->isInTestPart($position, $currentTestPart) === false) {
	            $msg = "Cannot jump to position '${position}' because the current submission mode is SIMULTANEOUS and the jump target is outside of the current testPart.";
	            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP);
	        }
	        
	        $oldPosition = $route->getPosition();
	        $this->getRoute()->setPosition($position);
	        
	        $this->selectEligibleItems();
	    }
	    catch (OutOfBoundsException $e) {
	        $msg = "Position '${position}' is out of the Route bounds.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::FORBIDDEN_JUMP);
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
	    
	    foreach ($pendingResponses as $pendingResponses) {
	        
	        $item = $pendingResponses->getAssessmentItemRef();
	        $occurence = $pendingResponses->getOccurence();
	        $itemSession = $itemSessionStore->getAssessmentItemSession($item, $occurence);
	        $responseProcessing = $item->getResponseProcessing();
	        
	        // If the item has a processable response processing...
	        if (is_null($responseProcessing) === false && ($responseProcessing->hasTemplate() === true || $responseProcessing->hasTemplateLocation() === true || count($responseProcessing->getResponseRules()) > 0)) {
	            try {
	                $engine = new ResponseProcessingEngine($responseProcessing, $itemSession);
	                $engine->process();
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
	    
	    return $result;
	}
	
	/**
	 * Ask the test session to move to next RouteItem in the Route sequence.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running or an error occurs during the transition.
	 */
	public function moveNext() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next item while the test session state is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    try {
	        $this->checkTimeLimits();
	        
	        if ($this->getCurrentSubmissionMode() === SubmissionMode::SIMULTANEOUS && $this->getRoute()->isLastOfTestPart() === true) {
	             
	            if ($this->isCurrentTestPartComplete() === false) {
	                $msg = "Cannot move to the next Test Part while the SIMULTANEOUS navigation mode is in force and not all items of the TestPart were responded.";
	                throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::MISSING_RESPONSES);
	            }
	            else {
	                // The testPart is complete so deffered response processing must take place.
	                $this->defferedResponseProcessing();
	            }
	        }
	         
	        $this->nextRouteItem();
	    }
	    catch (AssessmentTestSessionException $e) {
	        if ($e->getCode() === AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW) {
	            $this->setPendingResponseStore(new PendingResponseStore());
	            
	            if ($this->mustAutoForward() === true) {
	                $this->moveNextTestPart();
	            }
	        }
	        
	        throw $e;
	    }
	}
	
	/**
	 * Ask the test session to move to the previous RouteItem in the Route sequence.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running or an error occurs during the transition.
	 */
	public function moveBack() {
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the previous item while the test session state is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    try {
	        $this->checkTimeLimits();
	        $this->previousRouteItem();
	    }
	    catch (AssessmentTestSessionException $e) {
	        if ($e->getCode() === AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW) {
	            $this->setPendingResponseStore(new PendingResponseStore());
	             
	            if ($this->mustAutoForward() === true) {
	                $this->moveNextTestPart();
	            }
	        }
	         
	        throw $e;
	    }
	}
	
	/**
	 * Move to the next item in the route.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running.
	 */
	protected function nextRouteItem() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot move to the next position while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    $route = $this->getRoute();
	    $route->next();
	    $this->selectEligibleItems();
	    
	    if ($route->valid() === false) {
	        // This is the end of the test session.
	        // 1. Apply outcome processing.
	        $this->outcomeProcessing();
	        
	        // 2. End the test session.
	        $this->endTestSession();
	    }
	}
	
	/**
	 * Set the position in the Route at the very next TestPart in the Route sequence.
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
	 * @throws AssessmentTestSessionException If the test is not at its last route item or if an error occurs at OutcomeProcessing time.
	 */
	protected function outcomeProcessing() {
	    if ($this->getRoute()->isLast() === false) {
	        $msg = "Outcome Processing may be applied only if the current route item is the last one of the route.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    if ($this->getAssessmentTest()->hasOutcomeProcessing() === true) {
	        // As per QTI Spec:
	        // The values of the test's outcome variables are always reset to their defaults prior
	        // to carrying out the instructions described by the outcomeRules.
	        $this->resetOutcomeVariables();
	         
	        $outcomeProcessing = $this->getAssessmentTest()->getOutcomeProcessing();
	        
	        try {
	            $outcomeProcessingEngine = new OutcomeProcessingEngine($outcomeProcessing, $this);
	            $outcomeProcessingEngine->process();
	            
	            // Submit test-level results.
	            $this->submitTestResults();
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
	 */
	public function endTestSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot end the test session while the state of the test session is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
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
	            throw new InvalidArgumentException($msg, 0, $e);
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
	 * @return integer
	 */
	public function getRouteCount() {
	    return $this->getRoute()->count();
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
	    if ($this->getCurrentNavigationMode() === NavigationMode::LINEAR) {
	        return false;
	    }
	    else {
	        return $this->getRoute()->getPosition() > 0;
	    }
	}
	
	/**
	 * Get the Jump description objects describing to which RouteItem the candidate
	 * is able to "jump" to when the NONLINEAR navigation mode is in force.
	 * 
	 * If the LINEAR navigation mode is in force, an empty JumpCollection is returned.
	 * 
	 * @param boolean $anywhere Whether the candidate is allowed to jump to any item of the current test or only in the current test part.
	 * @return JumpCollection A collection of Jump objects.
	 */
	public function getPossibleJumps($anywhere = true) {
	    $jumps = new JumpCollection();
	    
	    if ($this->isRunning() === false || $this->getCurrentNavigationMode() === NavigationMode::LINEAR) {
	        // No possible jumps.
	        return $jumps;
	    }
	    else {
	        $jumpables = ($anywhere === true) ? $this->getRoute()->getAllRouteItems() : $this->getRoute()->getCurrentTestPartRouteItems();
	        
	        // Scan the route for "jumpable" items.
	        foreach ($jumpables as $routeItem) {
	            $itemRef = $routeItem->getAssessmentItemRef();
	            $occurence = $routeItem->getOccurence();
	            
	            // get the session related to this route item.
	            $store = $this->getAssessmentItemSessionStore();
	            $itemSession = $store->getAssessmentItemSession($itemRef, $occurence);
	            
	            $jumps[] = new Jump($itemRef, $occurence, $itemSession);
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
	 * Set the acceptable latency time for the AssessmentTestSession.
	 * 
	 * @param Duration $latency A Duration object.
	 */
	public function setAcceptableLatency(Duration $latency) {
	    $this->acceptableLatency = $latency;
	}
	
	/**
	 * Get the acceptable latency time for the AssessmentTestSession.
	 * 
	 * @return Duration A Duration object.
	 */
	public function getAcceptableLatency() {
	    return $this->acceptableLatency;
	}
	
	/**
	 * Get the duration of a given TestPart by specifying its identifier.
	 * 
	 * @param string $identifier The identifier of the TestPart you want to know the duration.
	 * @throws OutOfBoundsException If $identifier does not reference any TestPart in the AssessmentTestSession.
	 * @return Duration A Duration object.
	 */
	protected function getTestPartDuration($identifier) {
	    $duration = new Duration('PT0S');
	     
	    try {
	        $involvedRouteItems = $this->getRoute()->getRouteItemsByTestPart($identifier);
	        $itemSessionStore = $this->getAssessmentItemSessionStore();
	    
	        if ($this->getState() !== AssessmentTestSessionState::INITIAL) {
	            foreach ($involvedRouteItems as $routeItem) {
	                $itemSessions = $itemSessionStore->getAssessmentItemSessions($routeItem->getAssessmentItemRef());
	    
	                foreach ($itemSessions as $itemSession) {
	                    $duration->add($itemSession['duration']);
	                }
	            }
	        }
	         
	        return $duration;
	    }
	    catch (OutOfBoundsException $e) {
	        // No assessmentSection with $identifier in the Route.
	        $msg = "No TestPart with identifier '${identifier}' referenced in the AssessmentTestSession.";
	        throw new OutOfBoundsException($msg, 0, $e);
	    }
	}
	
	/**
	 * Get the current time limits in force for the current TestPart.
	 * 
	 * @throws AssessmentTestSessionException If the AssessmentTestSession is not running.
	 * @return null|TimeLimits A TimeLimits object or null if no TimeLimits is in force for the current TestPart.
	 */
	public function getTimeLimitsTestPart() {
	    if ($this->isRunning() === false) {
	        $msg = "The TimeLimts for the current TestPart cannot be determined if the state of the AssessmentTestSession is INITIAL or CLOSED.";
	        throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::STATE_VIOLATION);
	    }
	    
	    if (($timeLimits = $this->getCurrentTestPart()->getTimeLimits()) !== null) {
	        return $timeLimits;
	    }
	    else {
	        return null;
	    }
	}
	
	/**
	 * Get the time remaining on the current test part. If no timeLimits is in force
	 * for the current TestPart, null is returned.
	 * 
	 * @return null|Duration
	 */
	public function getRemainingTimeTestPart() {
	    
	    $remainingTime = null;
	    
	    if (($timeLimits = $this->getTimeLimitsTestPart()) !== null) {
	        if ($timeLimits->hasMaxTime() === true) {
	            $remainingTime = clone $timeLimits->getMaxTime();
	            $remainingTime->sub($this[$this->getCurrentTestPart()->getIdentifier() . '.duration']);
	        }
	    }
	    
	    return $remainingTime;
	}
	
	/**
	 * Checks if the timeLimits in force are respected. If this is not the case, an AssessmentTestSessionException
	 * will be raised with the appropriate error code.
	 * 
	 * @throws AssessmentItemSessionException
	 */
	public function checkTimeLimits() {
	    
	    if (($timeLimits = $this->getTimeLimitsTestPart()) !== null && $timeLimits->hasMaxTime() === true) {
	        $currentTestPart = $this->getCurrentTestPart();
	        $currentDuration = $this[$currentTestPart->getIdentifier() . '.duration'];
	        $referenceDuration = $timeLimits->getMaxTime();
	        
	        if ($currentDuration->getSeconds(true) > $referenceDuration->getSeconds(true)) {
	            $msg = "Maximum duration of TestPart '" . $currentTestPart->getIdentifier() . "' exceeded.";
	            throw new AssessmentTestSessionException($msg, AssessmentTestSessionException::TEST_PART_DURATION_OVERFLOW);
	        }
	    }
	}
	
	/**
	 * Get the current AssessmentItemSession object.
	 * 
	 * @throws AssessmentTestSessionException If the test session is not running.
	 * @return AssessmentItemSession The current AssessmentItemSession object.
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
	 * Update the durations involved in the AssessmentTestSession. This method can be useful for stateless systems
	 * that make use of QtiSm.
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
	public function suspendItemSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot suspend the item session if the test session is not running.";
	        $code = AssessmentTestSessionException::STATE_VIOLATION;
	        throw new AssessmentTestSessionException($msg, $code);
	    }
	    else if (($itemSession = $this->getCurrentAssessmentItemSession()) !== false) {
	        // @throws AssessmentItemSessionException.
	        $itemSession->suspend();
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
	public function interactWithItemSession() {
	    
	    if ($this->isRunning() === false) {
	        $msg = "Cannot set the item session in interacting state if test session is not running.";
	        $code = AssessmentTestSessionException::STATE_VIOLATION;
	        throw new AssessmentTestSessionException($msg, $code);
	    }
	    else if (($itemSession = $this->getCurrentAssessmentItemSession()) !== false) {
	        // @throws AssessmentItemSessionException.
	        $itemSession->interact();
	    }
	    else {
	        $msg = "Cannot retrieve the current item session.";
	        throw new UnexpectedValueException($msg);
	    }
	}
	
	/**
	 * Instantiate a new AssessmentItemSession from a factory.
	 * 
	 * @param AbstractAssessmentTestSessionFactory $factory
	 * @return AssessmentTestSession An instantiated AssessmentTestSession object.
	 */
	public static function instantiate(AbstractAssessmentTestSessionFactory $factory) {
	    
	    $routeStack = array();
	    
	    foreach ($factory->getAssessmentTest()->getTestParts() as $testPart) {
	       
	        foreach ($testPart->getAssessmentSections() as $assessmentSection) {
	            $trail = array();
	            $mark = array();
	            $visibleSectionStack = array();
	            
	            array_push($trail, $assessmentSection);
	            
	            while (count($trail) > 0) {
	               
	                $current = array_pop($trail);
	                
	                if (!in_array($current, $mark, true) && $current instanceof AssessmentSection) {
	                    // 1st pass on assessmentSection.
	                    $currentAssessmentSection = $current;
	                    
	                    if ($currentAssessmentSection->isVisible() === true) {
	                        array_push($visibleSectionStack, $currentAssessmentSection);
	                    }
	                    
	                    array_push($mark, $current);
	                    array_push($trail, $current);
	                    
	                    foreach (array_reverse($current->getSectionParts()->getArrayCopy()) as $sectionPart) {
	                        array_push($trail, $sectionPart);
	                    }
	                }
	                else if (in_array($current, $mark, true)) {
	                    // 2nd pass on assessmentSection.
	                    // Pop N routeItems where N is the children count of $current.
	                    $poppedRoutes = array();
	                    for ($i = 0; $i < count($current->getSectionParts()); $i++) {
	                        $poppedRoutes[] = array_pop($routeStack);
	                    }
	                    
	                    $selection = new BasicSelection($current, new SelectableRouteCollection(array_reverse($poppedRoutes)));
	                    $selectedRoutes = $selection->select();
	                    
	                    // The last visible AssessmentSection from the top to the bottom of the tree
	                    // is useful to know which RubrikBlock to apply on selected RouteItems
	                    $lastVisible = array_pop($visibleSectionStack);
	                    if (count($visibleSectionStack) === 0) {
	                        // top-level AssessmentSection, the visible container is actually the TestPart it belongs to.
	                        $lastVisible = $testPart;
	                    }
	                    
	                    // Shuffling can be applied on selected routes.
	                    // $route will contain the final result of the selection + ordering.
	                    $ordering = new BasicOrdering($current, $selectedRoutes);
	                    $selectedRoutes = $ordering->order();
	                    
                        $route = new SelectableRoute($current->isFixed(), $current->isRequired(), $current->isVisible(), $current->mustKeepTogether());
	                    foreach ($selectedRoutes as $r) {
	                        $route->appendRoute($r);
	                    }
	                    
	                    // Add to the last item of the selection the branch rules of the AssessmentSection/testPart
	                    // on which the selection is applied.
	                    $route->getLastRouteItem()->addBranchRules($current->getBranchRules());
	                    
	                    // Do the same as for branch rules for pre conditions, except that they must be
	                    // attached on the first item of the route.
	                    $route->getFirstRouteItem()->addPreConditions($current->getPreConditions());
	                    
	                    array_push($routeStack, $route);
	                }
	                else if ($current instanceof AssessmentItemRef) {
	                    // leaf node.
	                    $route = new SelectableRoute($current->isFixed(), $current->isRequired());
	                    $route->addRouteItem($current, $currentAssessmentSection, $testPart);
	                    array_push($routeStack, $route);
	                }
	            }
	        }
	    }
	    
	    $finalRoutes = $routeStack;
	    $route = new SelectableRoute();
	    foreach ($finalRoutes as $finalRoute) {
	        $route->appendRoute($finalRoute);
	    }
	    
	    $factory->setRoute($route);
	    
	    return $factory->createAssessmentTestSession();
	}
}