<?php

include_once "../vendor/autoload.php";

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code.");
$qrCode->setSize(200);

// If you specify a full path in your filename, check if the folder is already exists
$qrCode->save(__DIR__ . '/qr-code-name.png');
