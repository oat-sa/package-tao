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
 */
/**
 * Profiler config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

//default profilers config:
$defaultConfig = array(
	'context' => array(
		'active' => true
	),
	'timer' => array(
		'active' => true,
		'flags' => array()//empty => all (not impl yet)
	),
	'memoryPeak' => array(
		'active' => true
	),
	'countQueries' => array(
		'active' => true
	),
	'slowQueries' => array(
		'active' => true,
		'threshold'=> 100,//ms
	),
	'slowestQueries' => array(
		'active' => true,
		'count' => 3
	),
	'queries' => array(
		'active' => false,
		'count'=> 10,//most used queries?
	)
);

//archivers config:
$ftpArchiver = array(
	'class'			=> 'FtpArchiver',
	'ftp_server'	=> '127.0.0.1',
	'ftp_port'		=> 21,
	'ftp_user'		=> 'taoProfilerFtp',
	'ftp_password'	=> '123456',
	'directory'		=> FILES_PATH.'generis'.DIRECTORY_SEPARATOR.'profiler'.DIRECTORY_SEPARATOR, //(must be writable)
	'file_name'		=> 'mySystemProfile',
	'max_file_size' => 1048576, //(bits)
	'sent_time_interval' => 60, //(seconds)
	'sent_backup'	=> true,
 );
$udpArchiver = array(
	'class'			=> 'UdpArchiver',
	'udp_host'		=> '192.168.2.21',
	'udp_port'		=> 27072
);

/*
return array(
	array_merge(
		array(
			'class'		=> 'LoggerAppender',
			'tag'		=> 'PROFILER'
		), 
		$defaultConfig,
		array()
	)
	,array_merge(
		array(
			'class'			=> 'SystemProfileAppender',
			'local_server_comment'=> "This is Sam's computer",
			'archivers'		=> array(
				$udpArchiver
			)
		), 
		$defaultConfig
	)
);
*/

return array(array(
));
