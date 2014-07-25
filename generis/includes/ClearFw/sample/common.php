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
 * this code is executed before all other script
 */

# constants definition
define('HTTP_GET', 'GET');
define('HTTP_POST', 'POST');
define('HTTP_PUT', 'PUT');
define('HTTP_DELETE', 'DELETE');
define('HTTP_HEAD', 'HEAD');

# all error
error_reporting(E_ALL);

# xdebug custom error reporting
if (function_exists("xdebug_enable"))  {
	xdebug_enable();
}

require_once dirname(__FILE__). "/config.php";
require dirname(__FILE__).'/clearbricks/common/_main.php';

/**
 * @function fw_autoload
 * permits to include classes automatically
 * @param 	string		$pClassName		Name of the class
 */

function fw_autoload($pClassName) {
	if (isset($GLOBALS['classpath']) && is_array($GLOBALS['classpath'])) {
		foreach($GLOBALS['classpath'] as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
    			require_once $path . $pClassName . '.class.php';
    			break;
			}
		}
	}
}

spl_autoload_register("fw_autoload");
spl_autoload_register("Plugin::pluginClassAutoLoad");

?>