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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               

return array(
    'name' => 'taoBackOffice',
	'label' => 'Back Office',
	'description' => 'Base for back-office extensions',
    'license' => 'GPL-2.0',
    'version' => '0.11',
	'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'tao' => '>=2.8.0'
    ),
	'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoBackOfficeManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoBackOfficeManager', array('ext'=>'taoBackOffice')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#PropertyManagerRole', array('controller' => 'oat\taoBackOffice\controller\Lists')),
    ),
    'install' => array(
        'rdf' => array(
            __DIR__.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'ontology'.DIRECTORY_SEPARATOR.'structures.rdf'
        ),
        'php' => array(
            dirname(__FILE__).'/scripts/install/registerEntryPoint.php'
        )
    ),
    'uninstall' => array(
    ),
    'routes' => array(
        '/taoBackOffice' => 'oat\\taoBackOffice\\controller'
    ),
    'update' => 'oat\taoBackOffice\model\update\Updater',    
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
	    
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'taoBackOffice/',
	    
	    #BASE WWW required by JS
	    'BASE_WWW' => ROOT_URL.'taoBackOffice/views/'
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);