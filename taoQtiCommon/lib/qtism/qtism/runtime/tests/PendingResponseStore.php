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
use \SplObjectStorage;

/**
 * The PendingResponseStore aims at storing PendingResponses. It's main goal
 * is to offer a clean API to add and retrieve PendingResponses objects depending
 * on the context.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PendingResponseStore {
    
    /**
     * A map of arrays indexed by AssessmentItemRef objects.
     * 
     * @var SplObjectStorage
     */
    private $assessmentItemRefMap;
    
    public function __construct() {
        $this->setAssessmentItemRefMap(new SplObjectStorage());
    }
    
    /**
     * Get the AssessmentItemRef map.
     * 
     * @return SplObjectStorage
     */
    protected function getAssessmentItemRefMap() {
        return $this->assessmentItemRefMap;
    }
    
    /**
     * Set the AssessmentItemRef map.
     * 
     * @param SplObjectStorage $assessmentItemRefMap
     */
    protected function setAssessmentItemRefMap(SplObjectStorage $assessmentItemRefMap) {
        $this->assessmentItemRefMap = $assessmentItemRefMap;
    }
    
    /**
     * Get all the PendingResponses objects held by the store.
     * 
     * @return PendingResponsesCollection A collection of PendingResponses objects held by the store.
     */
    public function getAllPendingResponses() {
        $collection = new PendingResponsesCollection();
        $map = $this->getAssessmentItemRefMap();
        foreach ($map as $itemRef) {
            foreach ($map[$itemRef] as $v) {
                $collection[] = $v;
            }
        }
        
        return $collection;
    }
    
    /**
     * Add a PendingResponse object to the store.
     * 
     * @param PendingResponses $pendingResponses
     */
    public function addPendingResponses(PendingResponses $pendingResponses) {
        $map = $this->getAssessmentItemRefMap();
        $itemRef = $pendingResponses->getAssessmentItemRef();
        
        if (isset($map[$itemRef]) === false) {
            $map[$itemRef] = array();
        }
        
        $entry = $map[$itemRef];
        $entry[$pendingResponses->getOccurence()] = $pendingResponses;
        $map[$itemRef] = $entry;
        
        $this->getAllPendingResponses()->attach($pendingResponses);
    }
    
    /**
     * Whether the store holds a PendingResponses object related to $assessmentItemRef and
     * $occurence.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @param integer $occurence An occurence number.
     */
    public function hasPendingResponses(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
        $map = $this->getAssessmentItemRefMap();
        return isset($map[$assessmentItemRef]) && isset($map[$assessmentItemRef][$occurence]);
    }
    
    /**
     * Get the PendingResponses object related to $assessmentItemRef.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     * @param integer $occurence An occurence number.
     * @return false|PendingResponses
     */
    public function getPendingResponses(AssessmentItemRef $assessmentItemRef, $occurence = 0) {
        $returnValue = false;
        
        if ($this->hasPendingResponses($assessmentItemRef, $occurence)) {
            $map = $this->getAssessmentItemRefMap();
            $returnValue = $map[$assessmentItemRef][$occurence];
        }
        
        return $returnValue;
    }
}