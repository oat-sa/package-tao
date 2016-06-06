# Beeme (Basic Equation/Expression Math Engine)

Simple mathematical expression parser and calculator *based on the great work of
Adrean Boyadzhiev*.

## Install
The recommended way to install Beeme is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "oat-sa/lib-beeme": "dev-develop"
    }
}
```
## Usage

Here is an simple example of evaluation of mathematical expression
```php
<?php

$parser = new \oat\beeme\Parser();
$expression = '1 + 2 * 3 * ( 7 * 8 ) - ( 45 - 10 )';
$result = $parser->evaluate($expression);

echo $result; //302

```
## TODO
  - Add unit tests.

## License

MIT, see LICENSE.
