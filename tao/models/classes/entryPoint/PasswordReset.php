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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\model\entryPoint;

use oat\oatbox\PhpSerializable;
use tao_models_classes_accessControl_AclProxy;
use oat\oatbox\Configurable;

class PasswordReset extends Configurable implements Entrypoint
{

    public function getId() {
        return 'passwordreset';
    }
    
    public function getTitle() {
        return __("Unable to access your account?");
    }
    
    public function getLabel() {
        return __('Password reset');
    }
    
    public function getDescription() {
        return __('Request a password reset via Email.');
    }
    
    public function getUrl() {
        return _url('index', 'PasswordRecovery', 'tao');
    }

}