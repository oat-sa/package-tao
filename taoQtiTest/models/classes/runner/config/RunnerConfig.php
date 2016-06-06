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

namespace oat\taoQtiTest\models\runner\config;

use oat\taoQtiTest\models\runner\RunnerServiceContext;

/**
 * Interface RunnerOptions
 * @package oat\taoQtiTest\models\runner\options
 */
interface RunnerConfig
{
    /**
     * Returns the config related to the runner
     * @return mixed
     */
    public function getConfig();
    
    /**
     * Returns the value of a config entry
     * @param string $name
     * @return mixed
     */
    public function getConfigValue($name);
    
    /**
     * Returns the options related to the current test context
     * @param RunnerServiceContext $context The test context
     * @return mixed
     */
    public function getOptions(RunnerServiceContext $context);
}
