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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

/**
 * A QTI Resource from the point of view of the imsmanifest v1.1 : Content Packaging).
 *
 * @package taoQTI
 
 */
class Resource
{

    /**
     * defines the list of known authorized type of resources 
     *
     * @var array
     */
    protected static $allowedTypes = array(
        'imsqti_apipsectionroot_xmlv2p1',
        'controlfile/apip_xmlv1p0',
        'associatedcontent/apip_xmlv1p0/learning-application-resource'
    );
    
    /**
     * defines the list of known authorized type of qti test 
     *
     * @var array
     */
    protected static $testTypes = array(
        'imsqti_apiptestroot_xmlv2p1',
        'imsqti_test_xmlv2p1',
        'imsqti_assessment_xmlv2p1'
    );
    
    /**
     * defines the list of known authorized type of qti item 
     *
     * @var array
     */
    protected static $itemTypes = array(
        'imsqti_item_xmlv2p0',
        'imsqti_item_xmlv2p1',
        'imsqti_apipitemroot_xmlv2p1',
        'imsqti_apipitem_xmlv2p1'
    );

    /**
     * The identifier of the resource.
     *
     * @var string
     */
    protected $identifier = '';

    /**
     * The URI representing the file bound to the resource.
     *
     * @var string
     */
    protected $file = '';

    /**
     * The valid IMS resource type.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Array containing the auxiliary files.
     *
     * @access protected
     * @var array
     */
    protected $auxiliaryFiles = array();

    /**
     * Array containing dependencies.
     * 
     * @var array
     */
    protected $dependencies = array();
    
    /**
     * Create a new QTI Resource object.
     *
     * @param string $id
     * @param string $type
     * @param string $file
     */
    public function __construct($id, $type, $file){
        $this->identifier = $id;
        $this->type = $type;
        $this->file = $file;
    }

    /**
     * Check if the given type is allowed as a QTI Resource
     *
     * @param  string $type
     * @return boolean
     */
    public static function isAllowed($type){
        return (!empty($type) && (in_array($type, self::$allowedTypes))) || self::isAssessmentItem($type) || self::isAssessmentTest($type);
    }

    /**
     * Check if the given type is allowed as a QTI item type
     *
     * @param  string $type
     * @return boolean
     */
    public static function isAssessmentItem($type){
        return (!empty($type) && in_array($type, self::$itemTypes));
    }
    /**
     * Check if the given type is allowed as a QTI test type
     *
     * @param  string $type
     * @return boolean
     */
    public static function isAssessmentTest($type){
        return (!empty($type) && in_array($type, self::$testTypes));
    }

    /**
     * Get all valid test types
     *
     * @return array
     */
    public static function getTestTypes()
    {
        return self::$testTypes;
    }

    /**
     * Get the identifier of the resource.
     *
     * @return string
     */
    public function getIdentifier(){
        return $this->identifier;
    }
    
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Get the URI of the main referenced file.
     *
     * @return string
     */
    public function getFile(){
        return (string) $this->file;
    }
    
    /**
     * Get the qti resource type:
     *
     * @return string
     */
    public function getType() {
        return (string) $this->type;
    }
    
    /**
     * Set the list of auxiliary files bound to this resource.
     *
     * @param array $files An array of strings representing URIs.
     */
    public function setAuxiliaryFiles(array $files){
        $this->auxiliaryFiles = $files;
    }

    /**
     * Add an auxiliary file to the resource.
     *
     * @param string $file The URI referencing the file. 
     */
    public function addAuxiliaryFile($file){
        $this->auxiliaryFiles[] = $file;
    }

    /**
     * Get the list of auxiliary files bound to this resource.
     *
     * @return array An array of strings representing URIs.
     */
    public function getAuxiliaryFiles(){
        return (array) $this->auxiliaryFiles;
    }

    /**
     * Set the list of dependencies to this resource.
     * 
     * @param array $dependencies An array of strings representing identifiers.
     */
    public function setDependencies(array $dependencies) {
        $this->dependencies = $dependencies;
    }
    
    /**
     * Add a dependency to this resource.
     * 
     * @param string $dependency An identifier.
     */
    public function addDependency($dependency) {
        $this->dependencies[] = $dependency;
    }
    
    /**
     * Get the list of dependencies to this resource.
     * 
     * @return array An array of strings representing identifiers.
     */
    public function getDependencies() {
        return $this->dependencies;
    }
}