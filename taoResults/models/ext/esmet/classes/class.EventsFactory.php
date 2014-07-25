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

class eventsFactory extends eventsServices {

    //prepare the document according to the tao event
    public function __construct($taoEvents) {
        //pepare the ebvent consists of adding symbol numeration attribute and
        parent::__construct($taoEvents);
        $this->incEventNumber();
        $this->addSymbolAttribute();
        //now we have a list with event with two new attributes; one for the incrementation,
        //the other for the symbol very importante
     
    }
// add an attribute with increment number from 1
    private function incEventNumber(){
        
        $usedXml = $this->currentXml;
        $xmlDoc = simplexml_load_string($usedXml);
        $inc = 1;
        foreach($xmlDoc as $node){
            $node->addAttribute(EVENT_NUMBER, $inc++);
        }
        $this->currentXml = $xmlDoc->asXML();
        
    }
    //add symbol attribute
    private function addSymbolAttribute(){
        //Call the parent method 
        $this->addAttributeForAllNodes(EVENT_SYMBOL,NOISE_SYMBOL);
    }

    // the symbolization method put the accurate pattern Symbole for event according to the query
    public function eventSymbolization(symbolDescription $symbol){
        $patternSymbol= $symbol->symbolLetter;
        $patternQuery = $symbol->query;
        //for each event in after applaying the query, set the symbol value
        $query = '//'.EVENT_NODE.'['.$patternQuery.']';
        $this->setAttributesValue(EVENT_SYMBOL, $patternSymbol, $query);
    }
    //symbolization of all the log
    //as in put, an array of symbol
    public function fullSymbolization($patternSymbolCollection){
        
        foreach( $patternSymbolCollection as $symbol){
            $this->eventSymbolization($symbol);
            

        }
    }
    //create the symbolized event Log
    public function generateSymbolizedEvents(){
        $usedEvents = $this->currentXml;
        $listEvents = simplexml_load_string($usedEvents);
        $symbolizedLog = '';
        foreach ($listEvents as $event){
            $symbolLetter = $event->attributes()->EVENT_SYMBOL;
            $symbolizedLog=$symbolizedLog.$symbolLetter;
        }
        return $symbolizedLog;

    }


    //save the eventlog
    public function saveEvents($fileName="raffinedEvents.xml"){
        $xmlDoc = simplexml_load_string($this->currentXml);
        $xmlDoc->asXML($fileName);
    }
    //match the trace
    public function matchingPatternMatchin($patternToMatch,$symbolizedTraces){
        $usedTraces = $symbolizedTraces;
        $match = false;
        $match = preg_match('/'.$patternToMatch.'/', $usedTraces);
        return $match;
 
    }
}

?>
