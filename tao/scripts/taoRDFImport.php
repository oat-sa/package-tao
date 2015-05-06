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

new tao_scripts_TaoRDFImport(array(
	'min' => 3,
	'parameters' => array(
		array(
			'name' => 'verbose',
			'type' => 'boolean',
			'shortcut' => 'v',
			'description' => 'Verbose mode'
		),
		array(
			'name' => 'user',
			'type' => 'string',
			'shortcut' => 'u',
			'description' => 'Generis User (must be a TAO Manager)'
		),
		array(
			'name' => 'password',
			'type' => 'string',
			'shortcut' => 'p',
			'description' => 'Generis Password'
		),
		array(
			'name' => 'model',
			'type' => 'string',
			'shortcut' => 'm',
			'description' => 'The target model URI. If not provided, the target model will xml:base. If no xml:base is found, the local model is used. If provided, it will override the value of xml:base.'
		),
		array(
			'name' => 'input',
			'type' => 'file',
			'shortcut' => 'i',
			'description' => 'The canonical path to the RDF input file to import'
		)
	)
));
?>