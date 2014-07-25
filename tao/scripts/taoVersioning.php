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
?>
<?php
require_once dirname(__FILE__) .'/../includes/raw_start.php';

// this script is deprecated and only used by the buildServer

new tao_scripts_TaoVersioning(array(
	'min'		=> 5,
	'parameters' => array(
		array(
			'name' 			=> 'enable',
			'type' 			=> 'boolean',
			'shortcut'		=> 'e',
			'description'	=> 'Enable tao versioning',
		),
		array(
			'name' 			=> 'type',
			'type' 			=> 'string',
			'shortcut'		=> 't',
			'description'	=> 'Type of repository (svn)'
		),
		array(
			'name' 			=> 'login',
			'type' 			=> 'string',
			'shortcut'		=> 'u',
			'description'	=> 'Login to access to the remote repository',
			'required'		=> true
		),
		array(
			'name' 			=> 'password',
			'type' 			=> 'string',
			'shortcut'		=> 'p',
			'description'	=> 'Password to access to the remote repository',
			'required'		=> true
		),
		array(
			'name' 			=> 'url',
			'type' 			=> 'string',
			'description'	=> 'Url of the remote repository',
			'required'		=> true
		),
		array(
			'name' 			=> 'path',
			'type' 			=> 'string',
			'description'	=> 'Local location of the repository',
			'required'		=> true
		)
	)
));
?>
