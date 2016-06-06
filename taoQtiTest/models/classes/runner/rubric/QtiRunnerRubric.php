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
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoQtiTest\models\runner\rubric;

use oat\taoQtiTest\models\runner\RunnerServiceContext;
use qtism\data\View;

/**
 * Class QtiRunnerRubric
 * @package oat\taoQtiTest\models\runner\rubric
 */
class QtiRunnerRubric implements RunnerRubric
{
    /**
     * Gets the rubrics according to the current session state
     * The content is directly rendered into the page
     * @param RunnerServiceContext $context
     * @return mixed
     */
    public function getRubrics(RunnerServiceContext $context)
    {
        // TODO: make a better implementation for rubrics loading.
        
        /* @var AssessmentTestSession $session */
        $session = $context->getTestSession();
        $compilationDirs = $context->getCompilationDirectory();

        // -- variables used in the included rubric block templates.
        // base path (base URI to be used for resource inclusion).
        $basePathVarName = TAOQTITEST_BASE_PATH_NAME;
        $$basePathVarName = $compilationDirs['public']->getPublicAccessUrl();

        // state name (the variable to access to get the state of the assessmentTestSession).
        $stateName = TAOQTITEST_RENDERING_STATE_NAME;
        $$stateName = $session;

        // views name (the variable to be accessed for the visibility of rubric blocks).
        $viewsName = TAOQTITEST_VIEWS_NAME;
        $$viewsName = array(View::CANDIDATE);

        ob_start();
        foreach ($session->getRoute()->current()->getRubricBlockRefs() as $rubric) {
            include($compilationDirs['private']->getPath() . $rubric->getHref());
        }
        $rubrics = ob_get_contents();
        ob_end_clean();

        return $rubrics;
    }

}
