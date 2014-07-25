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
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
require_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Patrick plichart, <patrick@taotesting.com>
 * @package taoResults
 * @subpackage test
 */
class CampaignTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoResults_models_classes_CampaignService
	 */
	private $campaignService = null;
	
	//the class used to store the tested campaigns and remvoed afterwards
	public $campaignClass= null;
	//the campaign being tested
	private $campaign= null;
	
	private $delivery = null;
	/**
	 * tests initialization
	 */
	public function setUp(){	
		
		TaoTestRunner::initTest();
		$this->campaignService = taoCampaign_models_classes_CampaignService::singleton();
		
		
		$rootClass = new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS);
		$this->campaignClass = $this->campaignService->createCampaignClass($rootClass, "My Campaign Class");
		
		$this->campaign =  $this->campaignClass->createInstance("MyCampaign");
		
		$deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$this->delivery = $deliveryClass->createInstance("MyDelivery");
		
		$this->campaignService->setRelatedDeliveries($this->campaign, array($this->delivery->getUri()));
		
	}
	
	/**
	 * Test the campaign service implementation
	 * @see taoResults_models_classes_CampaignService::__construct
	 */
	public function testService(){
	    //die("never executed");
		
		$this->assertIsA($this->campaignService, 'taoCampaign_models_classes_CampaignService');
	}
	
	public function testCreateCampaignClass(){
	    $this->assertIsA($this->campaignClass, "core_kernel_classes_Class");
	}
	//used to create a campaign and test it further
	public function testCreateCampaign(){
	    
	    
	    $this->assertIsA($this->campaign, "core_kernel_classes_Resource");
	}
	
	public function testGetRelatedDeliveries(){
	    $deliveries = $this->campaignService->getRelatedDeliveries($this->campaign);
	     $this->assertEqual(count($deliveries), 1);
	    $delivery = new core_kernel_classes_Resource(array_pop($deliveries));
	     $this->assertEqual($delivery->getLabel(), "MyDelivery");
	}
	public function testIsCampaignClass(){
	    $this->assertEqual($this->campaignService->isCampaignClass(new core_kernel_classes_Class(TAO_DELIVERY_CAMPAIGN_CLASS)), true);
	    $this->assertEqual($this->campaignService->isCampaignClass($this->campaignClass), true);
	}
	public function testgetRootClass(){
	    $this->assertEqual($this->campaignService->getRootClass()->getUri(), TAO_DELIVERY_CAMPAIGN_CLASS);
	}
	
	public function tearDown(){
	   
	    $this->assertTrue($this->campaignService->deleteCampaignClass($this->campaignClass));
	    $this->assertTrue($this->campaignService->deleteCampaign( $this->campaign));
	    $this->assertTrue($this->delivery->delete());
	}
}   
?>