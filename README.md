QR Code Generation
==============

*By [ARCANEDEV](http://www.arcanedev.net/)*

This library helps you generate images containing a QR code.

```php
<?php

include_once "vendor/autoload.php";

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code");
$qrCode->setSize(200);

$qrCode->render();
header('Content-Type: image/png'); // Don't forget to add the header
```

Check the exemple folders for more tricks

## Laravel Version

Coming Soon

## License

This package is under the MIT license.
For the full copyright and license information, please view the LICENSE file that was distributed with this source code.