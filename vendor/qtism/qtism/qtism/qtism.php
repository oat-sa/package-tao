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
 * The QtiStateMachine PSR-O autoloader.
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 */
namespace qtism;

/**
 * Use this autoloader if you do not have a dependency management
 * system such as Composer.
 * 
 * @param string $class
 */
function qtism_autoload($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $class . '.php';
	
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register(__NAMESPACE__ . '\\qtism_autoload');