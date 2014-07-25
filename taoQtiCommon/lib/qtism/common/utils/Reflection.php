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
 * @subpackage
 *
 */

namespace qtism\common\utils;

use \ReflectionClass;
use \ReflectionException;

/**
 * A utility class focusing on Reflection.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Reflection {
    
    /**
     * An abstraction of the call to ReflectionClass::newInstanceArgs. The main
     * goal of this method is to avoid to encounter the issue with empty $args
     * argument described at: http://www.php.net/manual/en/reflectionclass.newinstanceargs.php#99517
     * 
     * @param ReflectionClass $class
     * @param unknown_type $args
     * @return mixed An instance of $class
     * @throws ReflectionException
     * @see http://www.php.net/manual/en/reflectionclass.newinstanceargs.php#99517
     */
    public static function newInstance(ReflectionClass $class, $args = array()) {
        if (empty($args) === true) {
            $fqName = $class->getName();
            return new $fqName();
        }
        else {
            return $class->newInstanceArgs($args);
        }
    }
}