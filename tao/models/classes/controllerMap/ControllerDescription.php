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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\model\controllerMap;

use ReflectionClass;
use ReflectionMethod;

/**
 * Description of a Tao Controller
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ControllerDescription
{
    private static $BLACK_LIST = array('forward', 'redirect', 'forwardUrl', 'setView');
    /**
     * Reflection of the controller
     * 
     * @var ReflectionClass
     */
    private $class;
    
    /**
     * Create a new lazy parsing controller description
     * 
     * @param ReflectionClass $controllerClass
     */
    public function __construct(ReflectionClass $controllerClass) {
        $this->class = $controllerClass;
    }
    
    /**
     * Returns the class name of the controller
     * 
     * @return string
     */
    public function getClassName() {
        return $this->class->getName();
    }
    
    /**
     * Returns ann array of ActionDescription objects
     *
     * @return array
     */
    public function getActions() {
        $actions = array();
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
            if (!$m->isConstructor() && !$m->isDestructor() && is_subclass_of($m->class, 'Module') && !in_array($m->name, self::$BLACK_LIST)) {
                $actions[] = new ActionDescription($m);
            }
        }
        return $actions;
    }
    
}