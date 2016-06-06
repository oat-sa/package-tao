define(['jquery'], function($) {

    var formElementHelper = {
        initShufflePinToggle: function(widget) {

            var $container = widget.$container,
                choice = widget.element,
                interaction = choice.getInteraction(),
                $shuffleToggle = $container.find('[data-role="shuffle-pin"]');

            $shuffleToggle.off('mousedown').on('mousedown', function(e) {
                e.stopPropagation();
                var $icon = $(this).children();
                if ($icon.length === 0) {
                    $icon = $(this);
                }
                if ($icon.hasClass('icon-shuffle')) {
                    $icon.removeClass('icon-shuffle').addClass('icon-pin');
                    choice.attr('fixed', true);
                } else {
                    $icon.removeClass('icon-pin').addClass('icon-shuffle');
                    choice.attr('fixed', false);
                }
            });

            var _toggleVisibility = function(show) {
                if (show) {
                    $shuffleToggle.show();
                } else {
                    $shuffleToggle.hide();
                }
                $('.qti-item').trigger('toolbarchange', {
                    callee: 'formElementHelper'
                });
            };

            _toggleVisibility(interaction.attr('shuffle'));

            //listen to interaction property change
            widget.on('attributeChange', function(data) {
                if (data.element.serial === interaction.serial && data.key === 'shuffle') {
                    _toggleVisibility(data.value);
                }
            });
        },
        initDelete: function(widget) {

            var $container = widget.$container;

            $container.find('[data-role="delete"]:not([data-html-editable] *)').on('mousedown', function(e) {
                e.stopPropagation();
                widget.changeState('deleting');
            });
        }
    };

    return formElementHelper;
});
