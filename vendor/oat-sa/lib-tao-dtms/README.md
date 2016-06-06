# DateTime with Microseconds ![](https://travis-ci.org/oat-sa/lib-tao-dtms.svg)

DateTime and DateInterval classes with support of microseconds

## NOTE! This package not fully tested, so use it with caution.

This small package with two classes just wrapper around built in PHP classes for work with date and time but with support operations with microseconds.

## Installation

Package use PSR-4 standard, so for using this classes just install package using Composer.

```bash
$ composer require oat-sa/lib-tao-dtms
```

```json
{
    "require": {
        "oat-sa/lib-tao-dtms": "~0.5"
    }
}
```

```php
<?php
require 'vendor/autoload.php';

use oat\dtms\DateTime;
use oat\dtms\DateInterval;

```

## Usage

After installation you can create DateTime instances with microseconds.

```php
$dt = new DateTime('2015-08-08 10:10:10.123456');
echo $dt->format('Y-m-d H:i:s.u'); // 2015-08-08 10:10:10.123456
```

Also you can modify DateTime with microseconds. Supported words "microseconds", "microsecond", "micro", "mic".

```php
$dt = new DateTime('2015-08-08 10:10:10.123456');
$dt->modify('123456 micro');
echo $dt->format('u'); // 246912
```

And of course this package allow usage true ISO8601 format for date intervals with microseconds.

```php
$interval = new DateInterval('PT2.2S');
echo $interval->format('PT%sS'); // PT2.200000S 
```

Class DateTime support standard methods, such as `add`, `sub`, `diff`, `format` etc.

For using native DateTime and DateInteval classes together with package use root namespace for it:

```php
$uDateTime = new DateTime(); // DateTime from package
$nativeDateTime = new \DateTime(); // built in DateTime
```
 
__Note!__ Current package may work incorrectly with different time zones, so it need extra check and more tests. 
  
If you found error or weird behavior, send me [issue report](/issues/new), please.

## Credits

- [Ivan Klimchuk](http://klimchuk.com), [Alroniks](https://github.com/Alroniks)
- [Open Assessment Technologies S.A.](http://www.taotesting.com/), [OAT](https://github.com/oat-sa)

Developed by Ivan Klimchuk for Open Assessment Technologies S.A.

## License

The GPLv2. Please see [LICENSE](/LICENSE) for more information.

 
