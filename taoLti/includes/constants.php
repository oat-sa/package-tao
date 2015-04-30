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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

//define specific constants to the taoLti extension:
$todefine = array(

	'CLASS_LTI_CONSUMER'			=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTIConsumer',

	'CLASS_LTI_USER'				=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTIUser',
	'PROPERTY_USER_LTIKEY'			=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#UserKey',
	'PROPERTY_USER_LTICONSUMER'		=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#UserConsumer',

	'CLASS_LTI_ROLES'				=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTIRole',
	'PROPERTY_LTI_ROLES_URN'		=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#RoleURN',
	
	'CLASS_LTI_TOOL'				=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTITool',
	'PROPERTY_LTITOOL_SERVICE'		=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#ToolService',

	'CLASS_LTI_LINK'				=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTILink',
	'CLASS_LTI_INCOMINGLINK'		=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiIncomingLink',
	'PROPERTY_LTI_LINK_ID'			=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTILinkId',
	'PROPERTY_LTI_LINK_CONSUMER'	=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTILinkConsumer',
	'PROPERTY_LTI_LINK_LAUNCHURL'	=> 'http://www.tao.lu/Ontologies/TAOLTI.rdf#ResourceLinkLaunchUrl',

	'INSTANCE_ROLE_CONTEXT_LEARNER' => 'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership#Learner',
	'INSTANCE_ROLE_LTI_BASE'        => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiBaseRole',
	
	'INSTANCE_LTI_CONSUMER_SERVICE' => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#ServiceLtiConsumer',
	'INSTANCE_FORMALPARAM_LTI_CONSUMER' => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiConsumerUri',
	'INSTANCE_FORMALPARAM_LTI_LAUNCH_URL' => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiLaunchUrl'
);