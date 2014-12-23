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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author lionel
 * @license GPLv2
 * @package package_name
 * @subpackage 
 *
 */

$root_dir = __DIR__ . '/../../';

//load config
require_once  $root_dir. 'generis/common/class.Config.php';
common_Config::load();

//load constants
require_once  ROOT_PATH. 'generis/common/constants.php';

require_once ROOT_PATH.'generis/common/legacy/class.LegacyAutoLoader.php';
if (!defined('GENERIS_BASE_PATH')){
    define( 'GENERIS_BASE_PATH' , ROOT_PATH.'generis' );
}
common_legacy_LegacyAutoLoader::register();


// autoloader
require_once ROOT_PATH.'vendor/autoload.php';