define([
    'jquery',
    'taoQtiItem/qtiCreator/editor/MathEditor',
    'mathJax'
],
function($, MathEditor, mathJax) {
    'use strict';

    QUnit.module('MathEditor latex rendering');

    var texExprs = [
            { title: 'short expr', input: '\\frac1 2' },
            { title: 'broken expr that use to lock the editor', input: '\\fr<ac1 2' },
            { title: 'simple expr', input: '\\dfrac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}' },
            { title: 'medium expr 1', input: '\\sum_{i=0}^n i^2 = \\frac{(n^2+n)(2n+1)}{6}' },
            { title: 'medium expr 2', input: '\\begin{align} f(x) & = (a+b)^2 \\\\ & = a^2+2ab+b^2 \\\\ \\end{align}' },
            { title: 'complex expr', input: 'A_{m,n} = \\begin{pmatrix} a_{1,1} & a_{1,2} & \\cdots & a_{1,n} \\\\ a_{2,1} & a_{2,2} & ' +
                '\\cdots & a_{2,n} \\\\ \\vdots & \\vdots & \\ddots & \\vdots \\\\ a_{m,1} & a_{m,2} & ' +
                '\\cdots & a_{m,n} \\end{pmatrix}' }
        ],
        displayTypes = [
            { title: ' with display = block', display: 'block' },
            { title: ' with display = other', display: 'other' }
        ];

    QUnit
        .cases(texExprs)
        .combinatorial(displayTypes)
        .asyncTest('Latex rendering', 1, function test(data, assert) {
            var mathjaxRenderingDelayMs = 750,

                $buffer = $('.mj-buffer'),
                $target = $('.mj-target'),

                mathEditor = new MathEditor({
                    buffer: $buffer,
                    target: $target,
                    display: data.display
                });

            if (typeof mathJax === 'undefined') {
                assert.ok(false, 'MathJax is not available');
                QUnit.start();

            } else {
                mathEditor.setTex(data.input);
                mathEditor.renderFromTex();

                setTimeout(function checkMathJaxOutput() {
                    var actualScript = $target.find('script').text(),
                        expectedScript = '\\displaystyle{' + data.input + '}';

                    assert.ok(actualScript === expectedScript,
                        'Error in Mathjax output: expected ' + expectedScript + ' but was ' + actualScript);

                    QUnit.start();
                }, mathjaxRenderingDelayMs);
            }
        });

});


