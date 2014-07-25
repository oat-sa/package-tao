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
 * Grants direct Access to compiled data
 * This is the fastest implementation but
 * allows anyone access that guesses the path
 * access to the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_models_classes_fsAccess_ActionAccessProvider
	extends tao_models_classes_fsAccess_BaseAccessProvider
{
	
	public function getAccessUrl(core_kernel_file_File $directory) {
	    throw new common_exception_NotImplemented();
	    /*
		$compiledFolder = taoDelivery_models_classes_DeliveryService::singleton()->getCompiledItemFolder(
			$delivery, $test, $item, array($language)
		);
		$deliveryExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItemRunner');
        $compileBaseFolder = $deliveryExtension->getConstant('COMPILE_FOLDER');
		$relPath = substr($compiledFolder, strlen($compileBaseFolder));
		return _url('access/'.base64_encode($relPath).'/index.html','ItemDeliveryService');
		*/
	}

	public function decodeUrl($url) {
		$rootUrlPath	= parse_url(ROOT_URL, PHP_URL_PATH);
		$absPath		= parse_url('/'.ltrim($url, '/'), PHP_URL_PATH);
		if (substr($absPath, 0, strlen($rootUrlPath)) != $rootUrlPath ) {
			throw new ResolverException('Request Uri '.$request.' outside of TAO path '.ROOT_URL);
		}
		$relPath		= substr($absPath, strlen($rootUrlPath));
		list($extension, $module, $action, $code, $filePath) = explode('/', $relPath, 5);;
		$subPath = base64_decode($code);
		$deliveryExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItemRunner');
        $compileBaseFolder = $deliveryExtension->getConstant('COMPILE_FOLDER');
		return $compileBaseFolder . $subPath . $filePath;
	}
	
	protected function getHtaccessContent() {
		return self::HTACCESS_DENY_CONTENT;
	}
}