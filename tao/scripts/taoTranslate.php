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

new tao_scripts_TaoTranslate(array(
	'min' => 1,
	'parameters' => array(
		array(
			'name' => 'verbose',
			'type' => 'boolean',
			'shortcut' => 'v',
			'description' => 'Verbose mode'
		),
		array(
			'name' => 'action',
			'type' => 'string',
			'shortcut' => 'a',
			'description' => 'Action to undertake. Available actions are create, update, updateall, delete, deleteall, enable, disable, compile, compileall'
		),
		array(
			'name' => 'language',
			'type' => 'string',
			'shortcut' => 'l',
			'description' => 'A language identifier like en-US, be-NL, fr, ...'
		),
		array(
			'name' => 'output',
			'type' => 'string',
			'shortcut' => 'o',
			'description' => 'An output directory (PO and JS files)'
		),
		array(
			'name' => 'input',
			'type' => 'string',
			'shortcut' => 'i',
			'description' => 'An input directory (source code)'
		),
		array(
			'name' => 'build',
			'type' => 'boolean',
			'shortcut' => 'b',
			'description' => 'Sets if the language has to be built when created or not'
		),
		array(
			'name' => 'force',
			'type' => 'boolean',
			'shortcut' => 'f',
			'description' => 'Force to erase an existing language if you use the create action'
		),
		array(
			'name' => 'extension',
			'type' => 'string',
			'shortcut' => 'e',
			'description' => 'The TAO extension for which the script will apply'
		),
        array(
            'name' => 'languageLabel',
            'type' => 'string',
            'shortcut' => 'll',
            'description' => 'Language label to use when creating a new language'
        ),
		array(
			'name' => 'targetLanguage',
			'type' => 'string',
			'shortcut' => 'tl',
			'description' => 'Target language code when you change the code of a locale'	
		),
        array(
            'name' => 'user',
            'type' => 'string',
            'shortcut' => 'u',
            'description' => 'TAO user (TaoManager Role)'
        ),
        array(
            'name' => 'password',
            'type' => 'string',
            'shortcut' => 'p',
            'description' => 'TAO password'
        )
	)
));
?>
