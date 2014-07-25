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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoQtiItem\controller;

use tao_actions_CommonModule;
use oat\taoQtiItem\helpers\CssHelper;
use tao_helpers_Request;
use common_exception_IsAjaxAction;
use common_exception_MissingParameter;
use common_exception_InvalidArgumentType;



/**
 * Class QtiCssAuthoring
 *
 * @package oat\taoQtiItem\controller
 */
class QtiCssAuthoring extends tao_actions_CommonModule {

    /**
     * Save custom CSS as file
     *
     * @throws \common_exception_IsAjaxAction
     */
    public function save() {
        if (!tao_helpers_Request::isAjax()) {
            throw new common_exception_IsAjaxAction(__METHOD__);
        }
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        if (!$this->hasRequestParameter('stylesheetUri')) {
            throw new common_exception_MissingParameter('stylesheetUri', __METHOD__);
        }
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        
        $item = new \core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $lang = $this->getRequestParameter('lang');
        $styleSheet = $this->getRequestParameter('stylesheetUri');
        if (!\tao_helpers_File::securityCheck($styleSheet, true)) {
            throw new \common_exception_Error('invalid stylesheet path "'.$styleSheet.'"');
        }
        
        
        CssHelper::saveCssFile($item, $lang, $styleSheet, $this -> getCssArray());
    }

    /**
     * Load the custom styles as JSON
     *
     * @throws \common_exception_IsAjaxAction
     * @throws \common_exception_MissingParameter
     */
    public function load() {

        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        if (!$this->hasRequestParameter('stylesheetUri')) {
            throw new common_exception_MissingParameter('stylesheetUri', __METHOD__);
        }
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        
        $item = new \core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $lang = $this->getRequestParameter('lang');
        $styleSheet = $this->getRequestParameter('stylesheetUri');
        if (!\tao_helpers_File::securityCheck($styleSheet, true)) {
            throw new \common_exception_Error('invalid stylesheet path "'.$styleSheet.'"');
        }



        $cssArray = CssHelper::loadCssFile($item, $lang, $styleSheet);
        echo json_encode($cssArray);
    }

    /**
     * Convert CSS JSON to array
     *
     * @return mixed
     * @throws \common_exception_MissingParameter
     * @throws \common_exception_InvalidArgumentType
     */
    private function getCssArray() {
        if (!$this->hasRequestParameter('cssJson')) {
            throw new common_exception_MissingParameter('cssJson', __CLASS__.'::'.\Context::getInstance()->getActionName());
        }
        $cssArr = json_decode($_POST['cssJson'], true);
        if(!is_array($cssArr)) {
            throw new common_exception_InvalidArgumentType(__CLASS__,\Context::getInstance()->getActionName(), 0, 'json encoded array');
        }
        return $cssArr;

    }

    /**
     * Download custom styles
     */
    public function download(){

        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        if (!$this->hasRequestParameter('stylesheetUri')) {
            throw new common_exception_MissingParameter('stylesheetUri', __METHOD__);
        }
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }


        $item = new \core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $lang = $this->getRequestParameter('lang');
        $styleSheet = $this->getRequestParameter('stylesheetUri');

        if (!\tao_helpers_File::securityCheck($styleSheet, true)) {
            throw new \common_exception_Error('invalid stylesheet path "'.$styleSheet.'"');
        }

        header('Set-Cookie: fileDownload=true');
        setcookie('fileDownload','true', 0, '/');
        header('Content-type: application/octet-stream');
        header(sprintf('Content-Disposition: attachment; filename=%s', basename($styleSheet)));
        echo CssHelper::downloadCssFile($item, $lang, $styleSheet);
    }

}
