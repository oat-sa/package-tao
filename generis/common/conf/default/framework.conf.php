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
 * framework config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */


# session namespace
define('SESSION_NAMESPACE', 'ClearFw');

# core directory
define("DIR_CORE"			, VENDOR_PATH . "/ClearFw/core/");

# core helpers directory
define("DIR_CORE_HELPERS"	, DIR_CORE . "helpers/");

# core utils directory
define("DIR_CORE_UTILS"		, DIR_CORE . "util/");

# constants definition
define('HTTP_GET', 		'GET');
define('HTTP_POST', 	'POST');
define('HTTP_PUT', 		'PUT');
define('HTTP_DELETE', 	'DELETE');
define('HTTP_HEAD', 	'HEAD');
