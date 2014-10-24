<?php

include_once "../vendor/autoload.php";

// If you specify a full path (URL) in your filename, check if the folder is already exists
$filename = 'qr-code-name.png';

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code.");
$qrCode->setSize(200);
$qrCode->save($filename);

// This is a helper to echo out an image, you can use your own function/HTML tag
echo img_tag($filename, 'Your Alt Image | Optional', ['class' => 'your-css-class optional']);
