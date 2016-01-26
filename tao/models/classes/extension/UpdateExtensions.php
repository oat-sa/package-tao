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

use common_report_Report;
/**
 * Extends the generis updater to take into account
 * the translation files 
 */
class UpdateExtensions extends \common_ext_UpdateExtensions
{
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\action\Action::__invoke()
     */
    public function __invoke($params)
    {
        $report = parent::__invoke($params);
        
        // regenrate locals
        $files = \tao_models_classes_LanguageService::singleton()->generateClientBundles();
        if (count($files) > 0) {
            $report->add(new common_report_Report(common_report_Report::TYPE_SUCCESS,__('Successfully updated %s client translation bundles', count($files))));
        } else {
            $report->add(new common_report_Report(common_report_Report::TYPE_ERROR,__('No client translation bundles updated')));
        }
        
        return $report;
    }
}
