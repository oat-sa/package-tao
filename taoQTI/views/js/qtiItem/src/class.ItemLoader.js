var ItemLoader = Class.extend({
    init : function(){
        this.item = null;
    },
    load : function(data){
        if(typeof data === 'object' && data.type === 'assessmentItem'){
            this.item = new Qti.Item(data.serial, data.attributes || {});
            this.loadContainer(this.item.getBody(), data.body);
            for(var i in data.responses){
                var response = this.buildResponse(data.responses[i]);
                if(response){
                    this.item.addResponseDeclaration(response);
                }
            }
            for(var i in data.outcomes){
                var outcome = this.buildOutcome(data.outcomes[i]);
                if(outcome){
                    this.item.addOutcomeDeclaration(outcome);
                }
            }
            for(var i in data.feedbacks){
                var feedback = this.buildElement(data.feedbacks[i]);
                if(feedback){
                    this.item.addModalFeedbacks(feedback);
                }
            }
            this.item.setNamespaces(data.namespaces);
            this.item.setStylesheets(data.stylesheets);
        }
        return this.item;
    },
    buildResponse : function(data){
        var response = this.buildElement(data);
        response.howMatch = data.howMath || null;
        response.correctResponses = data.correctResponses || {};
        response.mapping = data.mapping || {};
        response.areaMapping = data.areaMapping || {};
        response.mappingAttributes = data.mappingAttributes || {};
        return response;
    },
    buildOutcome : function(data){
        var outcome = this.buildElement(data);
        outcome.scale = data.scale || null;
        return outcome;
    },
    loadContainer : function(bodyObject, bodyData){
        if(!bodyObject instanceof Qti.Container){
            throw 'bodyObject must be a QTI Container';
        }

        if(bodyData && typeof bodyData.body === 'string' && (typeof bodyData.elements === 'array' || typeof bodyData.elements === 'object')){
            for(var serial in bodyData.elements){
                var eltData = bodyData.elements[serial];
                //check if class is loaded:
                var element = this.buildElement(eltData);
                if(element){
                    bodyObject.setElement(element, bodyData.body);
                }
            }
            bodyObject.body(bodyData.body);
        }else{
            throw 'wrong bodydata format';
        }
    },
    buildElement : function(elementData){

        var elt = null;
        if(elementData && elementData.type && elementData.serial && elementData.attributes){
            var className = elementData.type.charAt(0).toUpperCase() + elementData.type.slice(1); //ucfirst
            if(window.Qti[className]){
                elt = new window.Qti[className](elementData.serial, elementData.attributes);
                if(elt.body){
                    if(elt.bdy){
                        this.loadContainer(elt.getBody(), elementData.body);
                    }else{
//                        throw 'missing body data';
                    }
                }
                if(elt.object){
                    if(elt.object){
                        this.loadObjectData(elt.object, elementData.object);
                    }
                }
                if(elt instanceof Qti.Interaction){
                    this.loadInteractionData(elt, elementData);
                }else if(elt instanceof Qti.Choice){
                    this.loadChoiceData(elt, elementData);
                }else if(elt instanceof Qti.Math){
                    this.loadMathData(elt, elementData);
                }
            }else{
                throw 'the qti element class does not exist: ' + className;
            }
        }else{
            throw 'wrong elementData format';
        }
        return elt;
    },
    loadInteractionData : function(interaction, data){
        if(interaction instanceof Qti.BlockInteraction){
            if(data.prompt){
                this.loadContainer(interaction.prompt.getBody(), data.prompt);
            }
        }
        this.buildInteractionChoices(interaction, data);
    },
    buildInteractionChoices : function(interaction, data){

        //note: Qti.ContainerInteraction (Qti.GapMatchInteraction and Qti.HottextInteraction) has already been parsed by builtElement(interacionData);
        if(data.choices){
            if(interaction instanceof Qti.MatchInteraction){
                for(var set = 0; set < 2; set++){
                    if(!data.choices[set]){
                        throw 'missing match set #' + set;
                    }
                    var matchSet = data.choices[set];
                    for(var serial in matchSet){
                        var choice = this.buildElement(matchSet[serial]);
                        if(choice){
                            interaction.addChoice(choice, set);
                        }
                    }
                }
            }else{
                for(var serial in data.choices){
                    var choice = this.buildElement(data.choices[serial]);
                    if(choice){
                        interaction.addChoice(choice);
                    }
                }
            }

            if(interaction instanceof Qti.GraphicGapMatchInteraction){
                if(data.gapImgs){
                    for(var serial in data.gapImgs){
                        var gapImg = this.buildElement(data.gapImgs[serial]);
                        if(gapImg){
                            interaction.addGapImg(gapImg);
                        }
                    }
                }
            }

        }

    },
    loadChoiceData : function(choice, data){
        if(choice instanceof Qti.TextVariableChoice){
            choice.val(data.text);
        }else if(choice instanceof Qti.GapImg){
            //has already been taken care of in buildElement()
        }else if(choice instanceof Qti.ContainerChoice){
            //has already been taken care of in buildElement()
        }
    },
    loadObjectData : function(object, data){
        object.setAttributes(data.attributes);
        //@todo: manage object like a container
        if(data.alt){
            if(data.alt.type && data.alt.type === 'object'){
                object.alt = data.alt;
            }
        }
    },
    loadMathData : function(math, data){
        math.mathML = data.mathML || '';
        math.annotations = data.annotations || {};
    }
});


