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


namespace qtism\data\storage\xml;

use \DOMDocument;

/**
 * A class providing XML utility methods.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
	
	/**
	 * Get the local name of a given $nodeName.
	 *
	 * ex1: 'testPart' -> 'testPart'
	 * ex2: 'qti:testPart' -> 'testPart'
	 *
	 * @param string $nodeName A node name, prefixed or not.
	 * @return string The local name of $nodeName.
	 */
	public static function getLocalNodeName($nodeName) {
		$start = stripos($nodeName, ':');
		// look for an equivalent local name.
		if ($start !== false) {
			return substr($nodeName, $start + 1);
		}
		else {
			return $nodeName;
		}
	}
	
	/**
	 * Get the XML schema to use for a given QTI version.
	 *
	 * @return string A filename pointing at an XML Schema file.
	 */
	public static function getSchemaLocation($version = '2.1') {
		$dS = DIRECTORY_SEPARATOR;
	
		if ($version === '2.1') {
			$filename = dirname(__FILE__) . $dS . 'schemes' . $dS . 'imsqti_v2p1.xsd';
		}
		else {
			$filename = dirname(__FILE__) . $dS . 'schemes' . $dS . 'imsqti_v2p0.xsd';
		}
		
		return $filename;
	}
	
	/**
	 * Infer the QTI version of a given DOMDocument.
	 * 
	 * @param DOMDocument $document A DOMDocument object.
	 * @return string|boolean A QTI version (e.g. '2.1') or false if it could not be infered.
	 */
	public static function inferQTIVersion(DOMDocument $document) {
		$root = $document->documentElement;
		if (empty($root)) {
			return false;
		}
		else {
			switch (trim($root->lookupNamespaceURI(null))) {
				case 'http://www.imsglobal.org/xsd/imsqti_v2p1':
					return '2.1';
				break;
				
				case 'http://www.imsglobal.org/xsd/imsqti_v2p0':
					return '2.0';
				break;
				
				default:
					return false;
				break;
			}
		}
	}
}
