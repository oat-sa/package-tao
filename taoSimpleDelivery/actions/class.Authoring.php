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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

class taoSimpleDelivery_actions_Authoring extends tao_actions_TaoModule
{
	protected function getRootClass() {
	    $model = new taoSimpleDelivery_models_classes_ContentModel();
		return $model->getClass();
	}
    
    public function save()
    {
        $saved = false;
         
        $instance = $this->getCurrentInstance();
        $testUri = tao_helpers_Uri::decode($this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_DELIVERYCONTENT_TEST)));
    
        $saved = $instance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST ), $testUri);
         
        echo json_encode(array(
            'saved' => $saved
        ));
    }
}