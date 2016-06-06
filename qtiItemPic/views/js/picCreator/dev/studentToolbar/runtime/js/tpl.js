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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
/**
 * ORGINAL VERSION:
 * https://github.com/epeli/requirejs-hbs
 * Copyright 2013 Esa-Matti Suuronen
 * MIT License : https://github.com/epeli/requirejs-hbs/blob/master/LICENSE
 * 
 * MODIFIED VERSION:
 * @author Bertrand Chevrier <bertrand@taotesting.com> for OAT SA
 * - Minor code refactoring
 * - Add the i18n helper
 */
define(['handlebars', 'i18n', 'lodash'], function(Handlebars, __, _){
    var buildMap = {};
    var extension = '.tpl';

    //register a i18n helper
    Handlebars.registerHelper('__', function(key){
        return __(key);
    });

    //register join helper
    Handlebars.registerHelper('join', function(arr, glue, delimiter, wrapper){

        var ret = '';

        //set default arguments in the format: name1="value1" name2="value2"
        glue = typeof(glue) === 'string' ? glue : '=';
        delimiter = typeof(delimiter) === 'string' ? delimiter : ' ';
        wrapper = typeof(wrapper) === 'string' ? wrapper : '"';

        _.forIn(arr, function(value, key){
            if(value !== null || value !== undefined){
                if(typeof(value) === 'boolean'){
                    value = value ? 'true' : 'false';
                }else if(typeof(value) === 'object'){
                    value = _.values(value).join(' ');
                }
            }else{
                value = '';
            }
            ret += key + glue + wrapper + value + wrapper + delimiter;
        });
        if(ret){
            ret.substring(0, ret.length - 1);
        }

        return ret;
    });

    //register a classic "for loop" helper
    //it also adds a local variable "i" as the index in each iteration loop
    Handlebars.registerHelper('for', function(startIndex, stopIndex, increment, options){
        var ret = '';
        startIndex = parseInt(startIndex);
        stopIndex = parseInt(stopIndex);
        increment = parseInt(increment);

        for(var i = startIndex; i < stopIndex; i += increment){
            ret += options.fn(_.extend({}, this, {'i' : i}));
        }

        return ret;
    });

    Handlebars.registerHelper('equal', function(var1, var2, options){
        if(var1 == var2){
            return options.fn(this);
        }else{
            return options.inverse(this);
        }
    });

    return {
        load : function(name, req, onload, config){
            extension = extension || config.extension;

            if(config.isBuild){
                //optimization, r.js node.js version
                buildMap[name] = fs.readFileSync(req.toUrl(name + extension)).toString();
                onload();

            }else{
                req(["text!" + name + extension], function(raw){
                    // Just return the compiled template
                    onload(Handlebars.compile(raw));
                });
            }
        },
        write : function(pluginName, moduleName, write){
            if(moduleName in buildMap){
                var compiled = Handlebars.precompile(buildMap[moduleName]);
                // Write out pre-compiled version of the template function as AMD definition.
                write(
                    "define('tpl!" + moduleName + "', ['handlebars'], function(Handlebars){ \n" +
                    "return Handlebars.template(" + compiled.toString() + ");\n" +
                    "});\n"
                    );
            }
        }
    };
});