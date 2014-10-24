<?php namespace Arcanedev\QrCode\Contracts;

use Arcanedev\QrCode\Exceptions\ImageFunctionUnknownException;
use Arcanedev\QrCode\QrCode;

interface QrCodeInterface
{
    /**
     * Return text that will be hid in QR Code
     *
     * @return string
     */
    public function getText();

    /**
     * @param string $text
     *
     * @return QrCode
     */
    public function setText($text);

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getFormat();

    /**
     * Set image type for rendering
     *
     * @param string $format
     *
     * @return QrCode
     */
    public function setFormat($format);

    /**
     * Get available all available image types
     *
     * @return array
     */
    public function getAvailableFormats();

    /**
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     *
     * @return QrCode
     */
    public function setSize($size);

    /**
     * Return padding around the QR Code
     *
     * @return int
     */
    public function getPadding();

    /**
     * @param int $padding
     *
     * @return QrCode
     */
    public function setPadding($padding);

    /**
     * Return foreground color of the QR Code
     *
     * @return array
     */
    public function getForegroundColor();

    /**
     * @param $foregroundColor
     *
     * @return QrCode
     */
    public function setForegroundColor($foregroundColor);

    /**
     * @param string $foregroundHexColor
     *
     * @return QrCode
     */
    public function setForegroundHexColor($foregroundHexColor);

    /**
     * Return background color of the QR Code
     *
     * @return array
     */
    public function getBackgroundColor();

    /**
     * @param array $backgroundColor
     *
     * @return QrCode
     */
    public function setBackgroundColor($backgroundColor);

    /**
     * @param string $backgroundHexColor
     *
     * @return QrCode
     */
    public function setBackgroundHexColor($backgroundHexColor);

    /**
     * Return QR Code version
     *
     * @return int
     */
    public function getVersion();

    /**
     * Set QR Code version
     *
     * @param  int    $version QR Code version
     * @return QrCode
     */
    public function setVersion($version);

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create QR Code and return its content
     *
     * @param  string|null $format - Image type (gif, png, wbmp, jpeg)
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function get($format = null);

    /**
     * Return the data URI
     *
     * @return string
     */
    public function getDataUri();

    /**
     * Render the QR Code then save it to given file name or output it to the browser when file name omitted.
     * Available Formats of the file (png, jpeg, jpg, gif, wbmp)
     *
     * @param  null|string $format
     *
     * @throws ImageFunctionUnknownException
     *
     * @return QrCode
     */
    public function render($format = null);

    /**
     * Render the QR Code then save it to given file name
     *
     * @param string $filename
     *
     * @throws ImageFunctionUnknownException
     *
     * @return string
     */
    public function save($filename);

    /**
     * Generate img Tag with the qr-code
     *
     * @param string|null   $alt
     * @param array|null    $attributes
     *
     * @return string
     */
    public function image($alt = "", $attributes = []);
}
