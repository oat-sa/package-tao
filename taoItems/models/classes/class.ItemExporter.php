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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * The ItemExporter class acts as the foundation for exporting item in XML packages. Developpers
 * who wants to create dedicated export behaviours can extend this class. To create a new export behaviour
 * developpers only need to implement the export abstract method. This is especially useful if a developper
 * needs to apply specific export procedures for items of a given Item Model. When you extend this class, you have
 * to respect a class naming convention. Actually, the class name must logicaly bound to the related Item Model label
 * in the knowledge base.
 * 
 * QTI Items have their Item Model labeled QTI. Your ItemExporter should be then:
 * - named QTIItemExport where QTI is the Item model label, and ItemExport a suffix by convention.
 * - and the file containing its definition should be named class.QTIItemExport.php.
 * 
 * When an item matching the QTI item model will be exported, the class QTIItemExporter will be dynamically loaded
 * and its QTIItemExporter::export() method invoked.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 
 */

use Psr\Http\Message\StreamInterface;

abstract class taoItems_models_classes_ItemExporter {

	private $item;
	private $zip;

	/**
	 * Creates a new instance of ItemExporter for a particular item.
	 * 
	 * @param core_kernel_classes_Resource $item The item to be exported.
	 * @param ZipArchive $zip The ZIP archive were the files have to be exported.
	 */
	public function __construct(core_kernel_classes_Resource $item, ZipArchive $zip) {
	    $this->setItem($item);
		$this->setZip($zip);
	}
	
	/**
	 * Obtains a reference on the TAO Item Service.
	 *
	 * @return taoItems_models_classes_ItemsService A TAO Item Service.
	 */
	protected function getItemService() {
		return taoItems_models_classes_ItemsService::singleton();
	}
	
	/**
	 * Obtains a reference on the currently exported instance of Item.
	 *
	 * @return core_kernel_classes_Resource The currently exported item.
	 */
	protected function getItem() {
		return $this->item;
	}
	
	protected function setItem(core_kernel_classes_Resource $item) {
	    $this->item = $item;
	}
	
	/**
	 * Obtains a reference on the Zip Archive where the files related to the exported items will be stored.
	 * 
	 * @return ZipArchive A created and open ZipArchive instance.
	 */
	public function getZip() {
		return $this->zip;
	}
	
	public function setZip(ZipArchive $zip) {
	    $this->zip = $zip;
	}
	
	/**
	 * Obtains a reference on the Item Model related to the currently exported item instance.
	 *
	 * @return core_kernel_classes_Resource A resource depicting the Item Model.
	 */
	protected function getItemModel() {
		try {
			return $this->getItem()->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
		} 
		catch (common_Exception $e) {
			return null;
		}
	}
	
	/**
	 * Gets the location of the item of the file system. Usually, it is a folder on the file system located
	 * at /taoItems/data/i123 where i123 is the ID of the item in the knowledge base.
	 * 
	 * @return string the location of the item on the file system.
	 */
	protected function getItemLocation() {
		$returnValue = null;

		$returnValue = $this->getItemService()->getItemFolder($this->getItem());
		
		return $returnValue;
	}
	
	/**
	 * Add files or folders (and their content) to the Zip Archive that will contain all the files to the current export session.
	 * For instance, if you want to copy the file 'taoItems/data/i123/item.xml' as 'myitem.xml' to your archive call addFile('path_to_item_location/item.xml', 'myitem.xml').
	 * As a result, you will get a file entry in the final ZIP archive at '/i123/myitem.xml'.
	 * 
	 * @param string | StreamInterface $src The path to the source file or folder to copy into the ZIP Archive.
	 * @param string *dest The <u>relative</u> to the destination within the ZIP archive.
	 * @return integer The amount of files that were transfered from TAO to the ZIP archive within the method call.
	 */
	public function addFile($src, $dest) {

		$zip = $this->getZip();
		return tao_helpers_File::addFilesToZip($zip, $src, $dest);
	}
	
	public abstract function export();
}
?>