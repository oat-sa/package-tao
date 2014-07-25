<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */


namespace qtism\data;

use qtism\data\content\RubricBlockRefCollection;

/**
 * An extension of the assessmentSection QTI class aiming at storing
 * references to external rubricBlock definitions.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExtendedAssessmentSection extends AssessmentSection {
    
    /**
     * The rubrickBlockRefs components referenced by the 
     * extendedAssessmentSection.
     * 
     * @var RubricBlockRefCollection
     * @qtism-bean-property
     */
    private $rubricBlockRefs;
    
    /**
     * Create a new ExtendedAssessmentSection object.
     * 
     * @param string $identifier A QTI identifier.
     * @param string $title A title.
     * @param boolean $visible The visibility of the section.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct($identifier, $title, $visible) {
        parent::__construct($identifier, $title, $visible);
        $this->setRubricBlockRefs(new RubricBlockRefCollection());
    }
    
    /**
     * Set the RubricBlockRef objects held by the section.
     * 
     * @param RubricBlockRefCollection $rubricBlockRefs A collection of RubricBlockRef objects.
     */
    public function setRubricBlockRefs(RubricBlockRefCollection $rubricBlockRefs) {
        $this->rubricBlockRefs = $rubricBlockRefs;
    }
    
    /**
     * Get the RubricBlockRef objects held by the section.
     * 
     * @return RubricBlockRefCollection A collection of RubricBlockRef objects.
     */
    public function getRubricBlockRefs() {
        return $this->rubricBlockRefs;
    }
    
    /**
     * Create a new ExtendedAssessmentSection object from an existing
     * AssessmentSection object.
     * 
     * @param AssessmentSection $assessmentSection An AssessmentSection object.
     * @return ExtendedAssessmentSection An ExtendedAssessmentSection object built from $assessmentSection.
     */
    public static function createFromAssessmentSection(AssessmentSection $assessmentSection) {
        $extended = new static($assessmentSection->getIdentifier(), $assessmentSection->getTitle(), $assessmentSection->isVisible());
        $extended->setKeepTogether($assessmentSection->mustKeepTogether());
        $extended->setSelection($assessmentSection->getSelection());
        $extended->setOrdering($assessmentSection->getOrdering());
        $extended->setRubricBlocks($assessmentSection->getRubricBlocks());
        $extended->setSectionParts($assessmentSection->getSectionParts());
        $extended->setRequired($assessmentSection->isRequired());
        $extended->setPreConditions($assessmentSection->getPreConditions());
        $extended->setBranchRules($assessmentSection->getBranchRules());
        $extended->setItemSessionControl($assessmentSection->getItemSessionControl());
        $extended->setTimeLimits($assessmentSection->getTimeLimits());
        
        return $extended;
    }
    
    public function getComponents() {
        $parentComponents = parent::getComponents();
        $parentComponents->merge($this->getRubricBlockRefs());
        return $parentComponents;
    }
}