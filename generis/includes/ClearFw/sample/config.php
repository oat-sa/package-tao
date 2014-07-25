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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * main configuration file
 */

# plugins directory
define("DIR_PLUGIN"			, dirname(__FILE__). "/../plugins/");

# actions directory
define("DIR_ACTIONS"		, dirname(__FILE__). "/../actions/");

# models directory
define("DIR_MODELS"			, dirname(__FILE__). "/../models/");

# plugin directory
define('DIR_PLUGINS'		, dirname(__FILE__).'/../plugins/');

# views directory
define("DIR_VIEWS"			, dirname(__FILE__).'/../views/');

# helpers directory
define("DIR_HELPERS"		, dirname(__FILE__) . "/../helpers/");

# core directory
define("DIR_CORE"			, dirname(__FILE__) . "/core/");

# core helpers directory
define("DIR_CORE_HELPERS"	, DIR_CORE . "helpers/");

# core utils directory
define("DIR_CORE_UTILS"		, DIR_CORE . "util/");

# database config
define("DATABASE_LOGIN"		, "root");
define("DATABASE_PASS"		, "");
define("DATABASE_URL"		, "");
define("DATABASE_DRIVER"	, "");
define("DATABASE_NAME"		, "localhost");

# session namespace
define('SESSION_NAMESPACE', 'PHPFramework');

# default module name
define('DEFAULT_MODULE_NAME', 'AdvancedDefault');

#default action name
define('DEFAULT_ACTION_NAME', 'index');

$GLOBALS['classpath']			= array(DIR_CORE,
										DIR_CORE_UTILS,
										DIR_ACTIONS,
										DIR_MODELS);

# theme directory
$GLOBALS['dir_theme']		= "default/";

# language
$GLOBALS['lang']			= 'en';
?>