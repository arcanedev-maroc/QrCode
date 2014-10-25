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
    /** @test */
    public function testCreateQrCode()
    {
        $this->assertInstanceOf('\Arcanedev\QrCode\Builder', $this->qrCode->getBuilder());
        $this->assertInstanceOf('\Arcanedev\QrCode\Entities\Color', $this->qrCode->getForegroundColor());
        $this->assertInstanceOf('\Arcanedev\QrCode\Entities\Color', $this->qrCode->getBackgroundColor());
    }

    /** @test */
    public function testGettersAndSetters()
    {
        $this->assertEquals('Hello', $this->qrCode->setText('Hello')->getText());

        $this->assertEquals(300, $this->qrCode->setSize(300)->getSize());

        $this->assertEquals(20, $this->qrCode->setPadding(20)->getPadding());

        $this->assertEquals('png', $this->qrCode->setFormat('png')->getFormat());

        $this->assertEquals(['png', 'gif', 'jpeg', 'wbmp'], $this->qrCode->getAvailableFormats());

        $this->assertFalse(false);
    }

    /** @test */
    public function testColorsGettersAndSetters()
    {
        $black = ['r' => 0,   'g' => 0,   'b' => 0];
        $white = ['r' => 255, 'g' => 255, 'b' => 255];

        $this->qrCode->setForegroundColor($black);
        $this->qrCode->setBackgroundColor($white);

        $this->assertEquals($black, $this->qrCode->getForeground());
        $this->assertTrue(in_array($this->qrCode->getForegroundHex(), ['#000', '#000000', '#000', '#000000']));
        $this->assertEquals($white, $this->qrCode->getBackground());
        $this->assertTrue(in_array($this->qrCode->getBackgroundHex(), ['#FFF', '#FFFFFF', '#fff', '#ffffff']));
    }

    /**
     * @test
     * @requires PHP > 5.3
     * @runInSeparateProcess
     */
    public function testHeaderResponseAttributes()
    {
        $this->qrCode->setFormat('png')->setHeaderResponse();
        $this->assertEquals(['Content-Type: image/png'], \xdebug_get_headers());

        $this->qrCode->setFormat('jpg')->setHeaderResponse();
        $this->assertEquals(['Content-Type: image/jpeg'], \xdebug_get_headers());

        $this->qrCode->setFormat('gif')->setHeaderResponse();
        $this->assertEquals(['Content-Type: image/gif'], \xdebug_get_headers());
    }
}
