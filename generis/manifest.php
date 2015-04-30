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
 * 				 2013-2014 (update and modification) Open Assessment Technologies SA;
 *
 */

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
return array(
	'name' => 'generis',
	'label' => 'Core',
    'description' => 'Core extension, provide the low level framework and an API to manage ontologies',
    'license' => 'GPL-2.0',
    'version' => '2.7.5',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'requires' 	=> array(),
	'models' => array(
		'http://www.w3.org/1999/02/22-rdf-syntax-ns',
		'http://www.w3.org/2000/01/rdf-schema',
		'http://www.tao.lu/datatypes/WidgetDefinitions.rdf',
		'http://www.tao.lu/middleware/Rules.rdf',
		'http://www.tao.lu/Ontologies/generis.rdf'
	),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns', 'file' => dirname(__FILE__). '/core/ontology/22-rdf-syntax-ns.rdf'),
				array('ns' => 'http://www.w3.org/2000/01/rdf-schema', 'file' => dirname(__FILE__). '/core/ontology/rdf-schema.rdf'),
				array('ns' => 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf', 'file' => dirname(__FILE__). '/core/ontology/widgetdefinitions.rdf'),
				array('ns' => 'http://www.tao.lu/middleware/Rules.rdf', 'file' => dirname(__FILE__). '/core/ontology/rules.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/generis.rdf', 'file' => dirname(__FILE__). '/core/ontology/generis.rdf'),
		),
	    'checks' => array(
				//array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_generis_data', 'location' =>  'generis/data', 'rights' => 'rw'))
        )
	),
	'update' => 'oat\\generis\\scripts\\update\\Updater',
	'optimizableClasses' => array(
		'http://www.tao.lu/Ontologies/generis.rdf#User',
		'http://www.tao.lu/Ontologies/generis.rdf#ClassRole'
	),
	'optimizableProperties' => array(
		'http://www.tao.lu/Ontologies/generis.rdf#login',
		'http://www.tao.lu/Ontologies/generis.rdf#password'
	)
);