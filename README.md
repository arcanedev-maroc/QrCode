QR Code Generation
==============
This library helps you generate images containing a QR code.

*By [ARCANEDEV&copy;](http://www.arcanedev.net/)*

[![Build Status](http://img.shields.io/travis/arcanedev-maroc/QrCode.svg)](http://travis-ci.org/arcanedev-maroc/QrCode)
[![Version](http://img.shields.io/packagist/v/arcanedev/qr-code.svg)](https://packagist.org/packages/arcanedev/qr-code)
[![License](http://img.shields.io/packagist/l/arcanedev/qr-code.svg)](http://opensource.org/licenses/MIT)

### Contributing
If you have any suggestions or improvements feel free to create an issue or create a Pull Request.

### Requirements

    - PHP >= 5.4.0
    - GD extension
    
## Installation

QrCode should be installed using composer. The general composer instructions can be found on the composer website.

After that, just declare the qr-code dependency as follows:

    "require" : {
        ...
        "arcanedev/qr-code": "dev-master"
    }
    
Then, run `composer.phar update` and you should be good.

## USAGE

```php
<?php

include_once "vendor/autoload.php";

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code");
$qrCode->setSize(200);

echo $qrCode->image("image alt", ['class' => 'qr-code-img']);
```

Check the exemples folder to learn some tricks.

## Laravel Version

Coming Soon

### License

The QR Code Library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
