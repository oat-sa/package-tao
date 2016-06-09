define(['jquery', 'i18n', 'context'], function($, __, context){
    
    /**
     * Enable you to check if the login contained in the field identified by id is unique
     * An ajax request is sent to the url with the login and a JSON response <code>{"available": "true"}</code>
     * @param id
     * @param url
     * @return void
     */
    function checkLogin(id, url){
        var $login = $("input[id='" + id + "']");
        if($login.length > 0){
            $login.blur(function(){
                var elt = $(this);

                // trim value
                var trimmedValue = elt.val().replace(/^\s+/g,'').replace(/\s+$/g,'');
                var value = elt.val();

                if(trimmedValue === ''){
                    $('span.login-info').remove();
                } else{
                    $.postJson(url,
                        { login: value },
                        function(data){
                            $('span.login-info').remove();
                            if(data.available){
                                    elt.after("<span class='login-info'><img src='"+context.taobase_www+"img/tick.png' /> " + __('Login available') + "</span>");
                            } else{
                                    elt.after("<span class='login-info ui-state-error'><img src='"+context.taobase_www+"img/exclamation.png' class='icon' /> " + __('This Login is already in use') + "</span>");
                            }
                        }
                    );
                }
            });
        }
    }
    
    return {
        checkLogin : checkLogin
    };
});
