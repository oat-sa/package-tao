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

use oat\tao\model\requiredAction\RequiredActionServiceInterface;
use oat\tao\model\requiredAction\RequiredActionInterface;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;

/**
 * Class RequiredActionService
 *
 * RequiredActionService is the service for work with required actions
 * @see oat\tao\models\services\requiredAction\RequiredActionInterface
 *
 * @package oat\tao\models\services\requiredAction
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 */
class RequiredActionService extends ConfigurableService implements RequiredActionServiceInterface
{
    /**
     * Get list of required actions
     * @return RequiredAction[] array of required action instances
     */
    public function getRequiredActions()
    {
        $actions = $this->getOption(self::OPTION_REQUIRED_ACTIONS);
        return $actions ? $actions : [];
    }

    /**
     * Attach new action
     * @param RequiredActionInterface $action
     */
    public function attachAction(RequiredActionInterface $action)
    {
        $actions = $this->getRequiredActions();
        $actions[] = $action;
        $this->setOption(self::OPTION_REQUIRED_ACTIONS, $actions);
    }

    /**
     * Get required action by name
     * @param  string $name name of action
     * @return RequiredActionInterface array of required action instances
     */
    public function getRequiredAction($name)
    {
        $result = null;
        $actions = $this->getOption(self::OPTION_REQUIRED_ACTIONS);
        foreach ($actions as $action) {
            if ($action->getName() === $name) {
                $result = $action;
                break;
            }
        }
        return $result;
    }

    /**
     * Get first action which should be executed (one of action's rules return true).
     * @param string[] array of action names which should be checked. If array is empty all action will be checked.
     * @return null|RequiredAction
     */
    public function getActionToBePerformed($names = [])
    {
        $result = null;
        if (empty($names)) {
            $actionsToCheck = $this->getRequiredActions();
        } else {
            $actionsToCheck = [];
            foreach ($names as $name) {
                $actionsToCheck[] = $this->getRequiredAction($name);
            }
        }

        foreach ($actionsToCheck as $requiredAction) {
            if ($requiredAction->mustBeExecuted()) {
                $result = $requiredAction;
                break;
            }
        }
        return $result;
    }

    /**
     * Check if any action must be executed and execute first of them.
     * @throws \InterruptedActionException
     */
    public static function checkRequiredActions()
    {
        /** @var RequiredActionService $service */
        $service = ServiceManager::getServiceManager()->get(self::CONFIG_ID);
        $action = $service->getActionToBePerformed();
        if ($action !== null) {
            $action->execute();
        }
    }
}