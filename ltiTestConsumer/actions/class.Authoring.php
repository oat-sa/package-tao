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
 */

/**
 * Controller for actions related to the authoring of the simple test model
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTests
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class ltiTestConsumer_actions_Authoring extends tao_actions_SaSModule {

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_SaSModule::getClassService()
	 */
	protected function getClassService() {
		return taoTests_models_classes_TestsService::singleton();
	}
	
	/**
	 * save the related items from the checkbox tree or from the sequence box
	 * @return void
	 */
	public function save()
	{
	    $saved = false;
	    
	    $instance = $this->getCurrentInstance();
        $launchUrl = $this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_LTI_LINK_LAUNCHURL));	    
        $consumerUrl = $this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_LTI_LINK_CONSUMER));
        if (empty($launchUrl)) {
            return $this->returnError('Launch URL is required');
        }
        if (empty($consumerUrl)) {
            return $this->returnError('Consumer is required');
        }
        $consumer = new core_kernel_classes_Resource(tao_helpers_Uri::decode($consumerUrl));
        
        $saved = $instance->setPropertiesValues(array(
            PROPERTY_LTI_LINK_LAUNCHURL => $launchUrl,
            PROPERTY_LTI_LINK_CONSUMER => $consumer
        ));
	    
	    echo json_encode(array(
	    	'saved' => $saved
	    ));
	}
}