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
class tao_install_checks_DatabaseDrivers extends common_configuration_Component {
    
    public function check (){
        
        // One of these drivers must be found.
        $drivers = array(   'pdo_mysql',
                            'pdo_pgsql',
                            'pdo_sqlsrv',
                            'pdo_oci');
                         
        foreach ($drivers as $d){
        	$dbCheck = common_configuration_ComponentFactory::buildPHPDatabaseDriver($d);
            $dbReport = $dbCheck->check();
            
            if ($dbReport->getStatus() == common_configuration_Report::VALID){
                return new common_configuration_Report($dbReport->getStatus(),
                                                       "A suitable database driver is available.",
                                                       $this);
            }
        }
        
        return new common_configuration_Report(common_configuration_Report::INVALID,
                                               "No suitable database driver detected. Drivers supported by TAO are: " 
                                               . implode(', ', $drivers) . '.',
                                               $this);
    }
}
?>