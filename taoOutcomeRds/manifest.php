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
    'name' => 'taoOutcomeRds',
    'label' => 'extension-tao-outcomerds',
    'description' => 'extension that allows a storage in relational database',
    'license' => 'GPL-2.0',
    'version' => '1.1.0',
    'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'tao' => '>=2.7.0',
        'taoResultServer' => '>=2.6'
    ),
    // for compatibility
    'dependencies' => array('tao', 'taoResultServer'),
    'models' => array(
        'http://www.tao.lu/Ontologies/taoOutcomeRds.rdf#'
    ),
    'install' => array(
        'rdf' => array(
            dirname(__FILE__) . '/scripts/install/taoOutcomeRds.rdf'
        ),
        'php' => array(
            dirname(__FILE__) . '/scripts/install/createTables.php',
            dirname(__FILE__) . '/scripts/install/setDefault.php',
        )
    ),
    'uninstall' => array(
        'php' => array(
            dirname(__FILE__) . '/scripts/uninstall/removeTables.php',
        )
    ),
    'update' => 'oat\\taoOutcomeRds\\scripts\\update\\Updater',
    'autoload' => array(
        'psr-4' => array(
            'oat\\taoOutcomeRds\\' => dirname(__FILE__) . DIRECTORY_SEPARATOR
        )
    ),
    'routes' => array(
        '/taoOutcomeRds' => 'oat\\taoOutcomeRds\\controller'
    ),
    'constants' => array(
        # views directory
        "DIR_VIEWS" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR,
        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL . 'taoOutcomeRds/',
        #BASE WWW required by JS
        'BASE_WWW' => ROOT_URL . 'taoOutcomeRds/views/'
    ),
);
