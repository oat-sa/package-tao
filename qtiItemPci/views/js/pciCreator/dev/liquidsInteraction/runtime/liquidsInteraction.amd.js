define(['IMSGlobal/jquery_2_1_1', 'qtiCustomInteractionContext', 'OAT/util/event'], function($, qtiCustomInteractionContext, event){

    var liquidsInteraction = {
        
        /**
         * Custom Interaction Hook API: id
         */
        id : -1,
        
        /**
         * Custom Interaction Hook API: getTypeIdentifier
         * 
         * A unique identifier allowing custom interactions to be identified within an item.
         * 
         * @returns {String} The unique identifier of this PCI Hook implementation.
         */
        getTypeIdentifier : function(){
            return 'liquidsInteraction';
        },
        
        /**
         * 
         * 
         * @param {String} id
         * @param {Node} dom
         * @param {Object} config - json
         */
        initialize : function(id, dom, config){
            
            //add method on(), off() and trigger() to the current object
            event.addEventMgr(this);
            
            // Register the value for the 'id' attribute of this Custom Interaction Hook instance.
            // We consider in this proposal that the 'id' attribute 
            this.id = id;
            this.dom = dom;
            this.config = config || {};

            var canvas = $(this.dom).find('.liquids')[0];
            
            this._drawLiquidContainer();

            // Tell the rendering engine that I am ready.
            // Please note that in this proposal, we consider the 'id' attribute to be part of the
            // Custom Interaction Hood API. The Global Context will then inspect the 'id' attribute
            // to know which instance of the PCI hook is requesting a service to be achieved.
            qtiCustomInteractionContext.notifyReady(this);
            
            var self = this;
            
            // Bind events
            $(canvas).on('click', function(e) {
                var rect = canvas.getBoundingClientRect();
                
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                
                if (self._isCoordInLiquidContainer(x, y) === true) {
                    self._clearCanvas();
                    self._drawLiquidContainer(y);
                    self._updateResponse(y);
                    
                    //communicate the response change to the interaction
                    self.trigger('responsechange', [self.getResponse()]);
                }
            });
        },
        
        /**
         * Programmatically set the response following the json schema described in
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * 
         * @param {Object} interaction
         * @param {Object} response
         */
        setResponse : function(response){
            var y = this._yFromResponse(response);
            
            this._clearCanvas();
            this._drawLiquidContainer(y);
            this._updateResponse(y);
        },
        
        /**
         * Get the response in the json format described in
         * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
         * 
         * @param {Object} interaction
         * @returns {Object}
         */
        getResponse : function(){
            return this._currentResponse;
        },
        
        /**
         * Remove the current response set in the interaction
         * The state may not be restored at this point.
         * 
         * @param {Object} interaction
         */
        resetResponse : function(){
            this._clearCanvas();
            this._drawLiquidContainer();
            this._currentResponse = { base: null };
        },
        
        /**
         * Reverse operation performed by render()
         * After this function is executed, only the inital naked markup remains 
         * Event listeners are removed and the state and the response are reset
         * 
         * @param {Object} interaction
         */
        destroy : function(){

            var $container = $(this.dom);
            $container.find('.canvas').off();
        },
        
        /**
         * Restore the state of the interaction from the serializedState.
         * 
         * @param {Object} interaction
         * @param {Object} serializedState - json format
         */
        setSerializedState : function(state){

        },
        
        /**
         * Get the current state of the interaction as a string.
         * It enables saving the state for later usage.
         * 
         * @param {Object} interaction
         * @returns {Object} json format
         */
        getSerializedState : function(){
            return {};
        },
        
        _currentResponse: { base : null },
        _callbacks: [],
        
        _drawLiquidContainer : function(y) {
            
            var canvas = $(this.dom).find('.liquids')[0];
            var ctx = canvas.getContext('2d');
            var lineWidth = 2;
            var font = '16px Arial';
            var xLabel = '3 1/4 in';
            var zLabel = '4 in'

            ctx.lineWidth = lineWidth;
            ctx.strokeStyle = 'black';
            ctx.fillStyle = 'white';

            // Draw background rectangle
            ctx.beginPath();
            ctx.rect(210, 20, 160, 280);
            ctx.stroke();
            
            // Draw edges
            ctx.beginPath();
            ctx.moveTo(210, 20);
            ctx.lineTo(130, 70);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(210, 300);
            ctx.lineTo(130, 350);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(370, 300);
            ctx.lineTo(290, 350);
            ctx.stroke();
            
            // Draw liquid if needed
            if (typeof y !== 'undefined') {
                this._drawLiquid(ctx, y);
            }
            
            // Last edge...
            ctx.beginPath();
            ctx.moveTo(370, 20);
            ctx.lineTo(290, 70);
            ctx.stroke();
            
            // Draw foreground rectangle
            ctx.strokeStyle = 'black';
            ctx.fillStyle = 'black';
            ctx.beginPath();
            ctx.rect(130, 70, 160, 280);
            ctx.stroke();
            
            this._drawGraduations(ctx, lineWidth, font);
            this._drawLabels(ctx, font, xLabel, zLabel);
        },
        
        _drawGraduations : function(ctx, lineWidth, font) {
            
            ctx.lineWidth = lineWidth;
            ctx.font = font;
            ctx.textAlign = 'right'
            
            var i = 0;
            var x = 130;
            var y = 350;
            var space = 25;
            var width = 20;
            
            for (i; i <= 10; i++) {
                ctx.beginPath();
                ctx.moveTo(x - (width / 2), y);
                ctx.lineTo(x + (width / 2), y);
                ctx.stroke();
                
                ctx.fillText(i , x - width, y + 5);
                
                y -= space;
            }
        },
        
        _drawLabels : function(ctx, font, xLabel, zLabel) {
            
            ctx.font = font;
            ctx.textAlign = 'center';
            
            // Draw x label
            ctx.fillText(xLabel, 210, 370);
            
            // Draw z label
            ctx.fillText(zLabel, 360, 340);
            
        },
        
        _clearCanvas : function() {
            var canvas = $(this.dom).find('.liquids')[0];
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        },
        
        _isCoordInLiquidContainer : function(x, y) {
            return (x >= 130 && x <= 290 && y >= 100 && y <= 350);
        },

        _drawLiquid : function(ctx, y) {
            var closestY = this._closestMultiple(y, 25);
            y = closestY;
            
            ctx.fillStyle = (closestY !== 350) ? '#003ADB' : 'transparent';
            ctx.beginPath();
            
            // Draw liquid top
            ctx.moveTo(130, y);
            ctx.lineTo(290, y);
            ctx.lineTo(370, y - 50);
            ctx.lineTo(210, y - 50);
            ctx.lineTo(130, y);
            ctx.fill();
            
            // Draw liquid x face.
            ctx.beginPath();
            ctx.fillStyle = '#4B6FD1';
            ctx.rect(130, y, 160, 350 - y);
            ctx.fill();
            
            // Draw liquid z face.
            ctx.beginPath();
            ctx.fillStyle = '#7B96E3';
            ctx.moveTo(290, y);
            ctx.lineTo(369, y - 50);
            ctx.lineTo(369, y + (300 - y));
            ctx.lineTo(290, y + (350 - y));
            ctx.fill();
        },
        
        _closestMultiple : function(numeric, multiple) {
            return multiple * Math.floor((numeric + multiple / 2) / multiple);
        },
        
        _yFromResponse : function(response) {
            if (response.base === null) {
                return 350;
            } else {
                return 350 - this._closestMultiple(response.base.integer * 25, 25);
            }
        },
        
        _updateResponse : function(y) {
            this._currentResponse = { base: { integer: Math.abs((this._closestMultiple(y, 25) - 350) / 25) } };
        }
    };

    qtiCustomInteractionContext.register(liquidsInteraction);
});