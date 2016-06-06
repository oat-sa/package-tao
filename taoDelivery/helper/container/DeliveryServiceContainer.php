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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoDelivery\helper\container;

use oat\oatbox\service\ServiceManager;
use oat\taoDelivery\model\AssignmentService;
use oat\tao\helpers\Template;

class DeliveryServiceContainer extends AbstractContainer
{
    /**
     * @inheritDoc
     */
    protected $loaderTemplate = 'DeliveryServer/container/service/loader.tpl';

    /**
     * @inheritDoc
     */
    protected $contentTemplate = 'DeliveryServer/container/service/template.tpl';

    /**
     * The name of the extension containing the loader template
     * @var string
     */
    protected $templateExtension = 'taoDelivery';
    
    /**
     * @inheritDoc
     */
    protected function init()
    {
        $delivery = $this->deliveryExecution->getDelivery();
        $runtime = ServiceManager::getServiceManager()->get(AssignmentService::CONFIG_ID)->getRuntime($delivery);
        $this->setData('serviceApi', \tao_helpers_ServiceJavascripts::getServiceApi($runtime, $this->deliveryExecution->getIdentifier()));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\helper\container\AbstractContainer::getHeaderTemplate()
     */
    protected function getHeaderTemplate()
    {
        return Template::getTemplate($this->loaderTemplate, $this->templateExtension);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\helper\container\AbstractContainer::getBodyTemplate()
     */
    protected function getBodyTemplate()
    {
        return Template::getTemplate($this->contentTemplate, $this->templateExtension);        
    }
}
