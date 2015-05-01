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

use qtism\data\rules\Selection;
use qtism\data\AssessmentSection;

/**
 * The AbstractSelector aims at implementing the behaviour described
 * by the QTI selection class.
 * 
 * From IMS QTI:
 * 
 * The selection class specifies the rules used to select the child elements of a 
 * section for each test session. If no selection rules are given we assume that 
 * all elements are to be selected.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @link http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#section10093 QTI Test Structure
 *
 */
abstract class AbstractSelection {
    
    /**
     * The AssessmentSection object on which the selection
     * must occur.
     * 
     * @var AssessmentSection
     */
    private $assessmentSection;
    
    /**
     * The SelectableRoute objects that are selectable for the selection to be performed.
     * 
     * @var SelectableRouteCollection
     */
    private $selectableRoutes;
    
    /**
     * Create a new AbstractSelector object.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object which represents the QTI Data Model assessmentSection on which the selection occurs.
     * @param SelectableRouteCollection $selectableRoutes The collection of Routes that are selectable for this selection.
     */
    public function __construct(AssessmentSection $assessmentSection, SelectableRouteCollection $selectableRoutes) {
        $this->setAssessmentSection($assessmentSection);
        $this->setSelectableRoutes($selectableRoutes);
    }
    
    /**
     * Get the AssessmentSection object on which the selection
     * will occur.
     * 
     * @return AssessmentSection An AssessmentSection object.
     */
    public function getAssessmentSection() {
        return $this->assessmentSection;
    }
    
    /**
     * Set the AssessmentSection object on which the selection will occur.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object.
     */
    public function setAssessmentSection(AssessmentSection $assessmentSection) {
        $this->assessmentSection = $assessmentSection;
    }
    
    /**
     * Get the collection of Route objects that are selectable for the selection to be performed.
     * 
     * @return SelectableRouteCollection A collection of Route objects.
     */
    public function getSelectableRoutes() {
        return $this->selectableRoutes;
    }
    
    /**
     * Set the collection of Route objects that are selectable for the selection to be performed.
     * 
     * @param SelectableRouteCollection $selectableRoutes
     */
    public function setSelectableRoutes(SelectableRouteCollection $selectableRoutes) {
        $this->selectableRoutes = $selectableRoutes;
    }
    
    /**
     * Select the direct children components of the AssessmentSection on which the selection must be applied.
     * 
     * @return SelectableRouteCollection A collection of selected SelectableRoute object describing the selection.
     * @throws SelectionException
     */
    abstract public function select();
}