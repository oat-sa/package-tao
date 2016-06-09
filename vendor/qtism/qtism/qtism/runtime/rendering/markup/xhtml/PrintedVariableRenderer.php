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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\rendering\markup\xhtml;

use qtism\data\storage\php\Utils as PhpUtils;
use qtism\runtime\rendering\markup\Utils;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * PrintedVariable Renderer.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-identifier = qti:printedVariable->identifier
 * * data-format = qti:printedVariable->format
 * * data-power-form = qti:printedVariable->powerForm
 * * data-base = qti:printedVariable->base
 * * data-index = qti:printedVariable->index
 * * data-delimiter = qti:printedVariable->delimiter
 * * data-field = qti:printedVariable->field
 * * data-mapping-indicator = qti:printedVariable->mappingIndicator
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PrintedVariableRenderer extends BodyElementRenderer {
    
    /**
     * Create a new PrintedVariableRenderer object.
     *
     * @param AbstractRenderingContext $renderingContext
     */
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('span');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-printedVariable');
        
        $fragment->firstChild->setAttribute('data-identifier', $component->getIdentifier());
        
        if ($component->hasFormat() === true) {
            $fragment->firstChild->setAttribute('data-format', $component->getFormat());
        }
        
        $fragment->firstChild->setAttribute('data-power-form', ($component->mustPowerForm() === true) ? 'true' : 'false');
        $fragment->firstChild->setAttribute('data-base', $component->getBase());
        
        if ($component->hasIndex() === true) {
            $fragment->firstChild->setAttribute('data-index', $component->getIndex());
        }
        
        $fragment->firstChild->setAttribute('data-delimiter', $component->getDelimiter());
        
        if ($component->hasField() === true) {
            $fragment->firstChild->setAttribute('data-field', $component->getField());
        }
        
        $fragment->firstChild->setAttribute('data-mapping-indicator', $component->getMappingIndicator());
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        $renderingEngine = $this->getRenderingEngine();
        
        if ($renderingEngine !== null) {
            switch ($renderingEngine->getPrintedVariablePolicy()) {
                case AbstractMarkupRenderingEngine::CONTEXT_AWARE:
                    $value = Utils::printVariable($this->getRenderingEngine()->getState(),
                                                  $component->getIdentifier(),
                                                  $component->getFormat(),
                                                  $component->mustPowerForm(),
                                                  $component->getBase(),
                                                  $component->getIndex(),
                                                  $component->getDelimiter(),
                                                  $component->getField(),
                                                  $component->getMappingIndicator());
                    $fragment->firstChild->appendChild($fragment->ownerDocument->createTextNode($value));
                break;
                
                case AbstractMarkupRenderingEngine::TEMPLATE_ORIENTED:
                    $base = $component->getBase();
                    $index = $component->getIndex();
                    
                    $params = array('$' . $renderingEngine->getStateName(),
                                     PhpUtils::doubleQuotedPhpString($component->getIdentifier()),
                                     PhpUtils::doubleQuotedPhpString($component->getFormat()),
                                     ($component->mustPowerForm() === true) ? 'true' : 'false',
                                     (is_int($base) === true) ? $base : PhpUtils::doubleQuotedPhpString($base),
                                     (is_int($index) === true) ? $index : PhpUtils::doubleQuotedPhpString($index),
                                     PhpUtils::doubleQuotedPhpString($component->getDelimiter()),
                                     PhpUtils::doubleQuotedPhpString($component->getField()),
                                     PhpUtils::doubleQuotedPhpString($component->getMappingIndicator()));
                    
                    $value = " qtism-printVariable(" . implode(', ', $params) . ") ";
                    $fragment->firstChild->appendChild($fragment->ownerDocument->createComment($value));
                break;
            }
        }
    }
}