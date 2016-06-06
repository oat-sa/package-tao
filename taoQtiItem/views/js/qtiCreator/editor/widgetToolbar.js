define([
    'jquery',
    'i18n'
], function($, __){
    'use strict';


    var defaultConfig = [
        [
            {
                title : 'Question',
                'class' : 'question-trigger',
                status : 'off', // on | disabled | off => default
                fn : function(e){
                    var target = arguments[1],
                        toolbar = arguments[2],
                        responseButton = toolbar.find('.answer-trigger a'),
                        answerEditors = target.find('[data-edit="answer"]'),
                        questionEditors = target.find('[data-edit="question"]');

                    responseButton.removeClass('tlb-text-button-on').addClass('tlb-text-button-off');
                    questionEditors.show();
                    answerEditors.hide();
                    target.data('mode', 'question');
                }
            },
            {
                title : 'Answer',
                'class' : 'answer-trigger',
                fn : function(e){
                    var target = arguments[1],
                        toolbar = arguments[2],
                        contentButton = toolbar.find('.question-trigger a'),
                        answerEditors = target.find('[data-edit="answer"]'),
                        questionEditors = target.find('[data-edit="question"]');

                    contentButton.removeClass('tlb-text-button-on').addClass('tlb-text-button-off');
                    answerEditors.show();
                    questionEditors.hide();
                    target.data('mode', 'answer');
                    target.find('[contenteditable]').attr('contenteditable', false);
                    $(document).trigger('widgetBlur');
                    //$('.cke.cke_reset_all').remove();
                    //also remove ck and unbind
                }
            }
        ],
        'spacer', // |  ' '
//    [
//      {
//        icon: 'edit',
//        title: 'Edit',
//        fn: function () {
//          var widget = $(this).parents('.widget'),
//            id = widget.attr('id') || randId();
//
//          widget.attr('contentEditable', true);
//          widget.attr('id', id);
//
//          ckeditor.inline(id);
//          //ckFocusManager.focus();
//          console.log(widget);
//        }
//      }
//    ],
//    [
//      {
//        icon: 'move',
//        title: 'Move',
//        fn: function () {
//          console.log('Move');
//        }
//      },
//      'separator', // | '-'
//      {
//        icon: 'resize-grid',
//        title: 'Resize',
//        fn: function () {
//          console.log('Resize');
//        }
//      }
//   ],
        [
            {
                icon : 'bin',
                title : 'Delete',
                fn : function(e){
                    // arguments[1] is the box the toolbar belongs to
                    // arguments[2] is the toolbar itself
                    var target = arguments[1],
                        toolbar = arguments[2],
                        msg = $('<span>', {text : __('You have deleted an element.') + ' '}),
                    undo = $('<a>', {text : __('Undo'), href : '#'}),
                    icon = $('<span>', { 'class' : 'icon-info'}),
                    closer = $('<span>', {title : 'Remove Message', 'class' : 'icon-close close-trigger'}),
                    feedback = $('<div>', { 'class' : 'feedback-info'}).hide();

                    closer.on('click', function(){
                        feedback.fadeOut(function(){
                            feedback.remove();
                            target.remove();
                            toolbar.remove();
                        });
                    });

                    undo.on('click', function(e){
                        e.preventDefault();
                        feedback.remove();
                        target.removeClass('deleted').fadeIn();
                    });

                    feedback.append(icon);
                    feedback.append(msg);
                    feedback.append(undo);
                    feedback.append(closer);
                    target.before(feedback);

                    toolbar.hide();
                    target.hide().addClass('deleted');
                    feedback.fadeIn(function(){
                        setTimeout(function(){
                            closer.trigger('click');
                        }, 6500)
                    });
                }
            }
        ]
    ];



    /**
     * build breaks, separators etc.
     * @param type
     * @returns {*|HTMLElement}
     */
    var structure = function(type){
        // makes config more similar to ckeditor
        switch(type){
            case '/':
                type = 'break';
                break;
            case '-':
                type = 'separator'
                break;
            case ' ':
                type = 'spacer'
                break;
        }
        return $('<span>', { 'class' : 'tlb-' + type});
    };


    var bar = function(items, target, toolbar){
        var bar = $('<div>', {'class' : 'tlb-bar'}),
        group,
            // does first element have an icon? if not the items are buttons
            isTextButton = !items[0].icon,
            l = items.length,
            i;


        bar.append(structure('start'));

        if(isTextButton){
            for(i = 0; i < l; i++){
                if($.type(items[i]) === 'string'){
                    bar.append(structure(items[i]));
                }
                else{
                    items[i].target = target;
                    items[i].toolbar = toolbar;
                    bar.append(button(items[i], isTextButton));
                }
            }
        }
        else{
            group = $('<div>', {'class' : 'tlb-group'});
            bar.append(group);
            for(i = 0; i < l; i++){
                if($.type(items[i]) === 'string'){
                    group.append(structure(items[i]));
                }
                else{
                    items[i].target = target;
                    items[i].toolbar = toolbar;
                    group.append(button(items[i], isTextButton));
                }
            }
        }

        bar.append(structure('end'));
        return bar;
    };

    /**
     * build a single button
     *
     * @param config
     * @param isTextButton bool
     * @returns {*|HTMLElement}
     */
    var button = function(config, isTextButton){
        var a,
            span,
            button,
            icon,
            text,
            // re-assigning and deleting serves to
            // allow any attribute in the config
            status = config.status || 'off',
            fn = config.fn || function(){
        },
            target = config.target || $(),
            toolbar = config.toolbar || $();

        delete(config.status);
        delete(config.fn);
        delete(config.target);
        delete(config.toolbar);

        if(!isTextButton){

            // handle common misconfiguration with 'icon-' prefix
            icon = 'icon-' + (config.icon.replace('icon-', ''));
            delete(config.icon);

            config['class'] = config['class'] ? config['class'] + ' tlb-button-' + status : 'tlb-button-' + status;

            button = $('<a>', config);
            span = $('<span>', {'class' : icon});
            button.append(span);
        }
        else{
            text = __(config.title);
            delete(config.title);

            config['class'] = config['class'] ? config['class'] + ' tlb-text-button-box' : 'tlb-text-button-box';

            button = $('<span>', config);
            a = $('<a>', {'class' : 'tlb-text-button-' + status});
            span = $('<span>', {'class' : 'tlb-text', text : text});
            button.append(a);
            a.append(span);
        }

        button.on('click.toolbar', function(e){
            // disabled buttons do nothing
            if(button.hasClass('tlb-text-button-disabled') || button.hasClass('tlb-button-disabled')){
                return false;
            }
            if(isTextButton){
                button.find('a').toggleClass('tlb-text-button-off').toggleClass('tlb-text-button-on');
            }
            else{
                button.toggleClass('tlb-button-off').toggleClass('tlb-button-on');
            }
            fn.apply(this, [e, target, toolbar]);
        });

        return button;
    };


    /**
     * merge configurations
     *
     * @param config
     * @param extendDefaultConfig
     * @returns {*}
     */
    var handleConfig = function(config, extendDefaultConfig){
        // config = []
        if($.isArray(config) && !config.length){
            config = false;
        }

        // config is now either a proper array or false or undefined
        // 1. false or undefined - use the default
        if(!config){
            config = defaultConfig;
        }
        // 2. proper array - should the default be extended or not?
        else if(extendDefaultConfig){
            config = $.extend(defaultConfig, config);
        }

        return config;
    };

    /**
     * Configuration and resulting toolbar structure
     *
     * tlb            // implicit
     *   top          // implicit
     *
     *     ****** config starts here *****
     *
     *     box        // = parent element []
     *       bar      // = [] | string 'break' or '/' | string 'spacer' or ' '
     *         { icon, title, fn [, status, anyAttribute] } | { title, fn [, status, anyAttribute] } | string 'separator' or '-'
     *       /bar     // implicit
     *       bar | break
     *     /box       // implicit
     *
     *     ****** config ends here *****
     *
     *   /top         // implicit
     * /tlb           // implicit
     *
     * bar configuration:
     * []                    creates for each element one of the following
     *   { icon, title, fn [, status] } creates a square button with a text icon
     *   { title, fn [, status] }       creates a button with text
     *     icon:   can be with or without prefix e.g. 'icon-move' === 'move'
     *     title:  title attribute or text when icon is not set
     *     fn:     on click handler
     *     status: optional 'off'|'on'|'disabled', default is 'off',
     *     anyAttribute: any HTML attribute, 'class' will be appended to the default button class
     *   Note: { icon, title, fn [, status] } and { icon, title [, status] } cannot be mixed within one group!
     *   'separator' or '-'    creates a vertical line
     *
     * 'break' or '/'          creates a new line
     * 'spacer' or ' '         creates a horizontal distance in the size of a square button
     *
     * @param config
     * @param target
     */
    var buildToolbar = function(config, target, tlbWrapper){

        // 1st level container
        var tlb = $('<div>', {'class' : 'tlb'}),
        // 2nd level container
        top = $('<div>', {'class' : 'tlb-top'}),
        // 3rd level container
        box = $('<div>', {'class' : 'tlb-box'}),
        i,
            ic = config.length;

        top.append(box);
        tlb.append(top);

        // box level
        for(i = 0; i < ic; i++){
            if($.type(config[i]) === 'string'){
                box.append(structure(config[i]));
            }
            else{
                box.append(bar(config[i], target, tlbWrapper));
            }
        }
        tlb.data('edit', 'toolbar');
        return tlb;
    };



    return (function(){

        /**
         * Add a tool bar to another element
         *
         * @param buttonConfig
         * @param barConfig
         * @returns {boolean}
         */
        var attach = function(buttonConfig, barConfig){

            var defaultBarConfig = {
                    offsetTop : -2,
                    title : '',
                    extendDefaultConfig : false,
                    target : $()
                },
                toolbar,
                target = $(barConfig.target),
                title,
                top,
                tlbWrapper = target.prop('tlb') || $('<div>', {'class' : 'tlb-wrapper'});

            if(!target.prop('tlb')){

                buttonConfig = handleConfig(buttonConfig, barConfig.extendDefaultConfig);
                barConfig = $.extend(defaultBarConfig, (barConfig || {}));


                toolbar = buildToolbar(buttonConfig, target, tlbWrapper);

                if(barConfig.title){
                    title = $('<span>', {text : barConfig.title, 'class' : 'tlb-title', title : barConfig.title});
                    tlbWrapper.append(title);
                }

                tlbWrapper.append(toolbar);

                target.prop('tlb', tlbWrapper);

                target.append(tlbWrapper);

                // note: css parameters cannot be passed as an object
                // since the DOM element must have its position
                // before the size can be calculated
                tlbWrapper.css('position', 'absolute');
                tlbWrapper.css('display', 'none');
                tlbWrapper.css('width', '100%');

                top = defaultBarConfig.offsetTop - tlbWrapper.height();
                tlbWrapper.css('top', top);
            }

            tlbWrapper.fadeIn();
//
//      $(document).on('click', function(e){
//        if(!target.is(e.target)
//          && !tlbWrapper.is(e.target)
//          && target.has(e.target).length === 0
//          && tlbWrapper.has(e.target).length === 0
//          && !$(e.target).hasClass('.cke')) {
//          target.find('[data-edit]').hide();
//          tlbWrapper.hide();
//          $(document).trigger('widgetBlur');
//        }
//      });

            return tlbWrapper;
        };

        return {
            attach : attach
        };

    }());

});

