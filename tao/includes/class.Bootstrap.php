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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */
use oat\tao\model\mvc\Bootstrap as MvcBootstrap;
/**
 * The generis extension loader is included there ONCE!
 *  1. Load and initialize the API and so the database
 *  2. Initialize the autoloaders
 *  3. Initialize the extension manager
 */
require_once __DIR__ . '/../../vendor/autoload.php';
/**
 * The Bootstrap Class enables you to drive the application flow for a given extenstion.
 * A bootstrap instance initialize the context and starts all the services:
 * 	- session
 *  - database
 *  - user
 *  - i18n
 *
 * And it's used to disptach the Control Loop
 *  - control the platform status (redirect to the maintenance page if it is required)
 *  - dispatch to the convenient action
 *  - control code exceptions
 *
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @package tao
 * @example
 * <code>
 *  $bootStrap = new BootStrap('tao');	//create the Bootstrap instance
 *  $bootStrap->start();				//start all the services
 *  $bootStrap->dispatch();				//dispatch the http request into the control loop
 * </code>
 */
class Bootstrap extends MvcBootstrap {
    
    public function __construct($extName) {
        $configFile = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'generis.conf.php';
        parent::__construct($configFile);
        
        // load extension constants
        common_ext_ExtensionsManager::singleton()->getExtensionById($extName);
    }
    
}