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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */


namespace oat\taoOutcomeUi\controller;

use \core_kernel_classes_Class;
use \tao_actions_TaoModule;
use oat\taoOutcomeUi\model\ReportService;
use oat\taoOutcomeUi\model\StatisticsService;
use oat\tao\helpers\TaoOutcome;

/**
 * SimpleReport Module
 *
 * @author Patrick Plichart, <patrick.plichart@taotesting.com>
 * @package taoOutcomeUi
 *
 */
class SimpleReport extends \tao_actions_CommonModule
{

    /*
     * @var oat\taoOutcomeUi\model\ReportService 
     */
    protected $reportService = null;

    /**
     * constructor
     *
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     */
    public function __construct()
    {

        parent::__construct();
        $this->service = StatisticsService::singleton();
        $this->defaultData();
        //TODO define a hook for implemeitng differently the report structure with an interface
        $this->reportService = ReportService::singleton();
    }


    /**
     * build the report using statistics service and feeding the report service
     *
     * @author Patrick Plichart, <patrick.plichart@taotesting.com>
     * @return html stream
     */
    public function build()
    {

        $selectedDeliveryClass = $this->getCurrentClass();

        $this->reportService->setContextClass($selectedDeliveryClass);
        //extract statistics using the statistics service and feed the report
        $startTime = microtime(true);
        $this->reportService->setDataSet($this->service->extractDeliveryDataSet($selectedDeliveryClass));
        $dataTime = microtime(true);
        $this->setData("dataExtractionTime", round($dataTime - $startTime, 3));
        //add the required graphics computation and textual information for this particular report using reportService
        $reportData = $this->reportService->buildSimpleReport();
        $this->setData("reportBuildTime", round(microtime(true) - $dataTime, 3));
        foreach ($reportData as $dataIdentifier => $value) {
            $this->setData($dataIdentifier, $value);
        }
        //and select the corresponding view structure, could be (?) switched to something different
        $this->setView('simpleReport.tpl');
    }
}
