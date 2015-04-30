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
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoTestTaker\models;

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 *
 */
class TestTakerService extends \tao_models_classes_ClassService
{

    protected $subjectClass = null;

    public function __construct()
    {
        parent::__construct();
        $this->subjectClass = new \core_kernel_classes_Class(TAO_SUBJECT_CLASS);
    }

    public function getRootClass()
    {
        return $this->subjectClass;
    }

    /**
     * delete a subject instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Resource $subject
     * @return boolean
     */
    public function deleteSubject(\core_kernel_classes_Resource $subject)
    {
        $returnValue = (bool) false;

        if (! is_null($subject)) {
            $returnValue = $subject->delete();
        }

        return (bool) $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of Subject
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Class $clazz
     * @return boolean
     */
    public function isSubjectClass(\core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        if ($clazz->getUri() == $this->subjectClass->getUri()) {
            $returnValue = true;
        } else {
            foreach ($this->subjectClass->getSubClasses(true) as $subclass) {
                if ($clazz->getUri() == $subclass->getUri()) {
                    $returnValue = true;
                    break;
                }
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Set the proper role to the testTaker
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param \core_kernel_classes_Resource $instance
     */
    public function setTestTakerRole(\core_kernel_classes_Resource $instance){
        $roleProperty = new \core_kernel_classes_Property(PROPERTY_USER_ROLES);
        $subjectRole = new \core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);
        $instance->setPropertyValue($roleProperty, $subjectRole);
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param \core_kernel_classes_Resource $instance
     * @param \core_kernel_classes_Class $clazz
     * @throws \common_Exception
     * @throws \core_kernel_classes_EmptyProperty
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance(\core_kernel_classes_Resource $instance, \core_kernel_classes_Class $clazz = null)
    {
        $loginProperty = new \core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        $login = $instance->getUniquePropertyValue($loginProperty);
        
        $returnValue = parent::cloneInstance($instance, $clazz);
        $userService = \tao_models_classes_UserService::singleton();
        try {
            while ($userService->loginExists($login)) {
                $login .= (string) rand(0, 9);
            }

            $returnValue->editPropertyValues($loginProperty, $login);
        } catch (common_Exception $ce) {
            // empty
        }

        return $returnValue;
    }
}
