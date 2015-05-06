<?php
/*  
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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

class wfEngine_actions_WfModule extends tao_actions_CommonModule
{
	
    /**
     * Behaviour to adopt if the user is not allowed to access the current action.
     */
    protected function notAllowedRedirection()
    {
        if($this->hasRequestParameter('processUri') && $this->hasRequestParameter('activityUri')){
            $this->redirect(_url('index', 'Authentication', 'wfEngine', array(
                    'errorMessage' => urlencode(__('Please login to access the selected activity.')),
                    'processUri' => urlencode($this->getRequestParameter('processUri')),
                    'activityUri' => urlencode($this->getRequestParameter('activityUri'))
                    )
                ));
        }else{
            $this->redirect(_url('index', 'Authentication', 'wfEngine', array(
                    'errorMessage' => urlencode(__('Access denied. Please renew your authentication!'))
                )));
        }
    }
}