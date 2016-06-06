# Element Generator #

## What is this? ##
*Element* is a php class to create all sorts of XML/HTML elements with.

## How to use it ##
There are several possibilities for the usage of the class, the basic call is `Element::elementName($args)`. 

### Basic examples ###
If the argument is a string, the argument is normally used as content of the element.

* `Element::anyElement('content'); // returns <anyElement>content</anyElement>`
  
This also works, when the content is/contains HTML/XML

* `Element::anyElement('<elem>foo</elem>'); //returns <anyElement><elem>foo</elem></anyElement>`
  
Some elements don't have actual content but a particular attribute to hold the main information - if the element is known to the class (i.e. any regular HTML element), the content is moved to this attribute.

* `Element::script('some/script/src'); // returns <script src="some/script/src"></script>`
* `Element::iframe('some/iframe/src'); // returns <iframe src="some/iframe/src" frameborder="0"></iframe>`

As you can see in the above example, certain default attributes such as frameborder for iframes are automatically added
  
* `Element::img('some/img/src'); // returns <img src="some/img/src" alt="" />`

In this example you see that auto closing of the element is done automatically

### Basic examples with the `<a>` element ###
  
* `Element::a('some/link'); // returns  <a href="some/link">some/link</a>`
  
* `Element::a('http://www.example.com/'); // returns <a href="http://www.example.com/">www.example.com</a>`

Note that `http://` has been removed in the element text

* `Element::a('foo@example.com');`
  
* `Element::a('mailto:foo@example.com');`

Both examples will return `<a href="encrypted-address-including-mailto">encrypted-address-without-mailto</a>`. Encryption is based on the method used in Symfony 1.4.
  
### Advanced examples with multiple arguments ###

All functions take up to two arguments, the arrays `$attributes` and `$settings`. `$attributes` is basically analog to the attributes of the HTML/XML element.

Let's create an `<input>` element for emails (with a useless selected for the sake of example)


    $attributes = array(
      'type' => 'email', // default is text
      'value' => 'foo', 
      'class' => 'foo bar',
      'placeholder' => 'do seomething',
      'readonly' => true, // this resolves to 'readonly'
      'selected' => 'selected', // this resolves to 'selected="selected"'
    );
    Element::input($attributes); 
    //returns <input type="email" value="foo" class="foo bar" readonly selected="selected" />

Normally you would rarely use `$settings` but if so, you need to refer the member `$settings` of the class for details.