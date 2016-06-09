<?php

namespace oat\taoQtiItem\install\scripts;

use oat\oatbox\service\ServiceManager;
use oat\taoQtiItem\model\ValidationService;

class addValidationSettings extends \common_ext_action_InstallAction
{
    public function __invoke($params)
    {

        $this->setServiceLocator(ServiceManager::getServiceManager());
        $serviceManager = $this->getServiceManager();

        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
        if($ext->hasConfig('contentValidation')){
            $ext->unsetConfig('contentValidation');
        }

        if($ext->hasConfig('manifestValidation')){
            $ext->unsetConfig('manifestValidation');
        }

        //Set Validation service
        $validationService = new ValidationService();
        $validationService->setServiceManager($serviceManager);
        $serviceManager->register(ValidationService::SERVICE_ID, $validationService);

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Validation service has been successfully set up');
    }
}