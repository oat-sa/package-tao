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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * A class dedicated to load an Extension Manifest and retrieve the values
 * described into it through a common interface.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package generis
 * @since 2.3
 
 */
class common_ext_Manifest
{

    /**
     * The path to the file where the manifest is described.
     *
     * @access private
     * @var string
     */
    private $filePath = '';

    /**
     * The name of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * The human readable name of the extension
     *
     * @access private
     * @var string
     */
    private $label = '';
    
    /**
     * The description of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * The author of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $author = '';

    /**
     * The version of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $version = '';
    
    /**
     * The license of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $license = 'unknown';

    /**
     * The dependencies of the Extension the manifest describes.
     *
     * @access private
     * @var array
     */
    private $dependencies = array();

    /**
     * The RDF models that are required by the Extension the manifest describes.
     *
     * @access private
     * @var array
     */
    private $models = array();

    /**
     * The files corresponding to the RDF models to be imported at installation time.
     *
     * @access private
     * @var array
     */
    private $installModelFiles = array();

    /**
     * The configuration checks that have to be performed prior to installation.
     *
     * @access private
     * @var array
     */
    private $installChecks = array();

    /**
     * The paths to PHP Scripts to be run at installation time.
     *
     * @access private
     * @var array
     */
    private $installPHPFiles = array();
    
    /**
     * The data associated with the uninstall
     *
     * @access private
     * @var array
     */
    private $uninstallData = null;
    
    /**
     * The update handler
     *
     * @access private
     * @var string
     */
    private $updateHandler = null;
    
    /**
     * The routes to the controllers described by the manifest.
     *
     * @access private
     * @var array
     */
    private $routes = array();
    
    /**
     * The constants to be defined for the described extension.
     *
     * @access private
     * @var array
     */
    private $constants = array();

    /**
     * The Management Role of the extension described by the manifest.
     *
     * @access private
     * @var Resource
     */
    private $managementRoleUri = null;

    /**
     * Local data which can be added as an example
     * uses same format as install data
     *
     * @access private
     * @var array
     */
    private $localData = array();
    
    /**
     * The RDFS Classes that are considered optimizable for the described Extension.
     * 
     * @access private
     * @var array
     */
    private $optimizableClasses = array();
    
    /**
     * The RDF Properties that are considered optimizable for the described Extension.
     * @access private
     * @var array
     */
    private $optimizableProperties = array();

    /**
     * The Access Control Layer table
     * @access private
     * @var array
     */
    private $acl = array();
    
    /**
     * Extra information, not consumed by the framework
     * @access private
     * @var array
     */
    private $extra = array();
    

