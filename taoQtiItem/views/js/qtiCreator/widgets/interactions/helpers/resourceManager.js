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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

/**
 * Pre-configured instance of the resource manager
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'util/image',
    'ui/resourcemgr'
], function ($, _, __, imageUtil) {
    'use strict';

    var ns = 'upload';

    /**
     * @exports
     */
    return function($element, options){
        $element.resourcemgr({
            title : options.title,
            appendContainer : options.mediaManager.appendContainer,
            mediaSourcesUrl : options.mediaManager.mediaSourcesUrl,
            browseUrl : options.mediaManager.browseUrl,
            uploadUrl : options.mediaManager.uploadUrl,
            deleteUrl : options.mediaManager.deleteUrl,
            downloadUrl : options.mediaManager.downloadUrl,
            fileExistsUrl : options.mediaManager.fileExistsUrl,
            params : {
                uri : options.uri,
                lang : options.lang,
                filters : 'image/jpeg,image/png,image/gif'
            },
            pathParam : 'path',
            select : function(e, files){
                var selected;
                if(files.length > 0){
                    selected = files[0];
                    imageUtil.getSize(options.baseUrl + encodeURIComponent(files[0].file), function(size){
                        $element.trigger('selected.' + ns, {
                            selected: selected,
                            size: size
                        });
                    });
                }
            }
        });
    };
});
