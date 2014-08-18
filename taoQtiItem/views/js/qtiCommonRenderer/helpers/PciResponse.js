define(['lodash', 'taoQtiItem/qtiCommonRenderer/helpers/PciPrettyPrint'], function(_, prettyPrint){

    var _qtiModelPciResponseCardinalities = {
        single : 'base',
        multiple : 'list',
        ordered : 'list',
        record : 'record'
    };

    var _prettyPrintBase = function(value, withType) {
        var print = '';
        withType = (typeof withType !== 'undefined') ? withType : true;
        if(value.base){
            if (typeof value.base.boolean !== 'undefined') {
                // Display Boolean.
                print += (withType == true) ? '(boolean) ' : '';
                print += (value.base.boolean == true) ? 'true' : 'false';
            }
            else if (typeof value.base.integer !== 'undefined') {
                print += (withType == true) ? '(integer) ' : '';
                print += value.base.integer;
            }
            else if (typeof value.base.float !== 'undefined') {
                print += (withType == true) ? '(float) ' : '';
                print += value.base.float;
            }
            else if (typeof value.base.string != 'undefined') {
                print += (withType == true) ? '(string) ' : '';
                // In QTI, empty strings are considered to be NULL.
                print += (value.base.string == '') ? 'NULL' : ('"' + value.base.string + '"');
            }
            else if (typeof value.base.point != 'undefined') {
                print += (withType == true) ? '(point) ' : '';
                print += '[' + value.base.point[0] + ', ' + value.base.point[1] + ']';
            }
            else if (typeof value.base.pair != 'undefined') {
                print += (withType == true) ? '(pair) ' : '';
                print += '[' + value.base.pair[0] + ', ' + value.base.pair[1] + ']';
            }
            else if (typeof value.base.directedPair != 'undefined') {
                print += (withType == true) ? '(directedPair) ' : '';
                print += '[' + value.base.pair[0] + ', ' + value.base.pair[1] + ']';
            }
            else if (typeof value.base.duration != 'undefined') {
                print += (withType == true) ? '(duration) ' : '';
                print += value.base.duration;
            }
            else if (typeof value.base.file != 'undefined') {
                print += (withType == true) ? '(file) ' : '';
                print += 'binary data';
            }
            else if (typeof value.base.uri != 'undefined') {
                print += (withType == true) ? '(uri) ' : '';
                print += value.base.uri;
            }
            else if (typeof value.base.intOrIdentifier != 'undefined') {
                print += (withType == true) ? '(intOrIdentifier) ' : '';
                print += value.base.intOrIdentifier;
            }
            else if (typeof value.base.identifier != 'undefined') {
                print += (withType == true) ? '(identifier) ' : '';
                print += value.base.identifier;
            }
            else {
                throw 'Unknown PCI JSON BaseType';
            }
        }
        return print;
    };

    return {
        
        /**
         * Parse a response variable formatted according to IMS PCI: http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * 
         * @see serialize
         * @param {Object} response
         * @param {Object} interaction
         * @returns {Array}
         */
        unserialize : function(response, interaction){
            
            var ret = [],
                responseDeclaration = interaction.getResponseDeclaration(),
                baseType = responseDeclaration.attr('baseType'),
                cardinality = responseDeclaration.attr('cardinality'),
                mappedCardinality;
            
            if(_qtiModelPciResponseCardinalities[cardinality]){
                mappedCardinality = _qtiModelPciResponseCardinalities[cardinality];
                var responseValues = response[mappedCardinality];
                
                if(responseValues === null){
                    ret = [];
                }else if(_.isObject(responseValues)){
                    if(responseValues[baseType] !== undefined){
                        ret = responseValues[baseType];
                        ret = _.isArray(ret) ? ret : [ret];
                    }else{
                        throw 'invalid response baseType';
                    }
                }else{
                    throw 'invalid response cardinality, expected '+cardinality+' ('+mappedCardinality+')';
                }
            }else{
                throw 'unknown cardinality in the responseDeclaration of the interaction';
            }
            
            return ret;
        },
        /**
         * Serialize the input response array into the format to be send to result server according to IMS PCI recommendation :
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * With the only exception for empty response, which is represented by a javascript "null" value
         * 
         * @see http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * @param {Array} responseValues
         * @param {Object} interaction
         * @returns {Object|null}
         */
        serialize : function(responseValues, interaction){
            
            if(!_.isArray(responseValues)){
                throw 'invalid argument : responseValues must be an Array';
            }

            var response = {},
                responseDeclaration = interaction.getResponseDeclaration(),
                baseType = responseDeclaration.attr('baseType'),
                cardinality = responseDeclaration.attr('cardinality'),
                mappedCardinality;

            if(_qtiModelPciResponseCardinalities[cardinality]){
                mappedCardinality = _qtiModelPciResponseCardinalities[cardinality];
                if(mappedCardinality === 'base'){
                    if(responseValues.length === 0){
                        //return empty response:
                        response.base = null;
                    }else{
                        response.base = {};
                        response.base[baseType] = responseValues[0];
                    }
                }else{
                    response[mappedCardinality] = {};
                    response[mappedCardinality][baseType] = responseValues;
                }
            }else{
                throw 'unknown cardinality in the responseDeclaration of the interaction';
            }

            return response;
        },
        isEmpty : function(response){
            return (
                response === null
                || _.isEmpty(response)
                || response.base === null
                || _.isArray(response.list) && _.isEmpty(response.list)
                || _.isArray(response.record) && _.isEmpty(response.record)
            );
        },
        
        /**
         * Print a PCI JSON response into a human-readable string.
         * 
         * @param {Object} response A response in PCI JSON representation.
         * @returns {String} A human-readable version of the PCI JSON representation.
         */
        prettyPrint: function(response) {
            var print = '';
            
            if (typeof response.base !== 'undefined') {
                // -- BaseType.
                print += prettyPrint.printBase(response, true);
            }
            else if (typeof response.list !== 'undefined') {
                // -- ListType
                print += prettyPrint.printList(response, true);
            }
            else if (typeof response.record !== 'undefined') {
                // @todo pretty print of records.
            }
            else {
                throw 'Not a valid PCI JSON Response';
            }
            
            return print;
        } 
    };
});
