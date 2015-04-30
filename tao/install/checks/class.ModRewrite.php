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
class tao_install_checks_ModRewrite extends common_configuration_Component {
    
    public function check (){
        $status = null;
        $message = '';
        $modRewrite = false;
        $report = null;
        
        if (function_exists('apache_get_modules')){
            $modules = apache_get_modules();
            if (in_array('mod_rewrite', $modules)){
                $modRewrite = true;
            }
        }
        // TAO Main .htaccess file sets the HTTP_MOD_REWRITE.
        elseif ((getenv('HTTP_MOD_REWRITE')=='On' ? true : false) == true){
            $modRewrite = true;
        }
        // apache does weird things to environement variables
        elseif ((getenv('REDIRECT_HTTP_MOD_REWRITE')=='On' ? true : false) == true){
            $modRewrite = true;
        }
        // else test the behaviour
        elseif (php_sapi_name() != 'cli' && function_exists("curl_init")) {

            $server =
                ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https' : 'http')
                ."://".$_SERVER['SERVER_NAME']
                .(($_SERVER["SERVER_PORT"] != "80") ? ":".$_SERVER["SERVER_PORT"] : '');
            
            $request = $_SERVER["REQUEST_URI"];
            if(strpos($request, '?') !== false) {
                $request = substr($request, 0, strpos($request, '?'));
            }
            $request = substr($request, 0, strrpos($request, '/'));
                
            $url = $server.$request.'/checks/testRewrite/notworking';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = @curl_exec($ch);
            curl_close($ch);

            if ($output == 'working') {
                $modRewrite = true;
            }

        }

        if ($modRewrite == true){
            $report = new common_configuration_Report(common_configuration_Report::VALID,
                                                      'URL rewriting is enabled.',
                                                      $this);
        }
        else{
            $report = new common_configuration_Report(common_configuration_Report::INVALID,
                                                      'URL rewriting is disabled.',
                                                      $this);
        }
        
        return $report;
    }
}
?>
