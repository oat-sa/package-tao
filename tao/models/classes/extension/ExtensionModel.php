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

namespace oat\tao\model\extension;

use tao_models_classes_LanguageService;
use common_ext_Extension;
use core_kernel_classes_Resource;
use oat\generis\model\data\ModelManager;
use oat\tao\helpers\translation\rdf\RdfPack;

class ExtensionModel extends \common_ext_ExtensionModel
{

    public function __construct(common_ext_Extension $extension) {
        parent::__construct($extension);
        $this->addLanguages($extension);
    }
    
    protected function addLanguages($extension) {
        $langService = tao_models_classes_LanguageService::singleton();
        $dataUsage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_DATA);
        $dataOptions = array();

        $model = ModelManager::getModel();
        foreach ($langService->getAvailableLanguagesByUsage($dataUsage) as $lang) {
            $langCode = $langService->getCode($lang);
            $pack = new RdfPack($langCode, $extension);
            $this->append($pack->getIterator());
        }
    }
}