    /**
     * Creates a new instance of Manifest.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $filePath The path to the manifest.php file to parse.
     */
    public function __construct($filePath)
    {
        
    	// the file exists, we can refer to the $filePath.
    	if (is_readable($filePath)){
    		$this->setFilePath($filePath);
    		$array = require($this->getFilePath());
    		
    		// legacy support
    		if (isset($array['additional']) && is_array($array['additional'])) {
				foreach ($array['additional'] as $key => $val) {
					$array[$key] = $val;
				}
				unset($array['additional']);
			}
    		
    		// mandatory
    		if (!empty($array['name'])){
    			$this->setName($array['name']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'name' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		
    		if (!empty($array['label'])){
    		    $this->setLabel($array['label']);
    		}
    		if (!empty($array['description'])){
    			$this->setDescription($array['description']);
    		}
    		
    		if (!empty($array['license'])){
    		    $this->setLicense($array['license']);
    		}
    		
    		if (!empty($array['author'])){
    			$this->setAuthor($array['author']);	
    		}
    		
    		// mandatory
    		if (!empty($array['version'])){
    			$this->setVersion($array['version']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'version' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['requires'])){
    			$this->setDependencies($array['requires']);
    		} elseif (!empty($array['dependencies'])){
    			$this->setDependencies(array_fill_keys($array['dependencies'], '*'));
    		} elseif (!empty($array['dependances'])){
    			// legacy
    			$this->setDependencies(array_fill_keys($array['dependances'], '*'));
    		} 
    		
    		if (!empty($array['models'])){
    			$this->setModels($array['models']);
    		}
    		
    		if (!empty($array['acl'])){
    		    $this->setAclTable($array['acl']);
    		}
    		
    		if (!empty($array['install'])){
    			if (!empty($array['install']['rdf'])){
    				
					$files = is_array($array['install']['rdf']) ? $array['install']['rdf'] : array($array['install']['rdf']);
    				$this->setInstallModelFiles($files);
    			}
    			
    			if (!empty($array['install']['checks'])){
    				$this->setInstallChecks($array['install']['checks']);
    			}
    			
    			if (!empty($array['install']['php'])){
					$files = is_array($array['install']['php']) ? $array['install']['php'] : array($array['install']['php']);
    				$this->setInstallPHPFiles($files);
    			}
    		}
    		
    		if (isset($array['uninstall'])){
    		    $this->uninstallData = $array['uninstall']; 
    		}
    		
    		if (isset($array['update'])){
    		    $this->updateHandler = $array['update'];
    		}
    		
    		if (!empty($array['local'])){
    			$this->localData = $array['local']; 
    		}
    		
    		if (!empty($array['routes'])){
    		    $this->setRoutes($array['routes']);
    		}
    		
    		if (!empty($array['constants'])){
    			$this->setConstants($array['constants']);
    		}
    		
    		if (!empty($array['extra'])){
    		    $this->setExtra($array['extra']);
    		}
    		
    		if (!empty($array['managementRole'])){
    			$this->setManagementRole($array['managementRole']);
    		}
    		
    		if (!empty($array['optimizableClasses'])){
    			if (!is_array($array['optimizableClasses'])){
    				throw new common_ext_MalformedManifestException("The 'optimizableClasses' component must be an array.");
    			}
    			else{
    				$this->setOptimizableClasses($array['optimizableClasses']);
    			}
    		}
    		
    		if (!empty($array['optimizableProperties'])){
    			if (!is_array($array['optimizableProperties'])){
    				throw new common_ext_MalformedManifestException("The 'optimizableProperties' component must be an array.");
    			}
    			else{
    				$this->setOptimizableProperties($array['optimizableProperties']);
    			}
    		}
    	}
    	else{
    		throw new common_ext_ManifestNotFoundException("The Extension Manifest file located at '${filePath}' could not be read.");
    	}
    	
        $this->setFilePath($filePath);
    }

    /**
     * Get the path to the manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        if (!empty($this->filePath)){
        	$returnValue = $this->filePath;
        }

        return (string) $returnValue;
    }

    /**
     * Set the path to the manifest file.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $filePath An absolute path.
     */
    private function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get the name of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        if (!empty($this->name)){
        	$returnValue = $this->name;
        }
        else{
        	$returnValue = null;
        }

        return (string) $returnValue;
    }

    /**
     * Set the name of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $name A name
     */
    private function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Get the license of the Extension the manifest describes.
     *
     * @access public
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }
    
    /**
     * Set the license of the Extension the manifest describes.
     *
     * @access private
     * @param  string $license the livense
     */
    private function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * Get the description of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        if (!empty($this->description)){
        	$returnValue = $this->description;
        }

