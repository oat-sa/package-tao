define(['jquery', 'i18n', 'context', 'helpers'], function($, __, context, helpers){

    var ext_installed = [];
    var toInstall = [];
    var indexCurrentToInstall = -1;
    var percentByExt = 0;
    var installError = 0;

    function getDependencies(extension) {
            var dependencies = [];
            $('#' + extension + ' .dependencies li:not(.installed)').each(function() {
                    var ext = $(this).attr('rel');
                    var deps = getDependencies(ext);
                    deps.push(ext);
                    dependencies = dependencies.concat(deps);
            });
            return dependencies;
    }

    //Give an array with unique values
    function getUnique(orig){
            var a = [];
            for (var i = 0; i < orig.length; i++) {
                    if ($.inArray(orig[i], a) < 0) a.push(orig[i]);
            }
            return a;
    }

    function progressConsole(msg) {
            $('#installProgress .console').append('<p>' + msg + '</p>');
            $('#installProgress .console').prop({scrollTop: $('#installProgress .console').prop("scrollHeight")});
    }

    function installNextExtension() {
            var ext = toInstall[indexCurrentToInstall];
            $('#installProgress p.status').text(__('Installing extension %s...').replace('%s', ext));
            progressConsole(__('Installing extension %s...').replace('%s', ext));
            $.ajax({
                    type: "POST",
                    url: context.root_url + "tao/ExtensionsManager/install",
                    data: 'id='+ext,
                    dataType: 'json',
                    success: function(data) {
                            helpers.loaded();
                            if (data.success) {
                                    progressConsole(__('> Extension %s succesfully installed.').replace('%s', ext));

                                    $('#installProgress .bar').animate({width:'+=' + percentByExt + '%'}, 1000, function() {
                                            //Next
                                            indexCurrentToInstall++;
                                            hasNextExtensionToInstall();
                                    });
                            } else {
                                    installError = 1;
                                    progressConsole('Installation of ' + ext + ' failed');
                            }
                            helpers.createInfoMessage(data.message);
                    }
            });

            if (installError) {
                    progressConsole(__('A fatal error occured during the installation process.'));
            }
    }

    function hasNextExtensionToInstall() {
            if (indexCurrentToInstall >= toInstall.length) {
                    toInstall = [];
                    $('#installProgress .bar').animate({backgroundColor:'#bb6',width:'100%'}, 1000);
                    progressConsole(__('Generating cache...'));
                    $.ajax({
                            type: "GET",
                            url: $($('#main-menu a')[0]).prop('href'),
                            success: function(data) {
                                    helpers.loaded();
                                    $('#installProgress .bar').animate({backgroundColor:'#6b6'}, 1000);
                                    $('#installProgress p.status').text(__('Installation done.'));
                                    progressConsole(__('> Installation done.'));
                                    window.location.reload();
                            }
                    });
            } else {
                    installNextExtension();
            }
    }

    function styleTables(){
            // Clean all to make this function able to "restyle" after
            // data refresh.
            $('#Extensions_manager table tr').removeClass('extensionOdd')
                                                                             .removeClass('extensionEven');

            $('#Extensions_manager table tr:nth-child(even)').addClass('extensionEven');
            $('#Extensions_manager table tr:nth-child(odd)').addClass('extensionOdd');
    }

    function noAvailableExtensions(){
            var $noAvailableExtElement = $('<div/>');
            $noAvailableExtElement.attr('id', 'noExtensions')
                                                      .addClass('ui-state-highlight')
                                                      .text(__('No extensions available.'));

            $('#available-extensions-container').empty().append($noAvailableExtElement);
    }
    
    return {
        start : function(){
            
            // Table styling.
            styleTables();

            $('#installProgress').hide();
            
            $('#addButton').click(function() {
                var uri = helpers._url('create', 'ExtensionsManager', 'taoDevTools') + '?' + $.param({'uri': $('#openuri').val()});
                helpers.openTab(__('Create new extension'), uri);
                return false;
            });

            //Detect wich extension is already installed
            $('#extensions-manager-container .ext-id').each(function() {
                    var ext = $(this).text();
                    ext_installed.push(ext);
                    $('.ext-id.ext-' + ext).addClass('installed');
            });

            $('#available-extensions-container tr input').click(function(event){
                    event.stopPropagation();
            });

            $('#available-extensions-container tr input:checkbox').click(function() {
                    var $installButton = $('#installButton');
                    if ($(this).parent().parent().parent().find('input:checkbox:checked').length > 0){
                            $installButton.attr('disabled', false);
                    }
                    else{
                            $installButton.attr('disabled', true);
                    }
            });

            $('#installButton').click(function(event) {
                    //Prepare the list of extension to install in the order of dependency
                    toInstall = [];
                    $('#available-extensions-container input:checked').each(function() {
                            var ext = $(this).prop('name').split('_')[1];
                            var deps = getDependencies(ext);
                            if (deps.length) toInstall = toInstall.concat(deps);
                            toInstall.push(ext);
                    });
                    toInstall = getUnique(toInstall);
                    if (toInstall.length == 0) {
                            alert(__('Nothing to install !'));
                            return false;
                    }
                    //Let's go
                    percentByExt = 100 / toInstall.length;

                    //Show the dialog with the result
                    $('#installProgress p.status').text(__('%s extension(s) to install.').replace('%s', toInstall.length));
                    $('#installProgress .bar').width(0);
                    $('#installProgress .console').empty();
                    progressConsole(__('Do you wish to install the following extension(s):\n%s?').replace('%s', toInstall.join(', ')));
                    $('#installProgress').dialog({
                            modal: true,
                            width: 400,
                            height: 300,
                            buttons: [
                                    {
                                            text: __('No'),
                                            click: function() {
                                                    $(this).dialog('close');
                                            }
                                    },
                                    {
                                            text: __('Yes'),
                                            click: function() {
                                                    //Run the install one by one
                                                    progressConsole(__('Preparing installation...'));
                                                    $('.ui-dialog-buttonpane').remove();
                                                    installError = 0;
                                                    indexCurrentToInstall = 0;
                                                    installNextExtension();
                                            }
                                    }
                            ]
                    });
                    event.preventDefault();
            });
            
            $('.disableButton').click(function(event) {
            	var id = $(event.target).data('extid');
            	$.ajax({
                    type: "POST",
                    url: context.root_url + "taoDevTools/ExtensionsManager/disable",
                    data: 'id='+id,
                    dataType: 'json',
                    success: function(data) {
                        helpers.loaded();
                        helpers.createInfoMessage(data.message);
                        if (data.success) {
                            window.location.reload();
                        }
                    }
            	});
            });
            
            $('.enableButton').click(function(event) {
            	var id = $(event.target).data('extid');
            	$.ajax({
                    type: "POST",
                    url: context.root_url + "taoDevTools/ExtensionsManager/enable",
                    data: 'id='+id,
                    dataType: 'json',
                    success: function(data) {
                        helpers.loaded();
                        helpers.createInfoMessage(data.message);
                        if (data.success) {
                            window.location.reload();
                        }
                    }
            	});
            });
            
            $('.uninstallButton').click(function(event) {
            	var id = $(event.target).data('extid');
            	$.ajax({
                    type: "POST",
                    url: context.root_url + "taoDevTools/ExtensionsManager/uninstall",
                    data: 'id='+id,
                    dataType: 'json',
                    success: function(data) {
                        helpers.loaded();
                        helpers.createInfoMessage(data.message);
                        if (data.success) {
                            window.location.reload();
                        }
                    }
            	});
            });
        }
    };

});