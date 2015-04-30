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
     * @return array An associative array with two main keys. The 'injectors' and 'extractors' keys refer to sub-arrays containing respectively classnames of MetadataInjector and MetadataExtractor implementations.  
     */
    public function getMapping()
    {
        $mapping = $this->getExtension()->getConfig(self::CONFIG_ID);
        return is_array($mapping) ? $mapping : array('injectors' => array(), 'extractors' => array());
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
    public function unregisterMetadataIntjector($fqcn)
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
}