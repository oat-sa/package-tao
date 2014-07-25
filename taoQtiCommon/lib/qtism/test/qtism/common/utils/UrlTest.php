<?php
use qtism\common\utils\Url;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class UrlTest extends QtiSmTestCase {
	
    /**
     * @dataProvider validRelativeUrlProvider
     * @param string $url
     */
    public function testValidRelativeUrl($url) {
        $this->assertTrue(Url::isRelative($url));
    }
    
    /**
     * @dataProvider invalidRelativeUrlProvider
     * @param unknown_type $url
     */
    public function testInvalidRelativeUrl($url) {
        $this->assertFalse(Url::isRelative($url));
    }
    
    public function validRelativeUrlProvider() {
        return array(
            array('./path'),
            array('path'),
            array('../my-path'),
            array('./my-path'),
            array('path/to/something'),
            array('path/to/something/'),
            array('path/./to/../something')
        );
    }
    
    public function invalidRelativeUrlProvider() {
        return array(
            array('/'),
            array('http://www.google.com'),
            array('my+cool://www.funk.org'),
            array('my.cool.way://crazy.peop.le/funkmusik'),
            array('/home/jerome/dev'),
            array('/home/../dev'),
            array('mailto:jerome@taotesting.com'),
            array('mail:to/my-friend/')
        );
    }
}