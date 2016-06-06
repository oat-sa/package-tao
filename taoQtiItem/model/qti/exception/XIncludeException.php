<?php
/*  
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
 * 
 */

namespace oat\taoQtiItem\model\qti\exception;

use \common_Exception;
use \common_exception_UserReadableException;
use oat\taoQtiItem\model\qti\XInclude;

/**
 * Exception during QTI XInclude loading
 *
 * @access public
 * @author sam, <sam@taotesting.com>
 * @package taoQtiItem
 
 */
class XIncludeException
    extends common_Exception implements common_exception_UserReadableException
{   
    
    /**
     *
     * @var \oat\taoQtiItem\model\qti\XInclude 
     */
    protected $xinclude = null;
    
    /**
     * 
     * @param string $message
     * @param \oat\taoQtiItem\model\qti\XInclude $xinclude
     */
    public function __construct($message, XInclude $xinclude)
    {
        parent::__construct($message);
        $this->xinclude = $xinclude;
    }
    
    /**
     * Returns a human-readable message describing the error that occured.
     *
     * @return string
     */
    public function getUserMessage() {
        return __('An error occured while loading a shared stimulus.');
    }
    
    /**
     * Get the faulty XInclude instance
     * 
     * @return \oat\taoQtiItem\model\qti\XInclude
     */
    public function getXInclude(){
        return $this->xinclude;
    }
}