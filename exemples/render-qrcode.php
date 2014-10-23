<?php

include_once "../vendor/autoload.php";

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code");
$qrCode->setSize(200);

$qrCode->render();
header('Content-Type: image/png'); // Don't forget to add the header
