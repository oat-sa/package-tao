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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 
 *
 */

$GLOBALS['generis_persistences'] = array(
    'default' => array(
        'driver' => SGBD_DRIVER,
        'host' => DATABASE_URL,
        'dbname' => DATABASE_NAME,
        'user' => DATABASE_LOGIN,
        'password' => DATABASE_PASS
    ),
    'serviceState' => array(
        'driver' => 'phpfile',
    ),
    'config' => array(
        'driver' => 'phpfile',
        'dir' => FILES_PATH . DIRECTORY_SEPARATOR . 'generis' . DIRECTORY_SEPARATOR . 'config',
        'humanReadable' => true
    ),
/*	
    'session' => array(
	    'driver' => 'SqlKvWrapper',
	    'sqlPersistence' => 'default'
	),
*/
/*
    'session' => array(
	    'driver' => 'phpredis',
            'host' => '127.0.0.1',
            'port' => 6379
	),
*/

    'keyValueResult' => array(
	    'driver' => 'phpredis',
            'host' => '127.0.0.1',
            'port' => 6379
	),
/*
 * Used for key value user authentication see authKeyValue
    'authKeyValue' => array(
	    'driver' => 'phpredis',
            'host' => '127.0.0.1',
            'port' => 6379
	),
*/
);