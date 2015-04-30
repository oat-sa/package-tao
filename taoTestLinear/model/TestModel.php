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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\model;

use taoTests_models_classes_TestModel;
use common_ext_ExtensionsManager;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;

/**
 * the linear TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTestLinear

 */
class TestModel
	implements taoTests_models_classes_TestModel
{

	/**
     * (non-PHPdoc)
	 * @see taoTests_models_classes_TestModel::__construct()
	 */
	public function __construct() {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoTestLinear'); // loads the extension
	}

    /**
     * @see taoTests_models_classes_TestModel::getAuthoringUrl()
     */
    public function getAuthoringUrl( core_kernel_classes_Resource $test) {
        return _url('index', 'Authoring', 'taoTestLinear', array('uri' => $test->getUri()));
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
        $itemUris = array();
        foreach ($items as $item) {
            $itemUris[] = $item->getUri();
        }
        $this->save($test, $itemUris);
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        /** @var \core_kernel_classes_Literal $directoryId */
        $directoryId = $test->getOnePropertyValue($propInstanceContent);
        if(is_null($directoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }

        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);
        if(is_dir($directory->getPath())){
            \tao_helpers_File::delTree($directory->getPath());
        }

		$test->removePropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getItems()
     */
    public function getItems( core_kernel_classes_Resource $test) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        //get the DirectoryId
        $directoryId = $test->getOnePropertyValue($propInstanceContent);
        if(is_null($directoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }
        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);


        $items = array();
        if (!is_null($directory)) {
            $file = $directory->getPath().'content.json';
            //get the content of file or the encoded items if it's an old test
            if(is_dir($directory->getPath())){
                $json = file_get_contents($file);
            }
            else{
                $json = $directoryId;
            }

            $decoded = json_decode($json, true);
            if (isset($decoded['itemUris']) && is_array($decoded['itemUris'])) {
                foreach ($decoded['itemUris'] as $uri) {
                    $items[] = new core_kernel_classes_Resource($uri);
                }
            } else if(is_array($decoded)){
                foreach ($decoded as $uri) {
                    $items[] = new core_kernel_classes_Resource($uri);
                }
            }
            else{
                \common_Logger::w('Unable to decode item Uris');
            }
        }

        return $items;
    }

    public function getConfig( core_kernel_classes_Resource $test) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        //get the DirectoryId
        $directoryId = $test->getOnePropertyValue($propInstanceContent);
        if(is_null($directoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }
        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);


        $config = array();
        if (!is_null($directory)) {
            $file = $directory->getPath().'content.json';
            //get the content of file or the encoded items if it's an old test
            if(is_dir($directory->getPath())){
                $json = file_get_contents($file);
            }
            else{
                $json = $directoryId;
            }

            $decoded = json_decode($json, true);
            if (isset($decoded['config']) && is_array($decoded['config'])) {
                foreach ($decoded['config'] as $key => $value) {
                    $config[$key] = $value;
                }
            } else if(!is_array($decoded)){
                \common_Logger::w('Unable to decode item Uris');
            }
        }

        return $config;
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {

        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        //get the source DirectoryId
        $sourceDirectoryId = $source->getOnePropertyValue($propInstanceContent);

        if(is_null($sourceDirectoryId)){
            throw new \common_exception_FileSystemError(__('Unknown test directory'));
        }
        //get the real directory (or the encoded items if an old test)
        $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($sourceDirectoryId->literal);

        //an old test so create the content.json to copy
        if(!is_dir($directory->getPath())){
            $directory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
            $file = $directory->getPath().'content.json';
            file_put_contents($file, json_encode($sourceDirectoryId));
        }

        $destDirectoryId = $destination->getOnePropertyValue($propInstanceContent);

        if(is_null($destDirectoryId)){
            //create the destination directory
            $destDirectory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
        }
        else{
            //get the real directory (or the encoded items if an old test)
            $destDirectory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($destDirectoryId->literal);

            //an old test so create the directory
            if(!is_dir($destDirectory->getPath())){
                $destDirectory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
            }
        }

        \tao_helpers_File::copy($directory->getPath(), $destDirectory->getPath(), true);

        $destination->editPropertyValues($propInstanceContent, $destDirectory->getId());
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
        // do nothing
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getCompilerClass()
     */
    public function getCompilerClass() {
        return 'oat\\taoTestLinear\\model\\TestCompiler';
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getPackerClass()
     */
    public function getPackerClass() {
        throw new common_exception_NotImplemented("The packer isn't yet implemented for Linear tests");
    }

    /**
     *
     * @param core_kernel_classes_Resource $test
     * @param array $itemUris
     * @return boolean
     */
    public function save(core_kernel_classes_Resource $test, array $itemUris) {
        $propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        //get Directory ID
        $directoryId = $test->getOnePropertyValue($propInstanceContent);
        //null so create one
        if(is_null($directoryId)){
            $directory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
        }
        else{
            //get the real directory (or the encoded items if an old test)
            $directory = \tao_models_classes_service_FileStorage::singleton()->getDirectoryById($directoryId->literal);
            if(!is_dir($directory->getPath())){
                //create a new directory if items are stored in content
                $directory = \tao_models_classes_service_FileStorage::singleton()->spawnDirectory(true);
            }
        }
        $file = $directory->getPath().'content.json';

        file_put_contents($file, json_encode($itemUris));
        return $test->editPropertyValues($propInstanceContent, $directory->getId());
    }
}
