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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
    'name' => 'filemanager',
	'label' => 'Media manager',
	'description' => 'Mediamanager manages media-files that are used in several locations',
    'license' => 'GPL-2.0',
    'version' => '2.6',
	'author' => 'CRP Henri Tudor',
	'requires' => array(
	    'tao' => '2.6'
    ),
	'models' => array(
			'http://www.tao.lu/Ontologies/filemanager.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/filemanager.rdf'
		),
	    'php' => array(
	        dirname(__FILE__). '/install/script/addLocalSource.php'
        ),
		'checks' => array(
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'filemanager_extension_gd', 'name' => 'gd')),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_filemanager_views_data', 'location' => 'filemanager/views/data', 'rights' => 'rw'))
		)
	),
    'local'	=> array(
        'php'	=> array(
            dirname(__FILE__).'/install/script/addExamples.php'
        )
    ),
	'managementRole' => 'http://www.tao.lu/Ontologies/filemanager.rdf#MediaManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/filemanager.rdf#MediaManagerRole', array('ext'=>'filemanager')),
    ),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Browser',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . 'filemanager/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . 'filemanager/views/',
	 
	
		#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . 'tao/views/',
	
		# Max file size to upload
		'UPLOAD_MAX_SIZE'		=> '83886080',//10MB, to allow some multimedia file 
	
		# Allowed media for upload
		'allowed_media'			=> array(
			'application/ogg',						//OGG
			'audio/ogg',
			'video/ogg',
			'video/ogv',
			'application/pdf',						//PDF
			'application/x-shockwave-flash',		//Flash
			'application/x-subrip',					//Subtitles
			'audio/mpeg',							//MP3 MPEG
			'audio/x-ms-wma',						//Windows Media Audio
			'audio/vnd.rn-realaudio',				//RealAudio
			'audio/x-wav',							//WAV
			'image/gif',							//GIF 
			'image/jpeg',							//JPEG
			'image/png',							//PNG
			'image/tiff',							//TIFF
			'image/svg+xml',						//SVG
			'image/bmp',							//BMP
			'image/vnd.microsoft.icon',				//ICO 
			'video/mpeg',							//MPEG-1
			'video/mp4',							//MP4
			'video/webm',							//webm
			'video/quicktime',						//QuickTime
			'video/x-ms-wmv',						//Windows Media Video
			'video/x-msvideo',						//AVI
			'video/x-flv'							//Flash Video
		
		),
		// unused
		'allowed_file'			=> array(
			'application/pdf',
			'image/vnd.adobe.photoshop',
			'application/postscript',
			'application/msword',
			'application/rtf',
			'application/vnd.ms-excel',
			'application/vnd.ms-powerpoint',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.oasis.opendocument.spreadsheet',
			'text/xml',
		    'text/csv',
			'text/plain',
			'text/richtext',
			'text/rtf'
		)
	)
);