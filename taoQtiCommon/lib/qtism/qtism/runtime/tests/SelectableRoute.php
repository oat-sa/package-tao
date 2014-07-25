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
namespace qtism\runtime\tests;

use \OutOfBoundsException;

/**
 * The SelectableRoute class aims at representing a Route which is
 * subject to be selected in a selection process.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SelectableRoute extends Route {
    
    /**
     * If the SelectableRoute is fixed.
     * 
     * @var boolean
     */
    private $visible;
    
    /**
     * If the SelectableRoute is visible.
     * 
     * @var boolean
     */
    private $fixed;
    
    /**
     * If the SelectableRoute is required.
     * 
     * @var boolean
     */
    private $required;
    
    /**
     * If the RouteItems must be kept together.
     * 
     * @var boolean
     */
    private $keepTogether;
    
    /**
     * Create a new SelectableRoute object.
     * 
     * @param boolean $fixed If the SelectableRoute is fixed.
     * @param boolean $required If the SelectableRoutei is required.
     * @param boolean $visible If the SelectableRoute is visible.
     * @param boolean $keepTogether If the SelectableRoute must be kept together.
     */
    public function __construct($fixed = false, $required = false, $visible = true, $keepTogether = true) {
        parent::__construct();
        $this->setFixed($fixed);
        $this->setRequired($required);
        $this->setVisible($visible);
        $this->setKeepTogether($keepTogether);
    }
    
    /**
     * Whether the SelectableRoute is fixed.
     * 
     * @return boolean
     */
    public function isFixed() {
        return $this->fixed;
    }
    
    /**
     * Whether the SelectableRoute is visible.
     * 
     * @return boolean
     */
    public function isVisible() {
        return $this->visible;
    }
    
    /**
     * Whether the SelectableRoute is required.
     * 
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }
    
    /**
     * Set whether the SelectableRoute is fixed.
     * 
     * @param boolean $fixed
     */
    public function setFixed($fixed) {
        $this->fixed = $fixed;
    }
    
    /**
     * Set whether the SelectableRoute is visible.
     * 
     * @param boolean $visible
     */
    public function setVisible($visible) {
        $this->visible = $visible;
    }
    
    /**
     * Set Whether the SelectableRoute is required.
     * 
     * @param boolean $required
     */
    public function setRequired($required) {
        $this->required = $required;
    }
    
    /**
     * Set whether or not the RouteItem objects held by the Route must be kept together.
     * 
     * @param boolean $keepTogether
     */
    public function setKeepTogether($keepTogether) {
        $this->keepTogether = $keepTogether;
    }
    
    /**
     * Whether the RouteItem objects held by the Route must be kept together.
     * 
     * @return boolean
     */
    public function mustKeepTogether() {
        return $this->keepTogether;
    }
}