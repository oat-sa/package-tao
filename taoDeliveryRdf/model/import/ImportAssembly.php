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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */
namespace oat\taoDeliveryRdf\model\import;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\Action;
use oat\taoDeliveryRdf\model\import\Assembler;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
/**
 * Exports the specified Assembly
 * 
 * @author Joel Bout
 *
 */
class ImportAssembly extends ConfigurableService implements Action
{
    /**
     *
     * @param unknown $params
     */
    public function __invoke($params) {
        if (count($params) < 1) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Usage: %s ASSEMBLY_FILE [ASSEMBLY_FILE_2] [ASSEMBLY_FILE_3] ...', __CLASS__));
        }

        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDeliveryRdf');
        
        $deliveryClass = DeliveryAssemblyService::singleton()->getRootClass()->createSubClass('Import '.\tao_helpers_Date::displayeDate(time()));
        $importer = new Assembler();
        $report = new \common_report_Report(\common_report_Report::TYPE_INFO, __('Importing %1$s files into \'%2$s\'', count($params), $deliveryClass->getLabel()));
        while (!empty($params)) {
            $file = array_shift($params);
            $report->add($importer->importDelivery($deliveryClass, $file));
        }
        return $report;
    }

}
