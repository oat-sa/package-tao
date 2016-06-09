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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiItem\install\scripts;

use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\flyExporter\simpleExporter\ItemExporter;

/**
 * Create export directory
 *
 * Class createExportDirectory
 * @package oat\taoQtiItem\install\scripts
 */
class createExportDirectory extends \common_ext_action_InstallAction
{
    /**
     * Create filesystem for ItemExporter service
     *
     * @param $params
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        try {

            $serviceManager = ServiceManager::getServiceManager();
            $fsService = $serviceManager->get(FileSystemService::SERVICE_ID);
            $fsService->createLocalFileSystem(ItemExporter::EXPORT_FILESYSTEM);
            $serviceManager->register(FileSystemService::SERVICE_ID, $fsService);

        } catch (\Exception $e) {
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, 'Fail to create export directory.');
        }

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Export directory created.');
    }
}