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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\rendering\markup\xhtml;

use qtism\data\storage\xml\Utils;

use qtism\runtime\rendering\RenderingException;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;
use \RuntimeException;

/**
 * Math Renderer.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MathRenderer extends ExternalQtiComponentRenderer {
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        try {
            $dom = $component->getXml();
            $node = $fragment->ownerDocument->importNode($dom->documentElement, true);
            $node = Utils::anonimizeElement($node);
            $node->setAttribute('xmlns', 'http://www.w3.org/1998/Math/MathML');
            $fragment->appendChild($node);
        }
        catch (RuntimeException $e) {
            $msg = "An error occured while rendering the XML content of the 'MathML' external component.";
            throw new RenderingException($msg, RenderingException::UNKNOWN, $e);
        }
    }
}