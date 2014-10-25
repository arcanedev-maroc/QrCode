<?php

include_once "../vendor/autoload.php";

use Arcanedev\QrCode\QrCode;

$qrCode = new QrCode;
$qrCode->setText("I would love to change the world, but they won't give me the source code")
       ->setSize(200);

$qrCode->render();
