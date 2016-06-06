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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoMediaManager\model;

use core_kernel_classes_Class;
use SebastianBergmann\Exporter\Exception;
use tao_helpers_form_Form;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoMediaManager
 */
class FileImporter implements \tao_models_classes_import_ImportHandler
{


    public function __construct($instanceUri = null)
    {
        $this->instanceUri = $instanceUri;
    }

    /**
     * Returns a textual description of the import format
     *
     * @return string
     */
    public function getLabel()
    {
        return __('File');
    }

    /**
     * Returns a form in order to prepare the import
     * if the import is from a file, the form should include the file element
     *
     * @return tao_helpers_form_Form
     */
    public function getForm()
    {
        $form = new FileImportForm($this->instanceUri);
        return $form->getForm();
    }

    /**
     * Starts the import based on the form
     *
     * @param \core_kernel_classes_Class $class
     * @param \tao_helpers_form_Form $form
     * @return \common_report_Report $report
     */
    public function import($class, $form)
    {
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();
        try {
            $file = $form->getValue('source');

            $service = MediaService::singleton();
            $classUri = $class->getUri();
            if (is_null($this->instanceUri) || $this->instanceUri === $classUri) {
                //if the file is a zip do a zip import
                if ($file['type'] !== 'application/zip') {
                    if (!$service->createMediaInstance($file["uploaded_file"], $classUri, \tao_helpers_Uri::decode($form->getValue('lang')), $file["name"])) {
                        $report = \common_report_Report::createFailure(__('Fail to import media'));
                    } else {
                        $report = \common_report_Report::createSuccess(__('Media imported successfully'));
                    }
                } else {
                    $zipImporter = new ZipImporter();
                    $report = $zipImporter->import($class, $form);
                }
            } else {
                if ($file['type'] !== 'application/zip') {
                    $service->editMediaInstance($file["uploaded_file"], $this->instanceUri, \tao_helpers_Uri::decode($form->getValue('lang')));
                    $report = \common_report_Report::createSuccess(__('Media imported successfully'));
                } else {
                    $report = \common_report_Report::createFailure(__('You can\'t upload a zip file as a media'));
                }
            }


            return $report;

        } catch (\Exception $e) {
            $report = \common_report_Report::createFailure($e->getMessage());
            return $report;
        }
    }
}
