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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\tao\model\messaging\transportStrategy;

use oat\tao\model\messaging\Transport;
use oat\tao\model\messaging\transportStrategy\AbstractAdapter;
use oat\tao\model\messaging\Message;
use oat\oatbox\user\User;
use oat\oatbox\Configurable;

/**
 * MailAdapter sends email messages using PHPMailer. 
 * 
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class MailAdapter extends Configurable implements Transport
{
    const CONFIG_SMTP_CONFIG = 'SMTPConfig';
    
    const CONFIG_DEFAULT_SENDER = 'defaultSender';
    
    /**
     * Initialize PHPMailer
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return \PHPMailer
     */
    protected function getMailer()
    {
        $mailer = new \PHPMailer();
        
        $SMTPConfig = $this->getOption(self::CONFIG_SMTP_CONFIG);
        
        $mailer->IsSMTP();
        $mailer->SMTPKeepAlive = true;
        $mailer->Debugoutput = 'error_log';
        
        $mailer->Host = $SMTPConfig['SMTP_HOST'];
        $mailer->Port = $SMTPConfig['SMTP_PORT'];
        $mailer->Username = $SMTPConfig['SMTP_USER'];
        $mailer->Password = $SMTPConfig['SMTP_PASS'];
        
        if (isset($SMTPConfig['DEBUG_MODE'])) {
            $mailer->SMTPDebug = $SMTPConfig['DEBUG_MODE'];
        }
        if (isset($SMTPConfig['Mailer'])) {
            $mailer->Mailer = $SMTPConfig['Mailer'];
        }
        if (isset($SMTPConfig['SMTP_AUTH'])) {
            $mailer->SMTPAuth = $SMTPConfig['SMTP_AUTH'];
        }
        if (isset($SMTPConfig['SMTP_SECURE'])) {
            $mailer->SMTPSecure = $SMTPConfig['SMTP_SECURE'];
        }
        
        return $mailer;
    }

    /**
     * Sent email message
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param Message $message
     * @return boolean whether message was sent
     */
    public function send(Message $message)
    {
        $mailer = $this->getMailer();
        $mailer->SetFrom($this->getFrom($message));
        $mailer->AddReplyTo($this->getFrom($message));
        $mailer->Subject = $message->getTitle();
        $mailer->AltBody = strip_tags(preg_replace("/<br.*>/i", "\n", $message->getBody()));
        $mailer->MsgHTML($message->getBody());
        $mailer->AddAddress($this->getUserMail($message->getTo()));

        $result = false;
        try {
            if ($mailer->Send()) {
                $message->setStatus(\oat\tao\model\messaging\Message::STATUS_SENT);
                $result = true;
            }
            if ($mailer->IsError()) {
                $message->setStatus(\oat\tao\model\messaging\Message::STATUS_ERROR);
            }
        } catch (phpmailerException $pe) {
            \common_Logger::e($pe->getMessage());
        }
        $mailer->ClearReplyTos();
        $mailer->ClearAllRecipients();
        $mailer->SmtpClose();

        return $result;
    }
    
    /**
     * Get user email address.
     * @param User $user
     * @return string
     * @throws Exception if email address is not valid
     */
    public function getUserMail(User $user)
    {
        $userMail = current($user->getPropertyValues(PROPERTY_USER_MAIL));
        
        if (!$userMail || !filter_var($userMail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('User email is not valid.');
        }
        
        return $userMail;
    }
    
    /**
     * Get a "From" address. If it was not specified for message then value will be retrieved from config.
     * @param Message $message (optional)
     * @return string
     */
    public function getFrom(Message $message = null) 
    {
        $from = $message === null ? null : $message->getFrom();
        if (!$from) {
            $from = $this->getOption(self::CONFIG_DEFAULT_SENDER);
        }
        return $from;
    }
}
