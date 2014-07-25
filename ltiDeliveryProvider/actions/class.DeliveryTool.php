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

/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package ltiDeliveryProvider
 * @subpackage actions
 */
class ltiDeliveryProvider_actions_DeliveryTool extends taoLti_actions_ToolModule
{

    /**
     * (non-PHPdoc)
     * @see taoLti_actions_ToolModule::getTool()
     */
    protected function getTool()
    {
        return ltiDeliveryProvider_models_classes_LTIDeliveryTool::singleton();
    }

    /**
     * Returns the delivery associated with the current link
     * either from url or from the remote_link if configured
     * returns null if none found
     *
     * @return core_kernel_classes_Resource
     */
    private function getDelivery()
    {
        $returnValue = null;
        //passed as aprameter
        if ($this->hasRequestParameter('delivery')) {
            $returnValue = new core_kernel_classes_Resource($this->getRequestParameter('delivery'));
        } else {
            // encoded in url
            $rootUrlPath = parse_url(ROOT_URL, PHP_URL_PATH);
            $absPath = parse_url('/' . ltrim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
            if (substr($absPath, 0, strlen($rootUrlPath)) != $rootUrlPath) {
                throw new ResolverException('Request Uri ' . $request . ' outside of TAO path ' . ROOT_URL);
            }
            $relPath = substr($absPath, strlen($rootUrlPath));
            $parts = explode('/', $relPath, 4);
            ;
            if (count($parts) == 4) {
                list ($extension, $module, $action, $codedUri) = $parts;
                $params = unserialize(base64_decode($codedUri));
                $returnValue = new core_kernel_classes_Resource($params['delivery']);
            } else {
                // stored in link
                $returnValue = ltiDeliveryProvider_models_classes_LTIDeliveryTool::singleton()->getDeliveryFromLink();
            }
        }
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoLti_actions_ToolModule::run()
     */
    protected function run()
    {
        $compiledDelivery = $this->getDelivery();
        
        if (is_null($compiledDelivery)) {
            if (tao_helpers_funcACL_funcACL::hasAccess('ltiDeliveryProvider', 'LinkConfiguration', 'configureDelivery')) {
                // user authorised to select the Delivery
                $this->redirect(tao_helpers_Uri::url('configureDelivery', 'LinkConfiguration', null));
            } else {
                // user NOT authorised to select the Delivery
                $this->returnError(__('This tool has not yet been configured, please contact your instructor'), false);
            }
        } else {
            if (tao_helpers_funcACL_funcACL::hasAccess('ltiDeliveryProvider', 'LinkConfiguration', 'configureDelivery')) {
                $this->redirect(_url('showDelivery', 'LinkConfiguration', null, array('uri' => $compiledDelivery->getUri())));
            } elseif (tao_helpers_funcACL_funcACL::hasAccess('ltiDeliveryProvider', 'DeliveryRunner', 'runDeliveryExecution')) {
                $deliveryExecution = $this->getTool()->startResumeDelivery($compiledDelivery);
                $this->redirect(_url('runDeliveryExecution', 'DeliveryRunner', null, array('uri' => $deliveryExecution->getUri())));
            } else {
                $this->returnError(__('Access to this functionality is restricted to students'), false);
            }
        }
    }
}