(function () {
    var msgArea = document.getElementById('requirement-check').getElementsByClassName('requirement-msg-area')[0],
        tests = [
        ],
        i = tests.length;

    while(i--) {
        if(!Modernizr[tests[i]]) {
            msgArea.innerHTML = 'This browser is not supported by the TAO platform';
            document.documentElement.className = 'no-js';
            break;
        }
    }
}());

