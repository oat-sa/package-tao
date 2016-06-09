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
class tao_install_checks_AllowOverride extends common_configuration_Component {
    
    public function check (){
        $report = null;
        
        $server =
            ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https' : 'http')
            ."://".$_SERVER['SERVER_NAME']
            .(($_SERVER["SERVER_PORT"] != "80") ? ":".$_SERVER["SERVER_PORT"] : '');

        $request = $_SERVER["REQUEST_URI"];
        $request = substr($request, 0, strpos($request, '?'));
        $request = substr($request, 0, strrpos($request, '/'));

        $url = $server.$request.'/checks/testAllowOverride/noneOrAll/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == '403') {
	        $report = new common_configuration_Report(common_configuration_Report::VALID,
                                                            'The AllowOverride directive is set to All.',
                                                            $this);
        } else {
                $report = new common_configuration_Report(common_configuration_Report::INVALID,
                                                            'The AllowOverride directive may not be set to All.',
                                                            $this);
        }
        
        return $report;
    }
}
?>
