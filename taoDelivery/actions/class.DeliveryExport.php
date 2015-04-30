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
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Export controller that addes the published delivery exporter
 * 
 * @package taoDelivery
 * 
 */
class taoDelivery_actions_DeliveryExport extends tao_actions_Export {
    
    /**
     * (non-PHPdoc)
     * @see tao_actions_Import::getAvailableImportHandlers()
     */
	protected function getAvailableExportHandlers() {
		$returnValue = parent::getAvailableExportHandlers();
		
		// disabled due to possible security exploit that allows execution of php data in qti test driver
        // $returnValue[] = new taoDelivery_models_classes_export_AssemblyExporter();
        		
		return $returnValue;
	}
}