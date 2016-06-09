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
define(['lodash'], function(_){
    "use strict";
    /**
     * Argument cols format:
     * [{elt:elementRef, units:6, min:4}]
     *
     * Return format:
     * {last:6, middle:4, distributed:[{elt:elementRef, units:5}]}
     *
     * @param {array} cols
     * @param {int} min
     * @param {int} max
     * @returns {object}
     */
    function distributeUnits(cols, min, max){

        max = max || 12;
        var totalUnits = 0, size = 0, _cols = {}, ret = {}, minimized = [], decimals = [];

        for(var i in cols){
            _cols[i] = _.clone(cols[i]);
            totalUnits += cols[i].units;
            size++;
        }

        var avg = totalUnits / size;
        _cols[size + 1] = {
            'elt' : 'middle',
            'units' : avg,
            'min' : min
        };

        if(totalUnits + Math.round(avg) > max){

            var refactoredTotalUnits = 0;
            //need to squeeze the new col between existing ones:
            //refactored
            for(var i in _cols){

                var col = _cols[i];
                var refactoredUnits = col.units * max / (max + avg);//calculate the average, basis for the new inserted element's units
                decimals[i] = Math.round((refactoredUnits % 1) * 100);//get its decimals for later usage

                col.refactoredUnits = Math.round(refactoredUnits);
                if(col.min && col.refactoredUnits < col.min){//a col cannot be smaller than its min units
                    col.refactoredUnits = col.min;
                    minimized[i] = true;//marked it as not changeable
                }
                refactoredTotalUnits += col.refactoredUnits;

            }

            if(refactoredTotalUnits > max){
                //try ceil new units values:
                for(var i; i < size + 1; i++){
                    var col = _cols[i];
                    if(col.decimal > 50 && col.refactoredUnits - 1 > col.min){
                        col.refactoredUnits--;
                        refactoredTotalUnits--;
                    }
                    if(refactoredTotalUnits === max){
                        break;//target achieved
                    }
                }
            }

            var middleUnitValue = _cols[size + 1].refactoredUnits;
            var lastUnitValue = (refactoredTotalUnits > max) ? max : middleUnitValue;
            delete _cols[size + 1];

            ret = {
                last : lastUnitValue, //new col takes the remaining space
                middle : middleUnitValue,
                cols : _cols,
                refactoredTotalUnits : refactoredTotalUnits
            };
        }else{

            //there is "room" for the new element to fill the whole row:
            var last = max - totalUnits;
            ret = {
                last : last,
                middle : last,
                cols : {}//no need to resize cols
            };
        }

        return ret;
    }

    /**
     * Rebistributs the cols proportionally to fill out the "max" width
     *
     * @param {array} cols
     * @param {integer} max
     * @returns {object}
     */
    function redistributeUnits(cols, max){

        max = max || 12;//default to max

        var totalUnits = 0,
            _cols = [],
            totalRefactoredUnits = 0,
            negative = [],
            positive = [],
            ret = [];

        _.each(cols, function(col){
            _cols.push(col);
            totalUnits += col.units;
        });

        if(totalUnits > max){
            throw 'the total number of units exceed the maximum of ' + max;
        }

        _.each(_cols, function(col){

            var refactoredUnits = col.units * max / totalUnits;
            var rounded = Math.round(refactoredUnits);
            totalRefactoredUnits += rounded;

            col.refactoredUnits = rounded;

            if(rounded > refactoredUnits){
                positive.push(col);
            }else{
                negative.push(col);
            }

        });

        positive = _.sortBy(positive, 'refactoredUnits');
        negative = _.sortBy(negative, 'refactoredUnits');

        if(totalRefactoredUnits > max){
            //too much !

            //@todo : start with the hightest refactored
            _.eachRight(positive, function(col){
                col.refactoredUnits --;
                totalRefactoredUnits--;
                if(totalRefactoredUnits === max){
                    return false;
                }
            });

        }else if(totalRefactoredUnits < max){

            //@todo : start with the lowest refactored
            _.each(negative, function(col){
                col.refactoredUnits ++;
                totalRefactoredUnits++;
                if(totalRefactoredUnits === max){
                    return false;
                }
            });

        }

        _.each(negative, function(col){
            ret.push(col);
        });
        _.each(positive, function(col){
            ret.push(col);
        });

        return _cols;
    }


    return {
        distribute : function(cols, min, max){
            return distributeUnits(cols, min, max);
        },
        redistribute : function(cols, max){
            return redistributeUnits(cols, max);
        }

    };
});
