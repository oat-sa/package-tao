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
/**
 * Log config
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

#	 trace_level 	= 0;
#	 debug_level 	= 1;
#	 info_level 	= 2;
#	 warning_level 	= 3;
#	 error_level	= 4;
#	 fatal_level 	= 5;
$GLOBALS['COMMON_LOGGER_CONFIG'] = array(
/*
 array(
 		'class'			=> 'SingleFileAppender',
 		'threshold'		=> 4 ,
 		'file'			=> dirname(__FILE__).'/../../log/error.txt',
 		'format'		=> '%m'
 ),
array(
		'class'			=> 'ArchiveFileAppender',
		'mask'			=> 62 , // 111110
		'tags'			=> array('GENERIS', 'TAO')
		'file'			=> '/var/log/tao/debug.txt',
		'directory'		=> '/var/log/tao/',
		'max_file_size'	=> 10000000
),
		array(
				'class'			=> 'UDPAppender',
				'host'			=> '127.0.0.1',
				'port'			=> 5775,
				'threshold'		=> 1
		)
		/**/
);