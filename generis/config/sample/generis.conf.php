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

#mode
define('DEBUG_MODE', false);

#application state
define('SYS_READY', true);

#the time zone, required since PHP5.3
define('TIME_ZONE', 'UTC');

# Passsword Hash Preferences
define('PASSWORD_HASH_ALGORITHM', 'sha256');
define('PASSWORD_HASH_SALT_LENGTH', 10);


#if there is a .htaccess with an http auth, used for Curl request or virtual http requests
define('USE_HTTP_AUTH', false);
define('USE_HTTP_USER', '');
define('USE_HTTP_PASS', '');

#generis paths
define('VENDOR_PATH' , ROOT_PATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR);
define('EXTENSION_PATH' , ROOT_PATH);
define('FILES_PATH' , '');
define('GENERIS_CACHE_PATH', FILES_PATH.'generis'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);

#path to read configs from
define('CONFIG_PATH', ROOT_PATH.'config/');

# users cache
define('GENERIS_CACHE_USERS_ROLES', true);
