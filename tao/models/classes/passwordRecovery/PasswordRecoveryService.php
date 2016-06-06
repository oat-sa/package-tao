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
 */

namespace oat\tao\model\passwordRecovery;

use oat\tao\helpers\Template;
use oat\tao\model\messaging\MessagingService;
use oat\tao\model\messaging\Message;

/**
 * Password recovery service
 *
 * @access public
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class PasswordRecoveryService extends \tao_models_classes_Service
{

    const PROPERTY_PASSWORD_RECOVERY_TOKEN = 'http://www.tao.lu/Ontologies/generis.rdf#passwordRecoveryToken';
    
    /**
     * @var MessagingService
     */
    private $messagingSerivce;
    
    /**
     * Send email message with password recovery instructions.
     * 
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param core_kernel_classes_Resource $user The user has requested password recovery.
     * @return boolean Whether message was sent.
     */
    public function sendMail(\core_kernel_classes_Resource $user)
    {
        $messagingService = $this->getMessagingService();
        if (!$messagingService->isAvailable()) {
            throw new PasswordRecoveryException('Messaging service is not available.');
        }
        $generisUser = new \core_kernel_users_GenerisUser($user);
        $userNameProperty = new \core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);

        $messageData = array(
            'user_name' => (string) $user->getOnePropertyValue($userNameProperty),
            'link' => $this->getPasswordRecoveryLink($user)
        );
        
        $message = new Message();
        $message->setTo($generisUser);
        $message->setBody($this->getMailContent($messageData));
        $message->setTitle(__("Your TAO Password"));
        
        $result = $messagingService->send($message);
        
        return $result;
    }
    
    /**
     * Get user by property value
     * @param string $property uri
     * @param type $value property value
     * @return core_kernel_classes_Resource | null
     */
    public function getUser($property, $value)
    {
        $class = new \core_kernel_classes_Class(CLASS_GENERIS_USER);

        $users = \tao_models_classes_UserService::singleton()->searchInstances(
            array($property => $value), 
            $class,
            array('like' => false, 'recursive' => true)
        );
        $user = empty($users) ? null : current($users);
        
        return $user;
    }
    
    /**
     * Get user mail value
     * @param core_kernel_classes_Resource $user
     * @return string | null
     */
    public function getUserMail(\core_kernel_classes_Resource $user)
    {
        $userMailProperty = new \core_kernel_classes_Property(PROPERTY_USER_MAIL);
        $result = (string) $user->getOnePropertyValue($userMailProperty);
        if (!$result || !filter_var($result, FILTER_VALIDATE_EMAIL)) {
            $result = null;
        }
        return $result;
    }
    
    /**
     * Change user pasword
     * @param core_kernel_classes_Resource $user
     * @param string $newPassword New password value
     */
    public function setPassword(\core_kernel_classes_Resource $user, $newPassword) 
    {
        \tao_models_classes_UserService::singleton()->setPassword($user, $newPassword);
        $this->deleteToken($user);
    }
    
    /**
     * Delete password recovery token.
     * 
     * @param \core_kernel_classes_Resource $user
     * @return boolean
     */
    public function deleteToken(\core_kernel_classes_Resource $user)
    {
        $tokenProperty = new \core_kernel_classes_Property(self::PROPERTY_PASSWORD_RECOVERY_TOKEN);
        return $user->removePropertyValues($tokenProperty);
    }
    
    /**
     * Get messaging service
     * 
     * @return MessagingService
     */
    public function getMessagingService()
    {
        if (is_null($this->messagingSerivce)) {
            $this->messagingSerivce = MessagingService::singleton(); 
        }
        return $this->messagingSerivce;
    }
    
    /**
     * Function generates password recovery email message content
     * May be used in the following way:
     * <pre>
     * $this->getMailContent(array(
     *     'user_name'=>'John Doe',
     *     'link'=>$this->getPasswordRecoveryLink($user)
     * ));
     * </pre>
     * 
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param array $messageData
     * @return string Message content
     */
    private function getMailContent($messageData)
    {
        $renderer = new \Renderer();
        $renderer->setTemplate(Template::getTemplate('passwordRecovery/password-recovery-message.tpl', 'tao'));
        foreach ($messageData as $key => $value) {
            $renderer->setData($key, $value);
        }
        return $renderer->render();
    }

    /**
     * Get password recovery link.
     * 
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param core_kernel_classes_Resource $user The user has requested password recovery.
     * @return string Password recovery link.
     */
    private function getPasswordRecoveryLink(\core_kernel_classes_Resource $user)
    {
        $token = $this->generateRecoveryToken($user);
        return _url('resetPassword', 'PasswordRecovery', 'tao', array('token' => $token));
    }

    /**
     * Generate password recovery token. 
     * If user already has passwordRecoveryToken property then it will be replaced.
     * 
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param core_kernel_classes_Resource $user The user has requested password recovery.
     * @return string Password recovery token.
     */
    private function generateRecoveryToken(\core_kernel_classes_Resource $user)
    {
        $this->deleteToken($user);
        
        $token = md5(uniqid(mt_rand(), true));
        $tokenProperty = new \core_kernel_classes_Property(self::PROPERTY_PASSWORD_RECOVERY_TOKEN);
        $user->setPropertyValue($tokenProperty, $token);

        return $token;
    }
}

?>