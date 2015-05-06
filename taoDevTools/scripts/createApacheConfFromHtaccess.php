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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author lionel
 * @license GPLv2
 * @package package_name
 * @subpackage 
 *
 */

require_once dirname(__FILE__) . '/../includes/raw_start.php';

$params = array(
    'min' => 3,
    'parameters' => array(
        array(
            'name' 			=> 'serverName',
            'type' 			=> 'string',
            'shortcut'      => 's',
            'description'	=> 'server Name',
            'required'		=> true,
        ),
        array(
            'name' 			=> 'documentRoot',
            'type' 			=> 'string',
            'shortcut'      => 'd',
            'description'	=> 'documentRoot ',
            'required'		=> true,
        ),
        array(
            'name' 			=> 'target',
            'type' 			=> 'string',
            'shortcut'      => 't',
            'description'	=> 'target file must end in .conf ',
            'required'		=> true,
        ),
        array(
            'name' 			=> 'tpl',
            'type' 			=> 'string',
            'description'	=> 'apache site conf template file ',
            'required'		=> false,
        ),

    )
);

new taoDevTools_scripts_ApacheConfCreator($params);