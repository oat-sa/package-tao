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
 * @author Sam <sam@taotesting.com>
 */
define(['lodash', 'taoTests/runner/plugin'], function (_, pluginFactory){

    var _defaults = {};

    var pluginImpl = {
        name : 'responseSubmitter',
        init : function (testRunner, cfg){
            
            var self = this;
            
            //listen item response change
            var itemResponses = {
                RESPONSE1 : 1,
                RESPONSE2 : ['A', 'B', 'C']
            };
            
            this.active = true;
            
            //get ready to submit "on move" (warning ! not "on next" because it will currently fail)
            testRunner.before('move', function (e){
                
                var done = e.done();
                
                //submit it to the server (the delay simulates latency)
                _.delay(function(){
                    var success = true;
                    if(success){
                        self.trigger('submit', itemResponses);//this will also call testRunner.trigger('sumbit.responseSubmitter')
                        done();
                    }else{
                        //how to trigger error ?
                        e.prevent();
                    }
                }, 200);
            });
        },
        destroy : function (){
            //remove listners
        },
        //does not need to implement show/hide because it is not a graphic plugin
        enable : function (){
            this.active = true;
        },
        disable : function (){
            this.active = false;
        }
    };

    return function pluginSubmitter(config){
        return pluginFactory(pluginImpl, _defaults)(config);
    };
});