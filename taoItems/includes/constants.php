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

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$todefine = array(
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_CONTENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent',

	'TAO_ITEM_MODEL_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemRuntime', 
	'TAO_ITEM_MODEL_AUTHORING_PROPERTY' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemAuthoring',
	'TAO_ITEM_MODEL_DATAFILE_PROPERTY'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DataFileName',
	'TAO_ITEM_MODEL_QTI'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI',
	'TAO_ITEM_MODEL_PAPERBASED'			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Paperbased',
	'PROPERTY_ITEM_MODEL_SERVICE'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelService',

	'TAO_ITEM_SOURCENAME_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContentSourceName',
	'TAO_ITEM_MODELTARGET_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelTarget',
	'TAO_ITEM_ONLINE_TARGET'			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#OnlineTarget',
	'TAO_ITEM_PAPERBASED_TARGET'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#PaperbasedTarget',
	
	'TAO_ITEM_MODEL_STATUS_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus',
	'TAO_ITEM_MODEL_STATUS_STABLE' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable',
	'TAO_ITEM_MODEL_STATUS_DEPRECATED'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated',
	'TAO_ITEM_MODEL_STATUS_DEV' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDevelopment',
	'TAO_ITEM_MODEL_STATUS_EXPERIMENTAL'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental',
	
	'TAO_ITEM_MEASURMENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#hasMeasurement',
	'TAO_ITEM_MEASURMENT' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Measurement',
	'TAO_ITEM_IDENTIFIER_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#MeasurementIdentifier',
	'TAO_ITEM_MEASURMENT_HUMAN_ASSISTED'=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#isHumanAssisted', 
	'TAO_ITEM_SCALE_PROPERTY'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#hasScale',
	'TAO_ITEM_DESCRIPTION_PROPERTY'	 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#MeasurementDescription',
	'TAO_ITEM_SCALE'			 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Scale',
	'TAO_ITEM_NUMERICAL_SCALE'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#NumericalScale',
	'TAO_ITEM_DISCRETE_SCALE'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DiscreteScale',
	'TAO_ITEM_ENUMERATION_SCALE'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Enumeration',
	'TAO_ITEM_LOWER_BOUND_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ScaleLowerBound',
	'TAO_ITEM_UPPER_BOUND_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ScaleUpperBound',
	'TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DiscreteScaleDistance',
    
    'INSTANCE_SERVICE_ITEMRUNNER'      => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceItemRunner',
    'INSTANCE_FORMALPARAM_ITEMPATH'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemPath',
    'INSTANCE_FORMALPARAM_ITEMDATAPATH'    => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemDataPath',
	'INSTANCE_FORMALPARAM_ITEMURI'     => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemUri',
    'INSTANCE_FORMALPARAM_RESULTSERVER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamResultserver'
);
?>
