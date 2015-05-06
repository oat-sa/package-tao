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

use qtism\data\rules\Ordering;
use qtism\data\AssessmentSection;

/**
 * The AbstractOrdering class aims at implementing the behaviour described
 * by the QTI ordering class.
 * 
 * From IMS QTI:
 * 
 * The ordering class specifies the rule used to arrange the child elements of a section 
 * following selection. If no ordering rule is given we assume that the elements are to be 
 * ordered in the order in which they are defined.
 * 
 * If ndlr: shuffle is true causes the order of the child elements to be randomized, if 
 * false uses the order in which the child elements are defined.
 * 
 * A sub-section is always treated as a single block for selection but the way it is treated
 * when shuffling depends on its visibility. A visible sub-section is always treated as a 
 * single block but an invisible sub-section is only treated as a single block if its 
 * keepTogether attribute is true. Otherwise, the child elements of the invisible 
 * sub-section are mixed into the parent's selection prior to shuffling.
 * 
 * The ordering class also provides an opportunity for extensions to this specification to 
 * include support for more complex ordering algorithms.
 * 
 * The selection and ordering rules define a sequence of items for each instance of the test.
 * The sequence starts with the first item of the first section of the first test part and 
 * continues through to the last item of the last section of the last test part. This 
 * sequence is constant throughout the test. Normally this is the logical sequence 
 * perceived by the candidate but the use of preConditions and/or branchRules can affect the 
 * specific path taken.
 * 
 * The use of selection with replacement enables two or more instances of an item referred
 * to by the same assessmentItemRef to appear in the sequence of items for a test. It is 
 * therefore an error to make such an item the target of a branchRule. Furthermore, when 
 * reporting test results the sequence number of each item must also be reported to avoid 
 * ambiguity. See Results Reporting.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @link http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#section10093 QTI Test Structure
 *
 */
abstract class AbstractOrdering {
    
    /**
     * The AssessmentSection object on which the ordering
     * will occur.
     * 
     * @var AssessmentSection
     */
    private $assessmentSection;
    
    /**
     * The SelectableRoute objects that can be ordered.
     * 
     * @var SelectableRouteCollection
     */
    private $selectableRoutes;
    
    /**
     * Create a new AbstractOrdering object.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object which represents the QTI Data Model assessmentSection on which the ordering will occur.
     * @param SelectableRouteCollection $selectableRoutes The collection of Routes that might be ordered.
     */
    public function __construct(AssessmentSection $assessmentSection, SelectableRouteCollection $selectableRoutes) {
        $this->setAssessmentSection($assessmentSection);
        $this->setSelectableRoutes($selectableRoutes);
    }
    
    /**
     * Get the AssessmentSection object on which the ordering
     * will occur.
     * 
     * @return AssessmentSection An AssessmentSection object.
     */
    public function getAssessmentSection() {
        return $this->assessmentSection;
    }
    
    /**
     * Set the AssessmentSection object on which the ordering will occur.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object.
     */
    public function setAssessmentSection(AssessmentSection $assessmentSection) {
        $this->assessmentSection = $assessmentSection;
    }
    
    /**
     * Get the collection of Route objects that are selectable for the ordering.
     * 
     * @return SelectableRouteCollection A collection of Route objects.
     */
    public function getSelectableRoutes() {
        return $this->selectableRoutes;
    }
    
    /**
     * Set the collection of Route objects that are selectable for ordering.
     * 
     * @param SelectableRouteCollection $selectableRoutes A collection of Route objects.
     */
    public function setSelectableRoutes(SelectableRouteCollection $selectableRoutes) {
        $this->selectableRoutes = $selectableRoutes;
    }

    /**
     * Apply the ordering algorithm.
     * 
     * @return SelectableRouteCollection A collection of SelectableRoute object that were ordered accordingly.
     * @throws OrderingException If an error occurs while ordering the child elements of the target AssessmentSection.
     */
    abstract public function order();
}