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
 */

/**
 * Interface to implement by delivery models
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
interface taoDelivery_models_classes_ContentModel
{
    /**
     * constructor called
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function __construct();

    /**
     * Returns a new delivery content
     * 
     * @return Resource
     */
    public function createContent($tests = array());
    
    /**
     * renders the content authoring
     *
     * @access public
     * @param  Resource delivery
     * @return string
     */
    public function getAuthoring( core_kernel_classes_Resource $content);
    
    /**
     * Called when the label of a test changes
     * 
     * @param Resource $delivery
     */
    public function onChangeDeliveryLabel( core_kernel_classes_Resource $delivery);
    
    /**
     * Delete the content of the delivery
     * 
     * @param Resource $delivery
     */
    public function delete( core_kernel_classes_Resource $content);
    
    /**
     * Clones the content of one delivery
     * 
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource $content
     */
    public function cloneContent( core_kernel_classes_Resource $content);
    
    /**
     * Returns a the class of the delivery compiler implementation that can
     * prepare the content in any way it sees fit and
     * return a service call that can execute the prepared content
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return tao_models_classes_Compiler
     */
    public function getCompilerClass();    
}