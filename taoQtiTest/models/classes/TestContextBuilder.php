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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoQtiTest\models;

use qtism\runtime\tests\AssessmentTestSession;

/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

/**
 * Interface TestContextBuilder.
 * 
 * Provides a way to extend the assessment test context.
 * 
 * @package oat\taoQtiTest\models
 */
interface TestContextBuilder
{
    /**
     * Extends an already built context
     * 
     * @param array $context A reference to the context to extend
     * @param AssessmentTestSession $session A given AssessmentTestSession object.
     * @param array $testMeta An associative array containing meta-data about the test definition taken by the candidate.
     * @param string $qtiTestDefinitionUri The URI of a reference to an Assessment Test definition in the knowledge base.
     * @param string $qtiTestCompilationUri The Uri of a reference to an Assessment Test compilation in the knowledge base.
     * @param string $standalone
     * @param string $compilationDirs An array containing respectively the private and public compilation directories.
     * @return array The context of the candidate session.
     */
    public function extendAssessmentTestContext(array &$context, AssessmentTestSession $session, array $testMeta, $qtiTestDefinitionUri, $qtiTestCompilationUri, $standalone, $compilationDirs);
}
