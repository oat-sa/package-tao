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
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class EventsServiceTestCase extends UnitTestCase {
	
	/**
	 * @var tao_models_classes_EventsService
	 */
	protected $eventsService = null;
	
	/**
	 * @var string
	 */
	protected $eventFile;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * @see tao_models_classes_ServiceFactory::get
	 * @see tao_models_classes_EventsService::__construct
	 */
	public function testService(){
		
		$eventsService = tao_models_classes_EventsService::singleton();
		$this->assertIsA($eventsService, 'tao_models_classes_Service');
		$this->assertIsA($eventsService, 'tao_models_classes_EventsService');
		
		$this->eventsService = $eventsService;
		
		$this->eventFile = dirname(__FILE__).'/samples/events.xml';
		
		$this->assertTrue(file_exists($this->eventFile));
	}
	
	public function testParsing(){
		
		$clientEventList = $this->eventsService->getEventList($this->eventFile, 'client');
		$this->assertTrue(count($clientEventList) > 0);
		$this->assertEqual($clientEventList['type'], 'catch');
		$this->assertTrue(is_array($clientEventList['list']));
		$this->assertTrue(array_key_exists('click', $clientEventList['list']));
		$this->assertTrue(array_key_exists('keyup', $clientEventList['list']));
		$this->assertTrue(array_key_exists('keypress', $clientEventList['list']));
		
		$serverEventList = $this->eventsService->getEventList($this->eventFile, 'server');
		$this->assertTrue(count($serverEventList) > 0);		
		$this->assertEqual($serverEventList['type'], 'catch');
		$this->assertTrue(is_array($serverEventList['list']));
		$this->assertTrue(array_key_exists('click', $serverEventList['list']));
	}
	
	public function testTracing(){
		
		$events = array(
			'{"name":"div","type":"click","time":"1288780765981","id":"qunit-fixture"}',
			'{"name":"BUSINESS","type":"TEST","time":"1288780765982","id":"12"}',
			'{"name":"h2","type":"click","time":"1288780766000","id":"qunit-banner"}',
			'{"name":"h1","type":"click","time":"1288780765999","id":"qunit-header"}'
		);
		
		$folder = dirname(__FILE__).'/samples';
		
		$processId = '123456789';
		
		$eventFilter =  $this->eventsService->getEventList($this->eventFile, 'server');
		
		$this->assertTrue($this->eventsService->traceEvent($events, $processId, $folder, $eventFilter));
		
		$this->assertTrue($this->eventsService->traceEvent($events, $processId, $folder));
		
		$this->assertTrue(file_exists($folder. '/' . $processId . '_0.xml'));
		
		foreach(glob($folder . '/'. $processId . '*') as $trace_file){
			if(preg_match('/(xml|lock)$/', $trace_file)){
				unlink($trace_file);
			}
		}
	}
}
?>