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
use \SplObjectStorage;
use \OutOfBoundsException;

/**
 * An AssessmentItemSessionStore store AssessmentItemSession objects
 * by AssessmentItemRef objects.
 * 
 * In other words, it store the item sessions for a given AssessmentItemRef
 * involved in an AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemSessionStore {
    
    /**
     * Each shelve of the store contains a collection
     * of AssessmentItemSession related to a same
     * AssessmentItemRef object.
     * 
     * @var SplObjectStorage
     */
    private $shelves;
    
    public function __construct() {
        $this->setShelves(new SplObjectStorage());
    }
    
    /**
     * Set the SplObjectStorage object that will store AssessmentItemSessionCollection objects
     * by AssessmentItemRef.
     * 
     * @param SplObjectStorage $shelves An SplObjectStorage object that will store AssessmentItemSessionCollection objects.
     */
    protected function setShelves(SplObjectStorage $shelves) {
        $this->shelves = $shelves;
    }
    
    /**
     * Get the SplObjectStorage object that will store AssessmentItemSessionCollection objects
     * by AssessmentItemRef.
     * 
     * @return SplObjectStorage An SplObjectStorage object that will store AssessmentItemSessionCollection objects.
     */
    protected function getShelves() {
        return $this->shelves;    
    }
    
    /**
     * Add an AssessmentItemSession to the store, for a given $occurence number.
     * 
     * @param AssessmentItemSession $assessmentItemSession
     * @param integer $occurence The occurence number of the session.
     */
    public function addAssessmentItemSession(AssessmentItemSession $assessmentItemSession, $occurence = 0) {
        $shelves = $this->getShelves();
        $assessmentItemRef = $assessmentItemSession->getAssessmentItem();
        
        if (isset($shelves[$assessmentItemRef]) === false) {
            $shelves[$assessmentItemRef] = new AssessmentItemSessionCollection();
        }
        
        $shelves[$assessmentItemRef][$occurence] = $assessmentItemSession;
    }
    
    /**
     * Get an AssessmentItemSession by $assessmentItemRef and $occurence number.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @param integer $occurence An occurence number.
     * @return AssessmentItemSession An AssessmentItemSession object.
     * @throws OutOfBoundsException If there is no AssessmentItemSession for the given $assessmentItemRef and $occurence.
     */
    public function getAssessmentItemSession(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
        $shelves = $this->getShelves();
        
        if (isset($shelves[$assessmentItemRef]) && isset($shelves[$assessmentItemRef][$occurence]) === true) {
            return $this->shelves[$assessmentItemRef][$occurence];
        }
        else {
            $itemId = $assessmentItemRef->getIdentifier();
            $msg = "No AssessmentItemSession object bound to '${itemId}.${occurence}'.";
            throw new OutOfBoundsException($msg);
        }
    }
    
    /**
     * Whether the store contains an item session for $assessmentItemRef, $occurence.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @param integer $occurence An occurence number.
     */
    public function hasAssessmentItemSession(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
        $shelves = $this->getShelves();
        return isset($shelves[$assessmentItemRef][$occurence]);
    }
    
    /**
     * Get the item sessions related to $assessmentItemRef.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @throws OutOfBoundsException If no item sessions related to $assessmentItemRef are found.
     * @return AssessmentItemSessionCollection A collection of AssessmentItemSession objects related to $assessmentItemRef.
     */
    public function getAssessmentItemSessions(AssessmentItemRef $assessmentItemRef) {
        $shelves = $this->getShelves();
        if (isset($shelves[$assessmentItemRef]) === true) {
            return $shelves[$assessmentItemRef];
        }
        else {
            $itemId = $assessmentItemRef->getIdentifier();
            $msg = "No AssessmentItemSession objects bound to '${itemId}.${occurence}'.";
            throw new OutOfBoundsException($msg);
        }
    }
    
    /**
     * Whether the given $assessmentItemRef has multiple sessions registered in the store.
     * 
     * * If $assessmentItemRef is unknown by the store or there is a single session for this $assessmentItemRef, true is returned.
     * * If $assessmentItemRef is known by the store and there are more than a single session for this $assessmentItemRef, false is returned.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @return boolean
     */
    public function hasMultipleOccurences(AssessmentItemRef $assessmentItemRef) {
        $shelves = $this->getShelves();
        return isset($shelves[$assessmentItemRef]) && count($shelves[$assessmentItemRef]) > 1;
    }
    
    /**
     * Get all the AssessmentItemSession objects held by the store.
     * 
     * @return AssessmentItemSessionCollection A collection of AssessmentItemSession objects.
     */
    public function getAllAssessmentItemSessions() {
        $shelves = $this->getShelves();
        $collection = new AssessmentItemSessionCollection();
        
        foreach ($shelves as $itemRef) {
            foreach ($shelves[$itemRef] as $session) {
                $collection[] = $session;
            }
        }
        
        return $collection;
    }
}