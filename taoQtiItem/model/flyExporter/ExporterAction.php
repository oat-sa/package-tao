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

namespace oat\taoQtiItem\model\flyExporter;

use oat\oatbox\action\Action;
use oat\oatbox\service\ConfigurableService;
use oat\taoQtiItem\model\flyExporter\simpleExporter\SimpleExporter;

/**
 * Command line entry to generate item export
 *
 * Class ExporterAction
 * @package oat\taoQtiItem\model\simpleExporter
 */
class ExporterAction extends ConfigurableService implements Action
{
    /**
     * Call to launch export method of exporter
     *
     * php index.php 'oat\taoQtiItem\model\flyExporter\ExporterAction' $uri
     *
     * @param $params $params[0] is optional uri parameter
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        try {
            $exporterService = $this->getServiceManager()->get(SimpleExporter::SERVICE_ID);

            $uri = isset($params[0]) ? $params[0] : null;
            $filename = $exporterService->export($uri);

            return new \common_report_Report(\common_report_Report::TYPE_SUCCESS,
                "\nExport end.\nCSV export is located at: " . $filename . "\n");
        } catch (\Exception $e) {
            \common_Logger::w('Error during item metadata export: ' . $e->getMessage());
            return new \common_report_Report(\common_report_Report::TYPE_ERROR, "\n" . $e->getMessage() . "\n");
        }
    }
}