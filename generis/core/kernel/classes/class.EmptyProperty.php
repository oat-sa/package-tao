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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Thrown when retrieving an expected property
 * and not finding it (usualy via getUniqueProperty())
 * 
 * Extends common_exception_EmptyProperty to be backwards compatible
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
class core_kernel_classes_EmptyProperty
    extends common_exception_EmptyProperty
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * @access private
     * @var core_kernel_classes_Property
     */
    private $property = null;

    /**
     * @access private
     * @var core_kenel_classes_Resource
     */
    private $resource = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $this->resource = $resource;
        $this->property = $property;
        parent::__construct("Property ({$property->getUri()}) of resource ({$resource->getUri()}) should not be empty");
    }

    /**
     * Returns the property that was empty
     *
     * @access public
     * @return core_kernel_classes_Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Returns the resource with the empty property
     *
     * @access public
     * @return core_kernel_classes_Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns the severity of the exception
     * Used in the common/Logger
     * 
     * @return number
     */
    public function getSeverity() {
        return common_Logger::WARNING_LEVEL;
    }

}