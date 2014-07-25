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
 * The XHTML tr class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Tr extends BodyElement {
    
    /**
     * The TableCell objects composing the tr.
     * 
     * @var TableCellCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new Tr object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct(TableCellCollection $content, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent($content);
    }
    
    /**
     * Get the TableCell objects composing the tr.
     * 
     * @return TableCellCollection A collection of TableCell objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the TableCell objects composing the tr.
     * 
     * @param TableCellCollection $content A collection of TableCell objects.
     */
    public function setContent(TableCellCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the TableCell objects composing the tr.
     * 
     * @return TableCellCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getQtiClassName() {
        return 'tr';
    }
}