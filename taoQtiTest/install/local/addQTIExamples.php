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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

$testClass = taoQtiTest_models_classes_QtiTestService::singleton()->getRootClass();
$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qtiv2p1Examples.zip';

try {
    $report = taoQtiTest_models_classes_QtiTestService::singleton()->importMultipleTests(new core_kernel_classes_Class(TAO_TEST_CLASS), $file);
}
catch (Exception $e){
    common_Logger::e('An error occured while importing QTI Test Example. The system reported the following error: ' . $e->getMessage());
    throw $e;
}
