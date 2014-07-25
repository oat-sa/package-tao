define(function(){

    var Report = function(type, data){
        this.type = type;
        this.data = data;
    };

    Report.prototype.isError = function(){
        return (this.type === 'failure' || this.type === 'error');
    };

    return Report;
});
