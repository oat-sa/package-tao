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

use oat\tao\model\requiredAction\RequiredActionAbstract;
use \Exception;
use oat\tao\model\requiredAction\RequiredActionRuleInterface;
use oat\tao\model\routing\FlowController;

/**
 * Class RequiredAction
 *
 * RequiredAction is action which should be executed by user before performing any activities in the TAO
 *
 * @package oat\tao\model\requiredAction\implementation
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 */
class RequiredActionRedirect extends RequiredActionAbstract
{
    private $excludedRoutes = [
        [
            'extension' => 'tao',
            'module' => 'ClientConfig',
            'action' => 'config',
        ]
    ];

    /**
     * @var string
     */
    private $url;

    /**
     * RequiredActionRedirect constructor.
     * @param string $name
     * @param RequiredActionRuleInterface[] $rules
     * @param string $url
     * @throws Exception
     */
    public function __construct($name, array $rules, $url)
    {
        parent::__construct($name, $rules);
        $this->url = $url;
    }

    /**
     * Execute an action
     * @param array $params
     * @return mixed
     */
    public function execute(array $params = [])
    {
        $context = \Context::getInstance();
        $excludedRoutes = $this->getExcludedRoutes();
        $currentRoute = [
            'extension' => $context->getExtensionName(),
            'module' => $context->getModuleName(),
            'action' => $context->getActionName(),
        ];

        if (!in_array($currentRoute, $excludedRoutes)) {
            $currentUrl = \common_http_Request::currentRequest()->getUrl();
            $url = $this->url . (parse_url($this->url, PHP_URL_QUERY) ? '&' : '?') . 'return_url=' . urlencode($currentUrl);

            $flowController = new FlowController();
            $flowController->redirect($url);
        }
    }

    /**
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        $class = get_class($this);
        $name = $this->name;
        $url = $this->url;
        $rules = \common_Utils::toHumanReadablePhpString($this->getRules());
        return "new $class(
            '$name',
            $rules,
            '$url'
        )";
    }

    /**
     * Some actions should not be redirected (such as retrieving requireJs config)
     * @return array
     */
    private function getExcludedRoutes()
    {
        $result = $this->excludedRoutes;
        $resolver = new \Resolver($this->url);

        $result[] = [
            'extension' => $resolver->getExtensionFromURL(),
            'module' => $resolver->getModule(),
            'action' => $resolver->getAction(),
        ];

        return $result;
    }
}