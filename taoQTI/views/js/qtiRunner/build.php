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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
$dirname = dirname(__FILE__);
include_once($dirname."/../../../../tao/lib/jstools/minify.php");

//minimify QTI Javascript sources using JSMin
$jsFiles = array (
    $dirname.'/lib/mustache/mustache.js',
	$dirname.'/src/class.Renderer.js',
	$dirname.'/src/QtiRunner.js',
);
minifyJSFiles($jsFiles, $dirname."/qtiRunner.min.js");