        return (string) $returnValue;
    }

    /**
     * Set the description of the Extension that the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $description A description
     */
    private function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the author of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getAuthor()
    {
        $returnValue = (string) '';

        $returnValue = $this->author;

        return (string) $returnValue;
    }

    /**
     * Set the author of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $author The author name
     */
    private function setAuthor($author)
    {
        $this->author = $author;
    }
    
    /**
     * Get the human readable label of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->label;
    }
    
    /**
     * Set the human readable label of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $label The extensions label
     */
    private function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Sets the Access Controll Layer table
     * @param array $table
     */
    private function setAclTable($table)
    {
        $this->acl = $table;
    }
    
    /**
     * Returns the Access Controll Layer table
     * @return array
     */
    public function getAclTable()
    {
        return $this->acl;
    }
    
    /**
     * Get the version of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getVersion()
    {
        $returnValue = (string) '';

        if (!empty($this->version)){
        	$returnValue = $this->version;
        }

        return (string) $returnValue;
    }

    /**
     * Set the version of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $version A version number
     */
    private function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the dependencies of the Extension the manifest describes.
     * 
     * The content of the array are extensionIDs, represented as strings.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();

        $returnValue = $this->dependencies;

        return (array) $returnValue;
    }

    /**
     * Set the dependencies of the Extension the manifest describes.
     *
     * @access private
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $dependencies The dependencies
     */
    private function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get the models related to the Extension the manifest describes.
     * 
     * The returned value is an array containing model URIs as strings.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getModels()
    {
        $returnValue = array();

        $returnValue = $this->models;

        return (array) $returnValue;
    }

    /**
     * Set the models related to the Extension the manifest describes.
     * 
     * The $models parameter must be an array of strings that represent model URIs.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $models
     */
    private function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * returns an array of RDF files
     * to import during install. The returned array contains paths to the files
     * to be imported.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallModelFiles()
    {
        $returnValue = array();

        $returnValue = $this->installModelFiles;

        return (array) $returnValue;
    }

    /**
     * Sets the the RDF files to be imported during install. The array must contain
     * paths to the files to be imported.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $installModelFiles
     */
    private function setInstallModelFiles($installModelFiles)
    {
        $this->installModelFiles = array();
        $installModelFiles = is_array($installModelFiles) ? $installModelFiles : array($installModelFiles);
		foreach ($installModelFiles as $row) {
			if (is_string($row)) {
				$rdfpath = $row;
			} elseif (is_array($row) && isset($row['file'])) {
				$rdfpath = $row['file'];
			} else {
				throw new common_ext_InstallationException('Error in definition of model to add into the ontology for '.$this->extension->getId(), 'INSTALL');
			}
    		$this->installModelFiles[] = $rdfpath;
		}
    }

    /**
     * Get the installation checks to be performed prior installation of the described Extension.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallChecks()
    {
        $returnValue = array();

        $returnValue = $this->installChecks;

        return (array) $returnValue;
    }

    /**
     * Set the installation checks to be performed prior installation of the described Extension.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $installChecks
     */
    private function setInstallChecks($installChecks)
    {
        // Check if the content is well formed.
    	if (!is_array($installChecks)){
    		throw new common_ext_MalformedManifestException("The 'install->checks' component must be an array.");	
    	}
    	else{
    		foreach ($installChecks as $check){
    			// Mandatory fields for any kind of check are 'id' (string), 
    			// 'type' (string), 'value' (array).
    			if (empty($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component is mandatory.");	
    			}else if (!is_string($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component must be a string.");
    			}
    			
    			if (empty($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component is mandatory.");
    			}
    			else if (!is_array($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component must be an array.");	
    			}
    			
    			if (empty($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component is mandatory.");	
    			}
    			else if (!is_string($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component must be a string.");	
    			}
    			
    			switch ($check['type']){
    				case 'CheckPHPRuntime':
    					if (empty($check['value']['min'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->min' component is mandatory for PHPRuntime checks.");	
    					}
    				break;
    				
    				case 'CheckPHPExtension':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPExtension checks.");
    					}
    				break;
    				
    				case 'CheckPHPINIValue':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPINIValue checks.");
    					}
    					else if ($check['value']['value'] == ''){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->value' component is mandatory for PHPINIValue checks.");
    					}
    				break;
    				
    				case 'CheckFileSystemComponent':
    					if (empty($check['value']['location'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->location' component is mandatory for FileSystemComponent checks.");	
    					}
    					else if (empty($check['value']['rights'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->rights' component is mandatory for FileSystemComponent checks.");	
    					}
    				break;
    				
    				case 'CheckCustom':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for Custom checks.");	
    					}
    					else if (empty($check['value']['extension'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->extension' component is mandatory for Custom checks.");		
    					}
    				break;
    				
    				default:
    					throw new common_ext_MalformedManifestException("The 'install->checks->type' component value is unknown.");	
    				break;
    			}
    		}
    	}
    	
        $this->installChecks = $installChecks;
    }

    /**
     * Get a list of PHP files to be executed at installation time.
     * 
     * The returned array contains absolute paths to the files to execute.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallPHPFiles()
    {
        $returnValue = array();

        $returnValue = $this->installPHPFiles;

        return (array) $returnValue;
    }
    
    /**
     * Return the uninstall data as an array if present, or null if not
     * 
     * @return multitype:
     */
    public function getUninstallData()
    {
        return  $this->uninstallData;
    }
    
    /**
     * Return the className of the updateHandler
     *
     * @return string
     */
    public function getUpdateHandler()
    {
        return $this->updateHandler;
    }
    
   /**
     * PHP scripts to execute in order to add some sample data to an install
     *
     * @access public
     * @author joel.bout <joel@taotesting.com>
     * @return array
     */
    public function getLocalData()
    {
        return $this->localData;
    }

    /**
     * Set the PHP files to be run at installation time of the described Extension.
     * 
     * The array must contain absolute paths to theses PHP files.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $installPHPFiles
     */
    private function setInstallPHPFiles($installPHPFiles)
    {
        $this->installPHPFiles = $installPHPFiles;
    }

    /**
     * Sets the routes for this extension.
     *
     * @param string $routes
     */
    private function setRoutes($routes)
    {
        return $this->routes = $routes;
    }
    
    /**
     * Gets the controller routes of this extension.
     *
     * @return string
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get an array of constants to be defined where array keys are constant names
     * and values are the values of these constants.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getConstants()
    {
        $returnValue = array();

        $returnValue = $this->constants;

        return (array) $returnValue;
    }

    /**
     * Set an array of constants to be defined where array keys are constant names
     * and values are the values of these constants.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $constants
     */
    private function setConstants($constants)
    {
        $this->constants = $constants;
    }
    
    /**
     * Get the array with unformated extra data
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }
    
    /**
     * Set an array with extra data
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $constants
     */
    private function setExtra($extra)
    {
        $this->extra = $extra;
    }    
    
    /**
     * Extract checks from a given manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $file The path to a manifest.php file.
     * @return common_configuration_ComponentCollection
     */
    public static function extractDependencies($file)
    {
        $manifest = @include $file;
        return isset($manifest['requires']) && is_array($manifest['requires'])
            ? array_keys($manifest['requires'])
            : (isset($manifest['dependencies']) && is_array($manifest['dependencies'])
                ? $manifest['dependencies']
                : array()
        );
        
    }

    /**
     * Extract checks from a given manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  $string $file The path to a manifest.php file.
     * @return common_configuration_ComponentCollection
     */
    public static function extractChecks($file)
    {
        $returnValue = null;

        if (is_readable($file)){
        	$manifestPath = $file;
	    	$content = file_get_contents($manifestPath);
	    	$matches = array();
	    	preg_match_all("/(?:\"|')\s*checks\s*(?:\"|')\s*=>(\s*array\s*\((\s*array\((?:.*)\s*\)\)\s*,{0,1})*\s*\))/", $content, $matches);
	    	
	    	if (!empty($matches[1][0])){
	    		$returnValue = eval('return ' . $matches[1][0] . ';');
	    		
	    		foreach ($returnValue as &$component){
		    		if (strpos($component['type'], 'FileSystemComponent') !== false){
		    			$root = realpath(dirname(__FILE__) . '/../../../');
	        			$component['value']['location'] = $root . '/' . $component['value']['location'];
	        		}	
	    		}
	    	}
	    	else{
	    		$returnValue = array();	
	    	}
        }
        else{
        	$msg = "Extension Manifest file could not be found in '${file}'.";
        	throw new common_ext_ManifestNotFoundException($msg);
        }

        return $returnValue;
    }

    /**
     * Removing all generis references from framework
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     * @deprecated
     * @see common_ext_Manifest::getManagementRoleUri()
     */
    public function getManagementRole()
    {
        return is_null($this->managementRoleUri) ? null : new core_kernel_classes_Resource($this->managementRoleUri);
    }
    
    /**
     * Get the Role dedicated to manage this extension. Returns null if there is
     * 
     * @return string
     */
    public function getManagementRoleUri()
    {
        return $this->managementRoleUri;
    }
    

    /**
     * Set the Management Role of the Extension Manifest.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $managementRole The URI of the Management Role of the Extension.
     */
    private function setManagementRole($managementRoleUri)
    {
        $this->managementRoleUri = $managementRoleUri;
    }
    
    /**
     * Get an array of Class URIs (as strings) that are considered optimizable for the 
     * described Extension.
     * 
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getOptimizableClasses()
    {
    	$returnValue = array();
    	
    	$returnValue = $this->optimizableClasses;
    	
    	return $returnValue;
    }
    
    /**
     * Set the Classes that are considered optimizable for the described Extension.
     * 
     * The array passed as a parameter must be a set of URIs (as strings) referencing
     * RDFS Classes.
     * 
     * @param array $optimizableClasses
     */
    private function setOptimizableClasses(array $optimizableClasses)
	{
		$this->optimizableClasses = $optimizableClasses;
	}
	
	/**
	 * Get an array of Property URIs (as strings) that are considered optimizable for the
	 * described Extension.
	 * 
	 * @return array
	 */
	public function getOptimizableProperties()
	{
		$returnValue = array();
		
		$returnValue = $this->optimizableProperties;
		
		return $returnValue;
	}
	
	/**
	 * Set the Properties that are considered optimizable for the described Extension.
	 * 
	 * The array passed as a parameter must be a set of URIs (as strings) referencing
	 * RDF Properties.
	 * 
	 * @param array $optimizableProperties
	 */
	private function setOptimizableProperties(array $optimizableProperties)
	{
		$this->optimizableProperties = $optimizableProperties;
	}
}