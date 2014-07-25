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
 * Generis Object Oriented API - common\config.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 * @subpackage common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

#define path
define('GENERIS_BASE_PATH' , realpath(dirname(__FILE__).'/../'));

# generis
include_once dirname(__FILE__).'/conf/generis.conf.php';

# database
include_once dirname(__FILE__).'/conf/db.conf.php';

$defaultIterator =  new DirectoryIterator(dirname(__FILE__).'/conf/default');

foreach($defaultIterator as $fileinfo){
	if(!$fileinfo->isDot() && strpos( $fileinfo->getFilename(),'.conf.php')>0){
		$conf = $fileinfo->getFilename();
		if(file_exists(dirname(__FILE__).'/conf/'.$conf)){
			include_once dirname(__FILE__).'/conf/'.$conf;
		}
		else{
			include_once dirname(__FILE__).'/conf/default/'.$conf;
		}
	}
}


?>