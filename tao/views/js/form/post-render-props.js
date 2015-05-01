define([
    'jquery',
    'i18n'
],
    function($, __){


        /**
         * Toggle availability of mode switch (advanced/simple)
         *
         * @param newMode
         * @private
         */
        function _toggleModeBtn(newMode) {
            var $modeToggle = $('.property-mode');
            if(newMode === 'disabled') {
                $modeToggle.addClass('disabled');
            }
            else {
                $modeToggle.removeClass('disabled');
            }
        }

        /**
         * Reposition the radio buttons of a property and make them look nice.
         *
         * @private
         */
        function _upgradeRadioButtons($container) {

            $container.find('.form_radlst').not('.property-radio-list').each(function() {
                var $radioList = $(this);
                $radioList.addClass('property-radio-list');
                $radioList.parent().addClass('property-radio-list-box');
                $radioList.each(function() {
                    var $block = $(this),
                        $inputs = $block.find('input');

                    if($inputs.length <= 2) {
                        $block.find('br').remove();
                    }

                    $inputs.each(function() {
                        var $input = $(this),
                            $label = $block.find('label[for="' + this.id + '"]'),
                            $icon  = $('<span>', { 'class': 'icon-radio'});

                        $label.prepend($icon);
                        $label.prepend($input);
                    });
                });
            });
        }


        /**
         * Get reference to property container. If it doesn't' exist create one and add it to the DOM.
         *
         * @returns {*|HTMLElement}
         */
        function getPropertyContainer() {
            var $propertyContainer  = $('.content-block .property-container');
            if($propertyContainer.length) {
                return $propertyContainer;
            }
            $propertyContainer  = $('<div>', { 'class' : 'property-container' });
            $('.content-block .form-group').first().before($propertyContainer);
            return $propertyContainer;
        }


        /**
         * Add properties to the designated container. Also add some CSS classes for easier access.
         *
         * @param $properties
         * @private
         */
        function _wrapPropsInContainer($properties) {
            var $propertyContainer = getPropertyContainer($properties),
                // the reason why this is not done via a simple counter is that
                // the function could have been called multiple times, e.g. when
                // properties are created dynamically.
                hasAlreadyProperties = !!$propertyContainer.find('.property-block').length;


            $properties.each(function () {
                var $property = $(this);
                if($property.attr !== undefined){
                    var type = (function() {
                            switch($property.attr('id').replace(/_?property_[\d]+/, '')) {
                                case 'ro':
                                    return 'readonly-property';
                                case 'parent':
                                    return 'parent-property';
                                default:
                                    var $editIcon = $property.find('.icon-edit'),
                                        $editContainer = $property.children('div:first');

                                    $editContainer.addClass('property-edit-container');

                                    //on click on edit icon show property form or hide it
                                    $editIcon.on('click', function() {
                                        $editContainer.slideToggle(function() {
                                            $editContainer.parent().toggleClass('property-edit-container-open');
                                            if(!$('.property-edit-container-open').length) {
                                                _toggleModeBtn('disabled');
                                            }
                                            else {
                                                _toggleModeBtn('enabled');
                                            }
                                        });
                                    });
                                    return 'regular-property';
                            }
                        }());
                    $property.addClass(!hasAlreadyProperties ? 'property-block-first property-block ' + type : 'property-block ' + type);
                    $propertyContainer.append($property);
                    hasAlreadyProperties = true;

                }
            });
        }


        /**
         * Make properties look nice
         *
         * @param $properties (optional)
         */
        function init($properties) {
            var $container  = $('.content-block .xhtml_form:first form');

            // case no or empty argument -> find all properties not upgraded yet
            if(!$properties || !$properties.length){
                $properties = $container.children('div[id*="property_"]').not('.property-block');
            }
            if(!$properties.length) {
                return;
            }
            _wrapPropsInContainer($properties);
            _upgradeRadioButtons($container);
            _toggleModeBtn('disabled');
        }


    return {
        /**
         * Initialize post renderer, this can be done multiple times
         */
        init : init,
        getPropertyContainer: getPropertyContainer
    };
});


