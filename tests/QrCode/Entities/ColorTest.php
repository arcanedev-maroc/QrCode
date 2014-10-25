<?php namespace Arcanedev\QrCode\Tests\Entities;

use Arcanedev\QrCode\Entities\Color                     as Color;
use Arcanedev\QrCode\Entities\Contracts\ColorInterface  as ColorInterface;

class ColorTest extends \PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /* @var Color */
    protected $color;

    // Test Variables
    protected static $blackColor;
    protected static $whiteColor;
    protected static $badassColor;
    protected static $devilColor;
    protected static $badKeysColor;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public static function setUpBeforeClass()
    {
        self::$blackColor   = ['r' => 0,   'g' => 0,     'b' => 0];
        self::$whiteColor   = ['r' => 255, 'g' => 255,   'b' => 255];
        self::$badassColor  = ['r' => 186, 'g' => 218,   'b' => 85];
        self::$devilColor   = ['r' => 666, 'g' => 666,   'b' => 666];
        self::$badKeysColor = ['red' => 0, 'green' => 0, 'blue' => 0];
    }

    protected function setUp()
    {
        $this->color = new Color;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function testCanCreateColor()
    {
        $this->assertNotEmpty($this->color);

        $this->color->black();
        $this->assertEquals(0, $this->color->getRed());
        $this->assertEquals(0, $this->color->getGreen());
        $this->assertEquals(0, $this->color->getBlue());

        $this->color->white();
        $this->assertEquals(255, $this->color->getRed());
        $this->assertEquals(255, $this->color->getGreen());
        $this->assertEquals(255, $this->color->getBlue());
    }

    /** @test */
    public function testBasicColor()
    {
        $this->color->black();
        $this->assertEquals(self::$blackColor, $this->color->toArray());

        $this->color->white();
        $this->assertEquals(self::$whiteColor, $this->color->toArray());
    }

    /** @test */
    public function testCanSetColorFromArray()
    {
        $this->color->setFromArray(self::$badassColor);
        $this->assertEquals(186, $this->color->getRed());
        $this->assertEquals(218, $this->color->getGreen());
        $this->assertEquals(85, $this->color->getBlue());
    }

    /** @test */
    public function testCanGetColorArray()
    {
        $this->color->setFromArray(self::$badassColor);
        $this->assertEquals(self::$badassColor, $this->color->toArray());
    }

    /** @test */
    public function testCanSetHexColor()
    {
        $this->color->hex('#bada55');
        $this->assertEquals(self::$badassColor, $this->color->toArray());
    }

    /** @test */
    public function testCanGetHexColor()
    {
        $this->color->setFromArray(self::$badassColor);
        $this->assertEquals('#bada55', $this->color->getHex());
    }

    /**
     * @test
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidHexValueException
     */
    public function testHexValueMustThrowAnException()
    {
        $this->color->hex('#hashtag');
    }

    /**
     * @test
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidRGBValuesException
     */
    public function testRGBValuesMustThrowAnException()
    {
        $this->color->rgb(self::$devilColor['r'], self::$devilColor['g'], self::$devilColor['b']);
    }

    /**
     * @test
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidRGBArrayException
     */
    public function testRGBArrayOneMustThrowAnException()
    {
        $this->color->setFromArray([]);
    }

    /**
     * @test
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidRGBArrayException
     */
    public function testRGBArrayTwoMustThrowAnException()
    {
        $this->color->setFromArray(self::$badKeysColor);
    }

    /**
     * @test
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidRGBValuesException
     */
    public function testRGBArrayThreeMustThrowAnException()
    {
        $this->color->setFromArray(self::$devilColor);
    }
}
