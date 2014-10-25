<?php namespace Arcanedev\QrCode\Tests\Entities;

use Arcanedev\QrCode\Entities\ImageType                     as ImageType;
use Arcanedev\QrCode\Entities\Contracts\ImageTypeInterface  as ImageTypeInterface;

class ImageTypeTest extends \PHPUnit_Framework_TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /* @var ImageType */
    protected $imageType;

    // Test Variables
    protected static $defaultType;
    protected static $allAvailableTypes;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public static function setUpBeforeClass()
    {
        self::$defaultType          = 'png';
        self::$allAvailableTypes    = ['png', 'gif', 'jpeg', 'wbmp'];
    }

    protected function setUp()
    {
        $this->imageType = new ImageType;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function testCanCreateImageType()
    {
        $this->assertEquals(self::$defaultType, $this->imageType->getDefaultType());
        $this->assertEquals(self::$allAvailableTypes, $this->imageType->getAllAvailable());
    }

    public function testCanSetAndGetImageType()
    {
        $this->assertEquals('gif', $this->imageType->set('gif')->get());

        $this->assertEquals('jpeg', $this->imageType->set('jpg')->get());

        $this->assertEquals('wbmp', $this->imageType->set('wbmp')->get());

        $this->assertEquals('png', $this->imageType->set('png')->get());
    }

    public function testCanSetAngGetExtension()
    {
        $this->assertEquals('gif', $this->imageType->set('gif')->getExtension());

        $this->assertEquals('jpg', $this->imageType->set('jpg')->getExtension());

        $this->assertEquals('wbmp', $this->imageType->set('wbmp')->getExtension());

        $this->assertEquals('png', $this->imageType->set('png')->getExtension());
    }

    /**
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidImageTypeException
     */
    public function testSetAnInvalidTypeMustThrowAnException()
    {
        $this->imageType->set('svg');
    }

    /** @test */
    public function testCanGetTypeAndExtensionFromFilename()
    {
        $this->imageType->setTypeFromFilename('images/qr-code.png');
        $this->assertEquals('png', $this->imageType->get());
        $this->assertEquals('png', $this->imageType->getExtension());

        $this->imageType->setTypeFromFilename(__DIR__ . '/../images/qr-code.gif');
        $this->assertEquals('gif', $this->imageType->get());
        $this->assertEquals('gif', $this->imageType->getExtension());

        $this->imageType->setTypeFromFilename(__DIR__ . '/../images/qr-code.jpg');
        $this->assertEquals('jpeg', $this->imageType->get());
        $this->assertEquals('jpg', $this->imageType->getExtension());

        $this->imageType->setTypeFromFilename('qr-code.wbmp');
        $this->assertEquals('wbmp', $this->imageType->get());
        $this->assertEquals('wbmp', $this->imageType->getExtension());
    }

    /**
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidFileExtensionException
     */
    public function testSetFilenameWithInvalidExtensionMustThrowException()
    {
        $this->imageType->setTypeFromFilename('sorry-but-you-cant-use.svg');
    }

    /**
     * @expectedException \Arcanedev\QrCode\Entities\Exceptions\InvalidFileExtensionException
     */
    public function testSetFilenameWithoutExtensionMustThrowException()
    {
        $this->imageType->setTypeFromFilename('nicolas-cage');
    }

    /** @test */
    public function testTypeIsAvailable()
    {
        $this->assertTrue($this->imageType->isAvailable('png'));
        $this->assertTrue($this->imageType->isAvailable('gif'));
        $this->assertTrue($this->imageType->isAvailable('jpg'));
        $this->assertTrue($this->imageType->isAvailable('wbmp'));
    }

    public function testGetFunctionNameFromType()
    {
        $this->assertEquals('imagepng', $this->imageType->set('png')->getFunctionName());
        $this->assertEquals('imagejpeg', $this->imageType->set('jpg')->getFunctionName());
        $this->assertEquals('imagegif', $this->imageType->set('gif')->getFunctionName());
        $this->assertEquals('imagewbmp', $this->imageType->set('wbmp')->getFunctionName());
    }

    /**
     * @runInSeparateProcess
     */
    public function testImageHeader()
    {
        $this->imageType->set('png')->header();
        $this->assertEquals(['Content-Type: image/png'], \xdebug_get_headers());

        $this->imageType->set('jpg')->header();
        $this->assertEquals(['Content-Type: image/jpeg'], \xdebug_get_headers());

        $this->imageType->set('gif')->header();
        $this->assertEquals(['Content-Type: image/gif'], \xdebug_get_headers());
    }
}
