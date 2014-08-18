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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content\xhtml\lists;

use qtism\data\content\InlineCollection;
use \InvalidArgumentException;

/**
 * The dt XHTML class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Dt extends DlElement {
    
    /**
     * The Inline objects composing the Dt.
     * 
     * @var InlineCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new Dt object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new InlineCollection());
    }
    
    /**
     * Set the Inline objects composing the Dt.
     * 
     * @param InlineCollection $content A collection of Inline objects.
     */
    public function setContent(InlineCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the Inline objects composing the Dt.
     * 
     * @return InlineCollection 
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the Inline objects composing the Dt.
     * 
     * @return InlineCollection The Inline objects composing the Dt.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'dt';
    }
}