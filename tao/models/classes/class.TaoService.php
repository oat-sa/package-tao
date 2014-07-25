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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This class provide the services for the Tao extension
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_TaoService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * to stock the extension structure
     *
     * @access protected
     * @var array
     */
    protected static $structure = array();

    /**
     * The key to use to store the default TAO Upload File Source Repository URI
     * the TAO meta-extension configuration.
     *
     * @access public
     * @var string
     */
    const CONFIG_UPLOAD_FILESOURCE = 'defaultUploadFileSource';

    // --- OPERATIONS ---

    /**
     * Load the extension structure file.
     * Return the SimpleXmlElement object (don't forget to cast it)
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string extension
     * @return SimpleXMLElement
     */
    private function getStructuresXml($extensionID)
    {
        $returnValue = null;

        // section 127-0-1-1-5f1894ad:12457319d43:-8000:0000000000001A6C begin
		$structureFilePath = ROOT_PATH.'/'.$extensionID.'/actions/structures.xml';
		
		if(file_exists($structureFilePath)){
			return new SimpleXMLElement($structureFilePath, null, true);
		}
        // section 127-0-1-1-5f1894ad:12457319d43:-8000:0000000000001A6C end

        return $returnValue;
    }

    /**
     * Short description of method getAllStructures
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getAllStructures()
    {
        $returnValue = array();

        // section 127-0-1-1-64be1e2f:13774f13776:-8000:0000000000003A89 begin
    	if( count(self::$structure) == 0 ){
			$structure = array();
			foreach(common_ext_ExtensionsManager::singleton()->getEnabledExtensions() as $extID => $extension){
				$xmlStructures = $this->getStructuresXml($extID);
				if(!is_null($xmlStructures)){
					$structures = $xmlStructures->xpath("/structures/structure");
					foreach($structures as $xmlStructure){
						$id = (string)$xmlStructure['id'];
						if (!isset(self::$structure[$id])) {
							self::$structure[$id] = array(
								'extension' => $extID,
								'id'		=> (string)$xmlStructure['id'],
								'data'		=> $xmlStructure,
								'sections'	=> array(),
								'level'		=> (int)$xmlStructure['level']
							);
						}
						$sections = $xmlStructure->xpath("sections/section");
						foreach($sections as $section) {
							self::$structure[$id]['sections'][(string)$section['id']] = $section;
						}
					}
				}
			}
			usort(self::$structure, create_function('$a,$b', "return \$a['level'] - \$b['level']; "));
		}
		$returnValue = self::$structure;
        // section 127-0-1-1-64be1e2f:13774f13776:-8000:0000000000003A89 end

        return (array) $returnValue;
    }

    /**
     * Get the structure for the extension/section in parameters
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string extension
     * @param  string structure
     * @return array
     */
    public function getStructure($extension, $structure)
    {
        $returnValue = array();

        // section 127-0-1-1-5f1894ad:12457319d43:-8000:0000000000001A79 begin
		foreach($this->getAllStructures() as $struct){
			if($struct['extension'] == $extension && $struct['id'] == $structure){
				$returnValue = $struct;
				break;
			}
		}
		if (empty($returnValue)) {
			common_logger::w('Structure '.$structure.' not found for extension '.$extension);
    	}

        // section 127-0-1-1-5f1894ad:12457319d43:-8000:0000000000001A79 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getSection
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string extension
     * @param  string structure
     * @param  string section
     * @return array
     */
    public function getSection($extension, $structure, $section)
    {
        $returnValue = array();

        // section 127-0-1-1-64be1e2f:13774f13776:-8000:0000000000003A84 begin
        $structureArr = $this->getStructure($extension, $structure);
        if(is_array($structureArr) && isset($structureArr['sections'][$section])) {
        	$returnValue = $structureArr['sections'][$section];
		}
		if (empty($returnValue)) {
			common_logger::w('Section '.$section.' not found found for structure '.$structure);
    	}
        // section 127-0-1-1-64be1e2f:13774f13776:-8000:0000000000003A84 end

        return (array) $returnValue;
    }

    /**
     * Set the default file source for TAO File Upload.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Repository source The repository to be used as the default TAO File Upload Source.
     * @return void
     */
    public function setUploadFileSource( core_kernel_versioning_Repository $source)
    {
        // section 127-0-1-1-7b77f86d:13d16ab7c5c:-8000:0000000000003C63 begin
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
    	$ext->setConfig(self::CONFIG_UPLOAD_FILESOURCE, $source->getUri());
        // section 127-0-1-1-7b77f86d:13d16ab7c5c:-8000:0000000000003C63 end
    }

    /**
     * Returns the default TAO Upload File source repository.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_versioning_Repository
     */
    public function getUploadFileSource()
    {
        $returnValue = null;

        // section 127-0-1-1-7b77f86d:13d16ab7c5c:-8000:0000000000003C69 begin
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $uri = $ext->getConfig(self::CONFIG_UPLOAD_FILESOURCE);
        if (!empty($uri)) {
        	$returnValue = new core_kernel_versioning_Repository($uri);
        } else {
        	throw new common_Exception('No default repository defined for uploaded files storage.');
        }
        // section 127-0-1-1-7b77f86d:13d16ab7c5c:-8000:0000000000003C69 end

        return $returnValue;
    }

} /* end of class tao_models_classes_TaoService */

?>
