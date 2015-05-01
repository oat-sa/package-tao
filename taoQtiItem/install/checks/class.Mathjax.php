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
class taoQtiItem_install_checks_Mathjax extends common_configuration_Component {
    
    public function check (){
        $status = null;
        $mathJax = false;
        $report = null;
        
        $dp = DIRECTORY_SEPARATOR;
        $mathJaxFile = dirname(__FILE__) . "${dp}..${dp}..${dp}views${dp}js${dp}mathjax${dp}MathJax.js";
        
        if (@is_file($mathJaxFile)) {
            $report = new common_configuration_Report(common_configuration_Report::VALID, 'MathJax JavaScript library installed.', $this);
        }
        else {
            $report = new common_configuration_Report(common_configuration_Report::INVALID, 'MathJax JavaScript library not installed. To enable MathML expressions in your QTI items, you need to install the third-party MathJax library.', $this);
        }
        
        return $report;
    }
}
?>
