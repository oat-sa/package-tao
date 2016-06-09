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

use oat\tao\model\passwordRecovery\PasswordRecoveryService;

/**
 * Controller provide actions to reset user password
 * 
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class tao_actions_PasswordRecovery extends tao_actions_CommonModule
{
    /**
     * @var oat\tao\model\passwordRecovery\PasswordRecoveryService 
     */
    private $passwordRecoveryService;
    
    /**
     * @var \tao_models_classes_UserService 
     */
    private $userService;
    
    /**
     * Constructor performs initializations actions
     */
    public function __construct()
    {
        //initialize user service
        $this->passwordRecoveryService = PasswordRecoveryService::singleton();
        $this->userService = \tao_models_classes_UserService::singleton();
        $this->defaultData();
    }
    
    /**
     * Show password recovery request form
     *
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @return void
     */
    public function index() 
    {
        $formContainer = new tao_actions_form_PasswordRecovery();
        $form = $formContainer->getForm();
        
        if ($form->isSubmited() && $form->isValid()) {
            $mail = $form->getValue('userMail');
            $user = $this->passwordRecoveryService->getUser(PROPERTY_USER_MAIL, $mail);
            
            if ($user !== null) {
                \common_Logger::i("User requests a password (user URI: {$user->getUri()})");
                $this->sendMessage($user);
            } else {
                \common_Logger::i("Unsuccessful recovery password. Entered e-mail address: {$mail}.");
                $this->setData('header', __('An email has been sent'));
                $this->setData('info', __('A message with further instructions has been sent to your email address: %s', $mail));
            }
            $this->setData('content-template', array('passwordRecovery/password-recovery-info.tpl', 'tao'));
        } else {
            $this->setData('form', $form->render());
            $this->setData('content-template', array('passwordRecovery/index.tpl', 'tao'));
        }
        
        $this->setView('layout.tpl', 'tao');
    }
    
    /**
     * Password resrt form
     *
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @return void
     */
    public function resetPassword()
    {
        $token = $this->getRequestParameter('token');
        
        $formContainer = new tao_actions_form_ResetUserPassword();
        $form = $formContainer->getForm();
        
        $form->setValues(array('token'=>$token));
        
        $user = $this->passwordRecoveryService->getUser(PasswordRecoveryService::PROPERTY_PASSWORD_RECOVERY_TOKEN, $token);
        if ($user === null) {
            \common_Logger::i("Password recovery token not found. Token value: {$token}");
            $this->setData('header', __('User not found'));
            $this->setData('error', __('This password reset link is no longer valid. It may have already been used. If you still wish to reset your password please request a new link'));
            $this->setData('content-template', array('passwordRecovery/password-recovery-info.tpl', 'tao'));
        } else {
            if ($form->isSubmited() && $form->isValid()) {
                $this->passwordRecoveryService->setPassword($user, $form->getValue('newpassword'));
                \common_Logger::i("User {$user->getUri()} has changed the password.");
                $this->setData('info', __("Password successfully changed"));
                $this->setData('content-template', array('passwordRecovery/password-recovery-info.tpl', 'tao'));
            } else {
                $this->setData('form', $form->render());
                $this->setData('content-template', array('passwordRecovery/password-reset.tpl', 'tao'));
            }
        } 
        
        $this->setView('layout.tpl', 'tao');
    }
    
    /**
     * Send message with password recovery instructions
     * 
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @param User $user
     * @return void
     */
    private function sendMessage(core_kernel_classes_Resource $user)
    {
        try {
            $messageSent = $this->passwordRecoveryService->sendMail($user);
        } catch (Exception $e) {
            $messageSent = false;
            \common_Logger::w("Unsuccessful recovery password. {$e->getMessage()}.");
        }

        if ($messageSent) {
            $mail = $this->passwordRecoveryService->getUserMail($user);
            $this->setData('header', __('An email has been sent'));
            $this->setData('info', __('A message with further instructions has been sent to your email address: %s', $mail));
        } else {
            $this->setData('error', __('Unable to send the password reset request'));
        }
    }
}
