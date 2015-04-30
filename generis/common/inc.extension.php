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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Generis Object Oriented API - common\inc.extension.php
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author lionel.lecaque@tudor.lu
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

/*
 * to use it in command line
 */
if(PHP_SAPI == 'cli'){
	$_SERVER['HTTP_HOST'] = 'http://localhost';
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__).'/../..';
}
//load config
require_once  dirname(__FILE__). '/class.Config.php';
common_Config::load();

//load constants
require_once  dirname(__FILE__). '/constants.php';

//set the time zone
if(function_exists("date_default_timezone_set") && defined('TIME_ZONE')){
	date_default_timezone_set(TIME_ZONE);
}

// init legacy autoloader for non composer packages
require_once ROOT_PATH.'generis/common/legacy/class.LegacyAutoLoader.php';
if(!defined('GENERIS_BASE_PATH')){
    define( 'GENERIS_BASE_PATH' , ROOT_PATH.'generis' );
}
common_legacy_LegacyAutoLoader::register();

// autoloader
require_once ROOT_PATH.'vendor/autoload.php';
