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

use qtism\data\content\FlowCollection;
use qtism\common\collections\IdentifierCollection;
use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * In XHTML, table cells are represented by either th or td and these share 
 * the following attributes and content model:
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class TableCell extends BodyElement {
    
    /**
     * The headers of the tableCell.
     * 
     * @var IdentifierCollection
     * @qtism-bean-property
     */
    private $headers;
    
    /**
     * The XHTML scope attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $scope = -1;
    
    /**
     * The XHTML abbr attribute.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $abbr = '';
    
    /**
     * The XHTML axis attribute.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $axis = '';
    
    /**
     * The XHTML rowspan attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $rowspan = -1;
    
    /**
     * The XHTML colspan attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $colspan = -1;
    
    /**
     * The components composing the TableCell.
     * 
     * @var FlowCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new TableCell object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new FlowCollection());
        $this->setHeaders(new IdentifierCollection());
        $this->setScope(-1);
        $this->setAbbr('');
        $this->setAxis('');
        $this->setRowspan(-1);
        $this->setColspan(-1);
    }
    
    /**
     * Specify the th element each td element is related to.
     * 
     * @param IdentifierCollection $collection A collection of QTI identifiers.
     */
    public function setHeaders(IdentifierCollection $headers) {
        $this->headers = $headers;
    }
    
    /**
     * Get the th element each td element is related to.
     * 
     * @return IdentifierCollection A collection of QTI identifiers.
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * Whether at least one value is defined for the headers attribute.
     * 
     * @return boolean
     */
    public function hasHeaders() {
        return count($this->getHeaders()) > 0;
    }
    
    /**
     * Set the scope attribute.
     * 
     * @param integer $scope A value from the TableCellScope enumeration or -1 if no scope is defined.
     * @throws InvalidArgumentException If $scope is not a value from the TableCellScope enumeration nor -1.
     */
    public function setScope($scope) {
        if (in_array($scope, TableCellScope::asArray()) === true || $scope === -1) {
            $this->scope = $scope;
        }
        else {
            $msg = "The 'scope' argument must be a value from the TableCellScope enumeration, '" . $scope . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the scope attribute.
     * 
     * @return integer A value from the TableCellScope enumeration or -1 if no scope is defined.
     */
    public function getScope() {
        return $this->scope;
    }
    
    /**
     * Whether a scope is defined.
     * 
     * @return boolean
     */
    public function hasScope() {
        return $this->getScope() !== -1;
    }
    
    /**
     * Set the value of the abbr attribute.
     * 
     * @param string $attr A string or an empty string if no abbr is defined.
     * @throws InvalidArgumentException If $bbr is not a string.
     */
    public function setAbbr($abbr) {
        if (is_string($abbr) === true) {
            $this->abbr = $abbr;
        }
        else {
            $msg = "The 'abbr' attribute must be a string, '" . gettype($abbr) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value of the abbr attribute.
     * 
     * @return string A string or an empty string if no abbr is defined.
     */
    public function getAbbr() {
        return $this->abbr;
    }
    
    /**
     * Whether a value for the attribute is defined.
     * 
     * @return boolean
     */
    public function hasAbbr() {
        return $this->getAbbr() !== '';
    }
    
    /**
     * Set the value of the axis attribute.
     * 
     * @param string $axis A string. Give an empty string if no axis is indicated.
     * @throws InvalidArgumentException If $axis is not a string.
     */
    public function setAxis($axis) {
        if (is_string($axis) === true) {
            $this->axis = $axis;
        }
        else {
            $msg = "The 'axis' argument must be a string, '" . gettype($axis) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value of the axis attribute.
     * 
     * @return string A string. The string is empty if no axis is defined.
     */
    public function getAxis() {
        return $this->axis;
    }
    
    /**
     * Whether a value is defined for the axis attribute.
     * 
     * @return boolean
     */
    public function hasAxis() {
        return $this->getAxis() !== '';
    }
    
    /**
     * Set the value of the rowspan attribute. Give a negative value if 
     * no rowspan attribute is set.
     * 
     * @param integer $rowspan 
     * @throws InvalidArgumentException If $rowspan is not an integer.
     */
    public function setRowspan($rowspan) {
        if (is_int($rowspan) === true) {
            $this->rowspan = $rowspan;
        }
        else {
            $msg = "The 'rowspan' argument must be an integer, '" . gettype($rowspan) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value of the rowspan attribute. A negative value indicates that
     * no rowspan attribute is set.
     * 
     * @return integer
     */
    public function getRowspan() {
        return $this->rowspan;
    }
    
    /**
     * Whether a value for the rowspan attribute is set.
     * 
     * @return boolean
     */
    public function hasRowspan() {
        return $this->getRowspan() >= 0;
    }
    
    /**
     * Set the colspan attribute. Give a negative integer to indicate that
     * no colspan is set.
     * 
     * @param integer $colspan An integer.
     * @throws InvalidArgumentException If $colspan is not an integer.
     */
    public function setColspan($colspan) {
        if (is_int($colspan) === true) {
            $this->colspan = $colspan;
        }
        else {
            $msg = "The 'colspan' argument must be an integer, '" . gettype($colspan) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    } 
    
    /**
     * Get the colspan attribute. A negative value indicates that no colspan
     * is set.
     * 
     * @return integer
     */
    public function getColspan() {
        return $this->colspan;
    }
    
    /**
     * Whether a value for the colspan attribute is set.
     * 
     * @return boolean
     */
    public function hasColspan() {
        return $this->getColspan() >= 0;
    }
    
    /**
     * Set the components composing the TableCell.
     * 
     * @param FlowCollection $content A collection of Flow objects.
     */
    public function setContent(FlowCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the components composing the TableCell.
     * 
     * @return FlowCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the components composing the TableCell.
     * 
     * @return FlowCollection
     */
    public function getComponents() {
        return $this->getContent();
    }
}