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


require_once 'class.SymbolDescription.php';
class symbolFactory {
    private $symbolOfPatternCollection = array();

    public function  __construct() {
        $this->symbolOfPatternCollection= array();

    }
    //create the symbol
    public static function create ($symbolLetter,$patternQuery,$symbolComment='This symbol is ...'){
        $symbol = new symbolDescription($symbolLetter,$patternQuery,$symbolComment);
        return $symbol;
    }

    // add symbol the the list of symbols
    public function addSymbol(symbolDescription $symbol){
        $key = $symbol->symbolLetter;
        $this->symbolOfPatternCollection[$key]= $symbol;
        return $this->symbolOfPatternCollection;
    }
    //get the current collection
    public function getSymbolCollection(){
        return $this->symbolOfPatternCollection;
    }

}



?>
