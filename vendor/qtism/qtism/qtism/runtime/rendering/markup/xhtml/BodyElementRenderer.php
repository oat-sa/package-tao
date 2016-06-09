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
use qtism\runtime\rendering\markup\AbstractMarkupRenderer;
use qtism\runtime\rendering\markup\xhtml\AbstractXhtmlRenderer;
use \DOMDocumentFragment;

/**
 * BodyElement renderer.
 * 
 * This rendered will add the 'qti-bodyElement' class to the rendered
 * elements.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BodyElementRenderer extends AbstractXhtmlRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-bodyElement');
        $this->additionalClass('qti-' . $component->getQtiClassName());
        
        if ($component->hasId() === true) {
            $fragment->firstChild->setAttribute('id', $component->getId());
        }
        
        if ($component->hasClass() === true) {
            $fragment->firstChild->setAttribute('class', $component->getClass());
        }
        
        if ($component->hasLang() === true) {
            $fragment->firstChild->setAttribute('lang', $component->getLang());
        }
    }
}