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
 */
define([
    'jquery',
    'i18n',
    'module',
    'layout/actions',
    'ui/lock',
    'layout/section'
],
function($, __, module, actions, lock, section){
    'use strict';

    /**
     * The item properties controller
     */
    var editItemController = {

        /**
         * Controller entry point
         */
        start : function start(){
            var config = module.config();

            var isPreviewEnabled = !!config.isPreviewEnabled;
            var isAuthoringEnabled = !!config.isAuthoringEnabled;

            var previewAction = actions.getBy('item-preview');
            var authoringAction = actions.getBy('item-authoring');

            if(previewAction){
                previewAction.state.disabled = !config.isPreviewEnabled;
            }
            if(authoringAction){
                authoringAction.state.disabled = !config.isAuthoringEnabled;
            }
            actions.updateState();

            $('#lock-box').each(function() {
                lock($(this)).register();
            });

            //some of the others sections (like the authoring) might have an impact
            //on the state of the other actions, so we reload when we come back
            section
                .off('show')
                .on('show', function(sectionContext){
                    if(sectionContext.id === 'manage_items'){
                        actions.exec('item-properties');
                    }
                });
        }
    };

    return editItemController;
});
