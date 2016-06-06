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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */


/**
 * CommonRenderer for the EndAttempt interaction
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/endAttemptInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'i18n'
], function(_, tpl, containerHelper, pciResponse, __){
    'use strict';

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10402
     *
     * @param {object} interaction
     * @fires endattempt with the response identifier
     */
    var render = function render(interaction, options){

        var $container = containerHelper.get(interaction);

        //on click,
        $container.on('click.commonRenderer', function(){
            $container.val(true);

            containerHelper.triggerResponseChangeEvent(interaction);

            $container.trigger('endattempt', [interaction.attr('responseIdentifier')]);
        });
    };

    /**
     * Set the response to the rendered interaction.
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10402
     *
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function setResponse(interaction, response){

        _setVal(interaction, pciResponse.unserialize(response, interaction)[0]);
    };


    /**
     * Return the response of the rendered interaction
     *
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10402
     *
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function getResponse(interaction){
        var val = containerHelper.get(interaction).val();
        val = (val && val !== 'false' && val !== '0');
        return pciResponse.serialize([val], interaction);
    };

    /**
     * Reset the response ... wondering if it is useful ...

     * @param {type} interaction
     */
    var resetResponse = function resetResponse(interaction){
        _setVal(interaction, false);
    };

    /**
     *
     * @param {Object} interaction
     * @param {Boolean} val
     */
    var _setVal = function(interaction, val){

        containerHelper.get(interaction)
            .val(val)
            .change();

    };

    /**
     * Destroy the interaction to restore the dom as it is before render() is called
     *
     * @param {Object} interaction
     */
    var destroy = function destroy(interaction){
        //remove event
        containerHelper.get(interaction).off('.commonRenderer');
    };

    /**
     * Define default template data
     *
     * @param {Object} interaction
     * @param {Object} data
     * @returns {Object}
     */
    var getCustomData = function getCustomData(interaction, data){
        if(!data.attributes.title){
            data.attributes.title = __('End Attempt');
        }
        return data;
    };

    return {
        qtiClass : 'endAttemptInteraction',
        template : tpl,
        getData : getCustomData,
        render : render,
        getContainer : containerHelper.get,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy
    };
});
