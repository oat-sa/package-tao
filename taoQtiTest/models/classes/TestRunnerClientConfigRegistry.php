<?php
/*
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoQtiTest\models;

use oat\tao\model\ClientLibConfigRegistry;
/**
 * Description of TestRunnerConfigRegistry
 *
 * @author sam
 */
class TestRunnerClientConfigRegistry extends ClientLibConfigRegistry
{

    const AMD = 'taoQtiTest/controller/runtime/testRunner';

    /**
     * Register a qti tools in the client lib config registry
     * 
     * @param string $name
     * @param array $toolConfig
     */
    public function registerQtiTools($name, $toolConfig){
        $newConfig = array('qtiTools' => array());
        //@todo validate tool config structure before registration
        $newConfig['qtiTools'][$name] = $toolConfig;
        $this->register(self::AMD, $newConfig);
    }
}