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
 * Copyright (c) 2013-2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\marshalling;

use qtism\data\XInclude;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for Include.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class XIncludeMarshaller extends Marshaller
{
    /**
	 * Marshall an XInclude object into a DOMElement object.
	 *
	 * @param \qtism\data\QtiComponent $component An XInclude object.
	 * @return \DOMElement The according DOMElement object.
	 * @throws \qtism\data\storage\marshalling\MarshallingException
	 */
    protected function marshall(QtiComponent $component)
    {
        return self::getDOMCradle()->importNode($component->getXml()->documentElement, true);
    }

    /**
	 * Unmarshall a DOMElement object corresponding to a math element.
	 *
	 * @param \DOMElement $element A DOMElement object.
	 * @return \qtism\data\QtiComponent A Math object.
	 * @throws \qtism\data\storage\marshalling\UnmarshallingException
	 */
    protected function unmarshall(DOMElement $element)
    {
        $node = $element->cloneNode(true);

        return new XInclude($element->ownerDocument->saveXML($node));
    }

    /**
	 * @see \qtism\data\storage\xml\marshalling\Marshaller::getExpectedQtiClassName()
	 */
    public function getExpectedQtiClassName()
    {
        return 'include';
    }
}
