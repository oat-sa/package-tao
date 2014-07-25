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
require_once dirname(__FILE__) . '/../includes/raw_start.php';

new taoItems_scripts_MigrateLegacyItems(array(
	'min'		=> 1,
	'required'	=> array(
		array('input'),
		array('input', 'uri'),
		array('input', 'addResource'),
		array('input', 'output', 'pack')
	),
	'parameters' => array(
		array(
			'name' 			=> 'input',
			'type' 			=> 'file',
			'shortcut'		=> 'i',
			'required'		=> true,
			'description'	=> 'the intput file containing the legacy item'
		),
		array(
			'name' 			=> 'output',
			'type' 			=> 'dir',
			'shortcut'		=> 'o',
			'description'	=> 'the output directory to save the new item, by default the same than the input'
		),
		array(
			'name' 			=> 'uri',
			'type' 			=> 'string',
			'shortcut'		=> 'u',
			'description' 	=> 'the uri of an existing resource to bind the item content'
		),
		array(
			'name' 			=> 'addResource',
			'type' 			=> 'boolean',
			'shortcut'		=> 'a',
			'description' 	=> 'create a new resource to bind the item content'
		),
		array(
			'name' 			=> 'class',
			'type' 			=> 'string',
			'shortcut'		=> 'c',
			'description' 	=> 'the RDFS class where to add the resource'
		),
		array(
			'name' 			=> 'pack',
			'type' 			=> 'boolean',
			'shortcut'		=> 'p',
			'description' 	=> 'Create a package'
		)
	)
));
?>