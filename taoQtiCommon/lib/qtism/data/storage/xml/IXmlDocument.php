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

use qtism\data\Document;

/**
 * The interface an XML Document should expose.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface IXmlDocument extends Document {
	
	/**
	 * Returns the loaded/saved XmlDocument object.
	 * 
	 * @return XmlDocument The loaded/saved XmlDocument object.
	 * 
	 */
	public function getXmlDocument();
	
	/**
	 * Validate the XML document according to the relevant schema.
	 * 
	 * @param string $filename Force the schema to use located at $filename.
	 * @throws XmlStorageException If the validation fails.
	 */
	public function schemaValidate($filename = '');
}
