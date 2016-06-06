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
namespace oat\taoDeliveryRdf\model\export;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\Action;
use oat\taoDeliveryRdf\model\import\Assembler;
/**
 * Exports the specified Assembly Class
 * 
 * @author Joel Bout
 *
 */
class ExportAssemblyClass extends ConfigurableService implements Action
{
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\action\Action::__invoke()
     */
    public function __invoke($params) {
        
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDeliveryRdf');
        
        if (count($params) != 2) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Usage: %s DELIVERY_CLASS_URI OUTPUT_DIRECTORY', __CLASS__));
        }

        $deliveryClassUri = array_shift($params);
        $deliveryClass = new \core_kernel_classes_Class($deliveryClassUri);
        
        $dir = array_shift($params);
        if (!file_exists($dir) && !mkdir($dir)) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Directory %s doesn\'t exist', $dir));
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        $report = new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Exporting %s', $deliveryClass->getLabel()));
        foreach ($deliveryClass->getInstances(true) as $delivery) {
            $destFile = $dir.\tao_helpers_File::getSafeFileName($delivery->getLabel()).'.zip';
            $tmpFile = Assembler::exportCompiledDelivery($delivery);
            \tao_helpers_File::move($tmpFile, $destFile);
            $report->add(new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('Exported %1$s to %2$s', $delivery->getLabel(), $destFile)));
        }
        
        return $report;
    }

}
