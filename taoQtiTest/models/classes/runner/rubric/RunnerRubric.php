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

/**
 * Interface RunnerRubric
 * @package oat\taoQtiTest\models\runner\rubrick
 */
interface RunnerRubric
{
    /**
     * Gets the rubrics according to the current session state
     * @param RunnerServiceContext $context
     * @return mixed
     */
    public function getRubrics(RunnerServiceContext $context);
}
