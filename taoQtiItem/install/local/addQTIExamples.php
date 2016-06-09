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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

use oat\taoQtiItem\model\qti\ImportService;

$itemClass	= taoItems_models_classes_ItemsService::singleton()->getRootClass();
$file		= dirname(__FILE__).DIRECTORY_SEPARATOR.'qtiv2p1Examples.zip';

$service = ImportService::singleton();
try {
$service->importQTIPACKFile($file, $itemClass, false);
}
catch (Exception $e){
    common_Logger::e('Error Occurs when importing Qti Exemples ' . $e->getMessage());
    throw $e;
}
