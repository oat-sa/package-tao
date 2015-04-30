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
/**
 * Short description of class tao_helpers_transfert_MailAdapter
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class MailAdapter extends AbstractAdapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute mailer
     *
     * @access protected
     * @var PHPMailer
     */
    protected $mailer = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
    	$this->mailer = new PHPMailer();
    	
    	if(defined('SMTP_HOST')){
	    	$this->mailer->IsSMTP(); 	
	    	$this->mailer->SMTPKeepAlive = true;
			$this->mailer->SMTPDebug  = DEBUG_MODE;                    
			$this->mailer->SMTPAuth   = SMTP_AUTH; 
			$this->mailer->Host       = SMTP_HOST; 
			$this->mailer->Port       = SMTP_PORT;
			$this->mailer->Username   = SMTP_USER;
			$this->mailer->Password   = SMTP_PASS;
    	}
		
        
    }

    /**
     * Short description of method send
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return int
     */
    public function send()
    {
        $returnValue = (int) 0;

        
        
        foreach($this->messages as $message){
		    if($message instanceof tao_helpers_transfert_Message){
	        	
		    	$this->mailer->SetFrom($message->getFrom());
				$this->mailer->AddReplyTo($message->getFrom());
				
				$this->mailer->Subject    = $message->getTitle();
				
				$this->mailer->AltBody    = strip_tags(preg_replace("/<br.*>/i", "\n", $message->getBody()));
				
				$this->mailer->MsgHTML($message->getBody());
				
				$this->mailer->AddAddress($message->getTo());
				
				try{
					if($this->mailer->Send()) {
						$message->setStatus(tao_helpers_transfert_Message::STATUS_SENT);
						$returnValue++;
					}
					if($this->mailer->IsError()){
						if(DEBUG_MODE){
							echo $this->mailer->ErrorInfo."<br>";
						}
						$message->setStatus(tao_helpers_transfert_Message::STATUS_ERROR);
					}
				}
				catch(phpmailerException $pe){
					if(DEBUG_MODE){
						print $pe;
					}
				}
		    }
		    $this->mailer->ClearReplyTos();
		    $this->mailer->ClearAllRecipients();
        }
        
        $this->mailer->SmtpClose();
        

        return (int) $returnValue;
    }

}

?>