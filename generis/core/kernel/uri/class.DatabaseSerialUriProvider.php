<?php
/**
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
use oat\oatbox\Configurable;

/**
 * UriProvider implementation based on a serial stored in the database.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 */
class core_kernel_uri_DatabaseSerialUriProvider extends Configurable
    implements common_uri_UriProvider
{
    const OPTION_PERSISTENCE = 'persistence';
    
    const OPTION_NAMESPACE = 'namespace';
    // --- ASSOCIATIONS ---
        
    // --- ATTRIBUTES ---
        
    // --- OPERATIONS ---
    
    /**
     * @return common_persistence_SqlPersistence
     */
    public function getPersistence() {
        return common_persistence_SqlPersistence::getPersistence($this->getOption(self::OPTION_PERSISTENCE));
    }
    
    /**
     * Generates a URI based on a serial stored in the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     * @throws common_UriProviderException
     */
    public function provide()
    {
        $returnValue = (string) '';
        
        
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        try {
            $sth = $this->getPersistence()->query($this->getPersistence()->getPlatForm()->getSqlFunction("generis_sequence_uri_provider"), array(
                    $this->getOption(self::OPTION_NAMESPACE)
            ));
       
            if ($sth !== false) {
                
                $row = $sth->fetch();
                
                $returnValue = current($row);
                $sth->closeCursor();
            } else {
                throw new common_uri_UriProviderException("An error occured while calling the stored procedure for persistence ".$this->getOption(self::OPTION_PERSISTENCE).".");
            }
        } catch (Exception $e) {
        	throw new common_uri_UriProviderException("An error occured while calling the stored ': " . $e->getMessage() . ".");
        }

        return (string) $returnValue;
    }

}