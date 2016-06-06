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
use core_kernel_classes_Resource;
use oat\taoMediaManager\model\fileManagement\FileManager;
use tao_helpers_form_Form;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoMediaManager
 */
class ZipExporter implements \tao_models_classes_export_ExportHandler
{


    /**
     * Returns a textual description of the import format
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Zip');
    }


    /**
     * Returns a form in order to prepare the
     *
     * @param core_kernel_classes_Resource $resource the users selected resource or class
     * @return tao_helpers_form_Form
     */
    public function getExportForm(core_kernel_classes_Resource $resource)
    {

        $formData = array('resource' => $resource);
        $form = new ZipExportForm($formData);
        return $form->getForm();
    }

    /**
     * Export the resources to a file stored in $destinations
     *
     * @param array $formValues the values returned by the form provided by getForm
     * @param string $destPath path to export the resources to
     * @return string filepath
     */
    public function export($formValues, $destPath)
    {
        $file = null;
        if (isset($formValues['filename']) && isset($formValues['id'])) {

            $class = new core_kernel_classes_Class($formValues['id']);
            \common_Logger::i('Exporting ' . $class->getUri());

            $exportClasses = array();
            if ($class->isClass()) {
                $subClasses = $class->getSubClasses(true);
                $exportData = array($class->getLabel() => $class->getInstances());
                foreach ($subClasses as $subClass) {
                    $instances = $subClass->getInstances();
                    $exportData[$subClass->getLabel()] = $instances;

                    //get Class path
                    $parents = $subClass->getParentClasses();
                    $parent = array_shift($parents);
                    if (array_key_exists($parent->getLabel(), $exportClasses)) {
                        $exportClasses[$subClass->getLabel()] = $exportClasses[$parent->getLabel()] . '/' . $subClass->getLabel();
                    } else {
                        $exportClasses[$subClass->getLabel()] = $subClass->getLabel();
                    }
                }
            } else {
                $exportData = array($class->getLabel() => array($class));
            }

            $file = $this->createZipFile($formValues['filename'], $exportClasses, $exportData);
        }
        return $file;
    }

    private function createZipFile($filename, $exportClasses = array(), $exportFiles = array())
    {
        $zip = new \ZipArchive();
        $baseDir = \tao_helpers_Export::getExportPath();
        $path = $baseDir . '/' . $filename . '.zip';
        if ($zip->open($path, \ZipArchive::CREATE) !== TRUE) {
            throw new \common_Exception('Unable to create zipfile ' . $path);
        }
        if ($zip->numFiles === 0) {
            $nbFiles = 0;
            foreach ($exportFiles as $label => $files) {
                $archivePath = '';
                /** @var $class \core_kernel_classes_Class */
                if (array_key_exists($label, $exportClasses)) {
                    $archivePath = $exportClasses[$label] . '/';
                    $zip->addEmptyDir($archivePath);
                    $nbFiles++;
                }
                $nbFiles += count($files);
                //create the directory

                foreach ($files as $file) {
                    //add each file in the correct directory
                    $link = $file->getUniquePropertyValue(new \core_kernel_classes_Property(MEDIA_LINK));
                    if ($link instanceof \core_kernel_classes_Literal) {
                        $link = $link->literal;
                    }
                    $zip->addFile(FileManager::getFileManagementModel()->retrieveFile($link), $archivePath . $file->getLabel());
                }

            }

            \common_Logger::i("Number of file : " . $zip->numFiles . " / " . $nbFiles);
        }

        $zip->close();

        return $path;

    }
}
