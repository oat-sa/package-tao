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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoQtiItem\model\qti\metadata;

use \common_ext_Extension;
use \common_ext_ExtensionsManager;
use \InvalidArgumentException;

/**
 * MetadataRegistry objects enables you to register/unregister
 * MetadataExtractor and MetadataInjector objects to be used
 * in various situations accross the platform.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see oat\taoQtiItem\model\qti\metadata\MetadataExtractor The MetadataExtractor interface.
 * @see oat\taoQtiItem\model\qti\metadata\MetadataInjector The MetadataInjector interface.
 */
class MetadataRegistry
{
    
    /**
     * The key to be used in configuration to retrieve
     * or set the class mapping.
     * 
     * @var string
     */
    const CONFIG_ID = 'metadata_registry';
    
    /**
     * A pointer to the taoQtiItem extension
     * 
     * @var \common_ext_Extension
     */
    protected $extension;
    
    /**
     * Create a new MetadataRegistry object.
     * 
     */
    public function __construct()
    {
        $this->setExtension(common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem'));
    }
    
    /**
     * Set the extension to be used to store the mapping in configuration.
     * 
     * @param common_ext_Extension $extension
     */
    protected function setExtension(common_ext_Extension $extension)
    {
        $this->extension = $extension;
    }
    
    /**
     * Get the extension to be used to store the mapping configuration.
     * 
     * @return common_ext_Extension
     */
    protected function getExtension()
    {
        return $this->extension;
    }
    
    /**
     * Get the class mapping of Extractor/Injector classes.
     * 
     * @return array An associative array with two main keys. The 'injectors' and 'extractors' and 'guardians' keys refer to sub-arrays containing respectively classnames of MetadataInjector and MetadataExtractor implementations.  
     */
    public function getMapping()
    {
        $mapping = $this->getExtension()->getConfig(self::CONFIG_ID);
        
        if (is_array($mapping) === true) {
            if (isset($mapping['guardians']) === false) {
                // Sometimes, 'guardians' key is not set...
                $mapping['guardians'] = array();
            }
            
            if (isset($mapping['classLookups']) === false) {
                // Sometimes, 'classLookups' key is not set...
                $mapping['classLookups'] = array();
            }
            
            return $mapping;
        } else {
            
            return array('injectors' => array(), 'extractors' => array(), 'guardians' => array(), 'classLookups' => array());
        }
    }
    
    /**
     * Set the class mapping of Extractor/Injector classes.
     * 
     * @param array $mapping An associative array with two main keys. The 'injectors' and 'extractors' keys refer to sub-arrays containing respectively classnames of MetadataInjector and MetadataExtractor implementations.
     */
    protected function setMapping(array $mapping)
    {
        $this->getExtension()->setConfig(self::CONFIG_ID, $mapping);
    }
    
    /**
     * Register a MetadataInjector implementation by $fqcn (Fully Qualified Class Name).
     * 
     * @param string $fqcn A Fully Qualified Class Name.
     * @throws InvalidArgumentException If the given $fqcn does not correspond to an implementation of the MetadataInjector interface.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataInjector The MetadataInjector interface.
     */
    public function registerMetadataInjector($fqcn)
    {
        // Check if $fqcn class implements the correct interface.
        $interfaces = class_implements($fqcn);
        if (in_array('oat\\taoQtiItem\\model\\qti\metadata\\MetadataInjector', $interfaces) === false) {
            $msg = "Class ${fqcn} does not implement oat\\taoQtiItem\\model\\qti\metadata\\MetadataInjector interface";
            throw new InvalidArgumentException($msg);
        }
        
        $mapping = $this->getMapping();
        $mapping['injectors'][] = $fqcn;
        
        $this->setMapping($mapping);
    }
    
    /**
     * Unregister a MetadataInjector implementation by $fqcn (Fully Qualified Class Name).
     * 
     * @param string $fqcn A Fully Qualified Class Name.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataInjector The MetadataInjector interface.
     */
    public function unregisterMetadataInjector($fqcn)
    {
        $mapping = $this->getMapping();
        
        if (($key = array_search($fqcn, $mapping['injectors'])) !== false) {
            unset($mapping['injectors'][$key]);
        }
        
        $this->setMapping($mapping);
    }
    
    /**
     * Register a MetadataExtractor implementation by $fqcn (Fully Qualified Class Name).
     * 
     * @param string $fqcn A Fully Qualified Class Name.
     * @throws InvalidArgumentException If the given $fqcn does not correspond to an implementation of the MetadataExtractor interface.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataExtractor The MetadataExtractor interface.
     */
    public function registerMetadataExtractor($fqcn)
    {
        // Check if $fqcn class implements the correct interface.
        $interfaces = class_implements($fqcn);
        if (in_array('oat\\taoQtiItem\\model\\qti\metadata\\MetadataExtractor', $interfaces) === false) {
            $msg = "Class ${fqcn} does not implement oat\\taoQtiItem\\model\\qti\metadata\\MetadataExtractor interface";
            throw new InvalidArgumentException($msg);
        }
        
        $mapping = $this->getMapping();
        $mapping['extractors'][] = $fqcn;
        
        $this->setMapping($mapping);
    }
    
    /**
     * Unregister a MetadataExtractor implementation by $fqcn (Fully Qualified Class Name).
     * 
     * @param string $fqcn A Fully Qualified Class Name.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataExtractor The MetadataExtractor interface.
     */
    public function unregisterMetadataExtractor($fqcn)
    {
        $mapping = $this->getMapping();
        
        if (($key = array_search($fqcn, $mapping['extractors'])) !== false) {
            unset($mapping['extractors'][$key]);
        }
        
        $this->setMapping($mapping);
    }
    
    /**
     * Register a MetadataGuardian implementation by $fqcn (Fully Qualified Class Name).
     *
     * @param string $fqcn A Fully Qualified Class Name.
     * @throws InvalidArgumentException If the given $fqcn does not correspond to an implementation of the MetadataGuardian interface.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataGuardian The MetadataExtractor interface.
     */
    public function registerMetadataGuardian($fqcn)
    {
        // Check if $fqcn class implements the correct interface.
        $interfaces = class_implements($fqcn);
        if (in_array('oat\\taoQtiItem\\model\\qti\metadata\\MetadataGuardian', $interfaces) === false) {
            $msg = "Class ${fqcn} does not implement oat\\taoQtiItem\\model\\qti\metadata\\MetadataGuardian interface";
            throw new InvalidArgumentException($msg);
        }
    
        $mapping = $this->getMapping();
        $mapping['guardians'][] = $fqcn;
    
        $this->setMapping($mapping);
    }
    
    /**
     * Unregister a MetadataGuardian implementation by $fqcn (Fully Qualified Class Name).
     *
     * @param string $fqcn A Fully Qualified Class Name.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataGuardian The MetadataGuardian interface.
     */
    public function unregisterMetadataGuardian($fqcn)
    {
        $mapping = $this->getMapping();
    
        if (($key = array_search($fqcn, $mapping['guardians'])) !== false) {
            unset($mapping['guardians'][$key]);
        }
    
        $this->setMapping($mapping);
    }
    
    /**
     * Register a MetadataClassLookup implementation by $fqcn (Fully Qualified Class Name).
     *
     * @param string $fqcn A Fully Qualified Class Name.
     * @throws InvalidArgumentException If the given $fqcn does not correspond to an implementation of the MetadataClassLookup interface.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataClassLookup The MetadataClassLookup interface.
     */
    public function registerMetadataClassLookup($fqcn)
    {
        // Check if $fqcn class implements the correct interface.
        $interfaces = class_implements($fqcn);
        if (in_array('oat\\taoQtiItem\\model\\qti\metadata\\MetadataClassLookup', $interfaces) === false) {
            $msg = "Class ${fqcn} does not implement oat\\taoQtiItem\\model\\qti\metadata\\MetadataClassLookup interface";
            throw new InvalidArgumentException($msg);
        }
        
        $mapping = $this->getMapping();
        $mapping['classLookups'][] = $fqcn;
        
        $this->setMapping($mapping);
    }
    
    /**
     * Unregister a MetadataClassLookup implementation by $fqcn (Fully Qualified Class Name).
     *
     * @param string $fqcn A Fully Qualified Class Name.
     * @see oat\taoQtiItem\model\qti\metadata\MetadataClassLookup The MetadataClassLookup interface.
     */
    public function unregisterMetadataClassLookup($fqcn)
    {
        $mapping = $this->getMapping();
    
        if (($key = array_search($fqcn, $mapping['classLookups'])) !== false) {
            unset($mapping['classLookups'][$key]);
        }
    
        $this->setMapping($mapping);
    }
}