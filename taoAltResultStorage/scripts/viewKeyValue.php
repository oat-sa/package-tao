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
require_once dirname(__FILE__) . '/../includes/raw_start.php';

//output regarding the context
function out($msg = ''){
	print $msg;
	print (PHP_SAPI == 'cli') ? "\n" : "<br />";
}
out();
out("Running ".basename(__FILE__));

$prefix = 'callIdVariables';

$keyValueStorage = new taoAltResultStorage_models_classes_KeyValueResultStorage();

//retrieve all keys 
$keys = common_persistence_KeyValuePersistence::getPersistence('keyValueResult')->keys($prefix.'*');



foreach ($keys as $callId) {
   $callId = str_replace($prefix,'',$callId);
   out("----- Call ID -----".$callId);
   
   $variable = $keyValueStorage->getVariables($callId);
   foreach ($variable as $variableIdentifier => $data) {
       out("* Variable:\t".$variableIdentifier);
       out("* payload size:\t".strlen($data));
       //out("* payload :\t". $data);
   }
   //print_r(common_persistence_KeyValuePersistence::getPersistence('keyValueResult')->get($callId));
}
$prefix = 'resultsTestTaker';
$keys = common_persistence_KeyValuePersistence::getPersistence('keyValueResult')->keys($prefix.'*');
foreach ($keys as $key) {
   $key = str_replace($prefix,'',$key);
   out("----- Test Taker -----");
   $testTaker = $keyValueStorage->getTestTaker($key);
   out("* deliveryResultIdentifier:\t".$testTaker["deliveryResultIdentifier"]);
   out("* testTakerIdentifier:\t".$testTaker["testTakerIdentifier"]);
}
$prefix = 'resultsDelivery';
$keys = common_persistence_KeyValuePersistence::getPersistence('keyValueResult')->keys($prefix.'*');
foreach ($keys as $key) {
   $key = str_replace($prefix,'',$key);
   out("----- Delivery -----");
   $testTaker = $keyValueStorage->getTestTaker($key);
   out("* deliveryResultIdentifier:\t".$testTaker["deliveryResultIdentifier"]);
   out("* deliveryIdentifier:\t".$testTaker["testTakerIdentifier"]);
}


?>
