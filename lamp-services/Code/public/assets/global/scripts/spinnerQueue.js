(function( $ ) {
    $.widget( "my.spinnerQueue", {
 
        options: { 
            'queue'     : {},
            'showSpeed' : undefined,
            'hideSpeed' : undefined,
        },
 
        _create: function() {
            $(this.element).append(''+
                '<div class="spinnerQueue" style="display: none;">'+
                '   <div class="spinner-bg">'+
                '       <div class="spinner-image"></div>'+
                '   </div>' +
                '</div>'
            );

            this._hide();
        },

        _setOption: function( key, value ) {
            this.options[key] = value;

            if('queue' == key){
                this._deleteEmpty();

                if( 0 == this.queueSize()  ){
                    this._hide();
                    this._trigger('queueEnded');
                }
            }
        },

        destroy: function() {
            $(this.element).children('.spinnerQueue').remove();
        },
        
        started : function(eventName, isCumulative){
            isCumulative = undefined === isCumulative ? false : isCumulative;
            
            var value = this.options.queue[eventName];

            if(undefined === value){
                if(true === isCumulative){
                    value = 1;
                } else {
                    value = true;
                }
            } else {
                if(true === isCumulative){
                    value++;
                } else {
                    value = true;
                }
            }

            this.options.queue[eventName] = value;

            if( 1 <= this.queueSize() ){
                this._show();
                this._trigger('spinnerShown', [eventName]);
            }
        },

        finished : function(eventName){
            if(undefined !== this.options.queue[eventName]){
                if(this.options.queue[eventName] !== true) {
                    this.options.queue[eventName]--;
                }

                if(this.options.queue[eventName] === true || this.options.queue[eventName] < 1){
                    delete this.options.queue[eventName];
                }
            }

            this._deleteEmpty();

            if( 0 == this.queueSize()  ){
                this._hide();
                this._trigger('queueEnded');
            }
        },

        isInQueue : function(eventName){
            return undefined === this.options.queue[eventName];
        },

        isQueueEmpty : function(){
            return 0 == this.queueSize();
        },

        queueSize : function(){
            return Object.keys( this.options.queue ).length;
        },

        _show : function(){
            var height = Math.max( $(this.element).prop('scrollHeight'), $(this.element).height() );

            $(this.element).children('.spinnerQueue').stop().fadeIn( this.options.showSpeed );
            if(height) {
                $(this.element).children('.spinnerQueue').css('height', height + 'px');
            }
        },

        _hide : function(){
            $(this.element).children('.spinnerQueue').stop().fadeOut( this.options.hideSpeed );
        },

        _deleteEmpty : function(){
            $.each( this.options.queue, function( key, value ) {
                if(value === true || ($.isNumeric(value) && value > 0)){
                    // It's all ok
                } else {
                    delete this.options.queue[key];
                }
            });
        }

    });

}( jQuery ) );
