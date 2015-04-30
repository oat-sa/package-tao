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
use oat\oatbox\Configurable;
use oat\tao\model\messaging\Message;
/**
 * An implementation that writes the messages to the filesystem
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 */
class FileSink extends Configurable implements Transport
{
    const CONFIG_FILEPATH = 'path';
    
    public function send(Message $message)
    {
        $receiver = $message->getTo();
        $path = $this->getOption(self::CONFIG_FILEPATH).\tao_helpers_File::getSafeFileName($receiver->getIdentifier()).DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            mkdir($path);
        }
        $messageFile = $path.\tao_helpers_File::getSafeFileName('message.html', $path);
        \common_Logger::d($messageFile);
        $written = file_put_contents($messageFile, $message->getBody());
        return $written !== false;
    }
}
