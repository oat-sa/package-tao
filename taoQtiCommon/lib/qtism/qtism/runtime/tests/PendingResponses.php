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

use qtism\data\AssessmentItemRef;
use qtism\runtime\common\State;
use \InvalidArgumentException;

/**
 * The PendingResponses class represents a set of responses that have to be processed
 * later on e.g. in simultaneous submission mode.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PendingResponses {
    
    /**
     * A State object.
     * 
     * @var State
     */
    private $state;
    
    /**
     * The AssessmentItemRef object related to the State object.
     * 
     * @var AssessmentItemRef
     */
    private $assessmentItemRef;
    
    /**
     * The occurence number of the AssessmentItemRef object related to the State.
     * 
     * @var integer
     */
    private $occurence;
    
    /**
     * Create a new PendingResponses object.
     * 
     * @param State $state The ResponseState object that represent the pending responses.
     * @param AssessmentItemRef $assessmentItemRef The AssessmentItemRef the pending responses are related to.
     * @param integer $occurence The occurence number of the item the pending responses are related to.
     */
    public function __construct(State $state, AssessmentItemRef $assessmentItemRef, $occurence = 0) {
        $this->setState($state);
        $this->setAssessmentItemRef($assessmentItemRef);
        $this->setOccurence($occurence);
    }
    
    /**
     * Set the State object that represent the pending responses.
     * 
     * @param State $state A State object.
     */
    public function setState(State $state) {
        $this->state = $state;
    }
    
    /**
     * Get the State object that represent the pending responses.
     * 
     * @return State A State object.
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Set the AssessmentItemRef object related to the State object.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     */
    public function setAssessmentItemRef(AssessmentItemRef $assessmentItemRef) {
        $this->assessmentItemRef = $assessmentItemRef;
    }
    
    /**
     * Get the AssessmentItemRef object related to the State object.
     * 
     * @return AssessmentItemRef An AssessmentItemRef object.
     */
    public function getAssessmentItemRef() {
        return $this->assessmentItemRef;
    }
    
    /**
     * Set the occurence number of the AssessmentItemRef object related to the State.
     * 
     * @param integer $occurence An occurence number as a positive integer.
     * @throws InvalidArgumentException If $occurence is not a postive integer.
     */
    public function setOccurence($occurence) {
        if (gettype($occurence) !== 'integer') {
            $msg = "The 'occurence' argument must be an integer value, '" . gettype($occurence) . "' given.";
            throw new InvalidArgumentException($msg);
        }
        else {
            $this->occurence = $occurence;
        }
    }
    
    /**
     * Get the occurence number of the AssessmentItemRef object related to the State.
     * 
     * @return integer A postivie integer value.
     */
    public function getOccurence() {
        return $this->occurence;
    }
}