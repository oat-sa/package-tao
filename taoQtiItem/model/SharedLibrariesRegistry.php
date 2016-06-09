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
namespace oat\taoQtiItem\model;

use \common_ext_ExtensionsManager;
use DOMDocument;
use DOMXPath;
use oat\tao\model\ClientLibRegistry;

/**
 * The SharedLibrariesRegistry is a registration tool for PCI/PIC shared libraries.
 * 
 * It enables you to:
 * 
 * * Register a library from a file on the current file system and bind it to a library name, e.g. 'IMSGlobal/jquery_2_1_1'.
 * * Register the libraries referenced by a given item referencing a Portable Custom Interaction.
 * * List the registered libraries through a map of library names => library URLs.
 * * Now if a library is already registered for a given library name.
 * * Get the path of a library on the current file system for a given library name.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://www.imsglobal.org/assessment/PCI_Change_Request_v1pd.html The Pacific Metrics PCI Change Proposal introducing the notion of Shared Libraries.
 */
class SharedLibrariesRegistry
{
    
    private $basePath;
    
    private $baseUrl;

    /**
     * Create a new SharedLibrariesRegistry object.
     * 
     * @param string $basePath The path of the main directory to store library files.
     * @param string $baseUrl The base URL to serve these libraries.
     */
    public function __construct($basePath, $baseUrl)
    {
        $this->setBasePath($basePath);
        $this->setBaseUrl($baseUrl);
    }
    
    /**
     * Set the path on the file system where shared libraries
     * are stored.
     * 
     * @param string $basePath
     */
    protected function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, "\\/");
    }
    
    /**
     * Get the path on the file system where shared libraries
     * are stored.
     * 
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    /**
     * Set the URL where shared libraries are available.
     * 
     * @param string $baseUrl A URL.
     */
    protected function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, "\\/");
    }
    
    /**
     * Get the URL where shared libraries are available.
     * 
     * @return string A URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
       
    /**
     * Register a library from a file existing on the file system.
     * 
     * Example:
     * <code>
     * <?php
     * // ...
     * $registry = new SharedLibrariesRegistry($basePath, $baseUrl);
     * $registry->registerFromFile('IMSGlobal/jquery_2_1_1', '/tmp/jquery_2_1_1.js');
     * 
     * // The registry now maps 'IMSGlobal/jquery_2_1_1' to '$baseUrl/IMSGlobal/jquery_2_1_1.js'
     * ?>
     * </code>
     * 
     * @param string $id A shared library name e.g. 'IMSGlobal/jquery_2_1_1'.
     * @param string $path The path to library implementation to register.
     * @throws SharedLibraryNotFoundException If no library can be found at $path.
     */
    public function registerFromFile($id, $path)
    {
        if (file_exists($path) === false) {
            $msg = "Shared Library could not be found at location '${path}'.";
            throw new SharedLibraryNotFoundException($msg, $id);
        }
        
        $basePath = $this->getBasePath();
        $baseUrl = $this->getBaseUrl();
        $finalPath = "${basePath}/${id}";
    
        $dirName = pathinfo($finalPath, PATHINFO_DIRNAME);
        $dirName = str_replace(array('css!', 'tpl!'), '', $dirName);
    
        if (is_dir($dirName) === false) {
            mkdir($dirName, 0777, true);
        }
    
        $fileBaseName = pathinfo($path, PATHINFO_BASENAME);
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $destination = "${dirName}/${fileBaseName}";
    
        // Subtract eventual css!, tpl! prefixes.
        copy($path, $destination);
    
        // Subtract $basePath from final destination.
        $mappingPath = str_replace($basePath . '/', '', $destination);
        
        // Take care with windows...
        $mappingPath = str_replace("\\", '/', $mappingPath);
        
        $mappingDirname = pathinfo($mappingPath, PATHINFO_DIRNAME);
        ClientLibRegistry::getRegistry()->register($id, "${baseUrl}/${mappingDirname}/${fileName}");
    }
    
    /**
     * Register the libraries referenced by an item at location $path. All <pci:lib> elements will be parsed
     * and then registered through the registery.
     * 
     * If a library cannot be found at the locations listed by the referenced by the <pci:resources>->location attribute,
     * the registry will try to find these libraries on the file system, using the location of the item file as a base path. 
     * 
     * @param string $path The path to the XML file.
     * @throws SharedLibraryNotFoundException If a library referenced by a <pci:lib> element in the item cannot be found.
     */
    public function registerFromItem($path)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->load($path);
        $basePath = pathinfo($dom->documentURI, PATHINFO_DIRNAME);
        
        $basePath = preg_replace('/^file:\//', '', $basePath);
        
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('pci', 'http://www.imsglobal.org/xsd/portableCustomInteraction');
        $libElts = $xpath->query('//pci:lib');
        
        for ($i = 0; $i < $libElts->length; $i++) {
            $libElt = $libElts->item($i);
            
            if (($name = $libElt->getAttribute('name')) !== '') {
                // Is the library already registered?
                if (ClientLibRegistry::getRegistry()->isRegistered($name) === false) {
                    // So we consider to find the library at item's $basePath . $name
                    $expectedLibLocation = "${basePath}/". str_replace(array('tpl!', 'css!'), '', $name);
                    
                    // Might throw a SharedLibraryNotFoundException, let it go...
                    $this->registerFromFile($name, $expectedLibLocation);
                }
            }
        }
    }
}