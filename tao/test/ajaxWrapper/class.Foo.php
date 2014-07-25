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

require_once dirname(__FILE__) . '/../../../generis/common/inc.extension.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class Foo {
    
    public function defaultAjaxResponse()
    {
        $ajaxResponse = new common_AjaxResponse();
    }
    
    public function jsonAjaxResponse()
    {
        $ajaxResponse = new common_AjaxResponse(Array(
            "data" => "expected data"
            , "message" => "expected message"
        ));
    }
    
    public function failedAjaxResponse()
    {
        $ajaxResponse = new common_AjaxResponse(Array(
            "success" => false
            , "type" => "json"
            , "data" => "expected data"
            , "message" => "expected message"
        ));
    }
    
    public function exceptionAjaxResponse()
    {
        try{
            throw new common_Exception("An expected test case exception occured");
        }
        catch(common_Exception $e){
            $ajaxResponse = new common_AjaxResponse(Array(
                "success" => false
                , "type" => "exception"
                , "message" => $e->getMessage()
            ));
        }
    }
}

$actions = $_GET['action'];
$foo = new Foo();
$foo->$actions();

?>
