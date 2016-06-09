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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\model\requiredAction\implementation;

use oat\tao\model\requiredAction\RequiredActionRuleInterface;
use oat\tao\model\requiredAction\RequiredActionInterface;
use \DateTime;
use \DateInterval;
use oat\oatbox\user\User;

/**
 * Class TimeRule
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 */
class TimeRule implements RequiredActionRuleInterface
{
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAO.rdf#RequiredActionTimeRule';
    const PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAO.rdf#RequiredActionName';
    const PROPERTY_EXECUTION_TIME = 'http://www.tao.lu/Ontologies/TAO.rdf#RequiredActionExecutionTime';
    const PROPERTY_SUBJECT = 'http://www.tao.lu/Ontologies/TAO.rdf#RequiredActionSubject';

    /**
     * @var string|DateTime
     */
    protected $executionTime;

    /**
     * @var string|DateInterval
     */
    protected $interval;

    /**
     * @var RequiredActionInterface
     */
    protected $requiredAction;

    /**
     * TimeRule constructor.
     * @param DateTime|null $executionTime Time when the action was executed last time
     * @param DateInterval|null $interval Interval to specify how often action should be performed
     */
    public function __construct(DateInterval $interval = null, DateTime $executionTime = null)
    {
        $this->interval = $interval;
        $this->executionTime = $executionTime;
    }

    /**
     * Set required action instance
     * @param \oat\tao\model\requiredAction\RequiredActionInterface $requiredAction
     * @return null
     */
    public function setRequiredAction(RequiredActionInterface $requiredAction)
    {
        $this->requiredAction = $requiredAction;
    }

    /**
     * Check the rule.
     * @return bool
     */
    public function check()
    {
        return $this->checkTime();
    }

    /**
     * Mark rule as executed and save time of completed.
     * @return \core_kernel_classes_Resource
     */
    public function completed()
    {
        $resource = $this->getActionExecution();
        if ($resource === null) {
            $requiredActionClass = new \core_kernel_classes_Class(self::CLASS_URI);
            $resource = $requiredActionClass->createInstanceWithProperties(array(
                self::PROPERTY_SUBJECT => $this->getUser()->getIdentifier(),
                self::PROPERTY_NAME => $this->requiredAction->getName(),
                self::PROPERTY_EXECUTION_TIME => time(),
            ));
        }
        $timeProperty = (new \core_kernel_classes_Property(self::PROPERTY_EXECUTION_TIME));
        $resource->editPropertyValues($timeProperty, time());
        return $resource;
    }

    /**
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        $class = get_class($this);
        $executionTime = $this->getExecutionTime();
        if ($executionTime === null) {
            $serializedExecutionTime = 'null';
        } else {
            $serializedExecutionTime = 'new \DateTime(' . $executionTime->getTimestamp() . ')';
        }

        $interval = $this->getInterval();
        $serializedInterval = 'new \DateInterval("' . $interval->format('P%yY%mM%dDT%hH%iM%sS') . '")';

        return "new $class(
            $serializedInterval,
            $serializedExecutionTime
        )";
    }

    /**
     * Check if it is time to perform an action.
     * If `$this->lastExecution` is null (action has never been executed)
     * or since the last execution took time more than specified interval (`$this->interval`) then action must be performed.
     * @return bool
     */
    protected function checkTime()
    {
        $result = false;

        $lastExecution = $this->getExecutionTime();
        $interval = $this->getInterval();
        $anonymous = \common_session_SessionManager::isAnonymous();

        if ($lastExecution === null && !$anonymous) {
            $result = true;
        } elseif($lastExecution !== null && $interval !== null && !$anonymous) {
            $mustBeExecutedAt = clone($lastExecution);
            $mustBeExecutedAt->add($interval);
            $now = new DateTime('now');
            $result = ($mustBeExecutedAt < $now);
        }

        return $result;
    }

    /**
     * Get last execution time. If an action was not executed before returns `null`
     * @return DateTime|null
     */
    protected function getExecutionTime()
    {
        if ($this->executionTime === null) {
            $resource = $this->getActionExecution();

            if ($resource !== null) {
                /** @var \core_kernel_classes_Resource $resource */
                $time = (string) $resource->getOnePropertyValue(new \core_kernel_classes_Property(self::PROPERTY_EXECUTION_TIME));
                if (!empty($time)) {
                    $this->executionTime = new DateTime('@' . $time);
                }
            }
        }
        return $this->executionTime;
    }

    /**
     * @return DateInterval|null
     */
    protected function getInterval()
    {
        if (is_string($this->interval)) {
            $this->interval = new DateInterval($this->interval);
        }
        return $this->interval instanceof DateInterval ? $this->interval : null;
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        $user = \common_session_SessionManager::getSession()->getUser();
        return $user;
    }

    /**
     * @return \core_kernel_classes_Resource|null
     */
    protected function getActionExecution()
    {
        $result = null;
        $requiredActionClass = new \core_kernel_classes_Class(self::CLASS_URI);
        $resources = $requiredActionClass->searchInstances([
            self::PROPERTY_NAME => $this->requiredAction->getName(),
            self::PROPERTY_SUBJECT => $this->getUser()->getIdentifier(),
        ], [
            'like' => false,
        ]);

        if (!empty($resources)) {
            /** @var \core_kernel_classes_Resource $resource */
            $result = current($resources);
        }
        return $result;
    }
}