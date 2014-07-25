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
/*
 * converts an iputed trace to event TAO Event Model
 *
 */

/**
 * Description of class
 *
 * @author djaghloul
 */
class taoEventModelConverter {

    public $currentTrace = '';

    public function __construct($xml) {
        $this->currentTrace = $xml;
    }

    //convert a trace done by HAWAI item to Tao Event Model
    public function importFromHAWAI($xml='') {
        $unifiedArray = array();
        $usedXml = $xml;
        if ($xml == '') {
            $usedXml = $this->currentTrace;
        }

        $hawaiTrace = new DOMDocument();
        $hawaiTrace->loadXML($usedXml);
//create the unified array by parsing attribute and string
        $listEvent = $hawaiTrace->getElementsByTagName('taoEvent');

        $unifiedArray = $this->getTracesAsArray($listEvent);
        //create the Xml
        $es = new eventsServices();
        $finalXml = $es->simpleArrayToXml($unifiedArray, ROOT, EVENT_NODE);
        return $finalXml;
    }

    private function getTracesAsArray($domTrace=null) {

        $tracesArray = array();
        $taoEventList = $domTrace;


        //$tracesArray=0;
        //************************************************
        foreach ($taoEventList as $taoEvent) {
            //On commence par extraire les attributs de la trace de l'evenement

            $TE_Name = $taoEvent->getAttribute('Name');
            $TE_Type = $taoEvent->getAttribute('Type');
            $TE_Time = $taoEvent->getAttribute('Time');

            //Decoder le XML ou voir la section CDATA,
            $value = $taoEvent->nodeValue;
            //echo $value;
            $T_Values = array();
            $T_Values = $this->parseEventTraceValue($value);

            //Création du tableau des traces, il englobe toutes les variables ( tableau peyload + tableaux des attributs fixes)
            //$trace = $T_Values;

            $trace['name'] = $TE_Name;
            $trace['type'] = $TE_Type;
            $trace['time'] = $TE_Time;

            //Merge the tow arrays ( attribute + node values )
            $tout = array_merge($trace, $T_Values);
            $tracesArray[] = $tout;
        }

        return $tracesArray;
    }

    //******************************************************
    //Cette methode reçoit la valeur du noeud itemValue comme string, et la decode en variable en retournant le type d'evenement
    private function parseEventTraceValue($itemTraceValue) {

        $tabVarFinal = array();
        if ($itemTraceValue != '') {

            $itv = $itemTraceValue; //recuperer le string de la valeur;
            $tokenVar = array();
            $tabPart = explode(sep, $itv);
            $tabVarFinal = array();
            foreach ($tabPart as $part) {
                //parse_str($part, $tokenVar); //in  each element of the array we have this: var=value, we need to have $tokenVar[var]=value
                //we remplace pers_str with exploe because it ascapes directely some symbols
                $varValue = explode('=', $part);
                $tokenVar[$varValue[0]] = $varValue[1];
                $tabVarFinal = array_merge($tabVarFinal, $tokenVar);
            }
        }
        
        return $tabVarFinal;
    }

}

//test the converion



//file_put_contents('taoHawai.xml', $import);
?>
