/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'module', 'helpers', 'users'], function($, module, helpers, users) {
    'use strict';
       
    
    /**
     * The user add controller
     * @exports controller/users/add
     */    
    return {
        start : function(){
            var conf = module.config();
            var url  = helpers._url('checkLogin', 'Users', 'tao');
            users.checkLogin(conf.loginId, url);
            
            if(conf.exit === true){

                setTimeout(function(){
                    //TODO would be better to clean up the form and switch the section
                    window.location = helpers._url('index', 'Main', 'tao', {structure: 'users', ext : 'tao', section : 'list_users'});
                }, 1000);
            }
        }
    };
});
