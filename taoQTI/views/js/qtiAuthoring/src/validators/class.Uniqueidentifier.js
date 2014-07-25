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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
define(['require', 'jquery', root_url + 'taoQTI/views/js/qtiAuthoring/src/validators/class.Validator.js'], function(req, $, ValidatorClass){

    var validatorClassFunctions = {
        init : function(options){
            this._super('QTIuniqueIdentifier', options);
        },
        getDefaultOptions : function(){
            return {
                message : 'The identifier is already in use'
            };
        },
        validate : function(value, callback){

            if(this.options.element){
                var used = false;
                var $thisElt = this.options.element;
                var $parents = $thisElt.parents('div.formContainer_choice');
                if($thisElt.length && $parents.length){
                    var thisSerial = $($parents[0]).attr('id');
                    if(thisSerial){
                        $('div#formContainer_choices div.formContainer_choice').each(function(){
                            if($(this).attr('id') !== thisSerial){
                                if($thisElt.val() === $(this).find('input#choiceIdentifier').val()){
                                    used = true;
                                }
                            }
                        });
                    }

                    if(used){
                        //the identifier is already used
                        callback(false);
                    }else{

                        if(qtiEdit.idList){
                            thisSerial = thisSerial.replace('ChoiceForm_', '');
                            var existingSerial = qtiEdit.idList.exists(value);
                            callback((existingSerial && existingSerial !== thisSerial) ? false : true, value);
                        }else{
                            //check on the server :
                            qtiEdit.ajaxRequest({
                                type : "POST",
                                url : root_url + "taoQTI/QtiAuthoring/isIdentifierUsed",
                                data : {
                                    'identifier' : value
                                },
                                dataType : 'json',
                                success : function(r){
                                    callback(!r.used, value);
                                }
                            });//end of ajax call
                        }

                    }

                }

            }
        }

    }

    return ValidatorClass.extend(validatorClassFunctions);
});
