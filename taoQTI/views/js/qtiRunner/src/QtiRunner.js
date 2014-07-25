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
/**
 * A class to regroup QTI functionalities
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery {@link http://www.jquery.com}
 */

QtiRunner = function(){
    this.item = null;
    this.rpEngine = null;
    this.renderer = null;
    this.itemApi = undefined;
};

QtiRunner.prototype.setItemApi = function(itemApi){
    this.itemApi = itemApi;
};

QtiRunner.prototype.setRenderer = function(renderer){
    if(renderer instanceof Qti.Renderer){
        this.renderer = renderer;
    }else{
        throw 'invalid renderer';
    }
};

QtiRunner.prototype.initItem = function(data){

    var itemLoader = new ItemLoader();
    this.item = itemLoader.load(data);

    if(this.renderer){
        this.item.setRenderer(this.renderer);
        this.item.render({}, $('#qti_item'));
        this.item.postRender();
        this.initInteractionsResponse();
    }else{
        throw 'cannot initialize the qti item because no renderer set';
    }

};

QtiRunner.prototype.initInteractionsResponse = function(){
    if(this.item){
        var interactions = this.item.getInteractions();
        for(var i in interactions){
            var interaction = interactions[i];
            var responseId = interaction.attr('responseIdentifier');
            var values = this.itemApi.getVariable(responseId, function(){
                return function(values){
                    interaction.setResponse(values);
                }
            }());
        }
    }
}

QtiRunner.prototype.validate = function(){
	
    var responses = this.getResponses();
    // store values in context
    for(var key in responses){
        this.itemApi.setVariable(key, responses[key]);
    }

    // submit answers
    this.itemApi.saveResponses(responses);

    // Evaluate the user's responses
    if(this.rpEngine !== null){
        console.log('client side evaluation');
        this.rpEngine.process(responses, function(qtiRunner){
            return function(scores){
                qtiRunner.itemApi.saveScores(scores);
                this.itemApi.finish();
            };
        }(this));
    }else{
        this.itemApi.resultApi.setQtiRunner(this);
        this.itemApi.finish();
    }

};

QtiRunner.prototype.getResponses = function(){

    var responses = {};
    if(this.item){
        var interactions = this.item.getInteractions();
        for(var serial in interactions){
            var interactionResponse = interactions[serial].getResponse();
            responses[interactionResponse.identifier] = interactionResponse.value;
        }
    }

    return responses;
};

QtiRunner.prototype.setResponseProcessing = function(callback){
    this.rpEngine = callback;
};

QtiRunner.prototype.showFeedbacks = function(fbOutcomeIdentifiers, callback){

    //currently only modal feedbacks are available
    var feedbacksToBeDisplayed = [];
    for(var i in this.item.modalFeedbacks){
        var feedback = this.item.modalFeedbacks[i];
        var outcomeIdentifier = feedback.attr('outcomeIdentifier');
        var feedbackIds = fbOutcomeIdentifiers[outcomeIdentifier];
        if(feedbackIds){
            for(var j in feedbackIds){
                if(feedbackIds[j] === feedback.id()){
                    feedbacksToBeDisplayed.push(feedback);
                    break;
                }
            }
        }
    }

    //show in reverse order
    var len = feedbacksToBeDisplayed.length;
    for(var i = len - 1; i > 0; i--){
        var feedback = feedbacksToBeDisplayed[i];
        this.showModalFeedback(feedback);
    }
    //add callback to the last shown modal feedback
    this.showModalFeedback(feedbacksToBeDisplayed[0], callback);
    return len;
}

QtiRunner.prototype.showModalFeedback = function(modalFeedback, callback){
    if(modalFeedback instanceof Qti.ModalFeedback){
        var serial = modalFeedback.getSerial();
        $('#modalFeedbacks').append('<div id="' + serial + '"></div>');
        modalFeedback.render({}, $('#' + serial));
        modalFeedback.postRender({
            callback : callback
        });
    }
}