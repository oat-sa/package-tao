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
?>
<?php
/**
 * This controller provide the actions to export items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 
 *
 */
class taoItems_actions_ItemExport extends tao_actions_Export {

    /**
     * overwrite the parent index to add the requiresRight for Items only
     *
     * @requiresRight id READ
     * @see tao_actions_Import::index()
     */
    public function index()
    {
        parent::index();
    }

	protected function getAvailableExportHandlers() {
		$returnValue = parent::getAvailableExportHandlers();
		
		$resources = $this->getResourcesToExport();
		$itemModels = array();
		foreach ($resources as $resource) {
			$model = taoItems_models_classes_ItemsService::singleton()->getItemModel($resource);
			if (!is_null($model)) {
				$itemModels[$model->getUri()] = $model;
			}
		}
		foreach ($itemModels as $model) {
			$impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($model);
			if (in_array('tao_models_classes_export_ExportProvider', class_implements($impl))) {
				foreach ($impl->getExportHandlers() as $handler) {
					array_unshift($returnValue, $handler);
				}
			}
		}
		
		return $returnValue;
	}
    
    /**
     * Function returns items to export. 
     * Items that has no content (<b>QTI items</b> without <i>qti.xml</i> file or empty <b>Open Web Items</b>) will be filtered
     * @return core_kernel_classes_Resource[] An array of items.
     */
    protected function getResourcesToExport()
    {
        $resources = parent::getResourcesToExport();
        $service = taoItems_models_classes_ItemsService::singleton();
        
        $resources = array_filter($resources, function ($val) use ($service) {
            return $service->hasItemContent($val);
        });
        
        return $resources;
    }

}