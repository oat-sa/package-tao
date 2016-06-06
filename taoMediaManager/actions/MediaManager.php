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

namespace oat\taoMediaManager\actions;

use oat\taoMediaManager\model\editInstanceForm;
use oat\taoMediaManager\model\MediaService;
use oat\taoMediaManager\model\MediaSource;
use oat\taoMediaManager\model\fileManagement\FileManager;
use oat\taoMediaManager\model\fileManagement\FileManagement;

class MediaManager extends \tao_actions_SaSModule
{

    protected function getClassService()
    {
        return MediaService::singleton();
    }

    public function __construct()
    {

        parent::__construct();
        $this->service = $this->getClassService();
        //the service is initialized by default
        $this->defaultData();
    }

    /**
     * Show the form to edit an instance, show also a preview of the media
     */
    public function editInstance()
    {
        $clazz = $this->getCurrentClass();
        $instance = $this->getCurrentInstance();
        $myFormContainer = new editInstanceForm($clazz, $instance);

        $myForm = $myFormContainer->getForm();
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {

                $values = $myForm->getValues();
                // save properties
                $binder = new \tao_models_classes_dataBinding_GenerisFormDataBinder($instance);
                $instance = $binder->bind($values);
                $message = __('Instance saved');

                $this->setData('message', $message);
                $this->setData('reload', true);
            }
        }

        $this->setData('formTitle', __('Edit Instance'));
        $this->setData('myForm', $myForm->render());
        $uri = ($this->hasRequestParameter('id')) ? $this->getRequestParameter('id') : $this->getRequestParameter('uri');

        try{
            $mediaSource = new MediaSource(array());
            $fileInfo = $mediaSource->getFileInfo($uri);

            $mimeType = $fileInfo['mime'];
            $xml = in_array($mimeType, array('application/xml','text/xml'));
            $url = \tao_helpers_Uri::url(
                'getFile',
                'MediaManager',
                'taoMediaManager',
                array(
                    'uri' => $uri,
                )
            );
            $this->setData('xml', $xml);
            $this->setData('fileurl', $url);
            $this->setData('mimeType', $mimeType);

        }catch(\tao_models_classes_FileNotFoundException $e){
            $this->setData('error', __('No file found for this media'));
        }
        $this->setView('form.tpl');

    }

    public function getFile()
    {

        if ($this->hasRequestParameter('uri')) {
            $uri = urldecode($this->getRequestParameter('uri'));

            $mediaSource = new MediaSource(array());
            $fileInfo = $mediaSource->getFileInfo($uri);
            $link = $fileInfo['link'];
            
            $fileManagement = $this->getServiceManager()->get(FileManagement::SERVICE_ID);
            
            if($fileInfo['mime'] === 'application/qti+xml'){
                \tao_helpers_Http::returnStream($fileManagement->getFileStream($link), $fileManagement->getFileSize($link));
                return;
            }
            if($this->hasRequestParameter('xml')){
                $this->returnJson(htmlentities((string)$fileManagement->getFileStream($link)));
            }
            else{
                \tao_helpers_Http::returnStream($fileManagement->getFileStream($link), $fileManagement->getFileSize($link), $fileInfo['mime']);
            }
        } else {
            throw new \common_exception_Error('invalid media identifier');
        }
    }
}
