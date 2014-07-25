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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */



/**
 * Generis Configuration
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 * @subpackage conf
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */


# local namespace
define('LOCAL_NAMESPACE', '');

# platform identification
define('GENERIS_INSTANCE_NAME', '');
define('GENERIS_SESSION_NAME', '');

# paths
define('ROOT_PATH', '');
define('ROOT_URL',  '');

# language
define('DEFAULT_LANG', '');
$GLOBALS['default_lang']	= DEFAULT_LANG;

#mode
define('DEBUG_MODE', false);

#application state
define('SYS_READY', true);

# background user: to be used only for system related tasks
define('SYS_USER_LOGIN', 'generis');
define('SYS_USER_PASS', md5('g3n3r1s'));

#the time zone, required since PHP5.3
define("TIME_ZONE", 'Europe/Paris');

# Cache
define('CACHE_MAX_SIZE', 64000);

#if there is a .htaccess with an http auth, used for Curl request or virtual http requests
define('USE_HTTP_AUTH', false);
define('USE_HTTP_USER', '');
define('USE_HTTP_PASS', '');

#generis paths
define('INCLUDES_PATH' , GENERIS_BASE_PATH.DIRECTORY_SEPARATOR.'includes');
define('EXTENSION_PATH' , ROOT_PATH);
define('MANIFEST_NAME' , 'manifest.php');
define('GENERIS_FILES_PATH' , GENERIS_BASE_PATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR);
define('GENERIS_CACHE_PATH', GENERIS_FILES_PATH.'cache'.DIRECTORY_SEPARATOR);

# uri providers ('MicrotimeUriProvider'|'MicrotimeRandUriProvider'|'DatabaseSerialUriProvider')
define('GENERIS_URI_PROVIDER', 'DatabaseSerialUriProvider');

# path to RDFAPI-PHP
define('RDFAPI_INCLUDE_DIR', INCLUDES_PATH.'/rdfapi-php/api/');

# users cache
define('GENERIS_CACHE_USERS_ROLES', true);

# profiling
define('PROFILING', true);
