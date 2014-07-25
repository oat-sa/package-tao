<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data;

use \InvalidArgumentException;

interface Document {
	
	/**
	 * Set the QTI version of the document.
	 *
	 * @param string $version A QTI version.
	 */
	public function setVersion($version);
	
	/**
	 * Get the QTI version of the document.
	 *
	 * @return string A QTI version.
	*/
	public function getVersion();
	
	/**
	 * Save the Document at a specific location.
	 * 
	 * @param string $url The URI (Uniform Resource Identifier) describing the location where to save the file.
	 */
	public function save($uri);
	
	/**
	 * Save the Document from a specific location.
	 * 
	 * @param string $url The URI (Uniform Resource Identifier) describing the location from where the file has to be loaded.
	 */
	public function load($uri);
	
	/**
	 * Get the URI describing how/where the loaded/saved document is located. If the implementation
	 * is not aware yet of this location, an empty string ('') is returned.
	 * 
	 * @return string A Uniform Resource Identifier (URI) or an empty string.
	 */
	public function getUri();
}
