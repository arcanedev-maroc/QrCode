<?php namespace Arcanedev\QrCode\Tests;

use Arcanedev\QrCode\QrCode                     as QrCode;
use Arcanedev\QrCode\Contracts\QrCodeInterface  as QrCodeInterface;

class QrCodeTest extends \PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /* @var QrCode */
    protected $qrCode;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        $this->qrCode = new QrCode;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function testCreateQrCode()
    {
        $this->qrCode->setText("Test this test");
        $this->qrCode->setSize(300);
        $this->qrCode->get();

        $this->assertTrue(true);
    }

    public function testIfThereIsMoreTests()
    {
        // TODO: Add more tests

        $this->assertFalse(false);
    }
}
