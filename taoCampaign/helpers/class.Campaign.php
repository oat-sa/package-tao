<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * Campaign Controller provide actions performed from url resolution
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoCampaign
 * @subpackage helpers
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoCampaign_helpers_Campaign
{
	public static function renderCampaignTree(core_kernel_classes_Resource $delivery) {

		// ensure constant is known since this helper can be called out of context
		$campaignExt = common_ext_ExtensionsManager::singleton()->getExtensionById('taoCampaign');
		$loader = new common_ext_ExtensionLoader($campaignExt);
		$loader->load();
		
		$property = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
		$tree = tao_helpers_form_GenerisTreeForm::buildTree($delivery, $property);
		$tree->setData('title', __('Add to delivery campaign'));
		return $tree->render();

	}		
}
?>