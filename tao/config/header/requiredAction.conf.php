<?php
/**
 * RequiredAction is action which should be executed by user before performing any activities in the TAO.
 * Configuration example:
 * ```php
 *
 * use oat\tao\model\requiredAction\implementation\RequiredAction;
 * use oat\tao\model\requiredAction\implementation\TimeRule;
 * use oat\tao\model\requiredAction\implementation\RequiredActionService;
 *
 * //the service expects to receive a list of actions which should be executed as `required_actions` option
 * return new RequiredActionService([
 *    'required_actions' => [
 *        new RequiredAction(
 *            'codeOfConduct', //unique action name
 *            [ //list of rules to check whether action must be performed by user
 *                new TimeRule(), //check (by action name) when this action was executed by current user last time (or check an interval if an action is recurring).
 *                new CookieRule(), //check whether user has certain cookie.
 *                new CallbackRule('helperClass::shouldUserExecuteIt'), //execute callback to check if user should execute it.
 *            ]
 *        ),
 *       //another actions ...
 *     ]
 * ]);
 * ```
 */