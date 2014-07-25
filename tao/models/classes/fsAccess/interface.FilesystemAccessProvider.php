<?php
/**
 * 
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Grants direct Access to compiled data
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
interface tao_models_classes_fsAccess_FilesystemAccessProvider
{
	/**
	 * common constructor to ensure we can instantiate the class
	 * 
	 * @param core_kernel_fileSystem_FileSystem $filesystem
	 */
	public function __construct(core_kernel_fileSystem_FileSystem $filesystem);

	/**
	 * Returns the filesystem access is provided to
	 * 
	 * @return core_kernel_fileSystem_FileSystem
	 */
	public function getFileSystem();
	
	/**
	 * Returns an url that gives access to all files within the
	 * specified subdirectory
	 * 
	 * @param core_kernel_file_File $directory
	 */
	public function getAccessUrl(core_kernel_file_File $directory);

	/**
	 * prepare the system for this item acces provider
	 * called during instalation and switch of provider 
	 */
	public function prepareProvider();
	
	/**
	 * cleen up modifications done by this item acces provider
	 */
	public function cleanupProvider();
	
}