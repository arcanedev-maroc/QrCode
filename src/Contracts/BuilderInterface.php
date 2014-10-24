<?php namespace Arcanedev\QrCode\Contracts;

use Arcanedev\QrCode\Entities\Contracts\ColorInterface;
use Arcanedev\QrCode\Exceptions\DataDoesNotExistsException;
use Arcanedev\QrCode\Exceptions\ImageSizeTooLargeException;
use Arcanedev\QrCode\Exceptions\VersionTooLargeException;
use OverflowException;

interface BuilderInterface
{
    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Return text that will be hid in QR Code
     *
     * @return string
     */
    public function getText();

    /**
     * Set text to hide in QR Code
     *
     * @param  string $text
     */
    public function setText($text);

    /**
     * @return resource
     */
    public function getImage();

    /**
     * Return QR Code version
     *
     * @return int
     */
    public function getVersion();

    /**
     * Set QR Code version
     *
     * @param  int $version
     */
    public function setVersion($version);

    /**
     * Set foreground color of the QR Code
     *
     * @param  array  $color_foreground RGB color
     *
     * @return BuilderInterface
     */
    public function setForegroundColor($color_foreground);

    /**
     * @param string $foregroundHexColor
     *
     * @return BuilderInterface
     */
    public function setForegroundHexColor($foregroundHexColor);

    /**
     * @param string $backgroundHexColor
     *
     * @return BuilderInterface
     */
    public function setBackgroundHexColor($backgroundHexColor);

    /**
     * Return foreground color of the QR Code
     *
     * @return ColorInterface
     */
    public function getForegroundColor();

    /**
     * Set background color of the QR Code
     *
     * @param  array  $backgroundColor RGB color
     *
     * @return BuilderInterface
     */
    public function setBackgroundColor($backgroundColor);


    /**
     * Return background color of the QR Code
     *
     * @return ColorInterface
     */
    public function getBackgroundColor();

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getImageFormat();

    /**
     * Set image type for rendering
     *
     * @param  string $type - Image type
     */
    public function setImageFormat($type);

    public function getAvailableImageTypes();

    /**
     * Set structure append
     *
     * @param int    $n
     * @param int    $m
     * @param int    $parity
     * @param string $originalData
     *
     * @return BuilderInterface
     */
    public function setStructureAppend($n, $m, $parity, $originalData);

    /**
     * Set QR Code error correction level
     *
     * @param int $errorCorrection
     *
     * @return BuilderInterface
     */
    public function setErrorCorrection($errorCorrection);

    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    public function getErrorCorrection();

    /**
     * Return QR Code module size
     *
     * @return int
     */
    public function getModuleSize();

    /**
     * Set QR Code module size
     *
     * @param  int    $moduleSize Module size
     *
     * @return BuilderInterface
     */
    public function setModuleSize($moduleSize);

    /**
     * Set QR Code size (width)
     *
     * @param  int    $size Width of the QR Code
     *
     * @return BuilderInterface
     */
    public function setSize($size);

    /**
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize();

    /**
     * Set padding around the QR Code
     *
     * @param  int    $padding Padding around QR Code
     *
     * @return BuilderInterface
     */
    public function setPadding($padding);

    /**
     * Return padding around the QR Code
     *
     * @return int
     */
    public function getPadding();

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create the image.
     *
     * @throws DataDoesNotExistsException
     * @throws VersionTooLargeException
     * @throws ImageSizeTooLargeException
     * @throws OverflowException
     *
     * return resource
     */
    public function create();

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if image is already created
     *
     * @return bool
     */
    public function hasImage();
}
