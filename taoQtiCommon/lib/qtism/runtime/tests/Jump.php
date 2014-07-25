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

use qtism\data\AssessmentItemRef;
use qtism\runtime\tests\AssessmentItemSessionState;
use \InvalidArgumentException;

/**
 * The Jump class represents the a possible location in an AssessmentTestSession
 * a candidate can "jump" to. Indeed, when the NONLINEAR navigation mode is in force,
 * the candidate has the ability to "jump" to any RouteItem that belongs to the current TestPart.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Jump {
    
    /**
     * The AssessmentItemRef the candidate can jump to.
     * 
     * @var AssessmentItemRef
     */
    private $assessmentItemRef;
    
    /**
     * The occurence number of the AssessmentItemRef the candidate can jump to.
     * 
     * @var integer
     */
    private $occurence;
    
    /**
     * The AssessmentItemSession related to the $assessmentItemRef.$occurence
     * the Jump targets.
     * 
     * @var AssessmentItemSession
     */
    private $itemSession;
    
    /**
     * Create a new Jump object.
     * 
     * @param AssessmentItemRef $assessmentItemRef The AssessmentItemRef the candidate can jump to.
     * @param integer $occurence The occurence number of the $assessmentItemRef the candidate can jump to.
     * @param AssessmentItemSession $itemSession The AssessmentItemSession related to $assessmentItemRef.$occurence.
     * @throws InvalidArgumentException If $occurence is not an integer value or $itemSessionState is not a value from the AssessmentItemSessionState enumeration.
     */
    public function __construct(AssessmentItemRef $assessmentItemRef, $occurence, AssessmentItemSession $itemSession) {
        $this->setAssessmentItemRef($assessmentItemRef);
        $this->setOccurence($occurence);
        $this->setItemSession($itemSession);
    }
    
    /**
     * Set the AssessmentItemRef the candidate can jump to.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     */
    protected function setAssessmentItemRef(AssessmentItemRef $assessmentItemRef) {
        $this->assessmentItemRef = $assessmentItemRef;
    }
    
    /**
     * Get the AssessmentItemRef the candidate can jump to.
     * 
     * @return AssessmentItemRef An AssessmentItemRef object.
     */
    public function getAssessmentItemRef() {
        return $this->assessmentItemRef;
    }
    
    /**
     * Set the occurence number of the AssessmentItemRef the candidate can jump to.
     * 
     * @param integer $occurence An occurence number.
     * @throws InvalidArgumentException If $occurence is not an integer value.
     */
    protected function setOccurence($occurence) {
        $type = gettype($occurence);
        if ($type === 'integer') {
            $this->occurence = $occurence;
        }
        else {
            $msg = "The 'occurence' argument must be an integer, '${type}' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the occurence number of the AssessmentItemRef the candidate can jump to.
     * 
     * @return integer An occurence number.
     */
    public function getOccurence() {
        return $this->occurence;
    }
    
    /**
     * Set the AssessmentItemSession related to AssessmentItemRef.occurence.
     * 
     * @param AssessmentItemSession $itemSession An AssessmentItemSession object.
     * @throws InvalidArgumentException If $itemSessionState is not a value from the AssessmentItemSessionState enumeration.
     */
    protected function setItemSession(AssessmentItemSession $itemSession) {
        $this->itemSession = $itemSession;
    }
    
    /**
     * Get the AssessmentItemSession related to AssessmentItemRef.occurence.
     * 
     * @return AssessmentItemSession An AssessmentItemSession object.
     */
    public function getItemSession() {
        return $this->itemSession;
    }
}