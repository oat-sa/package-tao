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
    'name' => 'taoDacSimple',
	'label' => 'extension-tao-dac-simple',
	'description' => 'extension that allows admin to give access to some resources to other people',
    'license' => 'GPL-2.0',
    'version' => '1.2.2',
	'author' => 'Open Assessment Technologies SA',
	'requires' => array(
	   'taoBackOffice' => '>=0.9'
    ),
	// for compatibility
	'dependencies' => array('tao', 'taoItems'),
	'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoDacSimpleManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoDacSimpleManager', array('ext'=>'taoDacSimple')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', array('controller' => 'oat\taoDacSimple\controller\AdminAccessController')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole', array('controller' => 'oat\taoDacSimple\controller\AdminAccessController'))
    ),
    'install' => array(
        'php' => array(
            dirname(__FILE__).'/scripts/install/setDataAccess.php',
            dirname(__FILE__).'/scripts/install/registerAdmin.php'
        )
    ),
    'uninstall' => array(
        'php' => array(
            dirname(__FILE__).'/scripts/uninstall/unsetDataAccess.php',
        )
    ),
    'update' => 'oat\\taoDacSimple\\scripts\\update\\Updater',
    'routes' => array(
        '/taoDacSimple' => 'oat\\taoDacSimple\\controller'
    ),
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,

		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'taoDacSimple/',

        #BASE WWW the web resources path
        'BASE_WWW' => ROOT_URL.'taoDacSimple/views/'
	)
);
