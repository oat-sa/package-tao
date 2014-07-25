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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
/**
 * Service to handle delivery campaigns
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoCampaign
 
 */
class taoCampaign_models_classes_CampaignService
    extends tao_models_classes_ClassService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the rdfClass of campaign
     *
     * @access protected
     * @var core_kernel_classes_Class
     */
    protected $campaignClass = null;

    // --- OPERATIONS ---

    /**
     * call the (empty) parent constructor
     * initialise the campaign class
     *
     * @access protected
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    protected function __construct()
    {
		parent::__construct();
		
     	// ensure the taoCampaign extension is loaded, since it can be called from taoDelivery
		common_ext_ExtensionsManager::singleton()->getExtensionById('taoCampaign')->load();
		
		$this->campaignClass = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
    }

    /**
     * create a campaign subclass
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createCampaignClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        
		if(is_null($clazz)){
			$clazz = $this->campaignClass;
		}
		
		if($this->isCampaignClass($clazz)){
		
			$campaignClass = $this->createSubClass($clazz, $label);//call method form TAO_model_service
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $deliveryClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' campaign property from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
			}
			$returnValue = $campaignClass;
		}
        

        return $returnValue;
    }

    /**
     * rmeove a campaign
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource campaign
     * @return boolean
     */
    public function deleteCampaign( core_kernel_classes_Resource $campaign)
    {
        $returnValue = (bool) false;

        
		if(!is_null($campaign)){
			$returnValue = $campaign->delete();
		}
        

        return (bool) $returnValue;
    }

    /**
     * remove a campaign class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteCampaignClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        
		if(!is_null($clazz)){
			if($this->isCampaignClass($clazz) && $clazz->getUri() != $this->campaignClass->getUri()){
				$returnValue = $clazz->delete();
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * returns the campaign class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
		return $this->campaignClass;
	}

    /**
     * returns the uris of the deliveries associated with the specified campaign
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource campaign
     * @return array
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $campaign)
    {
        $returnValue = array();

        
		if(!is_null($campaign)){
		
			$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
			$deliveries = $deliveryClass->searchInstances(array(TAO_DELIVERY_CAMPAIGN_PROP => $campaign->getUri()), array('like'=>false, 'recursive' => 0));
			foreach ($deliveries as $delivery){
				if($delivery instanceof core_kernel_classes_Resource ){
					$returnValue[] = $delivery->getUri();
				}
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * is the class either the campaign class or a subclass of it
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isCampaignClass( core_kernel_classes_Class $clazz)
    {
        return $this->campaignClass->equals($clazz) || $clazz->isSubClassOf($this->campaignClass);
    }

    /**
     * Short description of method setRelatedDeliveries
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource campaign
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $campaign, $deliveries = array())
    {
        $returnValue = (bool) false;

        
		if(!is_null($campaign)){
			//the property of the DELIVERIES that will be modified
			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);
			
			//a way to remove the campaign property value of the delivery that are used to be associated to THIS campaign
			$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
			$oldDeliveries = $deliveryClass->searchInstances(array(TAO_DELIVERY_CAMPAIGN_PROP => $campaign->getUri()), array('like'=>false, 'recursive' => 0));
			foreach ($oldDeliveries as $oldRelatedDelivery) {
				//find a way to remove the property value associated to THIS campaign ONLY
				$remove = $oldRelatedDelivery->removePropertyValues($campaignProp, array('pattern' => $campaign->getUri()));
			}
			
			//assign the current compaign to the selected deliveries	
			$done = 0;
			foreach($deliveries as $delivery){
				//the delivery instance to be modified
				$deliveryInstance=new core_kernel_classes_Resource($delivery);
			
				//remove the property value associated to another delivery in case ONE delivery can ONLY be associated to ONE campaign
				//if so, then change the widget from comboBox to treeView in the delivery property definition
				// $deliveryInstance->removePropertyValues($campaignProp);
				
				//now, truly assigning the campaign uri to the affected deliveries
				if($deliveryInstance->setPropertyValue($campaignProp, $campaign->getUri())){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}
        

        return (bool) $returnValue;
    }
    
    /**
     * Short description of method getRelatedCampaigns
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @return array
     */
    public function getRelatedCampaigns( core_kernel_classes_Resource $delivery)
    {
        $returnValue = array();

        
		$campaigns = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP));

		if(count($campaigns)>0){
			$campaignSubClasses = array();
			foreach($this->getRootClass()->getSubClasses(true) as $campaignSubClass){
				$campaignSubClasses[] = $campaignSubClass->getUri();
			}
			foreach($campaigns as $campaignUri){
				$clazz = $this->getClass(new core_kernel_classes_Resource($campaignUri));
				if(!is_null($clazz)){
					if(in_array($clazz->getUri(), $campaignSubClasses)){
						$returnValue[] = $clazz->getUri();
					}
				}
				$returnValue[] = $campaignUri;
			}
		}
        

        return (array) $returnValue;
    }
    
    /**
     * Short description of method setRelatedCampaigns
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource delivery
     * @param  array campaigns
     * @return boolean
     */
    public function setRelatedCampaigns( core_kernel_classes_Resource $delivery, $campaigns = array())
    {
        $returnValue = (bool) false;

        
		if(!is_null($delivery)){

			$campaignProp = new core_kernel_classes_Property(TAO_DELIVERY_CAMPAIGN_PROP);

			$delivery->removePropertyValues($campaignProp);
			$done = 0;
			foreach($campaigns as $campaign){
				if($delivery->setPropertyValue($campaignProp, $campaign)){
					$done++;
				}
			}
			if($done == count($campaigns)){
				$returnValue = true;
			}
		}
        

        return (bool) $returnValue;
    }
} /* end of class taoCampaign_models_classes_CampaignService */

?>