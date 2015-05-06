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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
$extension = common_ext_ExtensionsManager::singleton()->getExtensionById('filemanager');
$dataPath = $extension ->getDir() . 'views' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
$dataUrl = $extension ->getConstant('BASE_WWW') . 'data/';

tao_helpers_File::emptyDirectory($dataPath);

$fileSystem = tao_models_classes_FileSourceService::singleton()->addLocalSource('Filemanager Directory', $dataPath);

$provider = tao_models_classes_fsAccess_DirectAccessProvider::spawnProvider($fileSystem, $dataUrl);
filemanager_helpers_FileUtils::setAccessProvider($provider);