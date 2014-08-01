<?php

use qtism\runtime\rendering\css\Utils as CssUtils;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class CssUtilsTest extends QtiSmTestCase {
    
    /**
     * @dataProvider mapSelectorProvider
     * 
     * @param string $expected
     * @param string $selector
     * @param array $map
     */
    public function testMapSelector ($selector, $expected, $map) {
        $this->assertEquals($expected, CssUtils::mapSelector($selector, $map));
    }
    
    public function mapSelectorProvider() {
        $map = array('div' => 'qtism-div',
                      'prompt' => 'qtism-prompt',
                      'a' => 'qtism-a',
                      'b' => 'qtism-b');
        
        return array(
            array('div', '.qtism-div', $map),
            array('prompt', '.qtism-prompt', $map),
            array('div prompt', '.qtism-div .qtism-prompt', $map),
            array('div > prompt', '.qtism-div > .qtism-prompt', $map),
            array('div>prompt', '.qtism-div>.qtism-prompt', $map),
            array('div div .a', '.qtism-div .qtism-div .a', $map),
            array('div >a', '.qtism-div >.qtism-a', $map),
            array('div.div', '.qtism-div.div', $map),
            array('.div~div', '.div~.qtism-div', $map),
            array('div > .cool +div + division + * .div,division + qti-div + div.golgoth div .hello.div~div div+div divdiv div>div', '.qtism-div > .cool +.qtism-div + division + * .div,division + qti-div + .qtism-div.golgoth .qtism-div .hello.div~.qtism-div .qtism-div+.qtism-div divdiv .qtism-div>.qtism-div', $map),
            array('a:hover', '.qtism-a:hover', $map),
            array('a:hover>a:hover', '.qtism-a:hover>.qtism-a:hover', $map),
            array('a[target=_blank]', '.qtism-a[target=_blank]', $map),
            array('prompt > b', '.qtism-prompt > .qtism-b', $map),
        );
    }
}