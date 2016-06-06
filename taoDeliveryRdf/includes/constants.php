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

//define specific constants to delivery extension:
$todefine = array(
    
	'TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP'   => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects',
    'TAO_DELIVERY_RESULTSERVER_PROP'	   => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer',
    'TAO_DELIVERY_MAXEXEC_PROP'            => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec',
	'TAO_DELIVERY_START_PROP'              => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart',
	'TAO_DELIVERY_END_PROP'                => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd',
	'TAO_DELIVERY_ACCESS_SETTINGS_PROP'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AccessSettings',

    //
    'CLASS_COMPILEDDELIVERY'               => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDelivery',
    'PROPERTY_COMPILEDDELIVERY_DELIVERY'   => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryDelivery',
    'PROPERTY_COMPILEDDELIVERY_TIME'       => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationTime',
    'PROPERTY_COMPILEDDELIVERY_RUNTIME'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryRuntime',
    'PROPERTY_COMPILEDDELIVERY_DIRECTORY'  => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AssembledDeliveryCompilationDirectory',

    'PROPERTY_GROUP_DELVIERY'              => 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries',

    'DELIVERY_GUEST_ACCESS'                => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#GuestAccess',
    'DELIVERY_DISPLAY_ORDER_PROP'          => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DisplayOrder'
);
