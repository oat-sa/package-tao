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

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * Base class for all Renderers that will render subclasses
 * of qti:interaction. It also adds a 'qti-interaction' additional
 * CSS class to rendered elements.
 * 
 * This rendering implementation will add the following data-X attributes:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class InteractionRenderer extends BodyElementRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
        $this->additionalClass('qti-interaction');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $fragment->firstChild->setAttribute('data-response-identifier', $component->getResponseIdentifier());
    }
}