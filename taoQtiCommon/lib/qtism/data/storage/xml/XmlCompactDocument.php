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

use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;
use \DOMElement;

class XmlCompactDocument extends XmlDocument {
	
	/**
	 * Override of XmlDocument::createMarshallerFactory in order
	 * to return an appropriate CompactMarshallerFactory.
	 * 
	 * @return CompactMarshallerFactory A CompactMarshallerFactory object.
	 */
	protected function createMarshallerFactory() {
		return new CompactMarshallerFactory();
	}
	
	/**
	 * Override of XmlDocument.
	 * 
	 * Specifices the correct XSD schema locations and main namespace
	 * for the root element of a Compact XML document.
	 */
	public function decorateRootElement(DOMElement $rootElement) {
		$rootElement->setAttribute('xmlns', "http://www.imsglobal.org/xsd/imsqti_v2p1");
		$rootElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$rootElement->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', "http://www.taotesting.com/xsd/qticompact_v1p0.xsd");
	}
}
