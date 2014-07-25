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
require_once 'esmet_config.php';

//get the XML log
$xmlDoc = simplexml_load_file('teplax.xml');
$xml = $xmlDoc->asXML();

$conv = new taoEventModelConverter($xml);
$taoEvents = $conv->importFromHAWAI();

//Now we have an XML log compliant with TAO Event Model

$ms = new matchnigScoringToolBox($taoEvents);
//*************Prepare the patterns
$pattern = array();
//patterns['varName'] = "(query)";

$patterns['next1'] = "(type= 'BUTTON') and (id = 'btn_next1')";
$patterns['next2'] = "(type= 'BUTTON') and (id = 'btn_next2')";
$patterns['next3'] = "(type= 'BUTTON') and (id = 'btn_next3')";
$patterns['next4'] = "(type= 'BUTTON') and (id = 'btn_next4')";

//the resetQuery corresponds to reset button
$resetQuery = "(type= 'BUTTON') and (id = 'btn_back')";


//You have to call this method 
$ls = $ms->finaleStateOfEvents_CheckBoxStyle($patterns, $resetQuery);
print_r($ls);


?>
