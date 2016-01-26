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
 * Short description of class core_kernel_api_ApiModel
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package generis
 
 */
interface core_kernel_api_ApiModel
    extends core_kernel_api_Api
{


    // --- OPERATIONS ---


    /**
     * build xml rdf containing rdf:Description of all meta-data the conected
     * may get
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array sourceNamespaces
     * @return string
     */
    public function exportXmlRdf($sourceNamespaces = array());

    /**
     * import xml rdf files into the knowledge base
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string targetNameSpace
     * @param  string fileLocation
     * @return boolean
     */
    public function importXmlRdf($targetNameSpace, $fileLocation);


    /**
     * returns an xml rdf serialization for uriResource with all meta dat found
     * inferenced from te knowlege base about this resource
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uriResource
     * @return string
     */
    public function getResourceDescriptionXML($uriResource);

    /**
     * returns metaclasses tat are not subclasses of other metaclasses
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getMetaClasses();

    /**
     * returns classes that are not subclasses of other classes
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRootClasses();

    /**
     * Short description of method getAllClasses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getAllClasses();

    /**
     * add a new statment to the knowledge base
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function setStatement($subject, $predicate, $object, $language);

    /**
     * Short description of method removeStatement
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function removeStatement($subject, $predicate, $object, $language);

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string predicate
     * @param  string object
     * @return core_kernel_classes_Resource
     */
    public function getSubject($predicate, $object);

    /**
     * Short description of method getObject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string subject
     * @param  string predicate
     * @return core_kernel_classes_ContainerCollection
     */
    public function getObject($subject, $predicate);

}
