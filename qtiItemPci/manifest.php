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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               

return array(
    'name' => 'qtiItemPci',
	'label' => 'QTI Portable Custom Interaction',
	'description' => '',
    'license' => 'GPL-2.0',
    'version' => '0.1',
	'author' => 'Open Assessment Technologies SA',
	'requires' => array('taoQtiItem' => '>=2.7.0'),
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#qtiItemPciManager', array('ext'=>'qtiItemPci')),
    ),
    'install' => array(
        'rdf' => array(
			dirname(__FILE__). '/install/ontology/registry.rdf',
		    dirname(__FILE__). '/install/ontology/role.rdf'
		),
        'php'	=> array(
			dirname(__FILE__).'/scripts/install/addHook.php'
		)
    ),
    'uninstall' => array(
    ),
    'autoload' => array (
        'psr-4' => array(
            'oat\\qtiItemPci\\' => dirname(__FILE__).DIRECTORY_SEPARATOR
        )
    ),
    'routes' => array(
        '/qtiItemPci' => 'oat\\qtiItemPci\\controller'
    ),    
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
	    
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'qtiItemPci/',
        
        #BASE WWW the web resources path
        'BASE_WWW' => ROOT_URL.'qtiItemPci/views/'
	)
);