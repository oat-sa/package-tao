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

namespace qtism\data\content\xhtml\tables;

use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The XHTML thead class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Thead extends BodyElement {
    
    /**
     * The Tr elements composing the Thead.
     * 
     * @var TrCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new Thead object.
     * 
     * @param TrCollection $content The Tr objects composing the Thead.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct(TrCollection $content, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent($content);
    }
    
    /**
     * Set the Tr objects composing the Thead.
     * 
     * @param TrCollection $content A collection of Tr objects.
     * @throws InvalidArgumentException If $content is empty.
     */
    public function setContent(TrCollection $content) {
        if (count($content) > 0) {
            $this->content = $content;
        }
        else {
            $msg = "A Thead object must be composed of at lease 1 Tr, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the Tr objects composing the Thead.
     * 
     * @return TrCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the Tr objects composing the Thead.
     * 
     * @return TrCollection A collection of Tr objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'thead';
    }
}