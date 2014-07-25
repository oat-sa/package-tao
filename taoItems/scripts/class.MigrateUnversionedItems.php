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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateUnversionedItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.05.2012, 15:53:18 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-includes begin
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-includes end

/* user defined constants */
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-constants begin
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-constants end

/**
 * This script will probably no longer work
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 * @deprecated
 */
class taoItems_scripts_MigrateUnversionedItems
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute items
     *
     * @access public
     * @var array
     */
    public $items = array();

    /**
     * Short description of attribute itemModels
     *
     * @access public
     * @var array
     */
    public $itemModels = array();

    /**
     * Short description of attribute itemService
     *
     * @access public
     * @var Service
     */
    public $itemService = null;

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function preRun()
    {
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C2 begin
		
		
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C2 end
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function run()
    {
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C4 begin
		
		$this->itemService = taoItems_models_classes_ItemsService::singleton();
		$this->itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$items = $itemClass->getInstances(true);
		$this->itemModels = array();
		$this->items = array();
		
		$this->out('generis default language : '.DEFAULT_LANG);
		foreach($items as $item){
			
			$itemModel = $this->itemService->getItemModel($item);
			if(!is_null($itemModel)){
				
				//lazy loading item model data:
				$itemModelLabel = '';
				$dataFile = '';
				if(isset($this->itemModels[$itemModel->getUri()])){
					$itemModelLabel = $this->itemModels[$itemModel->getUri()]['label'];
					$dataFile = $this->itemModels[$itemModel->getUri()]['file'];
				}else{
					$itemModelLabel = $itemModel->getLabel();
					$dataFile = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY))->literal;
					$this->itemModels[$itemModel->getUri()] = array(
						'label' => $itemModelLabel,
						'file' => $dataFile
					);
				}
				
				$this->setItemData($item, 'model', $itemModel);
				
				//migrate items with an item model only:
				$this->out('migrating item '.$itemModelLabel.' : '.$item->getLabel(). ' ('.$item->getUri().')', array('color'=>'light_cyan'));
				
				//switch from script parameters to one of these options:
//				$this->migrateToUnversionedItem($item);
//				$this->migrateToVersionedItem($item);
				
			}
			
		}
		
		
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C4 end
    }

    /**
     * version all tao items from items created in TAO 2.2 or migrated by the
     * 'migrateToNewItemPath' : e.g.
     * taoItems/data/i123456/EN -> generis/data/versioning/DEFAULT/i123465/itemContent/EN
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    protected function migrateToVersionedItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D3 begin
		
		//copy item content folder to the versioned path
		//get old file content, set file content
		//commit changes
		
		//used languages:
		$model = $this->itemModels[$this->getItemData($item, 'model')];
		$itemModelLabel = $model['label'];
		$dataFile = $model['file'];
		$usedLanguages = $item->getUsedLanguages($this->itemContentProperty);
		
		$oldSourceFolder = substr($item->getUri(), strpos($item->getUri(), '#') + 1);
		$oldSourceFolder = ROOT_PATH . '/taoItems/data/' . $oldSourceFolder . '/';
		$propItemContent = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		switch ($itemModel->getUri()) {
			case TAO_ITEM_MODEL_QTI:
			case TAO_ITEM_MODEL_XHTML:{
				foreach ($usedLanguages as $usedLanguage) {

					$destinationFolder = $this->itemService->getItemFolder($item, $usedLanguage) . '/' . $dataFile;

					//copy item start point
					if ($usedLanguage == DEFAULT_LANG || $usedLanguage == '') {
						$oldSourceFolder .= DEFAULT_LANG;
					} else {
						$oldSourceFolder .= $usedLanguage;
					}
					$source = $oldSourceFolder.'/'.$dataFile;
					$destination = $destinationFolder . '/' . $dataFile;

					$this->out('versioning ' . $destinationFolder . ' to ' . $destinationFolder);
					if (file_exists($oldSourceFolder) && is_dir($oldSourceFolder)) {

						//first copy all source files from source to destination:
						$this->out('copying ' . $oldSourceFolder . ' to ' . $destinationFolder);
						tao_helpers_File::copy($oldSourceFolder, $destinationFolder);

						//delete the old data file
						$content = file_get_contents($source);
						foreach ($item->getPropertyValuesByLg($propItemContent, $usedLanguage)->getIterator() as $file) {
							$file->delete(true);
						}

						//set the versioned file content
						$this->itemService->setItemContent($item, $content, $usedLanguage);
					}
				}
				break;
			}
			default : {
				$this->out('unknown item type : ' . $itemModel->getUri());
				return $returnValue;
			}
		}
		
		$returnValue = true;
		
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D3 end

        return (bool) $returnValue;
    }

    /**
     * unversion all tao items for items created in TAO 2.2 or migrated by the
     * 'migrateToNewItemPath' : e.g.
     * generis/data/versioning/DEFAULT/i123465/itemContent/EN -> taoItems/data/i123456/EN
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    protected function migrateToUnversionedItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D6 begin
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setItemData
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @param  string key
     * @param  string value
     * @return boolean
     */
    protected function setItemData( core_kernel_classes_Resource $item, $key, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D9 begin
		if(!isset($this->items[$item->getUri()])){
			$this->items[$item->getUri()] = array();
		}
		$this->items[$item->getUri()][$key] = $value;
		$returnValue = true;
		
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemData
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @param  string key
     * @return mixed
     */
    protected function getItemData( core_kernel_classes_Resource $item, $key)
    {
        $returnValue = null;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039DE begin
		if(isset($this->items[$item->getUri()]) && $this->items[$item->getUri()][$key]){
			$returnValue = $this->items[$item->getUri()][$key];
		}
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039DE end

        return $returnValue;
    }

} /* end of class taoItems_scripts_MigrateUnversionedItems */

?>