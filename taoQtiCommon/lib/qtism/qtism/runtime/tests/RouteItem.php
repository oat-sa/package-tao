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

use qtism\data\content\RubricBlockRefCollection;
use qtism\data\content\RubricBlockCollection;
use qtism\data\AssessmentTest;
use qtism\data\rules\PreConditionCollection;
use qtism\data\rules\PreCondition;
use qtism\data\rules\BranchRule;
use qtism\data\rules\BranchRuleCollection;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentSectionCollection;
use qtism\data\TestPart;
use qtism\data\AssessmentItemRef;

/**
 * The RouteItem class describes the composite items of a Route object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RouteItem {
    
    /**
     * The AssessmentTest the RouteItem is bound to.
     * 
     * @var AssessmentTest
     */
    private $assessmentTest;
    
    /**
     * The AssessmentItemRef object bound to the RouteItem.
     * 
     * @var AssessmentItemRef
     */
    private $assessmentItemRef;
    
    /**
     * The TestPart object bound to the RouteItem.
     * 
     * @var TestPart
     */
    private $testPart;
    
    /**
     * The AssessmentSectionCollection object bound to the RouteItem.
     * 
     * @var AssessmentSectionCollection
     */
    private $assessmentSections;
    
    /**
     * The BranchRule objects to be applied after the RouteItem.
     * 
     * @var BranchRuleCollection
     */
    private $branchRules;
    
    /**
     * The PreCondition objects to be applied prior to the RouteItem.
     * 
     * @var PreConditionCollection
     */
    private $preConditions;
    
    /**
     * The occurence number.
     * 
     * @var integer
     */
    private $occurence = 0;
    
    /**
     * Create a new RouteItem object.
     * 
     * @param AssessmentItemRef $assessmentItemRef The AssessmentItemRef object bound to the RouteItem.
     * @param AssessmentSection|AssessmentSectionCollection $assessmentSection The AssessmentSection object bound to the RouteItem.
     * @param TestPart $testPart The TestPart object bound to the RouteItem.
     * @param AssessmentTest $assessmentTest The AssessmentTest object bound to the RouteItem.
     */
    public function __construct(AssessmentItemRef $assessmentItemRef, $assessmentSections, TestPart $testPart, AssessmentTest $assessmentTest) {
        $this->setAssessmentItemRef($assessmentItemRef);
        $this->setAssessmentTest($assessmentTest);
        
        if ($assessmentSections instanceof AssessmentSection) {
            $this->setAssessmentSection($assessmentSections);
        }
        else {
            $this->setAssessmentSections($assessmentSections);
        }
        
        $this->setTestPart($testPart);
        $this->setBranchRules(new BranchRuleCollection());
        $this->setPreConditions(new PreConditionCollection());
        
        $this->addBranchRules($assessmentItemRef->getBranchRules());
        $this->addPreConditions($assessmentItemRef->getPreConditions());
    }
    
    /**
     * Set the AssessmentTest object bound to the RouteItem.
     * 
     * @param AssessmentTest $assessmentTest An AssessmentTest object.
     */
    public function setAssessmentTest(AssessmentTest $assessmentTest) {
        $this->assessmentTest = $assessmentTest;
    }
    
    /**
     * Get the AssessmentTest object bound to the RouteItem.
     * 
     * @return AssessmentTest An AssessmentTest object.
     */
    public function getAssessmentTest() {
        return $this->assessmentTest;
    }
    
    /**
     * Set the AssessmentItemRef object bound to the RouteItem.
     * 
     * @param AssessmentItemRef $assessmentItemRef An AssessmentItemRef object.
     */
    public function setAssessmentItemRef(AssessmentItemRef $assessmentItemRef) {
        $this->assessmentItemRef = $assessmentItemRef;
    }
    
    /**
     * Get the AssessmentItemRef object bound to the RouteItem.
     * 
     * @return AssessmentItemRef An AssessmentItemRef object.
     */
    public function getAssessmentItemRef() {
        return $this->assessmentItemRef;
    }
    
    /**
     * Set the TestPart object bound to the RouteItem.
     * 
     * @param TestPart $testPart A TestPart object.
     */
    public function setTestPart(TestPart $testPart) {
        $this->testPart = $testPart;
    }
    
    /**
     * Get the TestPart object bound to the RouteItem.
     * 
     * @return TestPart A TestPart object.
     */
    public function getTestPart() {
        return $this->testPart;
    }
    
    /**
     * Set the AssessmentSection object bound to the RouteItem.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object.
     */
    public function setAssessmentSection(AssessmentSection $assessmentSection) {
        $this->assessmentSections = new AssessmentSectionCollection(array($assessmentSection));
    }
    
    /**
     * Set the AssessmentSection objects bound to the RouteItem.
     * 
     * @param AssessmentSectionCollection $assessmentSections A collection of AssessmentSection objects.
     */
    public function setAssessmentSections(AssessmentSectionCollection $assessmentSections) {
        $this->assessmentSections = $assessmentSections;
    }
    
    /**
     * Set the occurence number.
     * 
     * @param integer $occurence An occurence number.
     */
    public function setOccurence($occurence) {
        $this->occurence = $occurence;
    }
    
    /**
     * Get the occurence number.
     * 
     * @return integer An occurence number.
     */
    public function getOccurence() {
        return $this->occurence;
    }
    
    /**
     * Get the BranchRule objects to be applied after the RouteItem.
     * 
     * @return BranchRuleCollection A collection of BranchRule objects.
     */
    public function getBranchRules() {
        return $this->branchRules;
    }
    
    /**
     * Set the BranchRule objects to be applied after the RouteItem.
     * 
     * @param BranchRuleCollection $branchRules A collection of BranchRule objects.
     */
    public function setBranchRules(BranchRuleCollection $branchRules) {
        $this->branchRules = $branchRules;
    }
    
    /**
     * Add a BranchRule object to be applied after the RouteItem.
     * 
     * @param BranchRule $branchRule A BranchRule object to be added.
     */
    public function addBranchRule(BranchRule $branchRule) {
        $this->branchRules->attach($branchRule);
    }
    
    /**
     * Add some BranchRule objects to be applied after the RouteItem.
     * 
     * @param BranchRuleCollection $branchRules A collection of BranchRule object.
     */
    public function addBranchRules(BranchRuleCollection $branchRules) {
        foreach ($branchRules as $branchRule) {
            $this->addBranchRule($branchRule);
        }
    }
    
    /**
     * Get the PreCondition objects to be applied prior to the RouteItem.
     *
     * @return PreConditionCollection A collection of PreCondition objects.
     */
    public function getPreConditions() {
        return $this->preConditions;
    }
    
    /**
     * Set the PreCondition objects to be applied prior to the RouteItem.
     *
     * @param PreConditionCollection $preConditions A collection of PreCondition objects.
     */
    public function setPreConditions(PreConditionCollection $preConditions) {
        $this->preConditions = $preConditions;
    }
    
    /**
     * Add a PreCondition object to be applied prior to the RouteItem.
     *
     * @param PreCondition $preCondition A PreCondition object to be added.
     */
    public function addPreCondition(PreCondition $preCondition) {
        $this->preConditions->attach($preCondition);
    }
    
    /**
     * Add some PreConditon objects to be applied prior to the RouteItem.
     *
     * @param PreConditionCollection $preConditions A collection of PreCondition object.
     */
    public function addPreConditions(PreConditionCollection $preConditions) {
        foreach ($preConditions as $preCondition) {
            $this->addPreCondition($preCondition);
        }
    }
    
    /**
     * Increment the occurence number by 1.
     * 
     */
    public function incrementOccurenceNumber() {
        $this->setOccurence($this->getOccurence() + 1);
    }
    
    /**
     * Get the unique AssessmentSection object bound to the RouteItem. If the RouteItem
     * is bound to multiple assessment sections, the nearest parent of the RouteItem's item's assessment section
     * will be returned.
     * 
     * @return AssessmentSection An AssessmentSection object.
     */
    public function getAssessmentSection() {
        $assessmentSections = $this->getAssessmentSections()->getArrayCopy();
        return $assessmentSections[count($assessmentSections) - 1];
    }
    
    /**
     * Get the AssessmentSection objects bound to the RouteItem.
     * 
     * @return AssessmentSectionCollection An AssessmentSectionCollection object.
     */
    public function getAssessmentSections() {
        return $this->assessmentSections;
    }
    
    /**
     * Get the ItemSessionControl in force for this RouteItem as a RouteItemSessionControl object.
     * 
     * @return RouteItemSessionControl|null The ItemSessionControl in force or null if the RouteItem is not under ItemSessionControl.
     */
    public function getItemSessionControl() {
        if (($isc = $this->getAssessmentItemRef()->getItemSessionControl()) !== null) {
            return RouteItemSessionControl::createFromItemSessionControl($isc, $this->getAssessmentItemRef());
        }
        else {
            $inForce = null;
            
            // Look in assessmentSections.
            foreach ($this->getAssessmentSections() as $section) {
                if (($isc = $section->getItemSessionControl()) !== null) {
                    $inForce = RouteItemSessionControl::createFromItemSessionControl($isc, $section);
                }
            }
            
            // Nothing found in assessmentSections, look in testPart.
            if ($inForce === null && ($isc = $this->getTestPart()->getItemSessionControl()) !== null) {
                $inForce = RouteItemSessionControl::createFromItemSessionControl($isc, $this->getTestPart());    
            }
            
            return $inForce;
        }
    }
    
    /**
     * Get the rubricBlocks related to this routeItem. The returned
     * rubricBlocks are be ordered from the top most to the bottom of 
     * the assessmentTest hierarchy.
     * 
     * @return RubricBlockCollection A collection of RubricBlock objects.
     */
    public function getRubricBlocks() {
        $rubrics = new RubricBlockCollection();
        
        foreach ($this->getAssessmentSections() as $section) {
            $rubrics->merge($section->getRubricBlocks());
        }
        
        return $rubrics;
    }
    
    /**
     * Get the rubricBlockRefs related to this routeItem. The returned
     * rubricBlockRefs are ordered from the top most to the bottom of 
     * the assessmentTest hierarchy.
     * 
     * @return RubricBlockRefCollection A collection of RubricBlockRef objects.
     */
    public function getRubricBlockRefs() {
        $rubrics = new RubricBlockRefCollection();
        
        foreach ($this->getAssessmentSections() as $section) {
            $rubrics->merge($section->getRubricBlockRefs());
        }
        
        return $rubrics;
    }
    
    /**
     * Get the TimeLimits in force for the RouteItem.
     * 
     * @param RouteTimeLimitsCollection $excludeItem Whether or not include the TimeLimits in force for the assessment item of the RouteItem.
     */
    public function getTimeLimits($excludeItem = false) {
        $timeLimits = new RouteTimeLimitsCollection();
        
        if (($tl = $this->getAssessmentTest()->getTimeLimits()) !== null) {
            $timeLimits[] = RouteTimeLimits::createFromTimeLimits($tl, $this->getAssessmentTest());
        }
        
        if (($tl = $this->getTestPart()->getTimeLimits()) !== null) {
            $timeLimits[] = RouteTimeLimits::createFromTimeLimits($tl, $this->getTestPart());
        }
        
        foreach ($this->getAssessmentSections() as $section) {
            if (($tl = $section->getTimeLimits()) !== null) {
                $timeLimits[] = RouteTimeLimits::createFromTimeLimits($tl, $section);
            }
        }
        
        if ($excludeItem === false && ($tl = $this->getAssessmentItemRef()->getTimeLimits()) !== null) {
            $timeLimits[] = RouteTimeLimits::createFromTimeLimits($tl, $this->getAssessmentItemRef());
        }
        
        return $timeLimits;
    }
}