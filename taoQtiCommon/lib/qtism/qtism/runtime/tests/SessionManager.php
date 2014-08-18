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

use qtism\data\AssessmentTest;
use qtism\data\IAssessmentItem;

/**
 * An SessionManager implementation that creates default AssessmentTestSession and
 * AssessmentItemSession objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SessionManager extends AbstractSessionManager {
    
    /**
     * Instantiates an AssessmentTestSession with the default implementation.
     * 
     * @param AssessmentTest $test
     * @param Route An optional route to be set. If not provided, the default instantiation process occurs.
     * @return AssessmentTestSession
     */
    protected function instantiateAssessmentTestSession(AssessmentTest $test, Route $route) {
        return new AssessmentTestSession($test, $this, $route);
    }
    
    /**
     * Instantiates an AssessmentItemSession with the default implementation.
     * 
     * @param IAssessmentItem $assessmentItem
     * @param integer $navigationMode A value from the NavigationMode enumeration.
     * @param integer $submissionMode A value from the SubmissionMode enumeration.
     * @return AssessmentItemSession
     */
    protected function instantiateAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode, $submissionMode) {
        return new AssessmentItemSession($assessmentItem, $this, $navigationMode, $submissionMode);
    }
}