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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * The event service enables you to manage events catching and tracing.
 * It provides methods to read files describing which event to catch 
 * and methods to print out the events of an item execution into a log file.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_models_classes_EventsService
    extends tao_models_classes_Service
{


    /**
     * Retrieve the list of events to catch from a XML file.
     * The XML format used describes events that can be catched either on client
     * on server side.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @param  string side
     * @return array
     */
    public function getEventList($file, $side = 'client')
    {
        $returnValue = array();


        
    	$knownSides = array('client', 'server');
        if(!in_array($side, $knownSides)){
        	throw new Exception("Unkown side {$side} to catch events. Expected: [".implode(',', $knownSides)."] ");
        }
        if(!file_exists($file)){
        	throw new Exception("Unable to open file $file");
        }
        
        $returnValue = array('type' => '', 'list' => array());
        
    	try
		{
			$event_dom = new DomDocument();
			$event_dom->load($file);

			$event_xpath = new DOMXPath($event_dom);
			$filter = $event_xpath->query("//filter[@where='$side']");
			if(count($filter->item(0)) == 1){
				$matches = array();
				preg_match_all('/([^\[|,]+)(\[([^\]]+)\]|,)*/', $filter->item(0)->nodeValue, $matches);
				
				if(isset($matches[1])){
					$event_array = array();
					foreach($matches[1] as $key => $event){
						$event_array[$event] = array();
						foreach(explode(',', $matches[3][$key]) as $attribute){
							if(!empty($attribute)){
								array_push($event_array[$event], $attribute);
							}
						}
					}
		
					$returnValue = array(
							'type' => $filter->item(0)->getAttribute('type'),
							'list' => $event_array
						);
				}
			}
		}
		catch(DomException $de){ 
			print  $de; 
		}
        


        return (array) $returnValue;
    }

    /**
     * Print out the events in parameters into a formated events log file.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array events
     * @param  string process_id
     * @param  string folder
     * @param  array eventFilter
     * @return boolean
     */
    public function traceEvent($events, $process_id, $folder, $eventFilter = array())
    {
        $returnValue = (bool) false;

        
        
    	try
		{
	//		$events_ = array(
	//		    array('name' => 'name1', 'type' => 'mousemove', 'time' => 'time1', 'key' => 'value'),
	//		    array('name' => 'name2', 'type' => 'click', 'time' => 'time2', 'key' => 'value'),
	//		    array('name' => 'name3', 'type' => 'type3', 'time' => 'time3', 'key' => 'value'),
	//		    array('name' => 'name4', 'type' => 'click', 'time' => 'time4', 'key' => 'value'),
	//		    array('name' => 'name5', 'type' => 'type5', 'time' => 'time5', 'key' => 'value')
	//		);
	//		$events_ = json_encode($events_);

			
			if(is_dir($folder) && !empty($process_id)){
			
				$file_pattern = $folder . '/' . $process_id;
				
				$i = 0;
				foreach(glob($file_pattern . '_*.xml') as $trace_file)
				{
					preg_match('/_([0-9]+).xml/', $trace_file, $matches);
					if($matches[1] >= $i) {
					    $i = $matches[1];
					}
				}
	
				$trace_file = $file_pattern . '_' . $i . '.xml';
				if(file_exists($trace_file) && filesize($trace_file) > (1024 * 8 * 10)){
					$trace_file = $file_pattern . '_' . ++$i . '.xml';
				}
				
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
	
				// setting a lock for concurrency access
				$lock_pointer = fopen($file_pattern.'.lock', 'w');
				flock($lock_pointer, LOCK_EX);
	
				if(file_exists($trace_file)){
					 $dom->load($trace_file);
				}
				else {
					$dom->loadXML('<events></events>');
				}
	
				$event_type = array();
				if(isset($eventFilter['list'])){
					foreach($eventFilter['list'] as $event_to_save_key => $event_to_save_value){
						array_push($event_type, $event_to_save_key);
					}
				}
				
				$event_type_attributes = array();
				if(isset($eventFilter['list'])){
					foreach($eventFilter['list'] as $event_to_save_key => $event_to_save_value){
						$event_type_attributes[$event_to_save_key] = array();
						foreach($eventFilter['list'][$event_to_save_key] as $attribute_to_save_key => $attribute_to_save_value){
							array_push($event_type_attributes[$event_to_save_key], $attribute_to_save_value);
						}
						//if no attribute is added in array, so we want to keep all attributes.
						if(count($event_type_attributes[$event_to_save_key]) == 0) {
						    continue;
						}
		
						//adding business attribute to log every time.
						array_push($event_type_attributes[$event_to_save_key],
							'id', 'name', 'type', 'time',
							'ACTIVITYID', 'ITEMID', 'PROCESSURI', 'LAYOUT_DIRECTION', 'LANGID');
					}
				}
	
				//adding business event to log every time.
				array_push($event_type, 'START_ITEM', 'END_ITEM', 'BUSINESS', 'ENDORSEMENT', 'RECOVERY');
	
				if(is_array($events) && count($events) > 0)
				{
					foreach($events as $event_index => $event_row)
					{
						// append the ($name_, $type_, $time_, ...) at eof as
						// <event>
						//	 <name><![CDATA[$name_]]></name>
						//	 <type><![CDATA[$type_]]></type>
						//	 <time><![CDATA[$time_]]></time>
						//	 ...
						// </event>
						
						$event_name = json_decode($event_row, true);
						if(isset($eventFilter['type'])){
							if($eventFilter['type'] == 'catch'
								&& !in_array($event_name['type'], $event_type)) {
							    continue;
							}
							if($eventFilter['type'] == 'nocatch'
								&& in_array($event_name['type'], $event_type)) {
							    continue;
							}
						}
						
						$event_element = $dom->createElement('event');
						foreach($event_name as $key => $value)
						{
							//if equal to 0, we want to get all atributes
							if(isset($event_type_attributes[$event_name['type']])
									&& count($event_type_attributes[$event_name['type']]) != 0 
									&& isset($eventFilter['type'])){
                                if ($eventFilter['type'] == 'catch' && ! in_array($key, $event_type_attributes[$event_name['type']])) {
                                    continue;
                                }
                                if ($eventFilter['type'] == 'nocatch' && in_array($key, $event_type_attributes[$event_name['type']])) {
                                    continue;
                                }
							}
	
							$key_element = $dom->createElement($key);
							$cdata_element = $dom->createCDATASection($value);
							$key_element->appendChild($cdata_element);
							$event_element->appendChild($key_element);
						}
						$dom->documentElement->appendChild($event_element);
					}
					if($dom->save($trace_file) !== false){
						$returnValue = true;
					}
				}
				// remove the lock of concurrency access
				flock($lock_pointer, LOCK_UN);
				fclose($lock_pointer);
			
			}
		}
		catch(Exception $e){ 
			print $e; 
		}
        
       

        return (bool) $returnValue;
    }

} 

?>