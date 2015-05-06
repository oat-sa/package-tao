define(function(){
    'use strict';

    /**
     * Equivalent of PHP's str_pad.
     * This uses http://phpjs.org/functions/str_pad/ and wraps it in require.
     * There are also some slight modifications such as converting input always to a string.
     * Renamed from str_pad to strPad
     *
     * License: https://github.com/kvz/phpjs/blob/master/LICENSE.txt (MIT)
     *
     * discuss at: http://phpjs.org/functions/str_pad/
     * original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     * improved by: Michael White (http://getsprink.com)
     * input by: Marco van Oort
     * bugfixed by: Brett Zamir (http://brett-zamir.me)
     * example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
     * returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
     * example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
     * returns 2: '------Kevin van Zonneveld-----'
     *
     * @param input
     * @param pad_length
     * @param pad_string
     * @param pad_type
     * @returns {*}
     */
    var strPad = function (input, pad_length, pad_string, pad_type) {

        var half = '',
            pad_to_go;

        var str_pad_repeater = function (s, len) {
            var collect = '';

            while (collect.length < len) {
                collect += s;
            }
            collect = collect.substr(0, len);

            return collect;
        };

        input = input.toString();

        input += '';
        pad_string = pad_string !== undefined ? pad_string : ' ';

        if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
            pad_type = 'STR_PAD_RIGHT';
        }
        if ((pad_to_go = pad_length - input.length) > 0) {
            if (pad_type === 'STR_PAD_LEFT') {
                input = str_pad_repeater(pad_string, pad_to_go) + input;
            } else if (pad_type === 'STR_PAD_RIGHT') {
                input = input + str_pad_repeater(pad_string, pad_to_go);
            } else if (pad_type === 'STR_PAD_BOTH') {
                half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
                input = half + input + half;
                input = input.substr(0, pad_length);
            }
        }

        return input;
    };

    return strPad;
});
